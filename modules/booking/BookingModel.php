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
    
    public function getTheatersByMovie($movie_id) {
        $today = date('Y-m-d');
        return $this->db->fetchAll("SELECT DISTINCT t.* FROM theaters t 
                                   JOIN showtimes s ON t.id = s.theater_id 
                                   WHERE s.movie_id = ? 
                                   AND s.show_date >= ?
                                   ORDER BY t.name", 
                                   [$movie_id, $today]);
    }
    
    public function getShowtimes($movie_id, $theater_id, $date) {
        $today = date('Y-m-d');
        $currentTime = date('H:i:s');
        
        // Nếu là ngày hôm nay, chỉ lấy các suất chiếu chưa bắt đầu
        if ($date === $today) {
            return $this->db->fetchAll("SELECT s.*, t.name as theater_name FROM showtimes s 
                                        JOIN theaters t ON s.theater_id = t.id 
                                        WHERE s.movie_id = ? AND s.theater_id = ? AND s.show_date = ? 
                                        AND s.show_time >= ?
                                        ORDER BY s.show_time", 
                                        [$movie_id, $theater_id, $date, $currentTime]);
        } else {
            // Nếu là ngày tương lai, lấy tất cả suất chiếu
            return $this->db->fetchAll("SELECT s.*, t.name as theater_name FROM showtimes s 
                                        JOIN theaters t ON s.theater_id = t.id 
                                        WHERE s.movie_id = ? AND s.theater_id = ? AND s.show_date = ? 
                                        ORDER BY s.show_time", 
                                        [$movie_id, $theater_id, $date]);
        }
    }
    
    public function getShowtimeById($id) {
        return $this->db->fetch("SELECT s.*, m.title as movie_title, t.name as theater_name, t.location 
                                 FROM showtimes s 
                                 JOIN movies m ON s.movie_id = m.id 
                                 JOIN theaters t ON s.theater_id = t.id 
                                 WHERE s.id = ?", [$id]);
    }
    
    public function getBookedSeats($showtime_id) {
        try {
            $bookedSeats = $this->db->fetchAll("SELECT seat FROM tickets 
                                                WHERE showtime_id = ? AND status = 'Đã đặt'", 
                                                [$showtime_id]);
            
            // Log để debug
            error_log("Get booked seats for showtime $showtime_id: " . count($bookedSeats) . " seats - " . implode(', ', array_column($bookedSeats, 'seat')));
            
            return $bookedSeats;
        } catch (Exception $e) {
            error_log("Error getting booked seats: " . $e->getMessage());
            return [];
        }
    }
    
    public function getBookedAndReservedSeats($showtime_id) {
        try {
            // Lấy ghế đã đặt từ database
            $booked = $this->db->fetchAll("SELECT seat, 'booked' as type FROM tickets 
                                          WHERE showtime_id = ? AND status = 'Đã đặt'", 
                                          [$showtime_id]);
            
            // Log để debug
            $bookedSeats = array_column($booked, 'seat');
            error_log("GetBookedAndReservedSeats for showtime $showtime_id - Booked seats: " . implode(', ', $bookedSeats));
            
            // Kiểm tra xem bảng seat_reservations có tồn tại không
            $tableExists = $this->db->fetch("SHOW TABLES LIKE 'seat_reservations'");
            $reserved = [];
            
            if ($tableExists) {
                // Lấy ghế đang được reserve
                try {
                    $this->cleanExpiredReservations();
                    $now = date('Y-m-d H:i:s');
                    $reserved = $this->db->fetchAll("SELECT seat, 'reserved' as type FROM seat_reservations 
                                                    WHERE showtime_id = ? AND expires_at > ?", 
                                                    [$showtime_id, $now]);
                    $reservedSeats = array_column($reserved, 'seat');
                    error_log("Reserved seats: " . implode(', ', $reservedSeats));
                } catch (Exception $e) {
                    // Nếu có lỗi khi query reservations, chỉ trả về ghế đã đặt
                    error_log("Error getting reserved seats: " . $e->getMessage());
                }
            }
            
            // Gộp lại
            $result = [];
            foreach ($booked as $item) {
                $result[$item['seat']] = $item;
            }
            foreach ($reserved as $item) {
                if (!isset($result[$item['seat']])) {
                    $result[$item['seat']] = $item;
                }
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error in getBookedAndReservedSeats: " . $e->getMessage());
            // Fallback: chỉ lấy ghế đã đặt
            $booked = $this->db->fetchAll("SELECT seat, 'booked' as type FROM tickets 
                                          WHERE showtime_id = ? AND status = 'Đã đặt'", 
                                          [$showtime_id]);
            $result = [];
            foreach ($booked as $item) {
                $result[$item['seat']] = $item;
            }
            return $result;
        }
    }
    
    public function createTicket($data) {
        // Đảm bảo status luôn là 'Đã đặt' khi tạo vé
        $status = $data['status'] ?? 'Đã đặt';
        
        // Kiểm tra xem ghế đã được đặt chưa (double check trước khi insert)
        $existing = $this->db->fetch(
            "SELECT id FROM tickets WHERE showtime_id = ? AND seat = ? AND status = 'Đã đặt'",
            [$data['showtime_id'], $data['seat']]
        );
        
        if ($existing) {
            error_log("Seat {$data['seat']} already booked for showtime {$data['showtime_id']}");
            return false; // Ghế đã được đặt
        }
        
        $sql = "INSERT INTO tickets (user_id, showtime_id, seat, price, qr_code, status) VALUES (?, ?, ?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['user_id'],
            $data['showtime_id'],
            $data['seat'],
            $data['price'],
            $data['qr_code'] ?? null,
            $status
        ]);
        $ticket_id = $this->db->lastInsertId();
        
        // Log để debug
        error_log("Created ticket ID: $ticket_id, user_id: {$data['user_id']}, showtime_id: {$data['showtime_id']}, seat: {$data['seat']}, price: {$data['price']}, status: $status");
        
        return $ticket_id;
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
    
    public function createSupportTicket($data) {
        $sql = "INSERT INTO support_tickets (user_id, subject, message, status, priority, tags) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['user_id'],
            $data['subject'],
            $data['message'],
            $data['status'] ?? 'Mới',
            $data['priority'] ?? 'Trung bình',
            $data['tags'] ?? null
        ]);
        return $this->db->lastInsertId();
    }
    
    // Seat Reservation Methods
    public function reserveSeats($showtime_id, $seats, $user_id, $session_id, $duration_minutes = 5) {
        try {
            // Kiểm tra bảng có tồn tại không
            $tableExists = $this->db->fetch("SHOW TABLES LIKE 'seat_reservations'");
            if (!$tableExists) {
                // Nếu bảng chưa tồn tại, vẫn trả về danh sách ghế (nhưng không reserve)
                return $seats;
            }
            
            $reserved_seats = [];
            $now = date('Y-m-d H:i:s');
            $expires_at = date('Y-m-d H:i:s', strtotime("+$duration_minutes minutes"));
            
            // Xóa reservations hết hạn
            $this->cleanExpiredReservations();
            
            foreach ($seats as $seat) {
                // Kiểm tra xem ghế đã được reserve bởi người khác chưa
                $existing = $this->db->fetch(
                    "SELECT * FROM seat_reservations 
                    WHERE showtime_id = ? AND seat = ? AND expires_at > ? 
                    AND (user_id != ? OR session_id != ?)",
                    [$showtime_id, $seat, $now, $user_id, $session_id]
                );
                
                if ($existing) {
                    continue; // Ghế đã được reserve
                }
                
                // Xóa reservation cũ của user này (nếu có)
                $this->db->execute(
                    "DELETE FROM seat_reservations WHERE showtime_id = ? AND seat = ? AND user_id = ?",
                    [$showtime_id, $seat, $user_id]
                );
                
                // Tạo reservation mới
                $this->db->execute(
                    "INSERT INTO seat_reservations (showtime_id, seat, user_id, session_id, expires_at) 
                    VALUES (?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE 
                    user_id = VALUES(user_id), 
                    session_id = VALUES(session_id), 
                    reserved_at = NOW(), 
                    expires_at = VALUES(expires_at)",
                    [$showtime_id, $seat, $user_id, $session_id, $expires_at]
                );
                
                $reserved_seats[] = $seat;
            }
            
            return $reserved_seats;
        } catch (Exception $e) {
            error_log("Error reserving seats: " . $e->getMessage());
            // Trả về danh sách ghế nhưng không reserve
            return $seats;
        }
    }
    
    public function getReservedSeats($showtime_id) {
        try {
            // Kiểm tra bảng có tồn tại không
            $tableExists = $this->db->fetch("SHOW TABLES LIKE 'seat_reservations'");
            if (!$tableExists) {
                return [];
            }
            
            $now = date('Y-m-d H:i:s');
            $this->cleanExpiredReservations();
            
            return $this->db->fetchAll(
                "SELECT seat, user_id, expires_at FROM seat_reservations 
                WHERE showtime_id = ? AND expires_at > ?",
                [$showtime_id, $now]
            );
        } catch (Exception $e) {
            error_log("Error getting reserved seats: " . $e->getMessage());
            return [];
        }
    }
    
    public function releaseSeats($showtime_id, $seats, $user_id = null) {
        if (empty($seats)) {
            return;
        }
        
        try {
            // Kiểm tra bảng có tồn tại không
            $tableExists = $this->db->fetch("SHOW TABLES LIKE 'seat_reservations'");
            if (!$tableExists) {
                return;
            }
            
            $params = [$showtime_id];
            $placeholders = [];
            
            foreach ($seats as $seat) {
                $placeholders[] = '?';
                $params[] = $seat;
            }
            
            $sql = "DELETE FROM seat_reservations 
                    WHERE showtime_id = ? AND seat IN (" . implode(',', $placeholders) . ")";
            
            if ($user_id) {
                $sql .= " AND user_id = ?";
                $params[] = $user_id;
            }
            
            $this->db->execute($sql, $params);
        } catch (Exception $e) {
            error_log("Error releasing seats: " . $e->getMessage());
        }
    }
    
    public function cleanExpiredReservations() {
        try {
            // Kiểm tra bảng có tồn tại không
            $tableExists = $this->db->fetch("SHOW TABLES LIKE 'seat_reservations'");
            if (!$tableExists) {
                return;
            }
            
            $now = date('Y-m-d H:i:s');
            $this->db->execute(
                "DELETE FROM seat_reservations WHERE expires_at <= ?",
                [$now]
            );
        } catch (Exception $e) {
            error_log("Error cleaning expired reservations: " . $e->getMessage());
        }
    }
    
    public function extendReservation($showtime_id, $seat, $user_id, $duration_minutes = 5) {
        try {
            // Kiểm tra bảng có tồn tại không
            $tableExists = $this->db->fetch("SHOW TABLES LIKE 'seat_reservations'");
            if (!$tableExists) {
                return;
            }
            
            $expires_at = date('Y-m-d H:i:s', strtotime("+$duration_minutes minutes"));
            $this->db->execute(
                "UPDATE seat_reservations 
                SET expires_at = ?, reserved_at = NOW() 
                WHERE showtime_id = ? AND seat = ? AND user_id = ?",
                [$expires_at, $showtime_id, $seat, $user_id]
            );
        } catch (Exception $e) {
            error_log("Error extending reservation: " . $e->getMessage());
        }
    }
}
?>

