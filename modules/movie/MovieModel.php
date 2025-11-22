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
        // Loại bỏ các từ phổ biến nếu từ khóa quá ngắn (ít hơn 3 ký tự)
        $keyword = trim($keyword);
        if (strlen($keyword) < 3) {
            // Nếu từ khóa quá ngắn, chỉ tìm trong title và phải bắt đầu bằng từ khóa
            $sql = "SELECT m.*, c.name as category_name,
                    CASE 
                        WHEN m.title LIKE ? THEN 1
                        ELSE 0
                    END as relevance
                    FROM movies m 
                    LEFT JOIN categories c ON m.category_id = c.id 
                    WHERE m.title LIKE ?";
            $searchPattern = $keyword . "%";
            $params = [$searchPattern, $searchPattern];
        } else {
            // Từ khóa đủ dài, tìm kiếm toàn diện với độ ưu tiên
            $sql = "SELECT m.*, c.name as category_name,
                    CASE 
                        WHEN m.title LIKE ? THEN 4
                        WHEN m.title LIKE ? THEN 3
                        WHEN m.director LIKE ? OR m.actors LIKE ? THEN 2
                        WHEN m.description LIKE ? THEN 1
                        ELSE 0
                    END as relevance
                    FROM movies m 
                    LEFT JOIN categories c ON m.category_id = c.id 
                    WHERE (m.title LIKE ? OR m.title LIKE ? OR m.director LIKE ? OR m.actors LIKE ? OR m.description LIKE ?)";
            $exactMatch = $keyword;
            $startsWith = $keyword . "%";
            $contains = "%" . $keyword . "%";
            $params = [
                $exactMatch,      // relevance = 4: title exact match
                $startsWith,      // relevance = 3: title starts with
                $contains,        // relevance = 2: director/actors contains
                $contains,        // relevance = 2: director/actors contains
                $contains,        // relevance = 1: description contains
                $exactMatch,      // WHERE: title exact match
                $startsWith,      // WHERE: title starts with
                $contains,        // WHERE: director contains
                $contains,        // WHERE: actors contains
                $contains         // WHERE: description contains
            ];
        }
        
        // Mặc định loại bỏ phim chiếu rạp, trừ khi người dùng chủ động filter
        if (!$status) {
            $sql .= " AND m.status != 'Chiếu rạp'";
        }
        
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
        
        // Sắp xếp theo độ liên quan (relevance) trước, sau đó mới đến rating
        $sql .= " ORDER BY relevance DESC, m.rating DESC, m.created_at DESC";
        return $this->db->fetchAll($sql, $params);
    }
    
    public function getByCategory($category_id) {
        return $this->db->fetchAll("SELECT m.*, c.name as category_name FROM movies m 
                                    LEFT JOIN categories c ON m.category_id = c.id 
                                    WHERE m.category_id = ? 
                                    ORDER BY m.created_at DESC", [$category_id]);
    }
    
    public function getTheaterMovies() {
        $today = date('Y-m-d');
        return $this->db->fetchAll("SELECT DISTINCT m.* FROM movies m 
                                    INNER JOIN showtimes s ON m.id = s.movie_id 
                                    WHERE m.status = 'Chiếu rạp' 
                                    AND s.show_date >= ? 
                                    ORDER BY m.title", 
                                    [$today]);
    }
    
    public function getByCountry($country, $type = null) {
        $sql = "SELECT m.*, c.name as category_name FROM movies m 
                LEFT JOIN categories c ON m.category_id = c.id 
                WHERE m.country = ?
                AND m.status != 'Chiếu rạp'";
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

