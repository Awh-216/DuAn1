<?php
/**
 * Middleware để ngăn cache cho các trang yêu cầu đăng nhập
 * Thêm vào đầu các trang protected để ngăn browser cache
 */

class NoCacheMiddleware {
    
    /**
     * Áp dụng headers ngăn cache
     */
    public static function apply() {
        // Ngăn browser cache trang
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Ngày trong quá khứ
    }
    
    /**
     * Kiểm tra và redirect nếu session không hợp lệ
     */
    public static function checkSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Nếu không có session, redirect về login
        if (!isset($_SESSION['user_id'])) {
            header('Location: ?route=auth/login');
            exit;
        }
        
        // Kiểm tra token nếu có
        if (isset($_SESSION['auth_token'])) {
            require_once __DIR__ . '/../modules/user/UserModel.php';
            $userModel = new UserModel();
            
            if (!$userModel->validateToken($_SESSION['auth_token'])) {
                // Token không hợp lệ, xóa session và redirect
                session_unset();
                session_destroy();
                header('Location: ?route=auth/login');
                exit;
            }
        }
        
        // Áp dụng no-cache headers
        self::apply();
    }
}
?>
