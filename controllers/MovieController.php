<?php
class MovieController extends Controller {
    
    public function index() {
        $movieModel = new MovieModel();
        $categoryModel = new CategoryModel();
        
        $search = $_GET['search'] ?? '';
        $category_id = $_GET['category'] ?? null;
        
        if ($search) {
            $movies = $movieModel->search($search, $category_id);
        } elseif ($category_id) {
            $movies = $movieModel->getByCategory($category_id);
        } else {
            $movies = $movieModel->getAll();
        }
        
        $categories = $categoryModel->getAll();
        
        $this->view('movie/index', [
            'movies' => $movies,
            'categories' => $categories,
            'search' => $search,
            'category_id' => $category_id,
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
        
        $this->view('movie/watch', [
            'movie' => $movie,
            'reviews' => $reviews,
            'user' => $user
        ]);
    }
}
?>

