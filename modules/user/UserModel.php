<?php
require_once __DIR__ . '/../../core/Database.php';

class UserModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getById($id) {
        $user = $this->db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
        if ($user) {
            try {
                $user['roles'] = $this->getUserRoles($id);
            } catch (Exception $e) {
                $user['roles'] = [];
            }
        }
        return $user;
    }
    
    public function getByEmail($email) {
        $user = $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
        if ($user) {
            try {
                $user['roles'] = $this->getUserRoles($user['id']);
            } catch (Exception $e) {
                $user['roles'] = [];
            }
        }
        return $user;
    }
    
    public function getUserRoles($userId) {
        try {
            return $this->db->fetchAll("
                SELECT r.* FROM roles r
                JOIN user_roles ur ON r.id = ur.role_id
                WHERE ur.user_id = ?
            ", [$userId]);
        } catch (Exception $e) {
            return [];
        }
    }
    
    public function create($data) {
        $sql = "INSERT INTO users (name, email, password, subscription_id) VALUES (?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['subscription_id'] ?? 1
        ]);
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE users SET name = ?, email = ?, avatar = ?, birthdate = ? WHERE id = ?";
        $this->db->execute($sql, [
            $data['name'],
            $data['email'],
            $data['avatar'] ?? null,
            $data['birthdate'] ?? null,
            $id
        ]);
    }
    
    /**
     * Cập nhật điểm của user
     */
    public function updatePoints($id, $points) {
        $sql = "UPDATE users SET points = ? WHERE id = ?";
        $this->db->execute($sql, [$points, $id]);
    }
    
    /**
     * Thêm điểm cho user
     */
    public function addPoints($id, $points) {
        $user = $this->getById($id);
        if ($user) {
            $newPoints = ($user['points'] ?? 0) + $points;
            $this->updatePoints($id, $newPoints);
            return $newPoints;
        }
        return false;
    }
    
    /**
     * Trừ điểm của user
     */
    public function deductPoints($id, $points) {
        $user = $this->getById($id);
        if ($user) {
            $currentPoints = $user['points'] ?? 0;
            if ($currentPoints >= $points) {
                $newPoints = $currentPoints - $points;
                $this->updatePoints($id, $newPoints);
                return $newPoints;
            }
        }
        return false;
    }
    
    /**
     * Tạo token mới cho user
     * @param int $userId ID của user
     * @param string $deviceInfo Thông tin thiết bị
     * @param string $ipAddress IP address
     * @return string Token string
     */
    public function createToken($userId, $deviceInfo = null, $ipAddress = null) {
        require_once __DIR__ . '/../../core/TokenHelper.php';
        
        $token = TokenHelper::generateSecureToken();
        $expiresAt = TokenHelper::getExpiryTime(30); // Token hết hạn sau 30 ngày
        
        $sql = "INSERT INTO user_tokens (user_id, token, device_info, ip_address, expires_at) VALUES (?, ?, ?, ?, ?)";
        $this->db->execute($sql, [
            $userId,
            $token,
            $deviceInfo,
            $ipAddress,
            $expiresAt
        ]);
        
        return $token;
    }
    
    /**
     * Lấy thông tin token theo giá trị token
     * @param string $token Token string
     * @return array|null Token info và user info
     */
    public function getTokenByValue($token) {
        $sql = "SELECT ut.*, u.* FROM user_tokens ut 
                JOIN users u ON ut.user_id = u.id 
                WHERE ut.token = ? AND ut.expires_at > NOW()";
        return $this->db->fetch($sql, [$token]);
    }
    
    /**
     * Xóa token cụ thể (đăng xuất thiết bị hiện tại)
     * @param string $token Token cần xóa
     * @return bool Success
     */
    public function deleteToken($token) {
        $sql = "DELETE FROM user_tokens WHERE token = ?";
        $this->db->execute($sql, [$token]);
        return true;
    }
    
    /**
     * Xóa tất cả token của user (đăng xuất khỏi tất cả thiết bị)
     * @param int $userId ID của user
     * @return bool Success
     */
    public function deleteAllUserTokens($userId) {
        $sql = "DELETE FROM user_tokens WHERE user_id = ?";
        $this->db->execute($sql, [$userId]);
        return true;
    }
    
    /**
     * Kiểm tra token có hợp lệ không
     * @param string $token Token cần kiểm tra
     * @return bool True nếu hợp lệ
     */
    public function validateToken($token) {
        $sql = "SELECT COUNT(*) as count FROM user_tokens WHERE token = ? AND expires_at > NOW()";
        $result = $this->db->fetch($sql, [$token]);
        return $result && $result['count'] > 0;
    }
    
    /**
     * Xóa các token đã hết hạn
     * @return int Số lượng token đã xóa
     */
    public function cleanExpiredTokens() {
        $sql = "DELETE FROM user_tokens WHERE expires_at <= NOW()";
        $this->db->execute($sql);
        return $this->db->rowCount();
    }
    
    /**
     * Lấy tất cả token của user (để hiển thị danh sách thiết bị)
     * @param int $userId ID của user
     * @return array Danh sách token
     */
    public function getUserTokens($userId) {
        $sql = "SELECT id, device_info, ip_address, created_at, expires_at 
                FROM user_tokens 
                WHERE user_id = ? AND expires_at > NOW()
                ORDER BY created_at DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }

}
?>

