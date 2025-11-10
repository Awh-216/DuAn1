<?php
class UserModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getById($id) {
        $user = $this->db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
        if ($user) {
            // Lấy roles của user (nếu bảng tồn tại)
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
            // Lấy roles của user (nếu bảng tồn tại)
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
}
?>

