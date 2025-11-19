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
        $type = $_GET['type'] ?? null;
        $min_rating = isset($_GET['min_rating']) && $_GET['min_rating'] !== '' ? floatval($_GET['min_rating']) : null;
        
        if ($search) {
            $movies = $movieModel->search($search, $category_id, $status, $country, $min_rating, $type);
        } elseif ($category_id) {
            $sql = "SELECT m.*, c.name as category_name FROM movies m 
                    LEFT JOIN categories c ON m.category_id = c.id 
                    WHERE m.category_id = ?";
            $params = [$category_id];
            
            if ($status) {
                $sql .= " AND m.status = ?";
                $params[] = $status;
            }
            
            if ($type) {
                $sql .= " AND m.type = ?";
                $params[] = $type;
            }
            
            $sql .= " ORDER BY m.rating DESC, m.created_at DESC";
            $movies = $movieModel->getDb()->fetchAll($sql, $params);
        } elseif ($country) {
            $movies = $movieModel->getByCountry($country, $type);
        } else {
            $sql = "SELECT m.*, c.name as category_name FROM movies m 
                    LEFT JOIN categories c ON m.category_id = c.id 
                    WHERE 1=1";
            $params = [];
            
            if ($status) {
                $sql .= " AND m.status = ?";
                $params[] = $status;
            }
            
            if ($type) {
                $sql .= " AND m.type = ?";
                $params[] = $type;
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
        
        // Lấy danh sách favorites của user nếu đã đăng nhập
        $favorites = [];
        $user = $this->getCurrentUser();
        if ($user) {
            $watchHistoryModel = new WatchHistoryModel();
            $favoriteMovies = $movieModel->getDb()->fetchAll("
                SELECT movie_id 
                FROM watch_history 
                WHERE user_id = ? AND favorite = 1
            ", [$user['id']]);
            $favorites = array_column($favoriteMovies, 'movie_id');
        }
        
        $this->view('movie/index', [
            'movies' => $movies,
            'categories' => $categories,
            'countries' => $countries,
            'search' => $search,
            'category_id' => $category_id,
            'status' => $status,
            'country' => $country,
            'type' => $type,
            'min_rating' => $min_rating,
            'user' => $user,
            'favorites' => $favorites
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
        
        // Kiểm tra gói thành viên
        $user = $this->getCurrentUser();
        if (!$user) {
            $_SESSION['error'] = 'Vui lòng đăng nhập để xem phim!';
            $this->redirect('?route=auth/login');
            return;
        }
        
        // Kiểm tra quyền xem phim dựa trên level
        $movieLevel = $movie['level'] ?? 'Free';
        $hasAccess = $this->checkMovieAccess($user, $movieLevel);
        
        if (!$hasAccess) {
            $subscriptionName = $this->getUserSubscriptionName($user['id']);
            $_SESSION['error'] = "Phim này yêu cầu gói {$movieLevel}. Gói hiện tại của bạn: " . ($subscriptionName ?: 'Free');
            $this->redirect('movie');
            return;
        }
        
        // Lưu lịch sử xem
        if ($user) {
            $watchHistoryModel = new WatchHistoryModel();
            $watchHistoryModel->add($user['id'], $movie_id);
        }
        
        $reviews = $reviewModel->getByMovie($movie_id);
        
        // Lấy episodes nếu là phim bộ
        $episodes = [];
        $currentEpisode = null;
        $episode_id = $_GET['episode_id'] ?? null;
        
        // Kiểm tra type của phim - hỗ trợ cả 'phimbo' và 'phim bộ'
        $movieType = $movie['type'] ?? 'phimle';
        $isPhimBo = ($movieType === 'phimbo' || $movieType === 'phim bộ');
        
        // Debug: Kiểm tra type của phim
        error_log("Movie type: " . $movieType . " for movie ID: " . $movie_id . " | Is Phim Bo: " . ($isPhimBo ? 'Yes' : 'No'));
        
        if ($isPhimBo) {
            require_once __DIR__ . '/../../core/Database.php';
            $db = Database::getInstance();
            try {
                // Kiểm tra xem bảng episodes có tồn tại không
                $db->fetch("SELECT 1 FROM episodes LIMIT 1");
                
                // Lấy tất cả episodes của phim này (kể cả chưa có video_url)
                $episodes = $db->fetchAll("SELECT * FROM episodes WHERE movie_id = ? ORDER BY episode_number ASC", [$movie_id]);
                error_log("Found " . count($episodes) . " episodes for movie ID: " . $movie_id);
                
                // Nếu không có episode_id và có tập, tự động chuyển đến tập 1 (chỉ nếu có video_url)
                if (!$episode_id && !empty($episodes)) {
                    // Tìm tập đầu tiên có video_url
                    $firstEpisodeWithVideo = null;
                    foreach ($episodes as $ep) {
                        if (!empty($ep['video_url'])) {
                            $firstEpisodeWithVideo = $ep;
                            break;
                        }
                    }
                    
                    // Nếu có tập với video, chuyển đến tập đó
                    if ($firstEpisodeWithVideo) {
                        $this->redirect('movie/watch&id=' . $movie_id . '&episode_id=' . $firstEpisodeWithVideo['id']);
                        return;
                    }
                    // Nếu không có tập nào có video, chọn tập đầu tiên (người dùng sẽ thấy thông báo)
                }
                
                // Lấy tập hiện tại nếu có episode_id
                if ($episode_id) {
                    $currentEpisode = $db->fetch("SELECT * FROM episodes WHERE id = ? AND movie_id = ?", [$episode_id, $movie_id]);
                    if (!$currentEpisode) {
                        error_log("Warning: Episode ID " . $episode_id . " not found for movie ID: " . $movie_id);
                        // Nếu episode không tồn tại, tự động chuyển đến tập đầu tiên có video
                        if (!empty($episodes)) {
                            $firstEpisodeWithVideo = null;
                            foreach ($episodes as $ep) {
                                if (!empty($ep['video_url'])) {
                                    $firstEpisodeWithVideo = $ep;
                                    break;
                                }
                            }
                            if ($firstEpisodeWithVideo) {
                                $this->redirect('movie/watch&id=' . $movie_id . '&episode_id=' . $firstEpisodeWithVideo['id']);
                                return;
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                // Log lỗi để debug
                error_log("Error fetching episodes for movie ID " . $movie_id . ": " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                $episodes = [];
                
                // Nếu lỗi do bảng chưa tồn tại, thông báo rõ ràng
                if (strpos($e->getMessage(), "doesn't exist") !== false || 
                    strpos($e->getMessage(), "Unknown table") !== false) {
                    error_log("CRITICAL: Episodes table does not exist! Please run the SQL schema to create it.");
                }
            }
        } else {
            error_log("Movie ID " . $movie_id . " is not phimbo. Type: " . $movieType);
        }
        
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
            'episodes' => $episodes,
            'currentEpisode' => $currentEpisode,
            'user' => $user,
            'isAdmin' => $isAdmin
        ]);
    }
    
    /**
     * Kiểm tra quyền truy cập phim dựa trên gói thành viên
     */
    private function checkMovieAccess($user, $movieLevel) {
        // Admin luôn có quyền xem
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }
        
        try {
            require_once __DIR__ . '/../../core/AdminMiddleware.php';
            if (AdminMiddleware::hasRole($user['id'], 'Super Admin') || 
                AdminMiddleware::hasRole($user['id'], 'Admin')) {
                return true;
            }
        } catch (Exception $e) {
            // Bỏ qua nếu bảng chưa tồn tại
        }
        
        // Free phim ai cũng xem được
        if ($movieLevel === 'Free') {
            return true;
        }
        
        // Lấy thông tin subscription của user
        $subscriptionName = $this->getUserSubscriptionName($user['id']);
        if (!$subscriptionName) {
            return false; // Không có gói
        }
        
        // Map subscription names to levels
        $levelHierarchy = [
            'Free' => 0,
            'Basic' => 1,
            'Silver' => 2,
            'Gold' => 3,
            'Premium' => 4
        ];
        
        $userLevel = $levelHierarchy[$subscriptionName] ?? 0;
        $requiredLevel = $levelHierarchy[$movieLevel] ?? 0;
        
        return $userLevel >= $requiredLevel;
    }
    
    /**
     * Lấy tên gói subscription của user
     */
    private function getUserSubscriptionName($userId) {
        require_once __DIR__ . '/../../core/Database.php';
        $db = Database::getInstance();
        
        $user = $db->fetch("
            SELECT u.subscription_id, s.name as subscription_name 
            FROM users u 
            LEFT JOIN subscriptions s ON u.subscription_id = s.id 
            WHERE u.id = ?
        ", [$userId]);
        
        return $user['subscription_name'] ?? null;
    }
    
    /**
     * Toggle favorite cho một phim
     */
    public function toggleFavorite() {
        if (!$this->isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Vui lòng đăng nhập']);
            exit;
        }
        
        $user = $this->getCurrentUser();
        $movie_id = $_POST['movie_id'] ?? $_GET['movie_id'] ?? null;
        
        if (!$movie_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Thiếu thông tin phim']);
            exit;
        }
        
        try {
            $watchHistoryModel = new WatchHistoryModel();
            $favorite = $watchHistoryModel->toggleFavorite($user['id'], $movie_id);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'favorite' => $favorite == 1,
                'message' => $favorite == 1 ? 'Đã thêm vào yêu thích' : 'Đã xóa khỏi yêu thích'
            ]);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
        }
        exit;
    }
}
?>

