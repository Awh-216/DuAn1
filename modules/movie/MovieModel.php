<?php
require_once __DIR__ . '/../../core/Database.php';

class MovieModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getDb() {
        return $this->db;
    }
    
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT m.*, c.name as category_name FROM movies m 
                LEFT JOIN categories c ON m.category_id = c.id 
                ORDER BY m.created_at DESC";
        if ($limit) {
            $limit = (int)$limit;
            $offset = (int)$offset;
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
        $limit = (int)$limit;
        return $this->db->fetchAll("SELECT m.*, c.name as category_name FROM movies m 
                                    LEFT JOIN categories c ON m.category_id = c.id 
                                    WHERE m.status = 'Chiếu online' 
                                    ORDER BY m.rating DESC LIMIT $limit");
    }
    
    public function search($keyword, $category_id = null, $status = null, $country = null, $min_rating = null, $type = null) {
        $sql = "SELECT m.*, c.name as category_name FROM movies m 
                LEFT JOIN categories c ON m.category_id = c.id 
                WHERE (m.title LIKE ? OR m.director LIKE ? OR m.actors LIKE ? OR m.description LIKE ?)";
        $params = ["%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"];
        
        if ($category_id) {
            $sql .= " AND m.category_id = ?";
            $params[] = $category_id;
        }
        
        if ($status) {
            $sql .= " AND m.status = ?";
            $params[] = $status;
        }
        
        if ($country) {
            $sql .= " AND m.country LIKE ?";
            $params[] = "%$country%";
        }
        
        if ($type) {
            $sql .= " AND m.type = ?";
            $params[] = $type;
        }
        
        if ($min_rating !== null) {
            $sql .= " AND m.rating >= ?";
            $params[] = floatval($min_rating);
        }
        
        $sql .= " ORDER BY m.rating DESC, m.created_at DESC";
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
    
    public function getByCountry($country, $type = null) {
        $sql = "SELECT m.*, c.name as category_name FROM movies m 
                LEFT JOIN categories c ON m.category_id = c.id 
                WHERE m.country = ?";
        $params = [$country];
        
        if ($type) {
            $sql .= " AND m.type = ?";
            $params[] = $type;
        }
        
        $sql .= " ORDER BY m.created_at DESC";
        return $this->db->fetchAll($sql, $params);
    }
}
?>

