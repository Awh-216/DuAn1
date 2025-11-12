<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/AdminMiddleware.php';
require_once __DIR__ . '/../../core/Database.php';

class AdminController extends Controller {
    
    public function __construct() {
        parent::__construct();
        AdminMiddleware::checkAdmin();
    }
    
    protected function adminView($view, $data = []) {
        extract($data);
        $current_page = $data['current_page'] ?? '';
        $title = $data['title'] ?? 'Admin Panel';
        $user = $data['user'] ?? AdminMiddleware::checkAdmin();
        
        ob_start();
        require_once __DIR__ . '/views/' . $view . '.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/views/layout.php';
    }
    
    // Dashboard Overview
    public function index() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        // Thống kê tổng quan
        $stats = [
            'total_users' => $db->fetch("SELECT COUNT(*) as count FROM users")['count'],
            'total_movies' => $db->fetch("SELECT COUNT(*) as count FROM movies")['count'],
            'total_tickets' => $db->fetch("SELECT COUNT(*) as count FROM tickets WHERE status = 'Đã đặt'")['count'],
            'total_revenue' => $db->fetch("SELECT SUM(amount) as total FROM transactions WHERE status = 'Thành công'")['total'] ?? 0,
            'today_revenue' => $db->fetch("SELECT SUM(amount) as total FROM transactions WHERE status = 'Thành công' AND DATE(created_at) = CURDATE()")['total'] ?? 0,
            'week_revenue' => $db->fetch("SELECT SUM(amount) as total FROM transactions WHERE status = 'Thành công' AND WEEK(created_at) = WEEK(NOW())")['total'] ?? 0,
            'month_revenue' => $db->fetch("SELECT SUM(amount) as total FROM transactions WHERE status = 'Thành công' AND MONTH(created_at) = MONTH(NOW())")['total'] ?? 0,
            'pending_tickets' => $db->fetch("SELECT COUNT(*) as count FROM support_tickets WHERE status = 'Mới'")['count'],
            'active_users_today' => $db->fetch("SELECT COUNT(DISTINCT user_id) as count FROM watch_history WHERE DATE(created_at) = CURDATE()")['count'],
        ];
        
        // Doanh thu theo ngày (7 ngày gần nhất)
        $revenueByDay = $db->fetchAll("
            SELECT DATE(created_at) as date, SUM(amount) as revenue
            FROM transactions
            WHERE status = 'Thành công' AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date DESC
        ");
        
        // Top phim xem nhiều nhất
        $topMovies = $db->fetchAll("
            SELECT m.title, COUNT(wh.id) as view_count
            FROM watch_history wh
            JOIN movies m ON wh.movie_id = m.id
            GROUP BY m.id, m.title
            ORDER BY view_count DESC
            LIMIT 5
        ");
        
        $this->adminView('dashboard', [
            'stats' => $stats,
            'revenueByDay' => $revenueByDay,
            'topMovies' => $topMovies,
            'user' => $user,
            'title' => 'Dashboard',
            'current_page' => 'dashboard'
        ]);
    }
    
    // Users Management
    public function users() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $sql = "SELECT u.*, s.name as subscription_name FROM users u 
                LEFT JOIN subscriptions s ON u.subscription_id = s.id WHERE 1=1";
        $params = [];
        
        if ($search) {
            $sql .= " AND (u.name LIKE ? OR u.email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($status) {
            $sql .= " AND u.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY u.created_at DESC";
        
        $users = $db->fetchAll($sql, $params);
        
        $this->adminView('users', [
            'users' => $users,
            'search' => $search,
            'status' => $status,
            'user' => $user,
            'title' => 'Quản lý người dùng',
            'current_page' => 'users'
        ]);
    }
    
    // Movies Management
    public function movies() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        require_once __DIR__ . '/../movie/CategoryModel.php';
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->getAll();
        
        $search = $_GET['search'] ?? '';
        $category_id = $_GET['category'] ?? null;
        $status = $_GET['status'] ?? '';
        
        $sql = "SELECT m.*, c.name as category_name FROM movies m 
                LEFT JOIN categories c ON m.category_id = c.id WHERE 1=1";
        $params = [];
        
        if ($search) {
            $sql .= " AND m.title LIKE ?";
            $params[] = "%$search%";
        }
        
        if ($category_id) {
            $sql .= " AND m.category_id = ?";
            $params[] = $category_id;
        }
        
        if ($status) {
            $sql .= " AND m.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY m.created_at DESC";
        
        $movies = $db->fetchAll($sql, $params);
        
        $this->adminView('movies', [
            'movies' => $movies,
            'categories' => $categories,
            'search' => $search,
            'category_id' => $category_id,
            'status' => $status,
            'user' => $user,
            'title' => 'Quản lý phim',
            'current_page' => 'movies'
        ]);
    }
    
    // Tickets Management
    public function tickets() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $tickets = $db->fetchAll("
            SELECT t.*, u.name as user_name, u.email, 
                   s.show_date, s.show_time, s.price,
                   m.title as movie_title, th.name as theater_name
            FROM tickets t
            JOIN users u ON t.user_id = u.id
            JOIN showtimes s ON t.showtime_id = s.id
            JOIN movies m ON s.movie_id = m.id
            JOIN theaters th ON s.theater_id = th.id
            ORDER BY t.created_at DESC
        ");
        
        $this->adminView('tickets', [
            'tickets' => $tickets,
            'user' => $user,
            'title' => 'Quản lý vé',
            'current_page' => 'tickets'
        ]);
    }
    
    // Theaters Management
    public function theaters() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $theaters = $db->fetchAll("SELECT * FROM theaters ORDER BY name");
        
        $this->adminView('theaters', [
            'theaters' => $theaters,
            'user' => $user,
            'title' => 'Quản lý rạp',
            'current_page' => 'theaters'
        ]);
    }
    
    // Analytics
    public function analytics() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $this->adminView('analytics', [
            'user' => $user,
            'title' => 'Phân tích',
            'current_page' => 'analytics'
        ]);
    }
    
    // Support Tickets
    public function support() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $tickets = $db->fetchAll("
            SELECT st.*, u.name as user_name, u.email
            FROM support_tickets st
            JOIN users u ON st.user_id = u.id
            ORDER BY st.created_at DESC
        ");
        
        $this->adminView('support', [
            'tickets' => $tickets,
            'user' => $user,
            'title' => 'Hỗ trợ',
            'current_page' => 'support'
        ]);
    }
    
    // Logs
    public function logs() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $logs = $db->fetchAll("
            SELECT al.*, u.name as user_name, u.email
            FROM admin_logs al
            JOIN users u ON al.user_id = u.id
            ORDER BY al.created_at DESC
            LIMIT 100
        ");
        
        $this->adminView('logs', [
            'logs' => $logs,
            'user' => $user,
            'title' => 'Nhật ký',
            'current_page' => 'logs'
        ]);
    }
}
?>

