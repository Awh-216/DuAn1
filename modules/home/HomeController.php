<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../movie/MovieModel.php';

class HomeController extends Controller {
    
    public function index() {
        $movieModel = new MovieModel();
        
        // Lấy slider phim nổi bật - mix cả phim lẻ và phim bộ
        // Ưu tiên phim có rating cao và thumbnail đẹp
        $sliderMovies = $movieModel->getDb()->fetchAll("
            SELECT m.*, c.name as category_name 
            FROM movies m 
            LEFT JOIN categories c ON m.category_id = c.id 
            WHERE m.status = 'Chiếu online' 
            AND m.status_admin = 'published'
            AND m.thumbnail IS NOT NULL 
            AND m.thumbnail != ''
            ORDER BY m.rating DESC, RAND()
            LIMIT 5
        ");
        
        // Nếu không đủ 5 phim, lấy thêm từ phim khác (không bao gồm phim chiếu rạp)
        if (count($sliderMovies) < 5) {
            $additionalMovies = $movieModel->getDb()->fetchAll("
                SELECT m.*, c.name as category_name 
                FROM movies m 
                LEFT JOIN categories c ON m.category_id = c.id 
                WHERE m.status != 'Chiếu rạp'
                AND m.status_admin = 'published'
                AND m.thumbnail IS NOT NULL 
                AND m.thumbnail != ''
                AND m.id NOT IN (" . (!empty($sliderMovies) ? implode(',', array_column($sliderMovies, 'id')) : '0') . ")
                ORDER BY m.rating DESC, RAND()
                LIMIT " . (5 - count($sliderMovies)) . "
            ");
            $sliderMovies = array_merge($sliderMovies, $additionalMovies);
        }
        
        // Shuffle để random thứ tự hiển thị
        if (!empty($sliderMovies)) {
            shuffle($sliderMovies);
        }
        
        // Lấy phim lẻ và phim bộ riêng biệt cho section (không bao gồm phim chiếu rạp)
        $phimLe = $movieModel->getDb()->fetchAll("
            SELECT m.*, c.name as category_name 
            FROM movies m 
            LEFT JOIN categories c ON m.category_id = c.id 
            WHERE (m.type = 'phimle' OR m.type IS NULL)
            AND m.status != 'Chiếu rạp'
            AND m.status_admin = 'published'
            ORDER BY m.rating DESC, m.created_at DESC 
            LIMIT 8
        ");
        
        $phimBo = $movieModel->getDb()->fetchAll("
            SELECT m.*, c.name as category_name 
            FROM movies m 
            LEFT JOIN categories c ON m.category_id = c.id 
            WHERE m.type = 'phimbo'
            AND m.status != 'Chiếu rạp'
            AND m.status_admin = 'published'
            ORDER BY m.rating DESC, m.created_at DESC 
            LIMIT 8
        ");
        
        // Phim mới nhất - cả phim lẻ và phim bộ (không bao gồm phim chiếu rạp)
        $latestMovies = $movieModel->getDb()->fetchAll("
            SELECT m.*, c.name as category_name 
            FROM movies m 
            LEFT JOIN categories c ON m.category_id = c.id 
            WHERE m.status != 'Chiếu rạp'
            AND m.status_admin = 'published'
            ORDER BY m.created_at DESC 
            LIMIT 12
        ");
        
        // Lấy danh sách favorites của user nếu đã đăng nhập
        $favorites = [];
        $user = $this->getCurrentUser();
        if ($user) {
            require_once __DIR__ . '/../movie/WatchHistoryModel.php';
            $favoriteMovies = $movieModel->getDb()->fetchAll("
                SELECT movie_id 
                FROM watch_history 
                WHERE user_id = ? AND favorite = 1
            ", [$user['id']]);
            $favorites = array_column($favoriteMovies, 'movie_id');
        }
        
        $this->view('home/index', [
            'sliderMovies' => $sliderMovies,
            'latestMovies' => $latestMovies,
            'phimLe' => $phimLe,
            'phimBo' => $phimBo,
            'user' => $user,
            'favorites' => $favorites
        ]);
    }
}
?>

