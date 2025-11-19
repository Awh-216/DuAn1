<?php
require_once __DIR__ . '/../../core/Database.php';

class WatchHistoryModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function add($user_id, $movie_id) {
        $sql = "INSERT INTO watch_history (user_id, movie_id) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE created_at = CURRENT_TIMESTAMP";
        $this->db->execute($sql, [$user_id, $movie_id]);
    }
    
    public function getUserHistory($user_id) {
        return $this->db->fetchAll("SELECT wh.*, m.title, m.thumbnail, m.duration 
                                    FROM watch_history wh 
                                    JOIN movies m ON wh.movie_id = m.id 
                                    WHERE wh.user_id = ? 
                                    ORDER BY wh.created_at DESC", [$user_id]);
    }
    
    /**
     * Toggle favorite cho một phim
     */
    public function toggleFavorite($user_id, $movie_id) {
        // Kiểm tra xem đã có trong watch_history chưa
        $existing = $this->db->fetch("SELECT * FROM watch_history WHERE user_id = ? AND movie_id = ?", [$user_id, $movie_id]);
        
        if ($existing) {
            // Update favorite status (toggle)
            $newFavorite = $existing['favorite'] ? 0 : 1;
            $this->db->execute("UPDATE watch_history SET favorite = ? WHERE user_id = ? AND movie_id = ?", 
                [$newFavorite, $user_id, $movie_id]);
            return $newFavorite;
        } else {
            // Tạo mới với favorite = 1
            $this->db->execute("INSERT INTO watch_history (user_id, movie_id, favorite) VALUES (?, ?, 1)", 
                [$user_id, $movie_id]);
            return 1;
        }
    }
    
    /**
     * Kiểm tra xem phim có trong favorite không
     */
    public function isFavorite($user_id, $movie_id) {
        $result = $this->db->fetch("SELECT favorite FROM watch_history WHERE user_id = ? AND movie_id = ?", 
            [$user_id, $movie_id]);
        return $result && $result['favorite'] == 1;
    }
}
?>

