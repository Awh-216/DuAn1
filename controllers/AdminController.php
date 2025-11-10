<?php
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
        require_once __DIR__ . '/../views/admin/' . $view . '.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/../views/admin/layout.php';
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
            SELECT m.id, m.title, COUNT(wh.id) as view_count
            FROM movies m
            LEFT JOIN watch_history wh ON m.id = wh.movie_id
            GROUP BY m.id, m.title
            ORDER BY view_count DESC
            LIMIT 5
        ");
        
        // Suất chiếu sắp tới
        $upcomingShowtimes = $db->fetchAll("
            SELECT s.*, m.title as movie_title, t.name as theater_name
            FROM showtimes s
            JOIN movies m ON s.movie_id = m.id
            JOIN theaters t ON s.theater_id = t.id
            WHERE s.show_date >= CURDATE()
            ORDER BY s.show_date, s.show_time
            LIMIT 10
        ");
        
        $this->adminView('dashboard', [
            'current_page' => 'dashboard',
            'title' => 'Dashboard',
            'stats' => $stats,
            'revenueByDay' => $revenueByDay,
            'topMovies' => $topMovies,
            'upcomingShowtimes' => $upcomingShowtimes,
            'user' => $user
        ]);
    }
    
    // Quản lý người dùng
    public function users() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';
        
        $where = "1=1";
        $params = [];
        
        if ($search) {
            $where .= " AND (name LIKE ? OR email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        $users = $db->fetchAll("
            SELECT u.*, s.name as subscription_name
            FROM users u
            LEFT JOIN subscriptions s ON u.subscription_id = s.id
            WHERE $where
            ORDER BY u.created_at DESC
            LIMIT $limit OFFSET $offset
        ", $params);
        
        $total = $db->fetch("SELECT COUNT(*) as count FROM users WHERE $where", $params)['count'];
        
        $this->adminView('users', [
            'current_page' => 'users',
            'title' => 'Quản lý người dùng',
            'users' => $users,
            'page' => $page,
            'total_pages' => ceil($total / $limit),
            'search' => $search,
            'user' => $user
        ]);
    }
    
    // Quản lý phim
    public function movies() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $where = "1=1";
        $params = [];
        
        if ($search) {
            $where .= " AND title LIKE ?";
            $params[] = "%$search%";
        }
        
        if ($status) {
            $where .= " AND status_admin = ?";
            $params[] = $status;
        }
        
        $movies = $db->fetchAll("
            SELECT m.*, c.name as category_name
            FROM movies m
            LEFT JOIN categories c ON m.category_id = c.id
            WHERE $where
            ORDER BY m.created_at DESC
            LIMIT $limit OFFSET $offset
        ", $params);
        
        $total = $db->fetch("SELECT COUNT(*) as count FROM movies WHERE $where", $params)['count'];
        $categories = $db->fetchAll("SELECT * FROM categories ORDER BY name");
        
        $this->adminView('movies', [
            'current_page' => 'movies',
            'title' => 'Quản lý phim',
            'movies' => $movies,
            'categories' => $categories,
            'page' => $page,
            'total_pages' => ceil($total / $limit),
            'search' => $search,
            'status' => $status,
            'user' => $user
        ]);
    }
    
    // Quản lý vé
    public function tickets() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $page = $_GET['page'] ?? 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $status = $_GET['status'] ?? '';
        
        $where = "1=1";
        $params = [];
        
        if ($status) {
            $where .= " AND t.status = ?";
            $params[] = $status;
        }
        
        $tickets = $db->fetchAll("
            SELECT t.*, u.name as user_name, u.email as user_email,
                   m.title as movie_title, th.name as theater_name,
                   s.show_date, s.show_time
            FROM tickets t
            JOIN users u ON t.user_id = u.id
            JOIN showtimes s ON t.showtime_id = s.id
            JOIN movies m ON s.movie_id = m.id
            JOIN theaters th ON s.theater_id = th.id
            WHERE $where
            ORDER BY t.created_at DESC
            LIMIT $limit OFFSET $offset
        ", $params);
        
        $total = $db->fetch("SELECT COUNT(*) as count FROM tickets WHERE $where", $params)['count'];
        
        $this->adminView('tickets', [
            'current_page' => 'tickets',
            'title' => 'Quản lý vé',
            'tickets' => $tickets,
            'page' => $page,
            'total_pages' => ceil($total / $limit),
            'status' => $status,
            'user' => $user
        ]);
    }
    
    // Quản lý rạp
    public function theaters() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $theaters = $db->fetchAll("SELECT * FROM theaters ORDER BY name");
        
        $this->adminView('theaters', [
            'current_page' => 'theaters',
            'title' => 'Quản lý rạp',
            'theaters' => $theaters,
            'user' => $user
        ]);
    }
    
    // Analytics & Reports
    public function analytics() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $period = $_GET['period'] ?? 'month'; // day, week, month, year
        
        // Revenue analytics
        $revenueData = [];
        switch ($period) {
            case 'day':
                $revenueData = $db->fetchAll("
                    SELECT DATE(created_at) as period, SUM(amount) as revenue, COUNT(*) as count
                    FROM transactions
                    WHERE status = 'Thành công' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY DATE(created_at)
                    ORDER BY period DESC
                ");
                break;
            case 'week':
                $revenueData = $db->fetchAll("
                    SELECT YEARWEEK(created_at) as period, SUM(amount) as revenue, COUNT(*) as count
                    FROM transactions
                    WHERE status = 'Thành công' AND created_at >= DATE_SUB(NOW(), INTERVAL 12 WEEK)
                    GROUP BY YEARWEEK(created_at)
                    ORDER BY period DESC
                ");
                break;
            case 'month':
                $revenueData = $db->fetchAll("
                    SELECT DATE_FORMAT(created_at, '%Y-%m') as period, SUM(amount) as revenue, COUNT(*) as count
                    FROM transactions
                    WHERE status = 'Thành công' AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                    ORDER BY period DESC
                ");
                break;
        }
        
        // Top movies by revenue
        $topMoviesByRevenue = $db->fetchAll("
            SELECT m.id, m.title, SUM(t.price) as revenue, COUNT(t.id) as ticket_count
            FROM movies m
            JOIN showtimes s ON m.id = s.movie_id
            JOIN tickets t ON s.id = t.showtime_id
            WHERE t.status = 'Đã đặt'
            GROUP BY m.id, m.title
            ORDER BY revenue DESC
            LIMIT 10
        ");
        
        $this->adminView('analytics', [
            'current_page' => 'analytics',
            'title' => 'Analytics & Báo cáo',
            'revenueData' => $revenueData,
            'topMoviesByRevenue' => $topMoviesByRevenue,
            'period' => $period,
            'user' => $user
        ]);
    }
    
    // Support Tickets
    public function support() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $status = $_GET['status'] ?? '';
        $priority = $_GET['priority'] ?? '';
        
        $where = "1=1";
        $params = [];
        
        if ($status) {
            $where .= " AND status = ?";
            $params[] = $status;
        }
        
        if ($priority) {
            $where .= " AND priority = ?";
            $params[] = $priority;
        }
        
        $tickets = $db->fetchAll("
            SELECT st.*, u.name as user_name, u.email as user_email,
                   a.name as assigned_name
            FROM support_tickets st
            JOIN users u ON st.user_id = u.id
            LEFT JOIN users a ON st.assigned_to = a.id
            WHERE $where
            ORDER BY st.created_at DESC
        ", $params);
        
        $this->adminView('support', [
            'current_page' => 'support',
            'title' => 'Hỗ trợ khách hàng',
            'tickets' => $tickets,
            'status' => $status,
            'priority' => $priority,
            'user' => $user
        ]);
    }
    
    // System Logs
    public function logs() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $page = $_GET['page'] ?? 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        $module = $_GET['module'] ?? '';
        
        $where = "1=1";
        $params = [];
        
        if ($module) {
            $where .= " AND module = ?";
            $params[] = $module;
        }
        
        $logs = $db->fetchAll("
            SELECT al.*, u.name as user_name, u.email as user_email
            FROM admin_logs al
            JOIN users u ON al.user_id = u.id
            WHERE $where
            ORDER BY al.created_at DESC
            LIMIT $limit OFFSET $offset
        ", $params);
        
        $total = $db->fetch("SELECT COUNT(*) as count FROM admin_logs WHERE $where", $params)['count'];
        
        $this->adminView('logs', [
            'current_page' => 'logs',
            'title' => 'System Logs',
            'logs' => $logs,
            'page' => $page,
            'total_pages' => ceil($total / $limit),
            'module' => $module,
            'user' => $user
        ]);
    }
}
?>

