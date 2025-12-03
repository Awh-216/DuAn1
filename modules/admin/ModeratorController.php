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
            header('Location: ?route=');
            exit;
        }
        
        // Lấy theater_id được gán cho moderator
        $this->theaterId = AdminMiddleware::getModeratorTheater($user['id']);
        
        if (!$this->theaterId) {
            $_SESSION['error'] = 'Bạn chưa được gán quản lý rạp nào!';
            header('Location: ?route=');
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
        
        // Thống kê cho rạp được gán
        $stats = [
            'total_showtimes' => $db->fetch("SELECT COUNT(*) as count FROM showtimes s INNER JOIN theater_screens ts ON s.screen_id = ts.id WHERE ts.theater_id = ?", [$this->theaterId])['count'] ?? 0,
            'today_showtimes' => $db->fetch("SELECT COUNT(*) as count FROM showtimes s INNER JOIN theater_screens ts ON s.screen_id = ts.id WHERE ts.theater_id = ? AND s.show_date = CURDATE()", [$this->theaterId])['count'] ?? 0,
            'total_tickets' => $db->fetch("SELECT COUNT(*) as count FROM tickets t INNER JOIN showtimes s ON t.showtime_id = s.id INNER JOIN theater_screens ts ON s.screen_id = ts.id WHERE ts.theater_id = ?", [$this->theaterId])['count'] ?? 0,
            'today_tickets' => $db->fetch("SELECT COUNT(*) as count FROM tickets t INNER JOIN showtimes s ON t.showtime_id = s.id INNER JOIN theater_screens ts ON s.screen_id = ts.id WHERE ts.theater_id = ? AND DATE(t.created_at) = CURDATE()", [$this->theaterId])['count'] ?? 0,
            'total_revenue' => $db->fetch("SELECT COALESCE(SUM(t.price), 0) as revenue FROM tickets t INNER JOIN showtimes s ON t.showtime_id = s.id INNER JOIN theater_screens ts ON s.screen_id = ts.id WHERE ts.theater_id = ? AND t.status = 'Đã đặt'", [$this->theaterId])['revenue'] ?? 0,
            'today_revenue' => $db->fetch("SELECT COALESCE(SUM(t.price), 0) as revenue FROM tickets t INNER JOIN showtimes s ON t.showtime_id = s.id INNER JOIN theater_screens ts ON s.screen_id = ts.id WHERE ts.theater_id = ? AND DATE(t.created_at) = CURDATE() AND t.status = 'Đã đặt'", [$this->theaterId])['revenue'] ?? 0
        ];
        
        // Doanh thu 7 ngày gần nhất
        $revenueByDay = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $revenue = $db->fetch("
                SELECT COALESCE(SUM(t.price), 0) as revenue 
                FROM tickets t 
                INNER JOIN showtimes s ON t.showtime_id = s.id 
                INNER JOIN theater_screens ts ON s.screen_id = ts.id 
                WHERE ts.theater_id = ? AND DATE(t.created_at) = ? AND t.status = 'Đã đặt'
            ", [$this->theaterId, $date])['revenue'] ?? 0;
            $revenueByDay[] = ['date' => $date, 'revenue' => $revenue];
        }
        
        // Top phim bán chạy
        $topMovies = $db->fetchAll("
            SELECT m.id, m.title, COUNT(t.id) as ticket_count, COALESCE(SUM(t.price), 0) as revenue
            FROM movies m
            INNER JOIN showtimes s ON m.id = s.movie_id
            INNER JOIN theater_screens ts ON s.screen_id = ts.id
            LEFT JOIN tickets t ON s.id = t.showtime_id AND t.status = 'Đã đặt'
            WHERE ts.theater_id = ?
            GROUP BY m.id, m.title
            ORDER BY ticket_count DESC
            LIMIT 5
        ", [$this->theaterId]);
        
        $this->moderatorView('dashboard', [
            'user' => $user,
            'theaterId' => $this->theaterId,
            'stats' => $stats,
            'revenueByDay' => $revenueByDay,
            'topMovies' => $topMovies,
            'title' => 'Dashboard',
            'current_page' => 'dashboard'
        ]);
    }
    
    // Xem và xử lý các yêu cầu thay đổi quyền
    public function permissionRequests() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        // Lấy tất cả các request chưa được xử lý cho rạp này
        $requests = $db->fetchAll("
            SELECT mpr.*, 
                   u1.name as requested_by_name,
                   u2.name as target_user_name,
                   t.name as theater_name
            FROM moderator_permission_requests mpr
            LEFT JOIN users u1 ON mpr.requested_by = u1.id
            LEFT JOIN users u2 ON mpr.target_user_id = u2.id
            LEFT JOIN theaters t ON mpr.theater_id = t.id
            WHERE mpr.theater_id = ? AND mpr.status = 'pending'
            ORDER BY mpr.created_at DESC
        ", [$this->theaterId]);
        
        // Nếu có id cụ thể, lấy request đó
        $requestId = $_GET['id'] ?? null;
        $selectedRequest = null;
        if ($requestId) {
            $selectedRequest = $db->fetch("
                SELECT mpr.*, 
                       u1.name as requested_by_name,
                       u1.email as requested_by_email,
                       u2.name as target_user_name,
                       u2.email as target_user_email,
                       t.name as theater_name
                FROM moderator_permission_requests mpr
                LEFT JOIN users u1 ON mpr.requested_by = u1.id
                LEFT JOIN users u2 ON mpr.target_user_id = u2.id
                LEFT JOIN theaters t ON mpr.theater_id = t.id
                WHERE mpr.id = ? AND mpr.theater_id = ?
            ", [$requestId, $this->theaterId]);
        }
        
        $this->moderatorView('permission_requests', [
            'user' => $user,
            'requests' => $requests,
            'selectedRequest' => $selectedRequest,
            'title' => 'Yêu cầu thay đổi quyền',
            'current_page' => 'permission_requests'
        ]);
    }
    
    // Chấp nhận hoặc từ chối request
    public function handlePermissionRequest() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('moderator/permissionRequests');
            return;
        }
        
        $requestId = $_POST['request_id'] ?? null;
        $action = $_POST['action'] ?? null; // 'approve' or 'reject'
        
        if (!$requestId || !$action) {
            $_SESSION['error'] = 'Thiếu thông tin!';
            $this->redirect('moderator/permissionRequests');
            return;
        }
        
        // Lấy request
        $request = $db->fetch("
            SELECT * FROM moderator_permission_requests 
            WHERE id = ? AND theater_id = ? AND status = 'pending'
        ", [$requestId, $this->theaterId]);
        
        if (!$request) {
            $_SESSION['error'] = 'Yêu cầu không tồn tại hoặc đã được xử lý!';
            $this->redirect('moderator/permissionRequests');
            return;
        }
        
        // Kiểm tra quyền: chỉ moderator của rạp này mới được xử lý
        if ($request['moderator_id'] != $user['id']) {
            $_SESSION['error'] = 'Bạn không có quyền xử lý yêu cầu này!';
            $this->redirect('moderator/permissionRequests');
            return;
        }
        
        if ($action === 'approve') {
            // Parse dữ liệu
            $newData = json_decode($request['new_data'], true);
            $targetUserId = $request['target_user_id'];
            $newRole = $newData['role'] ?? null;
            $theaterId = $newData['theater_id'] ?? null;
            
            // Cập nhật role và theater_id
            if ($newRole === 'moderator' && $theaterId) {
                try {
                    $db->execute("UPDATE users SET role = ?, theater_id = ? WHERE id = ?", [$newRole, $theaterId, $targetUserId]);
                } catch (Exception $e) {
                    error_log("Error updating user role: " . $e->getMessage());
                    $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật quyền!';
                    $this->redirect('moderator/permissionRequests');
                    return;
                }
            } else {
                $db->execute("UPDATE users SET role = ? WHERE id = ?", [$newRole, $targetUserId]);
            }
            
            // Cập nhật status của request
            $db->execute("
                UPDATE moderator_permission_requests 
                SET status = 'approved', responded_at = NOW() 
                WHERE id = ?
            ", [$requestId]);
            
            // Gửi thông báo cho admin đã yêu cầu
            try {
                $targetUser = $db->fetch("SELECT name FROM users WHERE id = ?", [$targetUserId]);
                $notificationMessage = "Yêu cầu thay đổi quyền của {$targetUser['name']} đã được chấp nhận bởi moderator của rạp.";
                $db->execute("
                    INSERT INTO notifications (user_id, type, title, message, link, is_read)
                    VALUES (?, 'success', 'Yêu cầu đã được chấp nhận', ?, ?, 0)
                ", [$request['requested_by'], $notificationMessage, '?route=admin/users']);
            } catch (Exception $e) {
                error_log("Error creating notification: " . $e->getMessage());
            }
            
            $_SESSION['success'] = 'Đã chấp nhận yêu cầu thay đổi quyền!';
        } else if ($action === 'reject') {
            // Cập nhật status của request
            $db->execute("
                UPDATE moderator_permission_requests 
                SET status = 'rejected', responded_at = NOW() 
                WHERE id = ?
            ", [$requestId]);
            
            // Gửi thông báo cho admin đã yêu cầu
            try {
                $targetUser = $db->fetch("SELECT name FROM users WHERE id = ?", [$request['target_user_id']]);
                $notificationMessage = "Yêu cầu thay đổi quyền của {$targetUser['name']} đã bị từ chối bởi moderator của rạp.";
                $db->execute("
                    INSERT INTO notifications (user_id, type, title, message, link, is_read)
                    VALUES (?, 'error', 'Yêu cầu bị từ chối', ?, ?, 0)
                ", [$request['requested_by'], $notificationMessage, '?route=admin/users']);
            } catch (Exception $e) {
                error_log("Error creating notification: " . $e->getMessage());
            }
            
            $_SESSION['success'] = 'Đã từ chối yêu cầu thay đổi quyền!';
        }
        
        $this->redirect('moderator/permissionRequests');
    }
    
    // Showtimes Management
    public function showtimes() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $date = $_GET['date'] ?? date('Y-m-d');
        
        // Lấy danh sách lịch chiếu - lấy tất cả showtimes của rạp, không chỉ theo ngày
        // Nếu có filter date thì lọc theo date, nếu không thì lấy tất cả
        if ($date) {
            $showtimes = $db->fetchAll("
                SELECT s.*, m.title as movie_title, m.thumbnail,
                       ts.screen_name, ts.screen_type
                FROM showtimes s
                INNER JOIN movies m ON s.movie_id = m.id
                LEFT JOIN theater_screens ts ON s.screen_id = ts.id
                WHERE s.theater_id = ? AND s.show_date = ?
                ORDER BY s.show_date DESC, s.show_time ASC
            ", [$this->theaterId, $date]);
        } else {
            // Lấy tất cả showtimes của rạp (7 ngày tới)
            $today = date('Y-m-d');
            $nextWeek = date('Y-m-d', strtotime('+7 days'));
            $showtimes = $db->fetchAll("
                SELECT s.*, m.title as movie_title, m.thumbnail,
                       ts.screen_name, ts.screen_type
                FROM showtimes s
                INNER JOIN movies m ON s.movie_id = m.id
                LEFT JOIN theater_screens ts ON s.screen_id = ts.id
                WHERE s.theater_id = ? AND s.show_date >= ? AND s.show_date <= ?
                ORDER BY s.show_date ASC, s.show_time ASC
            ", [$this->theaterId, $today, $nextWeek]);
        }
        
        // Lấy danh sách phim
        $movies = $db->fetchAll("SELECT id, title FROM movies WHERE status = 'Chiếu rạp' ORDER BY title");
        
        // Lấy danh sách phòng
        $screens = $db->fetchAll("
            SELECT id, screen_name, total_seats, screen_type 
            FROM theater_screens 
            WHERE theater_id = ? 
            ORDER BY screen_name
        ", [$this->theaterId]);
        
        $this->moderatorView('showtimes', [
            'user' => $user,
            'showtimes' => $showtimes,
            'movies' => $movies,
            'screens' => $screens,
            'date' => $date,
            'title' => 'Quản lý lịch chiếu',
            'current_page' => 'showtimes'
        ]);
    }
    
    public function showtimesStore() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
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
        
        // Kiểm tra phòng thuộc rạp này
        $screen = $db->fetch("SELECT * FROM theater_screens WHERE id = ? AND theater_id = ?", [$screen_id, $this->theaterId]);
        if (!$screen) {
            $_SESSION['error'] = 'Phòng chiếu không thuộc rạp của bạn!';
            $this->redirect('moderator/showtimes');
            return;
        }
        
        // Lấy theater_id từ screen (đảm bảo có theater_id)
        $theater_id = $screen['theater_id'] ?? $this->theaterId;
        
        try {
            $db->execute("
                INSERT INTO showtimes (movie_id, theater_id, screen_id, show_date, show_time, price)
                VALUES (?, ?, ?, ?, ?, ?)
            ", [$movie_id, $theater_id, $screen_id, $show_date, $show_time, $price]);
            
            $_SESSION['success'] = 'Thêm lịch chiếu thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        $this->redirect('moderator/showtimes');
    }
    
    public function showtimesUpdate() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
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
        $showtime = $db->fetch("
            SELECT s.* FROM showtimes s
            INNER JOIN theater_screens ts ON s.screen_id = ts.id
            WHERE s.id = ? AND ts.theater_id = ?
        ", [$id, $this->theaterId]);
        
        if (!$showtime) {
            $_SESSION['error'] = 'Lịch chiếu không thuộc rạp của bạn!';
            $this->redirect('moderator/showtimes');
            return;
        }
        
        // Kiểm tra phòng thuộc rạp này
        $screen = $db->fetch("SELECT * FROM theater_screens WHERE id = ? AND theater_id = ?", [$screen_id, $this->theaterId]);
        if (!$screen) {
            $_SESSION['error'] = 'Phòng chiếu không thuộc rạp của bạn!';
            $this->redirect('moderator/showtimes');
            return;
        }
        
        try {
            $db->execute("
                UPDATE showtimes 
                SET movie_id = ?, screen_id = ?, show_date = ?, show_time = ?, price = ?
                WHERE id = ?
            ", [$movie_id, $screen_id, $show_date, $show_time, $price, $id]);
            
            $_SESSION['success'] = 'Cập nhật lịch chiếu thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        $this->redirect('moderator/showtimes');
    }
    
    public function showtimesDelete() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Không tìm thấy lịch chiếu!';
            $this->redirect('moderator/showtimes');
            return;
        }
        
        // Kiểm tra lịch chiếu thuộc rạp này
        $showtime = $db->fetch("
            SELECT s.* FROM showtimes s
            INNER JOIN theater_screens ts ON s.screen_id = ts.id
            WHERE s.id = ? AND ts.theater_id = ?
        ", [$id, $this->theaterId]);
        
        if (!$showtime) {
            $_SESSION['error'] = 'Lịch chiếu không thuộc rạp của bạn!';
            $this->redirect('moderator/showtimes');
            return;
        }
        
        try {
            $db->execute("DELETE FROM showtimes WHERE id = ?", [$id]);
            $_SESSION['success'] = 'Xóa lịch chiếu thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        $this->redirect('moderator/showtimes');
    }
    
    // Screens Management
    public function screens() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $movie_id = $_GET['movie_id'] ?? null;
        
        // Lấy danh sách phòng
        $screens = $db->fetchAll("
            SELECT ts.*, 
                   GROUP_CONCAT(DISTINCT m.title SEPARATOR ', ') as current_movies
            FROM theater_screens ts
            LEFT JOIN showtimes s ON ts.id = s.screen_id AND s.show_date >= CURDATE()
            LEFT JOIN movies m ON s.movie_id = m.id
            WHERE ts.theater_id = ?
            GROUP BY ts.id
            ORDER BY ts.screen_name
        ", [$this->theaterId]);
        
        // Xử lý current_movies thành array
        foreach ($screens as &$screen) {
            if ($screen['current_movies']) {
                $screen['current_movies'] = array_map(function($title) {
                    return ['title' => trim($title)];
                }, explode(',', $screen['current_movies']));
            } else {
                $screen['current_movies'] = [];
            }
        }
        unset($screen);
        
        // Lọc theo phim nếu có
        if ($movie_id) {
            $screens = array_filter($screens, function($screen) use ($movie_id, $db) {
                $hasMovie = $db->fetch("
                    SELECT COUNT(*) as count FROM showtimes 
                    WHERE screen_id = ? AND movie_id = ? AND show_date >= CURDATE()
                ", [$screen['id'], $movie_id])['count'] > 0;
                return $hasMovie;
            });
        }
        
        // Lấy danh sách phim
        $movies = $db->fetchAll("
            SELECT DISTINCT m.id, m.title 
            FROM movies m
            INNER JOIN showtimes s ON m.id = s.movie_id
            INNER JOIN theater_screens ts ON s.screen_id = ts.id
            WHERE ts.theater_id = ? AND s.show_date >= CURDATE()
            ORDER BY m.title
        ", [$this->theaterId]);
        
        $this->moderatorView('screens', [
            'user' => $user,
            'screens' => $screens,
            'movies' => $movies,
            'title' => 'Quản lý phòng chiếu',
            'current_page' => 'screens'
        ]);
    }
    
    public function screensStore() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('moderator/screens');
            return;
        }
        
        $screen_name = $_POST['screen_name'] ?? null;
        $total_seats = $_POST['total_seats'] ?? null;
        $screen_type = $_POST['screen_type'] ?? '2D';
        
        if (!$screen_name || !$total_seats) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            $this->redirect('moderator/screens');
            return;
        }
        
        try {
            $db->execute("
                INSERT INTO theater_screens (theater_id, screen_name, total_seats, screen_type, is_active)
                VALUES (?, ?, ?, ?, 1)
            ", [$this->theaterId, $screen_name, $total_seats, $screen_type]);
            
            // Cập nhật total_screens của theater
            $db->execute("
                UPDATE theaters 
                SET total_screens = (SELECT COUNT(*) FROM theater_screens WHERE theater_id = ?)
                WHERE id = ?
            ", [$this->theaterId, $this->theaterId]);
            
            $_SESSION['success'] = 'Thêm phòng chiếu thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        $this->redirect('moderator/screens');
    }
    
    public function screenEdit() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Không tìm thấy phòng chiếu!';
            $this->redirect('moderator/screens');
            return;
        }
        
        $screen = $db->fetch("
            SELECT * FROM theater_screens 
            WHERE id = ? AND theater_id = ?
        ", [$id, $this->theaterId]);
        
        if (!$screen) {
            $_SESSION['error'] = 'Phòng chiếu không thuộc rạp của bạn!';
            $this->redirect('moderator/screens');
            return;
        }
        
        $this->moderatorView('screen_edit', [
            'user' => $user,
            'screen' => $screen,
            'title' => 'Chỉnh sửa layout phòng',
            'current_page' => 'screens'
        ]);
    }
    
    public function screenLayoutUpdate() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('moderator/screens');
            return;
        }
        
        $id = $_POST['screen_id'] ?? null;
        $layout = $_POST['layout'] ?? null;
        
        if (!$id || !$layout) {
            $_SESSION['error'] = 'Thiếu thông tin!';
            $this->redirect('moderator/screens');
            return;
        }
        
        // Kiểm tra phòng thuộc rạp này
        $screen = $db->fetch("
            SELECT * FROM theater_screens 
            WHERE id = ? AND theater_id = ?
        ", [$id, $this->theaterId]);
        
        if (!$screen) {
            $_SESSION['error'] = 'Phòng chiếu không thuộc rạp của bạn!';
            $this->redirect('moderator/screens');
            return;
        }
        
        try {
            $db->execute("
                UPDATE theater_screens 
                SET layout = ?
                WHERE id = ?
            ", [$layout, $id]);
            
            $_SESSION['success'] = 'Cập nhật layout thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        $this->redirect('moderator/screens');
    }
    
    public function screenMovies() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $screen_id = $_GET['screen_id'] ?? null;
        if (!$screen_id) {
            echo json_encode(['error' => 'Thiếu thông tin']);
            return;
        }
        
        // Kiểm tra phòng thuộc rạp này
        $screen = $db->fetch("
            SELECT * FROM theater_screens 
            WHERE id = ? AND theater_id = ?
        ", [$screen_id, $this->theaterId]);
        
        if (!$screen) {
            echo json_encode(['error' => 'Phòng chiếu không thuộc rạp của bạn']);
            return;
        }
        
        $movies = $db->fetchAll("
            SELECT DISTINCT m.id, m.title, m.thumbnail
            FROM movies m
            INNER JOIN showtimes s ON m.id = s.movie_id
            WHERE s.screen_id = ? AND s.show_date >= CURDATE()
            ORDER BY m.title
        ", [$screen_id]);
        
        header('Content-Type: application/json');
        echo json_encode(['movies' => $movies]);
    }
    
    public function screenAddMovie() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('moderator/screens');
            return;
        }
        
        $screen_id = $_POST['screen_id'] ?? null;
        $movie_id = $_POST['movie_id'] ?? null;
        $showtimes_time = $_POST['showtimes_time'] ?? [];
        $show_date = $_POST['show_date'] ?? null;
        $price = $_POST['price'] ?? null;
        
        if (!$screen_id || !$movie_id || !$show_date || !$price || empty($showtimes_time)) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            $this->redirect('moderator/screens');
            return;
        }
        
        // Kiểm tra phòng thuộc rạp này
        $screen = $db->fetch("
            SELECT * FROM theater_screens 
            WHERE id = ? AND theater_id = ?
        ", [$screen_id, $this->theaterId]);
        
        if (!$screen) {
            $_SESSION['error'] = 'Phòng chiếu không thuộc rạp của bạn!';
            $this->redirect('moderator/screens');
            return;
        }
        
        // Lấy theater_id từ screen
        $screen = $db->fetch("SELECT theater_id FROM theater_screens WHERE id = ? AND theater_id = ?", [$screen_id, $this->theaterId]);
        if (!$screen) {
            $_SESSION['error'] = 'Phòng chiếu không thuộc rạp của bạn!';
            $this->redirect('moderator/screens');
            return;
        }
        $theater_id = $screen['theater_id'] ?? $this->theaterId;
        
        try {
            foreach ($showtimes_time as $time) {
                $db->execute("
                    INSERT INTO showtimes (movie_id, theater_id, screen_id, show_date, show_time, price)
                    VALUES (?, ?, ?, ?, ?, ?)
                ", [$movie_id, $theater_id, $screen_id, $show_date, $time, $price]);
            }
            
            $_SESSION['success'] = 'Thêm phim vào phòng thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        $this->redirect('moderator/screens');
    }
    
    public function screenRemoveMovie() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('moderator/screens');
            return;
        }
        
        $screen_id = $_POST['screen_id'] ?? null;
        $movie_id = $_POST['movie_id'] ?? null;
        
        if (!$screen_id || !$movie_id) {
            $_SESSION['error'] = 'Thiếu thông tin!';
            $this->redirect('moderator/screens');
            return;
        }
        
        // Kiểm tra phòng thuộc rạp này
        $screen = $db->fetch("SELECT * FROM theater_screens WHERE id = ? AND theater_id = ?", [$screen_id, $this->theaterId]);
        if (!$screen) {
            $_SESSION['error'] = 'Phòng chiếu không thuộc rạp của bạn!';
            $this->redirect('moderator/screens');
            return;
        }
        
        // Lấy thông tin phim
        $movie = $db->fetch("SELECT * FROM movies WHERE id = ?", [$movie_id]);
        if (!$movie) {
            $_SESSION['error'] = 'Phim không tồn tại!';
            $this->redirect('moderator/screens');
            return;
        }
        
        try {
            // Đếm số suất chiếu sẽ bị xóa
            $deletedCount = $db->fetch("
                SELECT COUNT(*) as count FROM showtimes 
                WHERE screen_id = ? AND movie_id = ? AND show_date >= CURDATE()
            ", [$screen_id, $movie_id]);
            
            // Xóa tất cả lịch chiếu của phim trong phòng này (chỉ xóa lịch tương lai)
            $db->execute("
                DELETE FROM showtimes 
                WHERE screen_id = ? AND movie_id = ? AND show_date >= CURDATE()
            ", [$screen_id, $movie_id]);
            
            $_SESSION['success'] = 'Đã xóa ' . ($deletedCount['count'] ?? 0) . ' suất chiếu của phim "' . $movie['title'] . '" khỏi phòng "' . $screen['screen_name'] . '"!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi khi xóa lịch chiếu: ' . $e->getMessage();
        }
        
        $this->redirect('moderator/screens');
    }
    
    // Theater Management
    public function theater() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $theater = $db->fetch("SELECT * FROM theaters WHERE id = ?", [$this->theaterId]);
        if (!$theater) {
            $_SESSION['error'] = 'Không tìm thấy thông tin rạp!';
            $this->redirect('moderator');
            return;
        }
        
        $screens = $db->fetchAll("
            SELECT * FROM theater_screens 
            WHERE theater_id = ? 
            ORDER BY screen_name
        ", [$this->theaterId]);
        
        $this->moderatorView('theater', [
            'user' => $user,
            'theater' => $theater,
            'screens' => $screens,
            'title' => 'Thông tin rạp',
            'current_page' => 'theater'
        ]);
    }
    
    public function theaterUpdate() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('moderator/theater');
            return;
        }
        
        $name = $_POST['name'] ?? null;
        $location = $_POST['location'] ?? null;
        $address = $_POST['address'] ?? null;
        $phone = $_POST['phone'] ?? null;
        $total_screens = $_POST['total_screens'] ?? null;
        
        if (!$name || !$total_screens) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
            $this->redirect('moderator/theater');
            return;
        }
        
        try {
            $db->execute("
                UPDATE theaters 
                SET name = ?, location = ?, address = ?, phone = ?, total_screens = ?
                WHERE id = ?
            ", [$name, $location, $address, $phone, $total_screens, $this->theaterId]);
            
            $_SESSION['success'] = 'Cập nhật thông tin rạp thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        $this->redirect('moderator/theater');
    }
    
    // Tickets Management
    public function tickets() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $status = $_GET['status'] ?? '';
        $movie_id = $_GET['movie_id'] ?? '';
        
        // Build WHERE clause
        $where = "ts.theater_id = ?";
        $params = [$this->theaterId];
        
        if ($status) {
            $where .= " AND t.status = ?";
            $params[] = $status;
        }
        
        if ($movie_id) {
            $where .= " AND m.id = ?";
            $params[] = $movie_id;
        }
        
        // Lấy danh sách vé
        $tickets = $db->fetchAll("
            SELECT t.*, u.name as user_name, u.email as user_email,
                   m.title as movie_title, m.thumbnail,
                   s.show_date, s.show_time,
                   ts.screen_name
            FROM tickets t
            INNER JOIN users u ON t.user_id = u.id
            INNER JOIN showtimes s ON t.showtime_id = s.id
            INNER JOIN movies m ON s.movie_id = m.id
            INNER JOIN theater_screens ts ON s.screen_id = ts.id
            WHERE $where
            ORDER BY t.created_at DESC
            LIMIT $limit OFFSET $offset
        ", $params);
        
        $total = $db->fetch("
            SELECT COUNT(*) as count 
            FROM tickets t
            INNER JOIN showtimes s ON t.showtime_id = s.id
            INNER JOIN theater_screens ts ON s.screen_id = ts.id
            WHERE $where
        ", $params)['count'];
        
        // Lấy danh sách phim để filter
        $movies = $db->fetchAll("
            SELECT DISTINCT m.id, m.title 
            FROM movies m
            INNER JOIN showtimes s ON m.id = s.movie_id
            INNER JOIN theater_screens ts ON s.screen_id = ts.id
            WHERE ts.theater_id = ?
            ORDER BY m.title
        ", [$this->theaterId]);
        
        // Thống kê
        $stats = [
            'total' => $db->fetch("
                SELECT COUNT(*) as count 
                FROM tickets t
                INNER JOIN showtimes s ON t.showtime_id = s.id
                INNER JOIN theater_screens ts ON s.screen_id = ts.id
                WHERE ts.theater_id = ?
            ", [$this->theaterId])['count'] ?? 0,
            'sold' => $db->fetch("
                SELECT COUNT(*) as count 
                FROM tickets t
                INNER JOIN showtimes s ON t.showtime_id = s.id
                INNER JOIN theater_screens ts ON s.screen_id = ts.id
                WHERE ts.theater_id = ? AND t.status = 'Đã đặt'
            ", [$this->theaterId])['count'] ?? 0,
            'cancelled' => $db->fetch("
                SELECT COUNT(*) as count 
                FROM tickets t
                INNER JOIN showtimes s ON t.showtime_id = s.id
                INNER JOIN theater_screens ts ON s.screen_id = ts.id
                WHERE ts.theater_id = ? AND t.status = 'Đã hủy'
            ", [$this->theaterId])['count'] ?? 0,
            'pending' => $db->fetch("
                SELECT COUNT(*) as count 
                FROM tickets t
                INNER JOIN showtimes s ON t.showtime_id = s.id
                INNER JOIN theater_screens ts ON s.screen_id = ts.id
                WHERE ts.theater_id = ? AND t.status = 'Chờ thanh toán'
            ", [$this->theaterId])['count'] ?? 0,
            'revenue' => $db->fetch("
                SELECT COALESCE(SUM(t.price), 0) as revenue 
                FROM tickets t
                INNER JOIN showtimes s ON t.showtime_id = s.id
                INNER JOIN theater_screens ts ON s.screen_id = ts.id
                WHERE ts.theater_id = ? AND t.status = 'Đã đặt'
            ", [$this->theaterId])['revenue'] ?? 0
        ];
        
        $this->moderatorView('tickets', [
            'user' => $user,
            'tickets' => $tickets,
            'movies' => $movies,
            'stats' => $stats,
            'page' => $page,
            'total_pages' => ceil($total / $limit),
            'status' => $status,
            'movie_id' => $movie_id,
            'title' => 'Quản lý vé',
            'current_page' => 'tickets'
        ]);
    }
    
    // Food Items Management
    public function foodItems() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $search = $_GET['search'] ?? '';
        $type = $_GET['type'] ?? '';
        
        // Kiểm tra xem bảng food_items có cột theater_id không
        $hasTheaterId = false;
        try {
            $columns = $db->fetchAll("SHOW COLUMNS FROM food_items LIKE 'theater_id'");
            $hasTheaterId = !empty($columns);
        } catch (Exception $e) {
            // Bảng chưa có cột theater_id
        }
        
        $sql = "SELECT * FROM food_items WHERE 1=1";
        $params = [];
        
        // Moderator chỉ thấy combo/đồ ăn của rạp mình
        if ($hasTheaterId) {
            $sql .= " AND theater_id = ?";
            $params[] = $this->theaterId;
        } else {
            // Nếu chưa có cột theater_id, moderator không thấy gì cả
            $sql .= " AND 1=0";
        }
        
        if ($search) {
            $sql .= " AND (name LIKE ? OR description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($type) {
            $sql .= " AND type = ?";
            $params[] = $type;
        }
        
        $sql .= " ORDER BY type, name ASC";
        
        $foodItems = $db->fetchAll($sql, $params);
        
        // Lấy thông tin rạp để hiển thị
        $theater = $db->fetch("SELECT * FROM theaters WHERE id = ?", [$this->theaterId]);
        
        $this->moderatorView('food_items', [
            'user' => $user,
            'foodItems' => $foodItems,
            'search' => $search,
            'type' => $type,
            'theater' => $theater,
            'title' => 'Quản lý Combo & Đồ ăn',
            'current_page' => 'food_items'
        ]);
    }
    
    public function foodItemsCreate() {
        $user = AdminMiddleware::checkAdmin();
        $this->moderatorView('food_items/create', [
            'user' => $user,
            'title' => 'Thêm Combo/Đồ ăn mới',
            'current_page' => 'food_items'
        ]);
    }
    
    public function foodItemsStore() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('moderator/foodItems');
            return;
        }
        
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? 0;
        $type = $_POST['type'] ?? 'combo';
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($name) || $price <= 0) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            $this->redirect('moderator/foodItems/create');
            return;
        }
        
        // Xử lý upload ảnh
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../data/img/food/';
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            $fileType = $_FILES['image']['type'];
            $fileSize = $_FILES['image']['size'];
            
            if (!in_array($fileType, $allowedTypes)) {
                $_SESSION['error'] = 'Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WebP)!';
                $this->redirect('moderator/foodItems/create');
                return;
            }
            
            if ($fileSize > $maxSize) {
                $_SESSION['error'] = 'Kích thước file không được vượt quá 5MB!';
                $this->redirect('moderator/foodItems/create');
                return;
            }
            
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('food_') . '.' . $extension;
            $targetPath = $uploadDir . $fileName;
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imagePath = 'data/img/food/' . $fileName;
            }
        }
        
        try {
            // Kiểm tra và thêm cột theater_id nếu chưa có
            $hasTheaterId = false;
            try {
                $columns = $db->fetchAll("SHOW COLUMNS FROM food_items LIKE 'theater_id'");
                $hasTheaterId = !empty($columns);
            } catch (Exception $e) {
                // Bảng chưa có cột theater_id
            }
            
            if (!$hasTheaterId) {
                $pdo = $db->getConnection();
                $pdo->exec("ALTER TABLE food_items ADD COLUMN theater_id INT NULL AFTER is_active");
            }
            
            // Tự động gán theater_id cho moderator
            $db->execute("
                INSERT INTO food_items (name, description, price, image, type, is_active, theater_id)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ", [$name, $description, $price, $imagePath, $type, $is_active, $this->theaterId]);
            
            $_SESSION['success'] = 'Thêm combo/đồ ăn thành công!';
        } catch (Exception $e) {
            error_log("Error creating food item: " . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra khi thêm combo/đồ ăn: ' . $e->getMessage();
        }
        
        $this->redirect('moderator/foodItems');
    }
    
    public function foodItemsEdit() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Không tìm thấy combo/đồ ăn!';
            $this->redirect('moderator/foodItems');
            return;
        }
        
        $foodItem = $db->fetch("SELECT * FROM food_items WHERE id = ? AND theater_id = ?", [$id, $this->theaterId]);
        if (!$foodItem) {
            $_SESSION['error'] = 'Combo/đồ ăn không thuộc rạp của bạn!';
            $this->redirect('moderator/foodItems');
            return;
        }
        
        $this->moderatorView('food_items/edit', [
            'user' => $user,
            'foodItem' => $foodItem,
            'title' => 'Sửa Combo/Đồ ăn',
            'current_page' => 'food_items'
        ]);
    }
    
    public function foodItemsUpdate() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('moderator/foodItems');
            return;
        }
        
        $id = $_POST['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Không tìm thấy combo/đồ ăn!';
            $this->redirect('moderator/foodItems');
            return;
        }
        
        // Kiểm tra quyền: chỉ được sửa combo/đồ ăn của rạp mình
        $existingItem = $db->fetch("SELECT theater_id FROM food_items WHERE id = ?", [$id]);
        if (!$existingItem || $existingItem['theater_id'] != $this->theaterId) {
            $_SESSION['error'] = 'Bạn không có quyền sửa combo/đồ ăn này!';
            $this->redirect('moderator/foodItems');
            return;
        }
        
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? 0;
        $type = $_POST['type'] ?? 'combo';
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($name) || $price <= 0) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            $this->redirect('moderator/foodItems/edit&id=' . $id);
            return;
        }
        
        // Xử lý upload ảnh
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../data/img/food/';
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            $fileType = $_FILES['image']['type'];
            $fileSize = $_FILES['image']['size'];
            
            if (!in_array($fileType, $allowedTypes)) {
                $_SESSION['error'] = 'Chỉ chấp nhận file ảnh (JPEG, PNG, GIF, WebP)!';
                $this->redirect('moderator/foodItems/edit&id=' . $id);
                return;
            }
            
            if ($fileSize > $maxSize) {
                $_SESSION['error'] = 'Kích thước file không được vượt quá 5MB!';
                $this->redirect('moderator/foodItems/edit&id=' . $id);
                return;
            }
            
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('food_') . '.' . $extension;
            $targetPath = $uploadDir . $fileName;
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imagePath = 'data/img/food/' . $fileName;
            }
        }
        
        try {
            if ($imagePath) {
                $db->execute("
                    UPDATE food_items
                    SET name = ?, description = ?, price = ?, image = ?, type = ?, is_active = ?
                    WHERE id = ?
                ", [$name, $description, $price, $imagePath, $type, $is_active, $id]);
            } else {
                $db->execute("
                    UPDATE food_items
                    SET name = ?, description = ?, price = ?, type = ?, is_active = ?
                    WHERE id = ?
                ", [$name, $description, $price, $type, $is_active, $id]);
            }
            
            $_SESSION['success'] = 'Cập nhật combo/đồ ăn thành công!';
        } catch (Exception $e) {
            error_log("Error updating food item: " . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật combo/đồ ăn: ' . $e->getMessage();
        }
        
        $this->redirect('moderator/foodItems');
    }
    
    public function foodItemsDelete() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Không tìm thấy combo/đồ ăn!';
            $this->redirect('moderator/foodItems');
            return;
        }
        
        // Kiểm tra quyền: chỉ được xóa combo/đồ ăn của rạp mình
        $foodItem = $db->fetch("SELECT theater_id FROM food_items WHERE id = ?", [$id]);
        if (!$foodItem || $foodItem['theater_id'] != $this->theaterId) {
            $_SESSION['error'] = 'Bạn không có quyền xóa combo/đồ ăn này!';
            $this->redirect('moderator/foodItems');
            return;
        }
        
        try {
            $foodItem = $db->fetch("SELECT * FROM food_items WHERE id = ?", [$id]);
            if ($foodItem && $foodItem['image']) {
                $imagePath = __DIR__ . '/../../' . $foodItem['image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $db->execute("DELETE FROM food_items WHERE id = ?", [$id]);
            $_SESSION['success'] = 'Xóa combo/đồ ăn thành công!';
        } catch (Exception $e) {
            error_log("Error deleting food item: " . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa combo/đồ ăn: ' . $e->getMessage();
        }
        
        $this->redirect('moderator/foodItems');
    }
}
