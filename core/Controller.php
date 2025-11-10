<?php
class Controller {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    protected function view($view, $data = []) {
        extract($data);
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/' . $view . '.php';
        require_once __DIR__ . '/../views/layout/footer.php';
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
