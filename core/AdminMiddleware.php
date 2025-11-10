<?php
class AdminMiddleware {
    
    public static function checkAdmin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: http://localhost/DuAn1/?route=auth/login');
            exit;
        }
        
        $userModel = new UserModel();
        $user = $userModel->getById($_SESSION['user_id']);
        
        if (!$user) {
            $_SESSION['error'] = 'Người dùng không tồn tại!';
            header('Location: http://localhost/DuAn1/');
            exit;
        }
        
        // Kiểm tra role (hỗ trợ cả cột role cũ và bảng roles mới)
        $isAdmin = false;
        
        // Kiểm tra cột role cũ (nếu có)
        if (isset($user['role']) && $user['role'] === 'admin') {
            $isAdmin = true;
        }
        
        // Kiểm tra role trong bảng roles (nếu có)
        if (!$isAdmin) {
            try {
                $isAdmin = self::hasRole($user['id'], 'Super Admin') || self::hasRole($user['id'], 'Admin');
            } catch (Exception $e) {
                // Nếu bảng chưa tồn tại, chỉ kiểm tra cột role
                $isAdmin = isset($user['role']) && $user['role'] === 'admin';
            }
        }
        
        if (!$isAdmin) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập trang này!';
            header('Location: http://localhost/DuAn1/');
            exit;
        }
        
        return $user;
    }
    
    public static function hasPermission($userId, $permission) {
        try {
            $db = Database::getInstance();
            
            // Kiểm tra nếu là Super Admin
            $superAdmin = $db->fetch("
                SELECT ur.role_id FROM user_roles ur 
                JOIN roles r ON ur.role_id = r.id 
                WHERE ur.user_id = ? AND r.name = 'Super Admin'
            ", [$userId]);
            
            if ($superAdmin) {
                return true; // Super Admin có tất cả quyền
            }
            
            // Kiểm tra permission cụ thể
            $hasPermission = $db->fetch("
                SELECT rp.id FROM role_permissions rp
                JOIN user_roles ur ON rp.role_id = ur.role_id
                JOIN permissions p ON rp.permission_id = p.id
                WHERE ur.user_id = ? AND p.name = ?
            ", [$userId, $permission]);
            
            return $hasPermission !== false;
        } catch (Exception $e) {
            // Bảng chưa tồn tại, kiểm tra role cũ
            $userModel = new UserModel();
            $user = $userModel->getById($userId);
            return isset($user['role']) && $user['role'] === 'admin';
        }
    }
    
    public static function hasRole($userId, $roleName) {
        try {
            $db = Database::getInstance();
            $role = $db->fetch("
                SELECT ur.role_id FROM user_roles ur 
                JOIN roles r ON ur.role_id = r.id 
                WHERE ur.user_id = ? AND r.name = ?
            ", [$userId, $roleName]);
            
            return $role !== false;
        } catch (Exception $e) {
            // Bảng chưa tồn tại
            return false;
        }
    }
    
    public static function logAction($userId, $action, $module, $targetType = null, $targetId = null, $oldData = null, $newData = null) {
        try {
            $db = Database::getInstance();
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            $db->execute("
                INSERT INTO admin_logs (user_id, action, module, target_type, target_id, old_data, new_data, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", [
                $userId,
                $action,
                $module,
                $targetType,
                $targetId,
                $oldData ? json_encode($oldData) : null,
                $newData ? json_encode($newData) : null,
                $ip,
                $userAgent
            ]);
        } catch (Exception $e) {
            // Bảng admin_logs chưa tồn tại, bỏ qua
            error_log("Cannot log admin action: " . $e->getMessage());
        }
    }
}
?>

