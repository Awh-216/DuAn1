<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/MovieModel.php';
require_once __DIR__ . '/CategoryModel.php';
require_once __DIR__ . '/../review/ReviewModel.php';
require_once __DIR__ . '/WatchHistoryModel.php';

class MovieController extends Controller {
    
    public function index() {
        $movieModel = new MovieModel();
        $categoryModel = new CategoryModel();
        
        $search = trim($_GET['search'] ?? '');
        $category_id = !empty($_GET['category']) ? intval($_GET['category']) : null;
        $status = $_GET['status'] ?? null;
        $country = $_GET['country'] ?? null;
        $min_rating = isset($_GET['min_rating']) && $_GET['min_rating'] !== '' ? floatval($_GET['min_rating']) : null;
        
        if ($search) {
            $movies = $movieModel->search($search, $category_id, $status, $country, $min_rating);
        } elseif ($category_id) {
            $sql = "SELECT m.*, c.name as category_name FROM movies m 
                    LEFT JOIN categories c ON m.category_id = c.id 
                    WHERE m.category_id = ?";
            $params = [$category_id];
            
            if ($status) {
                $sql .= " AND m.status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY m.rating DESC, m.created_at DESC";
            $movies = $movieModel->getDb()->fetchAll($sql, $params);
        } elseif ($country) {
            $movies = $movieModel->getByCountry($country);
        } else {
            $sql = "SELECT m.*, c.name as category_name FROM movies m 
                    LEFT JOIN categories c ON m.category_id = c.id 
                    WHERE 1=1";
            $params = [];
            
            if ($status) {
                $sql .= " AND m.status = ?";
                $params[] = $status;
            }
            
            if ($min_rating !== null) {
                $sql .= " AND m.rating >= ?";
                $params[] = $min_rating;
            }
            
            $sql .= " ORDER BY m.created_at DESC";
            $movies = $movieModel->getDb()->fetchAll($sql, $params);
        }
        
        $categories = $categoryModel->getAll();
        
        // Lấy danh sách quốc gia để filter
        $countries = $movieModel->getDb()->fetchAll("
            SELECT DISTINCT country 
            FROM movies 
            WHERE country IS NOT NULL AND country != '' 
            ORDER BY country
        ");
        
        $this->view('movie/index', [
            'movies' => $movies,
            'categories' => $categories,
            'countries' => $countries,
            'search' => $search,
            'category_id' => $category_id,
            'status' => $status,
            'country' => $country,
            'min_rating' => $min_rating,
            'user' => $this->getCurrentUser()
        ]);
    }
    
    public function watch() {
        $movieModel = new MovieModel();
        $reviewModel = new ReviewModel();
        
        $movie_id = $_GET['id'] ?? null;
        
        if (!$movie_id) {
            $this->redirect('movie');
        }
        
        $movie = $movieModel->getById($movie_id);
        
        if (!$movie) {
            $this->redirect('movie');
        }
        
        // Lưu lịch sử xem
        $user = $this->getCurrentUser();
        if ($user) {
            $watchHistoryModel = new WatchHistoryModel();
            $watchHistoryModel->add($user['id'], $movie_id);
        }
        
        $reviews = $reviewModel->getByMovie($movie_id);
        
        // Kiểm tra nếu user là admin
        $isAdmin = false;
        if ($user) {
            require_once __DIR__ . '/../../core/AdminMiddleware.php';
            $isAdmin = AdminMiddleware::hasRole($user['id'], 'Super Admin') || 
                      AdminMiddleware::hasRole($user['id'], 'Admin') ||
                      (isset($user['role']) && $user['role'] === 'admin');
        }
        
        $this->view('movie/watch', [
            'movie' => $movie,
            'reviews' => $reviews,
            'user' => $user,
            'isAdmin' => $isAdmin
        ]);
    }
}
?>

