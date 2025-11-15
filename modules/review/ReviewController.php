<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/ReviewModel.php';
require_once __DIR__ . '/../../core/AdminMiddleware.php';

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
            $_SESSION['error'] = 'Vui lòng chọn điểm đánh giá!';
            $this->redirect('?route=movie/watch&id=' . $movie_id . '#reviews');
        }
        
        $reviewModel = new ReviewModel();
        $review_id = $reviewModel->create([
            'user_id' => $user['id'],
            'movie_id' => $movie_id,
            'rating' => $rating,
            'comment' => $comment
        ]);
        
        // Log activity nếu là admin
        if ($this->isAdmin()) {
            require_once __DIR__ . '/../../core/AdminMiddleware.php';
            AdminMiddleware::logAction(
                $user['id'],
                'Thêm bình luận',
                'Review',
                'review',
                $review_id,
                null,
                ['movie_id' => $movie_id, 'rating' => $rating]
            );
        }
        
        $_SESSION['success'] = 'Đánh giá của bạn đã được gửi!';
        $this->redirect('?route=movie/watch&id=' . $movie_id . '#reviews');
    }
    
    public function delete() {
        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này!';
            $this->redirect('');
        }
        
        $id = $_GET['id'] ?? null;
        $movie_id = $_GET['movie_id'] ?? null;
        
        if (!$id || !$movie_id) {
            $_SESSION['error'] = 'Thông tin không hợp lệ!';
            $this->redirect('?route=movie/watch&id=' . $movie_id . '#reviews');
        }
        
        $reviewModel = new ReviewModel();
        $review = $reviewModel->getById($id);
        
        if (!$review) {
            $_SESSION['error'] = 'Bình luận không tồn tại!';
            $this->redirect('?route=movie/watch&id=' . $movie_id . '#reviews');
        }
        
        // Log activity
        $user = $this->getCurrentUser();
        AdminMiddleware::logAction(
            $user['id'],
            'Xóa bình luận',
            'Review',
            'review',
            $id,
            [
                'review_id' => $id,
                'user_id' => $review['user_id'],
                'user_name' => $review['user_name'] ?? 'N/A',
                'movie_id' => $review['movie_id'],
                'movie_title' => $review['movie_title'] ?? 'N/A',
                'rating' => $review['rating'] ?? null,
                'comment' => $review['comment'] ?? '',
                'created_at' => $review['created_at'] ?? null
            ],
            null
        );
        
        $reviewModel->delete($id);
        $_SESSION['success'] = 'Đã xóa bình luận thành công!';
        $this->redirect('?route=movie/watch&id=' . $movie_id . '#reviews');
    }
    
    public function pin() {
        if (!$this->isAdmin()) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này!';
            $this->redirect('');
        }
        
        $id = $_GET['id'] ?? null;
        $movie_id = $_GET['movie_id'] ?? null;
        $is_pinned = $_GET['pin'] ?? 0;
        
        if (!$id || !$movie_id) {
            $_SESSION['error'] = 'Thông tin không hợp lệ!';
            $this->redirect('?route=movie/watch&id=' . $movie_id . '#reviews');
        }
        
        $reviewModel = new ReviewModel();
        $review = $reviewModel->getById($id);
        
        if (!$review) {
            $_SESSION['error'] = 'Bình luận không tồn tại!';
            $this->redirect('?route=movie/watch&id=' . $movie_id . '#reviews');
        }
        
        // Log activity
        $user = $this->getCurrentUser();
        AdminMiddleware::logAction(
            $user['id'],
            $is_pinned ? 'Ghim bình luận' : 'Bỏ ghim bình luận',
            'Review',
            'review',
            $id,
            ['is_pinned' => $review['is_pinned'] ?? 0],
            ['is_pinned' => $is_pinned]
        );
        
        $reviewModel->togglePin($id, $is_pinned ? 1 : 0);
        $_SESSION['success'] = $is_pinned ? 'Đã ghim bình luận!' : 'Đã bỏ ghim bình luận!';
        $this->redirect('?route=movie/watch&id=' . $movie_id . '#reviews');
    }
}
?>

