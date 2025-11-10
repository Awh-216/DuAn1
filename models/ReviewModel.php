<?php
class ReviewModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getByMovie($movie_id, $limit = 10) {
        $limit = (int)$limit; // Đảm bảo là số nguyên
        return $this->db->fetchAll("SELECT r.*, u.name as user_name FROM reviews r 
                                    JOIN users u ON r.user_id = u.id 
                                    WHERE r.movie_id = ? 
                                    ORDER BY r.created_at DESC LIMIT $limit", 
                                    [$movie_id]);
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
}
?>

