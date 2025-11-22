<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/BookingModel.php';
require_once __DIR__ . '/../movie/MovieModel.php';

class BookingController extends Controller {
    
    public function index() {
        $this->requireLogin();
        
        $movieModel = new MovieModel();
        $bookingModel = new BookingModel();
        
        $selected_movie_id = $_GET['movie'] ?? null;
        $selected_theater = $_GET['theater'] ?? null;
        $selected_date = $_GET['date'] ?? date('Y-m-d');
        $selected_time = $_GET['time'] ?? null;
        $selected_showtime_id = $_GET['showtime_id'] ?? null;
        
        $movies = $movieModel->getTheaterMovies();
        $theaters = [];
        $showtimes = [];
        $movie = null;
        $bookedSeats = [];
        
        if ($selected_movie_id) {
            $movie = $movieModel->getById($selected_movie_id);
            // Chỉ lấy các rạp có suất chiếu phim này
            $theaters = $bookingModel->getTheatersByMovie($selected_movie_id);
        }
        
        if ($selected_movie_id && $selected_theater && $selected_date) {
            $showtimes = $bookingModel->getShowtimes($selected_movie_id, $selected_theater, $selected_date);
        }
        
        if ($selected_showtime_id) {
            $bookedSeatsData = $bookingModel->getBookedSeats($selected_showtime_id);
            $bookedSeats = array_column($bookedSeatsData, 'seat');
        }
        
        // Tạo danh sách ngày (7 ngày tiếp theo, bắt đầu từ hôm nay)
        $dates = [];
        $today = date('Y-m-d');
        for ($i = 0; $i < 7; $i++) {
            $date = date('Y-m-d', strtotime("+$i days"));
            $dates[] = [
                'value' => $date,
                'label' => date('d/m', strtotime($date)),
                'day_name' => $this->getDayName(date('w', strtotime($date))),
                'is_today' => ($date === $today)
            ];
        }
        
        $this->view('booking/index', [
            'movies' => $movies,
            'theaters' => $theaters,
            'showtimes' => $showtimes,
            'movie' => $movie,
            'selected_movie' => $selected_movie_id,
            'selected_theater' => $selected_theater,
            'selected_date' => $selected_date,
            'selected_time' => $selected_time,
            'selected_showtime_id' => $selected_showtime_id,
            'dates' => $dates,
            'bookedSeats' => $bookedSeats,
            'user' => $this->getCurrentUser()
        ]);
    }
    
    private function getDayName($day) {
        $days = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
        return $days[$day] ?? '';
    }
    
    public function selectSeat() {
        $this->requireLogin();
        
        $bookingModel = new BookingModel();
        
        $showtime_id = $_GET['showtime'] ?? null;
        
        if (!$showtime_id) {
            $this->redirect('booking');
        }
        
        $showtime = $bookingModel->getShowtimeById($showtime_id);
        
        if (!$showtime) {
            $this->redirect('booking');
        }
        
        // Kiểm tra xem showtime đã qua chưa
        $today = date('Y-m-d');
        $currentTime = date('H:i:s');
        if ($showtime['show_date'] < $today || 
            ($showtime['show_date'] === $today && $showtime['show_time'] < $currentTime)) {
            $_SESSION['error'] = 'Suất chiếu này đã qua, không thể đặt vé!';
            $this->redirect('booking');
            return;
        }
        
        $bookedSeats = $bookingModel->getBookedSeats($showtime_id);
        $bookedSeatsArray = array_column($bookedSeats, 'seat');
        
        $this->view('booking/select-seat', [
            'showtime' => $showtime,
            'bookedSeats' => $bookedSeatsArray,
            'user' => $this->getCurrentUser()
        ]);
    }
    
    public function processBooking() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('booking');
        }
        
        $user = $this->getCurrentUser();
        $showtime_id = $_POST['showtime_id'] ?? null;
        $seats = $_POST['seats'] ?? [];
        
        if (!$showtime_id || empty($seats)) {
            $_SESSION['error'] = 'Vui lòng chọn ghế!';
            $this->redirect('booking/select-seat?showtime=' . $showtime_id);
        }
        
        $bookingModel = new BookingModel();
        $showtime = $bookingModel->getShowtimeById($showtime_id);
        
        if (!$showtime) {
            $this->redirect('booking');
        }
        
        // Kiểm tra xem showtime đã qua chưa
        $today = date('Y-m-d');
        $currentTime = date('H:i:s');
        if ($showtime['show_date'] < $today || 
            ($showtime['show_date'] === $today && $showtime['show_time'] < $currentTime)) {
            $_SESSION['error'] = 'Suất chiếu này đã qua, không thể đặt vé!';
            $this->redirect('booking');
            return;
        }
        
        foreach ($seats as $seat) {
            $qr_code = uniqid('TICKET_') . '_' . $user['id'] . '_' . $showtime_id;
            
            $bookingModel->createTicket([
                'user_id' => $user['id'],
                'showtime_id' => $showtime_id,
                'seat' => $seat,
                'price' => $showtime['price'],
                'qr_code' => $qr_code
            ]);
        }
        
        $_SESSION['success'] = 'Đặt vé thành công!';
        $this->redirect('booking/my-tickets');
    }
    
    public function myTickets() {
        $this->requireLogin();
        
        $bookingModel = new BookingModel();
        $user = $this->getCurrentUser();
        
        $tickets = $bookingModel->getUserTickets($user['id']);
        
        $this->view('booking/my-tickets', [
            'tickets' => $tickets,
            'user' => $user
        ]);
    }
    
    public function submitSupport() {
        $this->requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('booking');
            return;
        }
        
        $user = $this->getCurrentUser();
        $message = trim($_POST['message'] ?? '');
        $issue = trim($_POST['issue'] ?? '');
        
        if (empty($message) || empty($issue)) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            $this->redirect('booking');
            return;
        }
        
        // Tự động tạo subject từ issue type
        $subject = $issue;
        
        // Xác định priority dựa trên issue type
        $priority = 'Trung bình';
        if (in_array($issue, ['Lỗi thanh toán', 'Không nhận được vé', 'Lỗi hệ thống'])) {
            $priority = 'Cao';
        } elseif ($issue === 'Khác') {
            $priority = 'Thấp';
        }
        
        try {
            $bookingModel = new BookingModel();
            $ticketId = $bookingModel->createSupportTicket([
                'user_id' => $user['id'],
                'subject' => $subject,
                'message' => $message,
                'status' => 'Mới',
                'priority' => $priority,
                'tags' => 'Mua bán vé - ' . $issue
            ]);
            
            $_SESSION['success'] = 'Yêu cầu hỗ trợ của bạn đã được gửi thành công! Chúng tôi sẽ phản hồi sớm nhất có thể.';
            $this->redirect('booking');
        } catch (Exception $e) {
            error_log("Error creating support ticket: " . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra khi gửi yêu cầu hỗ trợ. Vui lòng thử lại sau!';
            $this->redirect('booking');
        }
    }
}
?>

