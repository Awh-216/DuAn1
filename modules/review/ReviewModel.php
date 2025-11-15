<?php
require_once __DIR__ . '/../../core/Database.php';

class ReviewModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getByMovie($movie_id, $limit = 10) {
        $limit = (int)$limit;
        try {
            return $this->db->fetchAll("SELECT r.*, u.name as user_name FROM reviews r 
                                        JOIN users u ON r.user_id = u.id 
                                        WHERE r.movie_id = ? 
                                        ORDER BY COALESCE(r.is_pinned, 0) DESC, r.created_at DESC LIMIT $limit", 
                                        [$movie_id]);
        } catch (Exception $e) {
            // Nếu cột is_pinned chưa tồn tại, query không có COALESCE
            if (strpos($e->getMessage(), 'is_pinned') !== false) {
                try {
                    $this->db->execute("ALTER TABLE reviews ADD COLUMN is_pinned tinyint(1) DEFAULT 0");
                } catch (Exception $e2) {
                    // Bỏ qua nếu đã tồn tại
                }
                return $this->db->fetchAll("SELECT r.*, u.name as user_name FROM reviews r 
                                            JOIN users u ON r.user_id = u.id 
                                            WHERE r.movie_id = ? 
                                            ORDER BY r.created_at DESC LIMIT $limit", 
                                            [$movie_id]);
            }
            throw $e;
        }
    }
    
    public function create($data) {
        $sql = "INSERT INTO reviews (user_id, movie_id, rating, comment) VALUES (?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['user_id'],
            $data['movie_id'],
            $data['rating'],
            $data['comment'] ?? ''
        ]);
        return $this->db->lastInsertId();
    }
    
    public function getById($id) {
        return $this->db->fetch("SELECT r.*, u.name as user_name, m.title as movie_title FROM reviews r 
                                 JOIN users u ON r.user_id = u.id 
                                 JOIN movies m ON r.movie_id = m.id 
                                 WHERE r.id = ?", [$id]);
    }
    
    public function delete($id) {
        return $this->db->execute("DELETE FROM reviews WHERE id = ?", [$id]);
    }
    
    public function togglePin($id, $is_pinned) {
        try {
            return $this->db->execute("UPDATE reviews SET is_pinned = ? WHERE id = ?", [$is_pinned, $id]);
        } catch (Exception $e) {
            // Nếu cột is_pinned chưa tồn tại, tạo cột
            if (strpos($e->getMessage(), 'is_pinned') !== false) {
                try {
                    $this->db->execute("ALTER TABLE reviews ADD COLUMN is_pinned tinyint(1) DEFAULT 0");
                    return $this->db->execute("UPDATE reviews SET is_pinned = ? WHERE id = ?", [$is_pinned, $id]);
                } catch (Exception $e2) {
                    error_log("Cannot add is_pinned column: " . $e2->getMessage());
                    throw $e;
                }
            }
            throw $e;
        }
    }
}
?>

