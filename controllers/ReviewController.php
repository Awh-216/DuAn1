<?php
class ReviewController extends Controller {
    
    public function create() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('movie');
        }
        
        $user = $this->getCurrentUser();
        $movie_id = $_POST['movie_id'] ?? null;
        $rating = $_POST['rating'] ?? null;
        $comment = $_POST['comment'] ?? '';
        
        if (!$movie_id || !$rating) {
            $this->redirect('movie/watch?id=' . $movie_id);
        }
        
        $reviewModel = new ReviewModel();
        $reviewModel->create([
            'user_id' => $user['id'],
            'movie_id' => $movie_id,
            'rating' => $rating,
            'comment' => $comment
        ]);
        
        $this->redirect('movie/watch?id=' . $movie_id);
    }
}
?>

