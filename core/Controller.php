<?php
class Controller {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    protected function view($view, $data = []) {
        extract($data);
        require_once __DIR__ . '/../shared/layout/header.php';
        
        // Xác định module từ view path
        $viewParts = explode('/', $view);
        $module = $viewParts[0] ?? 'home';
        $viewFile = $viewParts[1] ?? 'index';
        
        // Xử lý auth, profile và notifications views (đặc biệt)
        if ($module === 'auth' || $module === 'profile' || $module === 'notifications') {
            $module = 'user';
        }
        
        // Tìm view trong modules
        $viewPath = __DIR__ . '/../modules/' . $module . '/views/' . $viewFile . '.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        }
        
        require_once __DIR__ . '/../shared/layout/footer.php';
    }
    
    protected function redirect($url) {
        // Lấy base URL từ request hiện tại hoặc config
        $base_url = $this->getBaseUrl();
        
        // Nếu URL không bắt đầu bằng http hoặc ?route=, thêm ?route=
        if (strpos($url, 'http') !== 0 && strpos($url, '?route=') !== 0 && strpos($url, '/') !== 0) {
            $url = '?route=' . $url;
        }
        
        header('Location: ' . $base_url . $url);
        exit;
    }
    
    /**
     * Lấy base URL từ request hiện tại hoặc config
     */
    protected function getBaseUrl() {
        if (!class_exists('UrlHelper')) {
            require_once __DIR__ . '/UrlHelper.php';
        }
        return UrlHelper::getBaseUrl() . '/';
    }
    
    protected function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Kiểm tra session cơ bản
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Kiểm tra token trong database (nếu có)
        if (isset($_SESSION['auth_token'])) {
            try {
                require_once __DIR__ . '/../modules/user/UserModel.php';
                $userModel = new UserModel();
                
                // Validate token trong database
                if (!$userModel->validateToken($_SESSION['auth_token'])) {
                    // Token không hợp lệ hoặc đã bị xóa, xóa session
                    session_unset();
                    session_destroy();
                    return false;
                }
            } catch (Exception $e) {
                // Nếu có lỗi (ví dụ: bảng chưa tồn tại), vẫn cho phép đăng nhập bằng session
                error_log("Token validation error: " . $e->getMessage());
            }
        }
        
        return true;
    }
    
    protected function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        try {
            require_once __DIR__ . '/../modules/user/UserModel.php';
            $userModel = new UserModel();
            $user = $userModel->getById($_SESSION['user_id']);
            return $user ? $user : null;
        } catch (Exception $e) {
            error_log("Error getting current user: " . $e->getMessage());
            return null;
        }
    }
    
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['error'] = 'Vui lòng đăng nhập để tiếp tục!';
            $this->redirect('?route=auth/login');
        }
    }
    
    protected function isAdmin() {
        $user = $this->getCurrentUser();
        if (!$user) {
            return false;
        }
        
        require_once __DIR__ . '/AdminMiddleware.php';
        
        // Kiểm tra role cũ
        if (isset($user['role']) && $user['role'] === 'admin') {
            return true;
        }
        
        // Kiểm tra role trong bảng roles
        try {
            return AdminMiddleware::hasRole($user['id'], 'Super Admin') || 
                   AdminMiddleware::hasRole($user['id'], 'Admin');
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
