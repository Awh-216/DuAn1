<?php
require_once __DIR__ . '/../../core/Database.php';

class BookingModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getTheaters() {
        return $this->db->fetchAll("SELECT * FROM theaters ORDER BY name");
    }
    
    public function getShowtimes($movie_id, $theater_id, $date) {
        return $this->db->fetchAll("SELECT s.*, t.name as theater_name FROM showtimes s 
                                    JOIN theaters t ON s.theater_id = t.id 
                                    WHERE s.movie_id = ? AND s.theater_id = ? AND s.show_date = ? 
                                    ORDER BY s.show_time", 
                                    [$movie_id, $theater_id, $date]);
    }
    
    public function getShowtimeById($id) {
        return $this->db->fetch("SELECT s.*, m.title as movie_title, t.name as theater_name, t.location 
                                 FROM showtimes s 
                                 JOIN movies m ON s.movie_id = m.id 
                                 JOIN theaters t ON s.theater_id = t.id 
                                 WHERE s.id = ?", [$id]);
    }
    
    public function getBookedSeats($showtime_id) {
        return $this->db->fetchAll("SELECT seat FROM tickets 
                                    WHERE showtime_id = ? AND status = 'Đã đặt'", 
                                    [$showtime_id]);
    }
    
    public function createTicket($data) {
        $sql = "INSERT INTO tickets (user_id, showtime_id, seat, price, qr_code) VALUES (?, ?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['user_id'],
            $data['showtime_id'],
            $data['seat'],
            $data['price'],
            $data['qr_code'] ?? null
        ]);
        return $this->db->lastInsertId();
    }
    
    public function getUserTickets($user_id) {
        return $this->db->fetchAll("SELECT t.*, s.show_date, s.show_time, s.price, 
                                   m.title as movie_title, th.name as theater_name 
                                   FROM tickets t 
                                   JOIN showtimes s ON t.showtime_id = s.id 
                                   JOIN movies m ON s.movie_id = m.id 
                                   JOIN theaters th ON s.theater_id = th.id 
                                   WHERE t.user_id = ? 
                                   ORDER BY t.created_at DESC", [$user_id]);
    }
}
?>

