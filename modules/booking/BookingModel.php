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
    
    public function getTheaterById($theater_id) {
        return $this->db->fetch("SELECT * FROM theaters WHERE id = ?", [$theater_id]);
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
    
    public function getMoviesByTheater($theater_id) {
        $today = date('Y-m-d');
        return $this->db->fetchAll("SELECT DISTINCT m.*, c.name as category_name 
                                   FROM movies m 
                                   LEFT JOIN categories c ON m.category_id = c.id
                                   INNER JOIN showtimes s ON m.id = s.movie_id 
                                   WHERE s.theater_id = ? 
                                   AND m.status = 'Chiếu rạp'
                                   AND s.show_date >= ?
                                   ORDER BY m.title", 
                                   [$theater_id, $today]);
    }
    
    public function getShowtimes($movie_id, $theater_id, $date) {
        $today = date('Y-m-d');
        $currentTime = date('H:i:s');
        
        // Nếu là ngày hôm nay, chỉ lấy các suất chiếu chưa bắt đầu
        if ($date === $today) {
            return $this->db->fetchAll("SELECT s.*, t.name as theater_name, s.screen_id FROM showtimes s 
                                        JOIN theaters t ON s.theater_id = t.id 
                                        WHERE s.movie_id = ? AND s.theater_id = ? AND s.show_date = ? 
                                        AND s.show_time >= ?
                                        ORDER BY s.show_time", 
                                        [$movie_id, $theater_id, $date, $currentTime]);
        } else {
            // Nếu là ngày tương lai, lấy tất cả suất chiếu
            return $this->db->fetchAll("SELECT s.*, t.name as theater_name, s.screen_id FROM showtimes s 
                                        JOIN theaters t ON s.theater_id = t.id 
                                        WHERE s.movie_id = ? AND s.theater_id = ? AND s.show_date = ? 
                                        ORDER BY s.show_time", 
                                        [$movie_id, $theater_id, $date]);
        }
    }
    
    public function getShowtimeById($id) {
        return $this->db->fetch("SELECT s.*, m.title as movie_title, t.name as theater_name, t.location, s.screen_id 
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
        $seat_type = $data['seat_type'] ?? 'normal';
        
        // Kiểm tra xem ghế đã được đặt chưa (double check trước khi insert)
        $existing = $this->db->fetch(
            "SELECT id FROM tickets WHERE showtime_id = ? AND seat = ? AND status = 'Đã đặt'",
            [$data['showtime_id'], $data['seat']]
        );
        
        if ($existing) {
            error_log("Seat {$data['seat']} already booked for showtime {$data['showtime_id']}");
            return false; // Ghế đã được đặt
        }
        
        $sql = "INSERT INTO tickets (user_id, showtime_id, booking_pending_id, seat, seat_type, price, qr_code, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $this->db->execute($sql, [
            $data['user_id'],
            $data['showtime_id'],
            $data['booking_pending_id'] ?? null,
            $data['seat'],
            $seat_type,
            $data['price'],
            $data['qr_code'] ?? null,
            $status
        ]);
        $ticket_id = $this->db->lastInsertId();
        
        // Log để debug
        error_log("Created ticket ID: $ticket_id, user_id: {$data['user_id']}, showtime_id: {$data['showtime_id']}, seat: {$data['seat']}, seat_type: $seat_type, price: {$data['price']}, status: $status");
        
        return $ticket_id;
    }
    
    public function getUserTickets($user_id) {
        try {
            $tickets = $this->db->fetchAll("SELECT t.*, s.show_date, s.show_time, 
                                           COALESCE(t.price, s.price) as price,
                                           m.title as movie_title, th.name as theater_name,
                                           bp.qr_code as booking_qr_code, bp.id as booking_pending_id,
                                           'completed' as booking_type
                                           FROM tickets t 
                                           JOIN showtimes s ON t.showtime_id = s.id 
                                           JOIN movies m ON s.movie_id = m.id 
                                           JOIN theaters th ON s.theater_id = th.id 
                                           LEFT JOIN booking_pending bp ON t.booking_pending_id = bp.id
                                           WHERE t.user_id = ? 
                                           ORDER BY t.created_at DESC", [$user_id]);
            
            // Log để debug
            error_log("getUserTickets for user_id $user_id: Found " . count($tickets) . " tickets");
            
            return $tickets;
        } catch (Exception $e) {
            error_log("Error in getUserTickets: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy pending bookings của user (chưa thanh toán)
     */
    public function getUserPendingBookings($user_id) {
        try {
            // Xóa các booking đã hết hạn (tự động cleanup)
            $this->cleanExpiredPendingBookings();
            
            // Lấy các pending bookings còn hiệu lực
            $now = date('Y-m-d H:i:s');
            $pendingBookings = $this->db->fetchAll("
                SELECT bp.*, 
                       s.show_date, s.show_time, s.price as showtime_price,
                       m.title as movie_title, m.id as movie_id,
                       th.name as theater_name, th.id as theater_id
                FROM booking_pending bp
                JOIN showtimes s ON bp.showtime_id = s.id
                JOIN movies m ON s.movie_id = m.id
                JOIN theaters th ON s.theater_id = th.id
                WHERE bp.user_id = ? 
                AND bp.status = 'pending'
                AND (bp.expires_at IS NULL OR bp.expires_at > ?)
                AND (bp.created_at > DATE_SUB(?, INTERVAL 10 MINUTE))
                ORDER BY bp.created_at DESC
            ", [$user_id, $now, $now]);
            
            // Lấy danh sách food items để map ID sang tên
            $foodItemsMap = [];
            try {
                $foodItems = $this->getFoodItems();
                foreach ($foodItems as $food) {
                    $foodItemsMap[$food['id']] = $food;
                }
            } catch (Exception $e) {
                error_log("Error getting food items for mapping: " . $e->getMessage());
            }
            
            // Format dữ liệu để giống với tickets
            $formattedBookings = [];
            foreach ($pendingBookings as $booking) {
                $seats = json_decode($booking['seats'], true) ?? [];
                $totalAmount = floatval($booking['total_amount']);
                $foodItemsData = !empty($booking['food_items']) ? json_decode($booking['food_items'], true) : null;
                
                // Map food items ID sang tên
                $foodItemsWithNames = null;
                if ($foodItemsData) {
                    $foodItemsWithNames = [];
                    foreach ($foodItemsData as $foodId => $quantity) {
                        if ($quantity > 0 && isset($foodItemsMap[$foodId])) {
                            $foodItemsWithNames[] = [
                                'id' => $foodId,
                                'name' => $foodItemsMap[$foodId]['name'],
                                'quantity' => $quantity,
                                'price' => $foodItemsMap[$foodId]['price']
                            ];
                        }
                    }
                }
                
                // Tạo một entry cho mỗi ghế (hoặc một entry tổng hợp)
                $formattedBookings[] = [
                    'id' => 'pending_' . $booking['id'],
                    'booking_id' => $booking['id'],
                    'user_id' => $booking['user_id'],
                    'showtime_id' => $booking['showtime_id'],
                    'seat' => implode(', ', $seats), // Hiển thị tất cả ghế
                    'seats_array' => $seats,
                    'seat_type' => 'normal',
                    'price' => $totalAmount,
                    'status' => 'Chưa thanh toán',
                    'qr_code' => null,
                    'created_at' => $booking['created_at'],
                    'show_date' => $booking['show_date'],
                    'show_time' => $booking['show_time'],
                    'movie_title' => $booking['movie_title'],
                    'movie_id' => $booking['movie_id'],
                    'theater_name' => $booking['theater_name'],
                    'theater_id' => $booking['theater_id'],
                    'booking_type' => 'pending',
                    'vnp_txn_ref' => $booking['vnp_txn_ref'],
                    'expires_at' => $booking['expires_at'],
                    'food_items' => $foodItemsWithNames
                ];
            }
            
            return $formattedBookings;
        } catch (Exception $e) {
            error_log("Error in getUserPendingBookings: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Xóa các pending bookings đã hết hạn
     */
    public function cleanExpiredPendingBookings() {
        try {
            $now = date('Y-m-d H:i:s');
            // Xóa các booking đã hết hạn (expires_at < now hoặc created_at + 10 phút < now)
            $this->db->execute("
                DELETE FROM booking_pending 
                WHERE status = 'pending' 
                AND (
                    (expires_at IS NOT NULL AND expires_at <= ?) 
                    OR 
                    (created_at <= DATE_SUB(?, INTERVAL 10 MINUTE))
                )
            ", [$now, $now]);
        } catch (Exception $e) {
            error_log("Error cleaning expired pending bookings: " . $e->getMessage());
        }
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
    public function reserveSeats($showtime_id, $seats, $user_id, $session_id, $duration_minutes = 10) {
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
    
    public function extendReservation($showtime_id, $seat, $user_id, $duration_minutes = 10) {
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
    
    // Food Items Methods
    public function getFoodItems() {
        try {
            return $this->db->fetchAll("SELECT * FROM food_items WHERE is_active = 1 ORDER BY type, name");
        } catch (Exception $e) {
            error_log("Error getting food items: " . $e->getMessage());
            return [];
        }
    }
    
    public function getFoodItemById($id) {
        try {
            return $this->db->fetch("SELECT * FROM food_items WHERE id = ? AND is_active = 1", [$id]);
        } catch (Exception $e) {
            error_log("Error getting food item: " . $e->getMessage());
            return null;
        }
    }
    
    public function createBookingFoodItem($ticket_id, $food_item_id, $quantity, $price, $booking_pending_id = null) {
        try {
            $sql = "INSERT INTO booking_food_items (ticket_id, booking_pending_id, food_item_id, quantity, price) VALUES (?, ?, ?, ?, ?)";
            $this->db->execute($sql, [$ticket_id, $booking_pending_id, $food_item_id, $quantity, $price]);
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            error_log("Error creating booking food item: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Tạo booking pending (chờ thanh toán)
     */
    public function createPendingBooking($data) {
        try {
            // Kiểm tra xem bảng đã tồn tại chưa
            $tableExists = $this->db->fetch("SHOW TABLES LIKE 'booking_pending'");
            
            if (!$tableExists) {
                // Tạo bảng booking_pending nếu chưa có - dùng exec() cho DDL statements
                $pdo = $this->db->getConnection();
                $createTableSql = "
                    CREATE TABLE booking_pending (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        user_id INT NOT NULL,
                        showtime_id INT NOT NULL,
                        seats TEXT NOT NULL,
                        food_items TEXT,
                        customer_email VARCHAR(255) NOT NULL,
                        total_amount DECIMAL(10,2) NOT NULL,
                        vnp_txn_ref VARCHAR(100) UNIQUE,
                        qr_code VARCHAR(255) DEFAULT NULL,
                        status ENUM('pending','completed','cancelled') DEFAULT 'pending',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        expires_at TIMESTAMP NULL,
                        INDEX idx_user (user_id),
                        INDEX idx_showtime (showtime_id),
                        INDEX idx_txn_ref (vnp_txn_ref),
                        INDEX idx_status (status),
                        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                        FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
                ";
                $pdo->exec($createTableSql);
                error_log("Created booking_pending table successfully");
            }
        } catch (Exception $e) {
            // Bảng đã tồn tại hoặc có lỗi khác, log và tiếp tục
            error_log("Note when creating booking_pending table: " . $e->getMessage());
        }
        
        try {
            // Validate dữ liệu
            if (empty($data['user_id']) || empty($data['showtime_id']) || empty($data['seats']) || empty($data['customer_email']) || empty($data['vnp_txn_ref'])) {
                error_log("Missing required data for pending booking: " . json_encode($data));
                return false;
            }
            
            $expiresAt = isset($data['expires_at']) ? $data['expires_at'] : date('Y-m-d H:i:s', strtotime('+10 minutes'));
            $seatsJson = json_encode($data['seats']);
            $foodItemsJson = !empty($data['food_items']) ? json_encode($data['food_items']) : null;
            
            $sql = "INSERT INTO booking_pending (user_id, showtime_id, seats, food_items, customer_email, total_amount, vnp_txn_ref, expires_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            error_log("Creating pending booking with data: user_id=" . $data['user_id'] . ", showtime_id=" . $data['showtime_id'] . ", txn_ref=" . $data['vnp_txn_ref']);
            
            $this->db->execute($sql, [
                $data['user_id'],
                $data['showtime_id'],
                $seatsJson,
                $foodItemsJson,
                $data['customer_email'],
                $data['total_amount'],
                $data['vnp_txn_ref'],
                $expiresAt
            ]);
            
            $lastId = $this->db->lastInsertId();
            error_log("Pending booking created successfully with ID: " . $lastId);
            return $lastId;
            
        } catch (Exception $e) {
            error_log("Error creating pending booking: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            error_log("Data: " . json_encode($data));
            return false;
        }
    }
    
    /**
     * Lấy pending booking theo vnp_txn_ref
     */
    public function getPendingBookingByTxnRef($vnp_txn_ref) {
        try {
            return $this->db->fetch("SELECT * FROM booking_pending WHERE vnp_txn_ref = ? AND status = 'pending'", [$vnp_txn_ref]);
        } catch (Exception $e) {
            error_log("Error getting pending booking: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Cập nhật trạng thái pending booking
     */
    public function updatePendingBookingStatus($id, $status) {
        try {
            $sql = "UPDATE booking_pending SET status = ? WHERE id = ?";
            $this->db->execute($sql, [$status, $id]);
            return true;
        } catch (Exception $e) {
            error_log("Error updating pending booking status: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy pending booking theo ID
     */
    public function getPendingBookingById($id) {
        try {
            return $this->db->fetch("SELECT * FROM booking_pending WHERE id = ?", [$id]);
        } catch (Exception $e) {
            error_log("Error getting pending booking by ID: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Cập nhật QR code cho booking
     */
    public function updateBookingQRCode($booking_id, $qr_code) {
        try {
            // Đảm bảo cột qr_code tồn tại
            $pdo = $this->db->getConnection();
            try {
                $pdo->exec("ALTER TABLE booking_pending ADD COLUMN qr_code VARCHAR(255) DEFAULT NULL");
            } catch (Exception $e) {
                // Cột đã tồn tại, bỏ qua
            }
            
            $sql = "UPDATE booking_pending SET qr_code = ? WHERE id = ?";
            $this->db->execute($sql, [$qr_code, $booking_id]);
            return true;
        } catch (Exception $e) {
            error_log("Error updating booking QR code: " . $e->getMessage());
            return false;
        }
    }
    
    // Seat Layout Methods
    public function getScreenSeatLayout($screen_id) {
        try {
            $screen = $this->db->fetch("SELECT seat_layout_config FROM theater_screens WHERE id = ?", [$screen_id]);
            if ($screen && $screen['seat_layout_config']) {
                return json_decode($screen['seat_layout_config'], true);
            }
            // Default layout: 3 hàng đầu = thường, từ hàng 4 (D) trở xuống = VIP, cuối = ghế đôi
            $defaultRows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
            return [
                'rows' => $defaultRows,
                'cols' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                'vip_rows' => array_slice($defaultRows, 3, -1), // D, E, F, G, H, I, J, K (từ hàng 4 trở xuống)
                'couple_rows' => [end($defaultRows)], // L
                'normal_price' => 120000,
                'vip_price' => 180000,
                'couple_price' => 240000,
                'layout_type' => 'standard'
            ];
        } catch (Exception $e) {
            error_log("Error getting seat layout: " . $e->getMessage());
            return null;
        }
    }
    
    public function getSeatType($seat, $layout) {
        if (!$layout) {
            return 'normal';
        }
        
        $row = substr($seat, 0, 1);
        $vip_rows = $layout['vip_rows'] ?? [];
        $couple_rows = $layout['couple_rows'] ?? [];
        
        if (in_array($row, $couple_rows)) {
            return 'couple';
        } elseif (in_array($row, $vip_rows)) {
            return 'vip';
        }
        return 'normal';
    }
    
    public function getSeatPrice($seat, $layout, $base_price) {
        if (!$layout) {
            return $base_price;
        }
        
        $seat_type = $this->getSeatType($seat, $layout);
        
        switch ($seat_type) {
            case 'vip':
                return $layout['vip_price'] ?? ($base_price * 1.5);
            case 'couple':
                return $layout['couple_price'] ?? ($base_price * 2);
            default:
                return $layout['normal_price'] ?? $base_price;
        }
    }
    
    public function getShowtimeWithScreen($showtime_id) {
        try {
            return $this->db->fetch("SELECT s.*, m.title as movie_title, t.name as theater_name, t.location, 
                                    sc.id as screen_id, sc.seat_layout_config
                                    FROM showtimes s 
                                    JOIN movies m ON s.movie_id = m.id 
                                    JOIN theaters t ON s.theater_id = t.id 
                                    LEFT JOIN theater_screens sc ON s.screen_id = sc.id
                                    WHERE s.id = ?", [$showtime_id]);
        } catch (Exception $e) {
            error_log("Error getting showtime with screen: " . $e->getMessage());
            return null;
        }
    }
    
    public function getScreenInfo($screen_id) {
        try {
            return $this->db->fetch("SELECT id, screen_name, theater_id FROM theater_screens WHERE id = ?", [$screen_id]);
        } catch (Exception $e) {
            error_log("Error getting screen info: " . $e->getMessage());
            return null;
        }
    }
    
    public function getTheaterInfo($theater_id) {
        try {
            return $this->getTheaterById($theater_id);
        } catch (Exception $e) {
            error_log("Error getting theater info: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Lấy vé theo booking_id
     * @param int $booking_id ID của booking
     * @param int|null $user_id ID của user (null nếu không cần kiểm tra user, dùng cho verify QR code)
     */
    public function getTicketsByBookingId($booking_id, $user_id = null) {
        if ($user_id !== null) {
            return $this->db->fetchAll("
                SELECT t.*, s.show_date, s.show_time, s.price as showtime_price,
                       m.title as movie_title, th.name as theater_name
                FROM tickets t
                JOIN showtimes s ON t.showtime_id = s.id
                JOIN movies m ON s.movie_id = m.id
                JOIN theaters th ON s.theater_id = th.id
                WHERE t.booking_pending_id = ? AND t.user_id = ?
                ORDER BY t.seat
            ", [$booking_id, $user_id]);
        } else {
            // Không kiểm tra user_id (dùng cho verify QR code)
            return $this->db->fetchAll("
                SELECT t.*, s.show_date, s.show_time, s.price as showtime_price,
                       m.title as movie_title, th.name as theater_name
                FROM tickets t
                JOIN showtimes s ON t.showtime_id = s.id
                JOIN movies m ON s.movie_id = m.id
                JOIN theaters th ON s.theater_id = th.id
                WHERE t.booking_pending_id = ?
                ORDER BY t.seat
            ", [$booking_id]);
        }
    }
    
    /**
     * Lấy một vé theo ID
     * @param int $ticket_id ID của vé
     * @param int|null $user_id ID của user (null nếu không cần kiểm tra user, dùng cho verify QR code)
     */
    public function getTicketById($ticket_id, $user_id = null) {
        if ($user_id !== null) {
            return $this->db->fetch("
                SELECT t.*, s.show_date, s.show_time, s.price as showtime_price,
                       m.title as movie_title, th.name as theater_name
                FROM tickets t
                JOIN showtimes s ON t.showtime_id = s.id
                JOIN movies m ON s.movie_id = m.id
                JOIN theaters th ON s.theater_id = th.id
                WHERE t.id = ? AND t.user_id = ?
            ", [$ticket_id, $user_id]);
        } else {
            return $this->db->fetch("
                SELECT t.*, s.show_date, s.show_time, s.price as showtime_price,
                       m.title as movie_title, th.name as theater_name
                FROM tickets t
                JOIN showtimes s ON t.showtime_id = s.id
                JOIN movies m ON s.movie_id = m.id
                JOIN theaters th ON s.theater_id = th.id
                WHERE t.id = ?
            ", [$ticket_id]);
        }
    }
}
?>

