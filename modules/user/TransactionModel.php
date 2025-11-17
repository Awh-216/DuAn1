<?php
require_once __DIR__ . '/../../core/Database.php';

class TransactionModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Tạo transaction mới
     */
    public function create($data) {
        // Cần cập nhật bảng transactions để hỗ trợ type 'deposit'
        // ALTER TABLE transactions MODIFY type ENUM('ticket','subscription','deposit') NOT NULL;
        
        $sql = "INSERT INTO transactions (user_id, type, related_id, amount, method, status) VALUES (?, ?, ?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['user_id'],
            $data['type'] ?? 'deposit',
            $data['related_id'] ?? null,
            $data['amount'],
            $data['method'] ?? 'Momo',
            $data['status'] ?? 'Thành công'
        ]);
        
        $transactionId = $this->db->lastInsertId();
        
        // Nếu là deposit và thành công, chuyển đổi thành điểm
        if (($data['type'] ?? 'deposit') === 'deposit' && ($data['status'] ?? 'Thành công') === 'Thành công') {
            $this->convertDepositToPoints($data['user_id'], $data['amount']);
        }
        
        return $transactionId;
    }
    
    /**
     * Chuyển đổi tiền nạp thành điểm (1 VND = 1 điểm)
     */
    private function convertDepositToPoints($userId, $amount) {
        require_once __DIR__ . '/UserModel.php';
        $userModel = new UserModel();
        
        // Chuyển đổi: 1 VND = 1 điểm
        $points = intval($amount);
        $userModel->addPoints($userId, $points);
    }
    
    /**
     * Lấy tất cả transactions của user
     */
    public function getByUserId($userId) {
        return $this->db->fetchAll("
            SELECT * FROM transactions 
            WHERE user_id = ? 
            ORDER BY created_at DESC
        ", [$userId]);
    }
    
    /**
     * Lấy transaction theo ID
     */
    public function getById($id) {
        return $this->db->fetch("SELECT * FROM transactions WHERE id = ?", [$id]);
    }
}
?>

