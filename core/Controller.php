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
        
        // Xử lý auth views (đặc biệt)
        if ($module === 'auth') {
            $module = 'user';
        }
        
        // Tìm view trong modules
        $viewPath = __DIR__ . '/../modules/' . $module . '/views/' . $viewFile . '.php';
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            // Fallback cho views cũ
            $oldViewPath = __DIR__ . '/../views/' . $view . '.php';
            if (file_exists($oldViewPath)) {
                require_once $oldViewPath;
            }
        }
        
        require_once __DIR__ . '/../shared/layout/footer.php';
    }
    
    protected function redirect($url) {
        $base_url = 'http://localhost/DuAn1/';
        header('Location: ' . $base_url . $url);
        exit;
    }
    
    protected function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }
    
    protected function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        require_once __DIR__ . '/../modules/user/UserModel.php';
        $userModel = new UserModel();
        return $userModel->getById($_SESSION['user_id']);
    }
    
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('?route=auth/login');
        }
    }
}
?>
