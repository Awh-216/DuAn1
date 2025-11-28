<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/AdminMiddleware.php';
require_once __DIR__ . '/../../core/Database.php';

class ModeratorController extends Controller {
    
    private $theaterId;
    
    public function __construct() {
        parent::__construct();
        $user = AdminMiddleware::checkAdmin();
        
        // Kiểm tra xem user có phải moderator không
        if (!AdminMiddleware::isModerator($user['id'])) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: http://localhost/DuAn1/');
            exit;
        }
        
        // Lấy theater_id được gán cho moderator
        $this->theaterId = AdminMiddleware::getModeratorTheater($user['id']);
        
        if (!$this->theaterId) {
            $_SESSION['error'] = 'Bạn chưa được gán quản lý rạp nào!';
            header('Location: http://localhost/DuAn1/');
            exit;
        }
    }
    
    protected function moderatorView($view, $data = []) {
        extract($data);
        $current_page = $data['current_page'] ?? '';
        $title = $data['title'] ?? 'Quản lý rạp';
        $user = $data['user'] ?? AdminMiddleware::checkAdmin();
        $theaterId = $this->theaterId;
        
        // Lấy thông tin rạp
        $db = Database::getInstance();
        $theater = $db->fetch("SELECT * FROM theaters WHERE id = ?", [$theaterId]);
        
        ob_start();
        require_once __DIR__ . '/views/moderator/' . $view . '.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/views/moderator/layout.php';
    }
    
    // Dashboard cho moderator
    public function index() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        $theaterId = $this->theaterId;
        
        // Lấy thông tin rạp
        $theater = $db->fetch("SELECT * FROM theaters WHERE id = ?", [$theaterId]);
        
        // Thống kê rạp
        $stats = [
            'total_showtimes' => $db->fetch("SELECT COUNT(*) as count FROM showtimes WHERE theater_id = ?", [$theaterId])['count'],
            'today_showtimes' => $db->fetch("SELECT COUNT(*) as count FROM showtimes WHERE theater_id = ? AND show_date = CURDATE()", [$theaterId])['count'],
            'total_tickets' => $db->fetch("SELECT COUNT(*) as count FROM tickets t JOIN showtimes s ON t.showtime_id = s.id WHERE s.theater_id = ? AND t.status = 'Đã đặt'", [$theaterId])['count'],
            'today_tickets' => $db->fetch("SELECT COUNT(*) as count FROM tickets t JOIN showtimes s ON t.showtime_id = s.id WHERE s.theater_id = ? AND t.status = 'Đã đặt' AND DATE(t.created_at) = CURDATE()", [$theaterId])['count'],
            'total_revenue' => $db->fetch("SELECT SUM(t.amount) as total FROM transactions t JOIN tickets tk ON t.related_id = tk.id JOIN showtimes s ON tk.showtime_id = s.id WHERE s.theater_id = ? AND t.status = 'Thành công' AND t.type = 'ticket'", [$theaterId])['total'] ?? 0,
            'today_revenue' => $db->fetch("SELECT SUM(t.amount) as total FROM transactions t JOIN tickets tk ON t.related_id = tk.id JOIN showtimes s ON tk.showtime_id = s.id WHERE s.theater_id = ? AND t.status = 'Thành công' AND t.type = 'ticket' AND DATE(t.created_at) = CURDATE()", [$theaterId])['total'] ?? 0,
        ];
        
        // Doanh thu 7 ngày gần nhất
        $revenueByDay = $db->fetchAll("
            SELECT DATE(t.created_at) as date, SUM(t.amount) as revenue
            FROM transactions t
            JOIN tickets tk ON t.related_id = tk.id
            JOIN showtimes s ON tk.showtime_id = s.id
            WHERE s.theater_id = ? AND t.status = 'Thành công' AND t.type = 'ticket' 
            AND t.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(t.created_at)
            ORDER BY date ASC
        ", [$theaterId]);
        
        // Tạo mảng đầy đủ 7 ngày
        $allDays = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $allDays[$date] = ['date' => $date, 'revenue' => 0];
        }
        
        foreach ($revenueByDay as $day) {
            if (isset($allDays[$day['date']])) {
                $allDays[$day['date']] = $day;
            }
        }
        
        $revenueByDay = array_values($allDays);
        
        // Top phim được đặt vé nhiều nhất tại rạp
        $topMovies = $db->fetchAll("
            SELECT m.title, COUNT(tk.id) as ticket_count, SUM(tr.amount) as revenue
            FROM tickets tk
            JOIN showtimes s ON tk.showtime_id = s.id
            JOIN movies m ON s.movie_id = m.id
            LEFT JOIN transactions tr ON tr.related_id = tk.id AND tr.type = 'ticket' AND tr.status = 'Thành công'
            WHERE s.theater_id = ? AND tk.status = 'Đã đặt'
            GROUP BY m.id, m.title
            ORDER BY ticket_count DESC
            LIMIT 5
        ", [$theaterId]);
        
        $this->moderatorView('dashboard', [
            'theater' => $theater,
            'stats' => $stats,
            'revenueByDay' => $revenueByDay,
            'topMovies' => $topMovies,
            'user' => $user,
            'title' => 'Dashboard - ' . $theater['name'],
            'current_page' => 'dashboard'
        ]);
    }
    
    // Quản lý lịch chiếu
    public function showtimes() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        $theaterId = $this->theaterId;
        
        $theater = $db->fetch("SELECT * FROM theaters WHERE id = ?", [$theaterId]);
        
        // Lấy lịch chiếu của rạp
        $showtimes = $db->fetchAll("
            SELECT s.*, m.title as movie_title, m.thumbnail, th.name as theater_name, ts.screen_name
            FROM showtimes s
            JOIN movies m ON s.movie_id = m.id
            JOIN theaters th ON s.theater_id = th.id
            LEFT JOIN theater_screens ts ON s.screen_id = ts.id
            WHERE s.theater_id = ?
            ORDER BY s.show_date DESC, s.show_time DESC
        ", [$theaterId]);
        
        // Lấy danh sách phim để chọn
        $movies = $db->fetchAll("SELECT id, title FROM movies WHERE status = 'Đang chiếu' OR status = 'Chiếu rạp' ORDER BY title");
        
        // Lấy danh sách phòng của rạp
        $screens = $db->fetchAll("SELECT * FROM theater_screens WHERE theater_id = ? AND is_active = 1 ORDER BY screen_name", [$theaterId]);
        
        $this->moderatorView('showtimes', [
            'theater' => $theater,
            'showtimes' => $showtimes,
            'movies' => $movies,
            'screens' => $screens,
            'user' => $user,
            'title' => 'Quản lý lịch chiếu - ' . $theater['name'],
            'current_page' => 'showtimes'
        ]);
    }
    
    // Thêm lịch chiếu mới
    public function showtimesStore() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        $theaterId = $this->theaterId;
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('moderator/showtimes');
            return;
        }
        
        $movie_id = $_POST['movie_id'] ?? null;
        $screen_id = $_POST['screen_id'] ?? null;
        $show_date = $_POST['show_date'] ?? null;
        $show_time = $_POST['show_time'] ?? null;
        $price = $_POST['price'] ?? null;
        
        if (!$movie_id || !$screen_id || !$show_date || !$show_time || !$price) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            $this->redirect('moderator/showtimes');
            return;
        }
        
        // Kiểm tra screen thuộc rạp này
        $screen = $db->fetch("SELECT * FROM theater_screens WHERE id = ? AND theater_id = ?", [$screen_id, $theaterId]);
        if (!$screen) {
            $_SESSION['error'] = 'Phòng không thuộc rạp của bạn!';
            $this->redirect('moderator/showtimes');
            return;
        }
        
        try {
            $db->execute("
                INSERT INTO showtimes (movie_id, theater_id, screen_id, show_date, show_time, price)
                VALUES (?, ?, ?, ?, ?, ?)
            ", [$movie_id, $theaterId, $screen_id, $show_date, $show_time, $price]);
            
            AdminMiddleware::logAction(
                $user['id'],
                'Thêm lịch chiếu',
                'Showtime',
                'showtime',
                $db->lastInsertId(),
                null,
                ['movie_id' => $movie_id, 'theater_id' => $theaterId, 'show_date' => $show_date, 'show_time' => $show_time]
            );
            
            $_SESSION['success'] = 'Thêm lịch chiếu thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        $this->redirect('moderator/showtimes');
    }
    
    // Sửa lịch chiếu
    public function showtimesEdit() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        $theaterId = $this->theaterId;
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('moderator/showtimes');
            return;
        }
        
        // Kiểm tra lịch chiếu thuộc rạp này
        $showtime = $db->fetch("
            SELECT s.*, m.title as movie_title, ts.screen_name
            FROM showtimes s
            JOIN movies m ON s.movie_id = m.id
            LEFT JOIN theater_screens ts ON s.screen_id = ts.id
            WHERE s.id = ? AND s.theater_id = ?
        ", [$id, $theaterId]);
        
        if (!$showtime) {
            $_SESSION['error'] = 'Lịch chiếu không tồn tại hoặc không thuộc rạp của bạn!';
            $this->redirect('moderator/showtimes');
            return;
        }
        
        $theater = $db->fetch("SELECT * FROM theaters WHERE id = ?", [$theaterId]);
        $movies = $db->fetchAll("SELECT id, title FROM movies WHERE status = 'Đang chiếu' OR status = 'Chiếu rạp' ORDER BY title");
        $screens = $db->fetchAll("SELECT * FROM theater_screens WHERE theater_id = ? AND is_active = 1 ORDER BY screen_name", [$theaterId]);
        
        $this->moderatorView('showtimes_edit', [
            'theater' => $theater,
            'showtime' => $showtime,
            'movies' => $movies,
            'screens' => $screens,
            'user' => $user,
            'title' => 'Sửa lịch chiếu - ' . $theater['name'],
            'current_page' => 'showtimes'
        ]);
    }
    
    // Cập nhật lịch chiếu
    public function showtimesUpdate() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        $theaterId = $this->theaterId;
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('moderator/showtimes');
            return;
        }
        
        $id = $_POST['id'] ?? null;
        $movie_id = $_POST['movie_id'] ?? null;
        $screen_id = $_POST['screen_id'] ?? null;
        $show_date = $_POST['show_date'] ?? null;
        $show_time = $_POST['show_time'] ?? null;
        $price = $_POST['price'] ?? null;
        
        if (!$id || !$movie_id || !$screen_id || !$show_date || !$show_time || !$price) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            $this->redirect('moderator/showtimes');
            return;
        }
        
        // Kiểm tra lịch chiếu thuộc rạp này
        $oldShowtime = $db->fetch("SELECT * FROM showtimes WHERE id = ? AND theater_id = ?", [$id, $theaterId]);
        if (!$oldShowtime) {
            $_SESSION['error'] = 'Lịch chiếu không tồn tại hoặc không thuộc rạp của bạn!';
            $this->redirect('moderator/showtimes');
            return;
        }
        
        // Kiểm tra screen thuộc rạp này
        $screen = $db->fetch("SELECT * FROM theater_screens WHERE id = ? AND theater_id = ?", [$screen_id, $theaterId]);
        if (!$screen) {
            $_SESSION['error'] = 'Phòng không thuộc rạp của bạn!';
            $this->redirect('moderator/showtimes');
            return;
        }
        
        try {
            $db->execute("
                UPDATE showtimes 
                SET movie_id = ?, screen_id = ?, show_date = ?, show_time = ?, price = ?
                WHERE id = ? AND theater_id = ?
            ", [$movie_id, $screen_id, $show_date, $show_time, $price, $id, $theaterId]);
            
            AdminMiddleware::logAction(
                $user['id'],
                'Cập nhật lịch chiếu',
                'Showtime',
                'showtime',
                $id,
                ['movie_id' => $oldShowtime['movie_id'], 'show_date' => $oldShowtime['show_date'], 'show_time' => $oldShowtime['show_time']],
                ['movie_id' => $movie_id, 'show_date' => $show_date, 'show_time' => $show_time]
            );
            
            $_SESSION['success'] = 'Cập nhật lịch chiếu thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        $this->redirect('moderator/showtimes');
    }
    
    // Xóa lịch chiếu
    public function showtimesDelete() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        $theaterId = $this->theaterId;
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('moderator/showtimes');
            return;
        }
        
        // Kiểm tra lịch chiếu thuộc rạp này
        $showtime = $db->fetch("SELECT * FROM showtimes WHERE id = ? AND theater_id = ?", [$id, $theaterId]);
        if (!$showtime) {
            $_SESSION['error'] = 'Lịch chiếu không tồn tại hoặc không thuộc rạp của bạn!';
            $this->redirect('moderator/showtimes');
            return;
        }
        
        try {
            $db->execute("DELETE FROM showtimes WHERE id = ? AND theater_id = ?", [$id, $theaterId]);
            
            AdminMiddleware::logAction(
                $user['id'],
                'Xóa lịch chiếu',
                'Showtime',
                'showtime',
                $id,
                ['movie_id' => $showtime['movie_id'], 'show_date' => $showtime['show_date'], 'show_time' => $showtime['show_time']],
                null
            );
            
            $_SESSION['success'] = 'Xóa lịch chiếu thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        $this->redirect('moderator/showtimes');
    }
    
    // Thêm phòng mới
    public function screensStore() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        $theaterId = $this->theaterId;
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('moderator/showtimes');
            return;
        }
        
        $screen_name = $_POST['screen_name'] ?? '';
        $total_seats = intval($_POST['total_seats'] ?? 0);
        $screen_type = $_POST['screen_type'] ?? '2D';
        
        if (empty($screen_name) || $total_seats <= 0) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin phòng!';
            $this->redirect('moderator/showtimes');
            return;
        }
        
        try {
            $db->execute("
                INSERT INTO theater_screens (theater_id, screen_name, total_seats, screen_type, is_active)
                VALUES (?, ?, ?, ?, 1)
            ", [$theaterId, $screen_name, $total_seats, $screen_type]);
            
            AdminMiddleware::logAction(
                $user['id'],
                'Thêm phòng',
                'Screen',
                'screen',
                $db->lastInsertId(),
                null,
                ['screen_name' => $screen_name, 'theater_id' => $theaterId]
            );
            
            $_SESSION['success'] = 'Thêm phòng thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        $this->redirect('moderator/showtimes');
    }
    
    // Quản lý vé
    public function tickets() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        $theaterId = $this->theaterId;
        
        $theater = $db->fetch("SELECT * FROM theaters WHERE id = ?", [$theaterId]);
        
        // Lấy vé của rạp
        $tickets = $db->fetchAll("
            SELECT t.*, u.name as user_name, u.email as user_email, 
                   m.title as movie_title, s.show_date, s.show_time, s.price,
                   th.name as theater_name
            FROM tickets t
            JOIN users u ON t.user_id = u.id
            JOIN showtimes s ON t.showtime_id = s.id
            JOIN movies m ON s.movie_id = m.id
            JOIN theaters th ON s.theater_id = th.id
            WHERE s.theater_id = ?
            ORDER BY t.created_at DESC
        ", [$theaterId]);
        
        // Thống kê
        $stats = [
            'total' => count($tickets),
            'sold' => count(array_filter($tickets, function($t) { return $t['status'] === 'Đã đặt'; })),
            'cancelled' => count(array_filter($tickets, function($t) { return $t['status'] === 'Đã hủy'; })),
            'pending' => count(array_filter($tickets, function($t) { return $t['status'] === 'Chờ thanh toán'; }))
        ];
        
        $this->moderatorView('tickets', [
            'theater' => $theater,
            'tickets' => $tickets,
            'stats' => $stats,
            'user' => $user,
            'title' => 'Quản lý vé - ' . $theater['name'],
            'current_page' => 'tickets'
        ]);
    }
    
    // Thông tin rạp
    public function theater() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        $theaterId = $this->theaterId;
        
        $theater = $db->fetch("SELECT * FROM theaters WHERE id = ?", [$theaterId]);
        
        // Lấy danh sách phòng chiếu
        $screens = $db->fetchAll("SELECT * FROM theater_screens WHERE theater_id = ? ORDER BY screen_name", [$theaterId]);
        
        $this->moderatorView('theater', [
            'theater' => $theater,
            'screens' => $screens,
            'user' => $user,
            'title' => 'Thông tin rạp - ' . $theater['name'],
            'current_page' => 'theater'
        ]);
    }
    
    // Cập nhật thông tin rạp
    public function theaterUpdate() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        $theaterId = $this->theaterId;
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('moderator/theater');
            return;
        }
        
        $name = $_POST['name'] ?? '';
        $location = $_POST['location'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $total_screens = intval($_POST['total_screens'] ?? 1);
        
        if (empty($name)) {
            $_SESSION['error'] = 'Tên rạp không được để trống!';
            $this->redirect('moderator/theater');
            return;
        }
        
        try {
            // Lấy thông tin rạp cũ để log
            $oldTheater = $db->fetch("SELECT * FROM theaters WHERE id = ?", [$theaterId]);
            
            $db->execute("
                UPDATE theaters 
                SET name = ?, location = ?, phone = ?, address = ?, total_screens = ?
                WHERE id = ?
            ", [$name, $location, $phone, $address, $total_screens, $theaterId]);
            
            // Log activity
            AdminMiddleware::logAction(
                $user['id'],
                'Cập nhật rạp',
                'Theater',
                'theater',
                $theaterId,
                ['name' => $oldTheater['name'] ?? '', 'location' => $oldTheater['location'] ?? ''],
                ['name' => $name, 'location' => $location]
            );
            
            $_SESSION['success'] = 'Cập nhật thông tin rạp thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        $this->redirect('moderator/theater');
    }
}

