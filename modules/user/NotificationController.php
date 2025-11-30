<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Database.php';

class NotificationController extends Controller {
    
    public function index() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }
        
        $user_id = $_SESSION['user_id'];
        
        // Tự động tạo bảng notifications nếu chưa có
        try {
            $pdo = $this->db->getConnection();
            $pdo->exec("CREATE TABLE IF NOT EXISTS notifications (
                id INT(11) NOT NULL AUTO_INCREMENT,
                user_id INT(11) NOT NULL,
                type VARCHAR(50) NOT NULL DEFAULT 'info',
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                link VARCHAR(255) DEFAULT NULL,
                is_read TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY user_id (user_id),
                KEY is_read (is_read)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        } catch (Exception $e) {
            // Bảng đã tồn tại
        }
        
        // Lấy danh sách thông báo
        $notifications = $this->db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50",
            [$user_id]
        );
        
        // Đếm số thông báo chưa đọc
        $unreadCount = $this->db->fetch(
            "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0",
            [$user_id]
        )['count'] ?? 0;
        
        $this->view('notifications/index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
            'title' => 'Thông báo'
        ]);
    }
    
    public function markAsRead() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
            exit;
        }
        
        try {
            $user_id = $_SESSION['user_id'];
            $notification_id = $_POST['id'] ?? $_GET['id'] ?? null;
            
            if ($notification_id) {
                $this->db->execute(
                    "UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?",
                    [$notification_id, $user_id]
                );
            } else {
                $this->db->execute(
                    "UPDATE notifications SET is_read = 1 WHERE user_id = ?",
                    [$user_id]
                );
            }
            
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false]);
        }
        exit;
    }
    
    public function delete() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('auth/login');
            return;
        }
        
        $user_id = $_SESSION['user_id'];
        $notification_id = $_GET['id'] ?? null;
        
        if ($notification_id) {
            $this->db->execute(
                "DELETE FROM notifications WHERE id = ? AND user_id = ?",
                [$notification_id, $user_id]
            );
            $_SESSION['success'] = 'Đã xóa thông báo!';
        }
        
        $this->redirect('notifications/index');
    }
    
    public function getUnreadCount() {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['count' => 0]);
            exit;
        }
        
        try {
            $user_id = $_SESSION['user_id'];
            $count = $this->db->fetch(
                "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0",
                [$user_id]
            )['count'] ?? 0;
            
            echo json_encode(['count' => $count]);
        } catch (Exception $e) {
            echo json_encode(['count' => 0]);
        }
        exit;
    }
    
    public function getNotifications() {
        // Tắt output buffering và xóa mọi output trước đó
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        // Đảm bảo không có output nào trước đó
        if (headers_sent()) {
            // Nếu headers đã được gửi, không thể set header mới
            die(json_encode(['notifications' => [], 'error' => 'Headers already sent']));
        }
        
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        if (!isset($_SESSION['user_id'])) {
            die(json_encode(['notifications' => []]));
        }
        
        $user_id = $_SESSION['user_id'];
        $limit = intval($_GET['limit'] ?? 10);
        
        // Tự động tạo bảng nếu chưa có
        try {
            $pdo = $this->db->getConnection();
            $pdo->exec("CREATE TABLE IF NOT EXISTS notifications (
                id INT(11) NOT NULL AUTO_INCREMENT,
                user_id INT(11) NOT NULL,
                type VARCHAR(50) NOT NULL DEFAULT 'info',
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                link VARCHAR(255) DEFAULT NULL,
                is_read TINYINT(1) DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY user_id (user_id),
                KEY is_read (is_read)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
        } catch (Exception $e) {
            // Bảng đã tồn tại
        }
        
        try {
            // LIMIT không thể dùng placeholder, phải dùng intval để đảm bảo an toàn
            $limit = intval($limit);
            if ($limit <= 0 || $limit > 100) {
                $limit = 10; // Giới hạn tối đa 100, mặc định 10
            }
            
            $notifications = $this->db->fetchAll(
                "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT " . $limit,
                [$user_id]
            );
            
            // Log để debug
            error_log("getNotifications: user_id={$user_id}, limit={$limit}, found=" . count($notifications));
            
            // Format thời gian và đảm bảo các trường cần thiết
            foreach ($notifications as &$notif) {
                $notif['time_ago'] = $this->timeAgo($notif['created_at']);
                // Đảm bảo các trường có giá trị
                $notif['id'] = (int)$notif['id'];
                $notif['user_id'] = (int)$notif['user_id'];
                $notif['is_read'] = (int)$notif['is_read'];
                $notif['type'] = $notif['type'] ?? 'info';
                $notif['title'] = $notif['title'] ?? '';
                $notif['message'] = $notif['message'] ?? '';
                $notif['link'] = $notif['link'] ?? null;
            }
            unset($notif);
            
            $response = ['notifications' => $notifications];
            error_log("getNotifications response: " . json_encode($response, JSON_UNESCAPED_UNICODE));
            die(json_encode($response, JSON_UNESCAPED_UNICODE));
        } catch (Exception $e) {
            error_log("getNotifications error: " . $e->getMessage());
            die(json_encode(['notifications' => [], 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE));
        }
    }
    
    private function timeAgo($datetime) {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;
        
        if ($diff < 60) {
            return 'Vừa xong';
        } elseif ($diff < 3600) {
            return floor($diff / 60) . ' phút trước';
        } elseif ($diff < 86400) {
            return floor($diff / 3600) . ' giờ trước';
        } elseif ($diff < 604800) {
            return floor($diff / 86400) . ' ngày trước';
        } else {
            return date('d/m/Y', $timestamp);
        }
    }
}

