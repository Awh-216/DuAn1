<?php
class MovieModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT m.*, c.name as category_name FROM movies m 
                LEFT JOIN categories c ON m.category_id = c.id 
                ORDER BY m.created_at DESC";
        if ($limit) {
            $limit = (int)$limit; // Đảm bảo là số nguyên
            $offset = (int)$offset; // Đảm bảo là số nguyên
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        return $this->db->fetchAll($sql);
    }
    
    public function getById($id) {
        return $this->db->fetch("SELECT m.*, c.name as category_name FROM movies m 
                                LEFT JOIN categories c ON m.category_id = c.id 
                                WHERE m.id = ?", [$id]);
    }
    
    public function getHotMovies($limit = 6) {
        $limit = (int)$limit; // Đảm bảo là số nguyên
        return $this->db->fetchAll("SELECT m.*, c.name as category_name FROM movies m 
                                    LEFT JOIN categories c ON m.category_id = c.id 
                                    WHERE m.status = 'Chiếu online' 
                                    ORDER BY m.rating DESC LIMIT $limit");
    }
    
    public function search($keyword, $category_id = null) {
        $sql = "SELECT m.*, c.name as category_name FROM movies m 
                LEFT JOIN categories c ON m.category_id = c.id 
                WHERE m.title LIKE ?";
        $params = ["%$keyword%"];
        
        if ($category_id) {
            $sql .= " AND m.category_id = ?";
            $params[] = $category_id;
        }
        
        $sql .= " ORDER BY m.created_at DESC";
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getByCategory($category_id) {
        return $this->db->fetchAll("SELECT m.*, c.name as category_name FROM movies m 
                                    LEFT JOIN categories c ON m.category_id = c.id 
                                    WHERE m.category_id = ? 
                                    ORDER BY m.created_at DESC", [$category_id]);
    }
    
    public function getTheaterMovies() {
        return $this->db->fetchAll("SELECT * FROM movies WHERE status = 'Chiếu rạp' ORDER BY title");
    }
}
?>

