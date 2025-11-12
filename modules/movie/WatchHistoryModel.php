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
}
?>

