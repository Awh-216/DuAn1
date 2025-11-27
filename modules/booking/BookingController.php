<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Database.php';
require_once __DIR__ . '/BookingModel.php';
require_once __DIR__ . '/../movie/MovieModel.php';
require_once __DIR__ . '/../../core/Email.php';

class BookingController extends Controller {
    
    public function index() {
        try {
            $this->requireLogin();
            
            $movieModel = new MovieModel();
            $bookingModel = new BookingModel();
            
            $selected_movie_id = $_GET['movie'] ?? null;
            $selected_theater = $_GET['theater'] ?? null;
            $selected_date = $_GET['date'] ?? date('Y-m-d');
            $selected_time = $_GET['time'] ?? null;
            $selected_showtime_id = $_GET['showtime_id'] ?? null;
            
            // Lấy tất cả phim đang chiếu rạp
            $allMovies = $movieModel->getTheaterMovies();
            
            $theaters = [];
            $showtimes = [];
            $movie = null;
            $bookedSeats = [];
            $reservedSeats = [];
            
            // Nếu có showtime_id, lấy thông tin showtime để đảm bảo có đầy đủ thông tin
            if ($selected_showtime_id) {
                $user = $this->getCurrentUser();
                
                // Kiểm tra xem người dùng có bị cấm đặt vé phòng này không
                if ($this->isUserBannedFromScreen($user['id'], $selected_showtime_id)) {
                    $_SESSION['error'] = 'Bạn đã bị cấm đặt vé phòng này do vi phạm quy định thời gian đặt vé!';
                    $this->redirect('booking');
                    return;
                }
                
                // Bắt đầu tracking session
                $this->startBookingSession($user['id'], $selected_showtime_id);
                
                $showtime = $bookingModel->getShowtimeById($selected_showtime_id);
                if ($showtime) {
                    // Tự động lấy lại selected_movie_id, selected_theater, selected_date từ showtime
                    $selected_movie_id = $showtime['movie_id'];
                    $selected_theater = $showtime['theater_id'];
                    $selected_date = $showtime['show_date'];
                    
                    // Lấy thông tin movie và theaters
                    if (!$movie) {
                        $movie = $movieModel->getById($selected_movie_id);
                    }
                    if (empty($theaters)) {
                        $theaters = $bookingModel->getTheatersByMovie($selected_movie_id);
                    }
                }
            }
            
            if ($selected_movie_id) {
                if (!$movie) {
                    $movie = $movieModel->getById($selected_movie_id);
                }
                // Chỉ lấy các rạp có suất chiếu phim này
                if (empty($theaters)) {
                    $theaters = $bookingModel->getTheatersByMovie($selected_movie_id);
                }
            }
            
            if ($selected_movie_id && $selected_theater && $selected_date) {
                $showtimes = $bookingModel->getShowtimes($selected_movie_id, $selected_theater, $selected_date);
            }
            
            $seatLayout = null;
            $screenInfo = null;
            $theaterInfo = null;
            
            // Lấy food items luôn (không cần showtime)
            $foodItems = [];
            try {
                $foodItems = $bookingModel->getFoodItems();
            } catch (Exception $e) {
                error_log("Error getting food items: " . $e->getMessage());
                $foodItems = [];
            }
            
            if ($selected_showtime_id) {
                try {
                    // Lấy thông tin showtime với screen layout
                    // Tìm showtime trong mảng showtimes để lấy screen_id
                    $showtimeWithScreen = null;
                    foreach ($showtimes as $st) {
                        if ($st['id'] == $selected_showtime_id) {
                            $showtimeWithScreen = $st;
                            break;
                        }
                    }
                    
                    // Nếu không tìm thấy trong showtimes, lấy trực tiếp từ database
                    if (!$showtimeWithScreen) {
                        $showtimeWithScreen = $bookingModel->getShowtimeById($selected_showtime_id);
                    }
                    
                    if ($showtimeWithScreen) {
                        // Lấy thông tin screen (số phòng) và theater (tên rạp)
                        if (isset($showtimeWithScreen['screen_id']) && $showtimeWithScreen['screen_id']) {
                            $seatLayout = $bookingModel->getScreenSeatLayout($showtimeWithScreen['screen_id']);
                            $screenInfo = $bookingModel->getScreenInfo($showtimeWithScreen['screen_id']);
                        }
                        
                        // Lấy thông tin theater
                        if (isset($showtimeWithScreen['theater_id']) && $showtimeWithScreen['theater_id']) {
                            $theaterInfo = $bookingModel->getTheaterInfo($showtimeWithScreen['theater_id']);
                        } elseif (isset($showtimeWithScreen['theater_name'])) {
                            // Nếu đã có theater_name trong showtime, tạo array tương ứng
                            $theaterInfo = [
                                'id' => $showtimeWithScreen['theater_id'] ?? $selected_theater,
                                'name' => $showtimeWithScreen['theater_name'],
                                'location' => $showtimeWithScreen['location'] ?? null
                            ];
                        }
                    }
                    
                    // Lấy cả ghế đã đặt và đang được reserve
                    $bookedAndReserved = $bookingModel->getBookedAndReservedSeats($selected_showtime_id);
                    $bookedSeats = [];
                    $reservedSeats = [];
                    
                    // Debug: Log raw data
                    error_log("Raw bookedAndReserved data: " . print_r($bookedAndReserved, true));
                    
                    foreach ($bookedAndReserved as $seat => $data) {
                        if (isset($data['type']) && $data['type'] === 'booked') {
                            $bookedSeats[] = $seat;
                        } else if (isset($data['type']) && $data['type'] === 'reserved') {
                            $reservedSeats[] = $seat;
                        }
                    }
                    
                    // Debug: Log final arrays
                    error_log("Final bookedSeats for showtime $selected_showtime_id: " . print_r($bookedSeats, true));
                    error_log("Final reservedSeats for showtime $selected_showtime_id: " . print_r($reservedSeats, true));
                    
                } catch (Exception $e) {
                    // Nếu bảng seat_reservations chưa tồn tại, chỉ lấy ghế đã đặt
                    error_log("Error getting reserved seats: " . $e->getMessage());
                    error_log("Stack trace: " . $e->getTraceAsString());
                    
                    try {
                        $bookedSeatsData = $bookingModel->getBookedSeats($selected_showtime_id);
                        $bookedSeats = array_column($bookedSeatsData, 'seat');
                        error_log("Fallback bookedSeats: " . print_r($bookedSeats, true));
                        $reservedSeats = [];
                    } catch (Exception $e2) {
                        error_log("Error in fallback getBookedSeats: " . $e2->getMessage());
                        $bookedSeats = [];
                        $reservedSeats = [];
                    }
                }
            } else {
                $bookedSeats = [];
                $reservedSeats = [];
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
            
            $user = $this->getCurrentUser();
            
            $this->view('booking/index', [
                'allMovies' => $allMovies,
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
                'reservedSeats' => $reservedSeats,
                'seatLayout' => $seatLayout,
                'normalPrice' => $seatLayout['normal_price'] ?? 120000,
                'vipPrice' => $seatLayout['vip_price'] ?? 180000,
                'couplePrice' => $seatLayout['couple_price'] ?? 240000,
                'foodItems' => $foodItems,
                'user' => $user,
                'screenInfo' => $screenInfo ?? null,
                'theaterInfo' => $theaterInfo ?? null
            ]);
        } catch (Exception $e) {
            // Log lỗi để debug
            error_log("Error in BookingController->index(): " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            error_log("GET params: " . print_r($_GET, true));
            
            // Lấy lại parameters từ GET
            $selected_movie_id = $_GET['movie'] ?? null;
            $selected_theater = $_GET['theater'] ?? null;
            $selected_date = $_GET['date'] ?? date('Y-m-d');
            $selected_time = $_GET['time'] ?? null;
            $selected_showtime_id = $_GET['showtime_id'] ?? null;
            
            // Vẫn hiển thị trang booking nhưng với lỗi và fallback dữ liệu
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['error'] = 'Có lỗi xảy ra khi tải trang đặt vé: ' . $e->getMessage();
            
            // Fallback: hiển thị với dữ liệu tối thiểu để không bị redirect về trang chủ
            try {
                $movieModel = new MovieModel();
                $bookingModel = new BookingModel();
                
                $movies = $movieModel->getTheaterMovies();
                $movie = $selected_movie_id ? $movieModel->getById($selected_movie_id) : null;
                $theaters = $selected_movie_id ? $bookingModel->getTheatersByMovie($selected_movie_id) : [];
                $showtimes = ($selected_movie_id && $selected_theater && $selected_date) 
                    ? $bookingModel->getShowtimes($selected_movie_id, $selected_theater, $selected_date) 
                    : [];
                
                // Lấy ghế đã đặt (không dùng reserved để tránh lỗi)
                $bookedSeats = [];
                $reservedSeats = [];
                if ($selected_showtime_id) {
                    try {
                        $bookedSeatsData = $bookingModel->getBookedSeats($selected_showtime_id);
                        $bookedSeats = array_column($bookedSeatsData, 'seat');
                    } catch (Exception $e2) {
                        error_log("Error getting booked seats: " . $e2->getMessage());
                    }
                }
                
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
                
                $user = $this->getCurrentUser();
                
                $allMovies = $movieModel->getTheaterMovies();
                
            $this->view('booking/index', [
                'allMovies' => $allMovies,
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
                'reservedSeats' => $reservedSeats,
                'seatLayout' => $seatLayout,
                'normalPrice' => $normalPrice,
                'vipPrice' => $vipPrice,
                'couplePrice' => $couplePrice,
                'user' => $user
            ]);
            } catch (Exception $e2) {
                // Nếu vẫn lỗi, redirect về booking nhưng giữ nguyên parameters
                error_log("Error in fallback view: " . $e2->getMessage());
                $redirectUrl = '?route=booking/index';
                if ($selected_movie_id) $redirectUrl .= '&movie=' . urlencode($selected_movie_id);
                if ($selected_theater) $redirectUrl .= '&theater=' . urlencode($selected_theater);
                if ($selected_date) $redirectUrl .= '&date=' . urlencode($selected_date);
                if ($selected_showtime_id) $redirectUrl .= '&showtime_id=' . urlencode($selected_showtime_id);
                header('Location: http://localhost/DuAn1/' . $redirectUrl);
                exit;
            }
        }
    }
    
    private function getDayName($day) {
        $days = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
        return $days[$day] ?? '';
    }
    
    /**
     * Validate seat selection rules:
     * 1. Không đặt cách 1 ghế (phải liền kề)
     * 2. Không bỏ trống ghế ở giữa (kể cả ghế đã được đặt)
     * 3. Khi hàng có >= 4 cột và đặt từ 2 ghế trở lên, không được bỏ trống ghế ngoài cùng bên trái hoặc phải
     */
    private function validateSeatSelection($seats, $showtime_id = null) {
        if (empty($seats) || count($seats) == 1) {
            return null; // 1 ghế hoặc không có ghế thì không cần validate
        }
        
        // Lấy danh sách ghế đã được đặt nếu có showtime_id
        $bookedSeats = [];
        $seatLayout = null;
        if ($showtime_id) {
            $bookingModel = new BookingModel();
            $bookedSeatsData = $bookingModel->getBookedSeats($showtime_id);
            $bookedSeats = array_column($bookedSeatsData, 'seat');
            
            // Lấy seat layout để biết số cột trong mỗi hàng
            $showtime = $bookingModel->getShowtimeById($showtime_id);
            if ($showtime && isset($showtime['screen_id']) && $showtime['screen_id']) {
                $seatLayout = $bookingModel->getScreenSeatLayout($showtime['screen_id']);
            }
        }
        
        // Sắp xếp ghế theo hàng và cột
        $seatsByRow = [];
        foreach ($seats as $seat) {
            $row = substr($seat, 0, 1);
            $col = (int)substr($seat, 1);
            if (!isset($seatsByRow[$row])) {
                $seatsByRow[$row] = [];
            }
            $seatsByRow[$row][] = $col;
        }
        
        // Kiểm tra từng hàng
        foreach ($seatsByRow as $row => $cols) {
            sort($cols);
            
            // Nếu chỉ có 1 ghế trong hàng, không cần validate
            if (count($cols) <= 1) continue;
            
            // Kiểm tra không bỏ trống ghế ở giữa - KHÔNG cho phép gap
            for ($i = 0; $i < count($cols) - 1; $i++) {
                $gap = $cols[$i + 1] - $cols[$i];
                if ($gap > 1) {
                    // Luôn báo lỗi nếu có gap, không cần kiểm tra ghế đã đặt
                    return "Không được bỏ trống ghế ở giữa! Các ghế phải liền kề nhau. Vui lòng chọn các ghế liền kề.";
                }
            }
            
            // Kiểm tra quy tắc: Khi hàng có >= 4 cột và đặt từ 2 ghế trở lên, không được bỏ trống ghế ngoài cùng
            // NHƯNG bỏ qua quy tắc này nếu các ghế được chọn nằm trong nhóm chỉ có 3 cột
            if (count($cols) >= 2) {
                // Lấy danh sách tất cả các cột có thể có trong hàng này
                $allColsInRow = $this->getAllColumnsInRow($row, $seatLayout);
                
                if (count($allColsInRow) >= 4) {
                    // Kiểm tra xem các ghế được chọn có nằm trong nhóm 3 cột không
                    $isInThreeColumnGroup = $this->isSeatsInThreeColumnGroup($row, $cols, $seatLayout);
                    
                    // Chỉ áp dụng quy tắc nếu KHÔNG nằm trong nhóm 3 cột
                    if (!$isInThreeColumnGroup) {
                        // Tìm cột nhỏ nhất và lớn nhất trong hàng
                        $minCol = min($allColsInRow);
                        $maxCol = max($allColsInRow);
                        
                        // Tìm cột nhỏ nhất và lớn nhất trong các ghế đã chọn
                        $selectedMinCol = min($cols);
                        $selectedMaxCol = max($cols);
                        
                        // Kiểm tra nếu bỏ trống ghế ngoài cùng bên trái
                        if ($selectedMinCol > $minCol) {
                            // Có ghế bên trái chưa được chọn, kiểm tra xem có ghế nào có thể chọn được không (không bị đặt)
                            $hasAvailableLeftSeat = false;
                            for ($checkCol = $minCol; $checkCol < $selectedMinCol; $checkCol++) {
                                $checkSeat = $row . $checkCol;
                                // Nếu ghế này không bị đặt và nằm trong danh sách cột hợp lệ
                                if (!in_array($checkSeat, $bookedSeats) && in_array($checkCol, $allColsInRow)) {
                                    $hasAvailableLeftSeat = true;
                                    break;
                                }
                            }
                            if ($hasAvailableLeftSeat) {
                                return "Không được bỏ trống ghế ngoài cùng bên trái! Vui lòng chọn các ghế từ đầu hàng.";
                            }
                        }
                        
                        // Kiểm tra nếu bỏ trống ghế ngoài cùng bên phải
                        if ($selectedMaxCol < $maxCol) {
                            // Có ghế bên phải chưa được chọn, kiểm tra xem có ghế nào có thể chọn được không (không bị đặt)
                            $hasAvailableRightSeat = false;
                            for ($checkCol = $selectedMaxCol + 1; $checkCol <= $maxCol; $checkCol++) {
                                $checkSeat = $row . $checkCol;
                                // Nếu ghế này không bị đặt và nằm trong danh sách cột hợp lệ
                                if (!in_array($checkSeat, $bookedSeats) && in_array($checkCol, $allColsInRow)) {
                                    $hasAvailableRightSeat = true;
                                    break;
                                }
                            }
                            if ($hasAvailableRightSeat) {
                                return "Không được bỏ trống ghế ngoài cùng bên phải! Vui lòng chọn các ghế đến cuối hàng.";
                            }
                        }
                    }
                }
            }
        }
        
        return null; // Valid
    }
    
    /**
     * Lấy danh sách tất cả các cột trong một hàng từ seat layout
     */
    private function getAllColumnsInRow($row, $seatLayout) {
        if (!$seatLayout) {
            return [];
        }
        
        $allCols = [];
        
        // Nếu có seat_groups (layout phức tạp)
        if (isset($seatLayout['seat_groups']) && is_array($seatLayout['seat_groups'])) {
            foreach ($seatLayout['seat_groups'] as $group) {
                $groupRows = $group['rows'] ?? [];
                $groupCols = $group['cols'] ?? [];
                
                if (in_array($row, $groupRows)) {
                    foreach ($groupCols as $col) {
                        if (!in_array($col, $allCols)) {
                            $allCols[] = $col;
                        }
                    }
                }
            }
        } elseif (isset($seatLayout['cols']) && is_array($seatLayout['cols'])) {
            // Layout tiêu chuẩn
            $allCols = $seatLayout['cols'];
        }
        
        sort($allCols);
        return $allCols;
    }
    
    /**
     * Kiểm tra xem các ghế được chọn có nằm trong nhóm chỉ có 3 cột không
     */
    private function isSeatsInThreeColumnGroup($row, $selectedCols, $seatLayout) {
        if (!$seatLayout || !isset($seatLayout['seat_groups']) || !is_array($seatLayout['seat_groups'])) {
            return false;
        }
        
        // Kiểm tra từng nhóm
        foreach ($seatLayout['seat_groups'] as $group) {
            $groupRows = $group['rows'] ?? [];
            $groupCols = $group['cols'] ?? [];
            
            // Nếu nhóm này có đúng 3 cột và hàng này nằm trong nhóm
            if (count($groupCols) == 3 && in_array($row, $groupRows)) {
                // Kiểm tra xem tất cả các ghế được chọn có nằm trong nhóm này không
                $allSeatsInGroup = true;
                foreach ($selectedCols as $col) {
                    if (!in_array($col, $groupCols)) {
                        $allSeatsInGroup = false;
                        break;
                    }
                }
                
                if ($allSeatsInGroup) {
                    return true;
                }
            }
        }
        
        return false;
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
        $customer_email = trim($_POST['customer_email'] ?? '');
        $food_items = $_POST['food_items'] ?? []; // Array of [food_item_id => quantity]
        
        // Validate showtime và seats
        if (!$showtime_id || empty($seats)) {
            $_SESSION['error'] = 'Vui lòng chọn ghế!';
            $redirectUrl = '?route=booking/index';
            if ($showtime_id) {
                $redirectUrl .= '&showtime_id=' . urlencode($showtime_id);
            }
            $this->redirect($redirectUrl);
            return;
        }
        
        // Kiểm tra IP spam
        require_once __DIR__ . '/../../core/IPSpamChecker.php';
        $ipCheck = IPSpamChecker::checkIPSpam(null, 'booking');
        if (!$ipCheck['allowed']) {
            $_SESSION['error'] = $ipCheck['message'];
            $redirectUrl = '?route=booking/index';
            if ($showtime_id) {
                $redirectUrl .= '&showtime_id=' . urlencode($showtime_id);
            }
            $this->redirect($redirectUrl);
            return;
        }
        
        // Kiểm tra thời gian thực và vi phạm
        $timeCheck = $this->checkBookingTimeAndViolations($user['id'], $showtime_id);
        if (!$timeCheck['allowed']) {
            $_SESSION['error'] = $timeCheck['message'];
            $redirectUrl = '?route=booking/index';
            if ($showtime_id) {
                $redirectUrl .= '&showtime_id=' . urlencode($showtime_id);
            }
            $this->redirect($redirectUrl);
            return;
        }
        
        // Validate: Giới hạn 8 vé/lần
        if (count($seats) > 8) {
            $_SESSION['error'] = 'Bạn chỉ có thể đặt tối đa 8 vé một lần!';
            $redirectUrl = '?route=booking/index&showtime_id=' . urlencode($showtime_id);
            $this->redirect($redirectUrl);
            return;
        }
        
        // Kiểm tra spam: Nếu chọn >8 ghế, log lại
        $seatCount = count($seats);
        $isSpamAttempt = ($seatCount > 8);
        
        // Log việc chọn ghế
        $this->logSeatSelection($user['id'], $showtime_id, $seatCount, $seats, $isSpamAttempt);
        
        // Log IP action nếu là spam
        if ($isSpamAttempt) {
            require_once __DIR__ . '/../../core/IPSpamChecker.php';
            IPSpamChecker::logIPAction(null, 'booking', true, "Chọn $seatCount ghế (vượt quá 8)", $user['id']);
        }
        
        // Kiểm tra số lần spam trong ngày
        if ($isSpamAttempt) {
            $spamCount = $this->getSpamCountToday($user['id']);
            if ($spamCount >= 3) {
                // Cấm tài khoản
                $this->banUser($user['id'], 'Spam chọn ghế quá 3 lần trong ngày');
                $_SESSION['error'] = 'Tài khoản của bạn đã bị khóa do vi phạm quy định đặt vé!';
                $this->redirect('auth/logout');
                return;
            } else {
                $_SESSION['error'] = 'Bạn chỉ có thể đặt tối đa 8 vé một lần! Lần vi phạm: ' . ($spamCount + 1) . '/3';
                $redirectUrl = '?route=booking/index&showtime_id=' . urlencode($showtime_id);
                $this->redirect($redirectUrl);
                return;
            }
        }
        
        // Validate: Không đặt cách 1 ghế và không bỏ trống ghế ở giữa
        $validationError = $this->validateSeatSelection($seats, $showtime_id);
        if ($validationError) {
            $_SESSION['error'] = $validationError;
            $redirectUrl = '?route=booking/index&showtime_id=' . urlencode($showtime_id);
            $this->redirect($redirectUrl);
            return;
        }
        
        // Nếu không có email từ form, dùng email của user
        if (empty($customer_email) && isset($user['email'])) {
            $customer_email = $user['email'];
        }
        
        // Validate email
        if (empty($customer_email) || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Vui lòng nhập email hợp lệ để nhận vé!';
            $redirectUrl = '?route=booking/index&showtime_id=' . urlencode($showtime_id);
            $this->redirect($redirectUrl);
            return;
        }
        
        $bookingModel = new BookingModel();
        $showtime = $bookingModel->getShowtimeWithScreen($showtime_id);
        
        if (!$showtime) {
            $this->redirect('booking');
            return;
        }
        
        // Lấy seat layout để tính giá
        $seatLayout = null;
        if (isset($showtime['screen_id']) && $showtime['screen_id']) {
            $seatLayout = $bookingModel->getScreenSeatLayout($showtime['screen_id']);
        }
        
        // Kiểm tra xem showtime đã qua chưa
        $today = date('Y-m-d');
        $currentTime = date('H:i:s');
        if ($showtime['show_date'] < $today || 
            ($showtime['show_date'] === $today && $showtime['show_time'] < $currentTime)) {
            $_SESSION['error'] = 'Suất chiếu này đã qua, không thể đặt vé!';
            $redirectUrl = '?route=booking/index&showtime_id=' . urlencode($showtime_id);
            $this->redirect($redirectUrl);
            return;
        }
        
        // Kiểm tra ghế đã được đặt chưa (double booking check)
        $existingTickets = $bookingModel->getBookedSeats($showtime_id);
        $bookedSeats = array_column($existingTickets, 'seat');
        $seatsToBook = array_diff($seats, $bookedSeats);
        
        if (empty($seatsToBook)) {
            $_SESSION['error'] = 'Tất cả ghế đã được đặt! Vui lòng chọn ghế khác!';
            $redirectUrl = '?route=booking/index&showtime_id=' . urlencode($showtime_id);
            $this->redirect($redirectUrl);
            return;
        }
        
        if (count($seatsToBook) < count($seats)) {
            $conflictingSeats = array_intersect($seats, $bookedSeats);
            $_SESSION['error'] = 'Một số ghế đã được đặt: ' . implode(', ', $conflictingSeats) . '. Vui lòng chọn ghế khác!';
            $redirectUrl = '?route=booking/index&showtime_id=' . urlencode($showtime_id);
            $this->redirect($redirectUrl);
            return;
        }
        
        // Xóa reservations trước khi tạo vé
        $bookingModel->releaseSeats($showtime_id, $seats, $user['id']);
        
        // Tạo vé cho mỗi ghế đã chọn
        $createdTickets = [];
        $db = Database::getInstance()->getConnection();
        
        try {
            // Bắt đầu transaction để đảm bảo tất cả vé được tạo hoặc không tạo gì cả
            $db->beginTransaction();
            
            // Lấy danh sách ghế đã đặt trước khi tạo vé (một lần duy nhất)
            $existingTickets = $bookingModel->getBookedSeats($showtime_id);
            $existingSeats = array_column($existingTickets, 'seat');
            
            foreach ($seats as $seat) {
                // Kiểm tra lại ghế đã được đặt chưa (double check trong transaction)
                if (in_array($seat, $existingSeats)) {
                    throw new Exception("Ghế $seat đã được đặt bởi người khác!");
                }
                
                // Xác định loại ghế và giá
                $seat_type = $bookingModel->getSeatType($seat, $seatLayout);
                $seat_price = $bookingModel->getSeatPrice($seat, $seatLayout, $showtime['price']);
                
                $qr_code = uniqid('TICKET_') . '_' . $user['id'] . '_' . $showtime_id . '_' . time() . '_' . $seat;
                
                $ticket_id = $bookingModel->createTicket([
                    'user_id' => $user['id'],
                    'showtime_id' => $showtime_id,
                    'seat' => $seat,
                    'seat_type' => $seat_type,
                    'price' => $seat_price,
                    'qr_code' => $qr_code
                ]);
                
                if (!$ticket_id) {
                    throw new Exception("Không thể tạo vé cho ghế $seat!");
                }
                
                // Thêm food items cho vé này (nếu có)
                if (!empty($food_items)) {
                    foreach ($food_items as $food_item_id => $quantity) {
                        if ($quantity > 0) {
                            $foodItem = $bookingModel->getFoodItemById($food_item_id);
                            if ($foodItem) {
                                $bookingModel->createBookingFoodItem(
                                    $ticket_id,
                                    $food_item_id,
                                    $quantity,
                                    $foodItem['price']
                                );
                            }
                        }
                    }
                }
                
                $createdTickets[] = [
                    'id' => $ticket_id,
                    'seat' => $seat,
                    'seat_type' => $seat_type,
                    'qr_code' => $qr_code,
                    'price' => $seat_price
                ];
                
                // Thêm ghế vừa tạo vào danh sách để tránh duplicate trong cùng một transaction
                $existingSeats[] = $seat;
            }
            
            // Commit transaction nếu tất cả vé được tạo thành công
            $db->commit();
            
            // Log thành công
            error_log("Successfully created " . count($createdTickets) . " tickets for user " . $user['id'] . " on showtime " . $showtime_id);
            
            // Log IP action thành công (không phải spam)
            require_once __DIR__ . '/../../core/IPSpamChecker.php';
            IPSpamChecker::logIPAction(null, 'booking', false, "Đặt vé thành công: " . count($createdTickets) . " vé", $user['id']);
            
        } catch (Exception $e) {
            // Rollback nếu có lỗi
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Error creating tickets: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['error'] = 'Có lỗi xảy ra khi đặt vé: ' . $e->getMessage();
            $redirectUrl = '?route=booking/index&showtime_id=' . urlencode($showtime_id);
            $this->redirect($redirectUrl);
            return;
        }
        
        // Gửi email với QR code và thông tin vé
        try {
            $this->sendTicketEmail($customer_email, $showtime, $createdTickets, $user);
        } catch (Exception $e) {
            error_log("Error sending ticket email: " . $e->getMessage());
            // Không cần dừng quá trình nếu gửi email lỗi, vé đã được tạo thành công
        }
        
        $_SESSION['success'] = 'Đặt vé thành công! Vé và QR code đã được gửi đến email ' . htmlspecialchars($customer_email);
        
        // Đảm bảo dữ liệu đã được commit vào database trước khi redirect
        // Thêm một query để verify vé đã được tạo
        $verifyTickets = $bookingModel->getBookedSeats($showtime_id);
        $verifySeats = array_column($verifyTickets, 'seat');
        error_log("After booking - Verified booked seats for showtime $showtime_id: " . implode(', ', $verifySeats));
        
        // Redirect về trang booking để xem ghế đã bán, giữ nguyên showtime_id
        // Thêm timestamp để force refresh và tránh cache
        $movie = isset($showtime['movie_id']) ? $showtime['movie_id'] : null;
        $theater = isset($showtime['theater_id']) ? $showtime['theater_id'] : null;
        $date = isset($showtime['show_date']) ? $showtime['show_date'] : date('Y-m-d');
        
        $redirectUrl = '?route=booking/index';
        if ($movie) $redirectUrl .= '&movie=' . urlencode($movie);
        if ($theater) $redirectUrl .= '&theater=' . urlencode($theater);
        if ($date) $redirectUrl .= '&date=' . urlencode($date);
        if ($showtime_id) $redirectUrl .= '&showtime_id=' . urlencode($showtime_id);
        $redirectUrl .= '&_t=' . time(); // Cache busting
        
        $this->redirect($redirectUrl);
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
    
    /**
     * Gửi email với QR code và thông tin vé
     */
    private function sendTicketEmail($email, $showtime, $tickets, $user) {
        require_once __DIR__ . '/../../core/Email.php';
        
        $emailService = new Email();
        
        $subject = 'Vé xem phim của bạn - ' . htmlspecialchars($showtime['movie_title']);
        
        // Tạo QR code URL (sử dụng API online để tạo QR code)
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=';
        
        // Tạo HTML email
        $seatsList = array_column($tickets, 'seat');
        $totalPrice = array_sum(array_column($tickets, 'price'));
        
        $qrCodesHtml = '';
        foreach ($tickets as $ticket) {
            $qrData = urlencode($ticket['qr_code']);
            $qrCodeImage = $qrCodeUrl . $qrData;
            $qrCodesHtml .= '
                <div style="margin: 20px 0; text-align: center; padding: 20px; background: #f8f9fa; border-radius: 10px;">
                    <h3 style="color: #e50914; margin-bottom: 10px;">Ghế: ' . htmlspecialchars($ticket['seat']) . '</h3>
                    <img src="' . $qrCodeImage . '" alt="QR Code" style="max-width: 200px; border: 3px solid #e50914; padding: 10px; background: white; border-radius: 10px;">
                    <p style="margin-top: 10px; font-family: monospace; font-size: 12px; color: #666;">Mã vé: ' . htmlspecialchars($ticket['qr_code']) . '</p>
                </div>';
        }
        
        $emailBody = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Vé xem phim của bạn</title>
        </head>
        <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
                <tr>
                    <td align="center">
                        <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            <!-- Header -->
                            <tr>
                                <td style="background: linear-gradient(135deg, #e50914 0%, #b20710 100%); padding: 30px; text-align: center;">
                                    <h1 style="color: #ffffff; margin: 0; font-size: 28px;">
                                        <i class="fas fa-ticket-alt" style="margin-right: 10px;"></i>
                                        CineHub - Vé xem phim
                                    </h1>
                                </td>
                            </tr>
                            
                            <!-- Content -->
                            <tr>
                                <td style="padding: 30px;">
                                    <h2 style="color: #333333; margin-top: 0;">Xin chào ' . htmlspecialchars($user['username'] ?? 'Khách hàng') . '!</h2>
                                    <p style="color: #666666; font-size: 16px; line-height: 1.6;">
                                        Cảm ơn bạn đã đặt vé tại CineHub. Vé xem phim của bạn đã được xác nhận thành công!
                                    </p>
                                    
                                    <!-- Thông tin vé -->
                                    <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #e50914;">
                                        <h3 style="color: #e50914; margin-top: 0; font-size: 22px;">' . htmlspecialchars($showtime['movie_title']) . '</h3>
                                        <table width="100%" cellpadding="5">
                                            <tr>
                                                <td style="color: #666666; width: 150px;"><strong>Rạp chiếu:</strong></td>
                                                <td style="color: #333333;">' . htmlspecialchars($showtime['theater_name']) . ' - ' . htmlspecialchars($showtime['location']) . '</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #666666;"><strong>Ngày chiếu:</strong></td>
                                                <td style="color: #333333;">' . date('d/m/Y', strtotime($showtime['show_date'])) . '</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #666666;"><strong>Giờ chiếu:</strong></td>
                                                <td style="color: #333333;">' . date('H:i', strtotime($showtime['show_time'])) . '</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #666666;"><strong>Ghế đã đặt:</strong></td>
                                                <td style="color: #333333; font-weight: bold; font-size: 18px;">' . implode(', ', $seatsList) . '</td>
                                            </tr>
                                            <tr>
                                                <td style="color: #666666;"><strong>Tổng tiền:</strong></td>
                                                <td style="color: #e50914; font-weight: bold; font-size: 20px;">' . number_format($totalPrice, 0, ',', '.') . ' đ</td>
                                            </tr>
                                        </table>
                                    </div>
                                    
                                    <!-- QR Codes -->
                                    <div style="margin: 30px 0;">
                                        <h3 style="color: #333333; text-align: center; margin-bottom: 20px;">QR Code vé của bạn</h3>
                                        <p style="text-align: center; color: #666666; margin-bottom: 20px;">
                                            Vui lòng xuất trình QR code này tại rạp chiếu để vào xem phim.
                                        </p>
                                        ' . $qrCodesHtml . '
                                    </div>
                                    
                                    <!-- Lưu ý -->
                                    <div style="background-color: #fff3cd; padding: 15px; border-radius: 8px; border-left: 4px solid #ffc107; margin: 20px 0;">
                                        <p style="margin: 0; color: #856404;">
                                            <strong>Lưu ý:</strong><br>
                                            • Vui lòng đến rạp trước 15 phút để làm thủ tục vào rạp.<br>
                                            • QR code chỉ có hiệu lực cho suất chiếu đã đặt.<br>
                                            • Mang theo giấy tờ tùy thân khi đến rạp (nếu cần).<br>
                                            • Vé không được hoàn lại sau khi đặt.
                                        </p>
                                    </div>
                                    
                                    <p style="color: #666666; font-size: 14px; margin-top: 30px; text-align: center;">
                                        Trân trọng,<br>
                                        <strong style="color: #e50914;">Đội ngũ CineHub</strong>
                                    </p>
                                </td>
                            </tr>
                            
                            <!-- Footer -->
                            <tr>
                                <td style="background-color: #141414; padding: 20px; text-align: center;">
                                    <p style="color: #b3b3b3; font-size: 12px; margin: 5px 0;">
                                        © ' . date('Y') . ' CineHub. Tất cả quyền được bảo lưu.
                                    </p>
                                    <p style="color: #b3b3b3; font-size: 12px; margin: 5px 0;">
                                        Nếu có thắc mắc, vui lòng liên hệ hỗ trợ khách hàng.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>';
        
        // Gửi email
        $emailService->send($email, $subject, $emailBody, true);
    }
    
    // API endpoints for real-time seat reservations
    public function reserveSeatsApi() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $this->requireLogin();
        $user = $this->getCurrentUser();
        
        $input = json_decode(file_get_contents('php://input'), true);
        $showtime_id = $input['showtime_id'] ?? null;
        $seats = $input['seats'] ?? [];
        
        if (!$showtime_id || empty($seats)) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        
        $bookingModel = new BookingModel();
        $session_id = session_id();
        
        // Reserve seats (10 minutes)
        $reserved = $bookingModel->reserveSeats($showtime_id, $seats, $user['id'], $session_id, 10);
        
        echo json_encode([
            'success' => true,
            'reserved_seats' => $reserved
        ]);
    }
    
    public function getSeatStatusApi() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $showtime_id = $_GET['showtime_id'] ?? null;
        
        if (!$showtime_id) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        
        $bookingModel = new BookingModel();
        
        // Lấy ghế đã đặt
        $bookedSeatsData = $bookingModel->getBookedSeats($showtime_id);
        $bookedSeats = array_column($bookedSeatsData, 'seat');
        
        // Lấy ghế đang được reserve
        $reservedSeatsData = $bookingModel->getReservedSeats($showtime_id);
        $reservedSeats = [];
        
        foreach ($reservedSeatsData as $item) {
            $reservedSeats[$item['seat']] = [
                'seat' => $item['seat'],
                'user_id' => $item['user_id'],
                'expires_at' => $item['expires_at']
            ];
        }
        
        echo json_encode([
            'success' => true,
            'booked_seats' => $bookedSeats,
            'reserved_seats' => $reservedSeats
        ]);
    }
    
    public function releaseSeatsApi() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $this->requireLogin();
        $user = $this->getCurrentUser();
        
        $input = json_decode(file_get_contents('php://input'), true);
        $showtime_id = $input['showtime_id'] ?? null;
        $seats = $input['seats'] ?? [];
        
        if (!$showtime_id || empty($seats)) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        
        $bookingModel = new BookingModel();
        $bookingModel->releaseSeats($showtime_id, $seats, $user['id']);
        
        echo json_encode(['success' => true]);
    }
    
    public function extendReservationApi() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $this->requireLogin();
        $user = $this->getCurrentUser();
        
        $input = json_decode(file_get_contents('php://input'), true);
        $showtime_id = $input['showtime_id'] ?? null;
        $seats = $input['seats'] ?? [];
        
        if (!$showtime_id || empty($seats)) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        
        $bookingModel = new BookingModel();
        
        foreach ($seats as $seat) {
            $bookingModel->extendReservation($showtime_id, $seat, $user['id'], 10);
        }
        
        echo json_encode(['success' => true]);
    }
    
    /**
     * Log việc chọn ghế để phát hiện spam
     */
    private function logSeatSelection($user_id, $showtime_id, $seat_count, $seats, $is_spam = false) {
        $db = Database::getInstance();
        require_once __DIR__ . '/../../core/TokenHelper.php';
        $ipAddress = TokenHelper::getClientIp();
        
        // Kiểm tra xem cột ip_address có tồn tại không
        try {
            $db->execute("
                INSERT INTO seat_selection_logs (user_id, ip_address, showtime_id, seat_count, seats, is_spam, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ", [
                $user_id,
                $ipAddress,
                $showtime_id,
                $seat_count,
                json_encode($seats),
                $is_spam ? 1 : 0
            ]);
        } catch (Exception $e) {
            // Nếu cột ip_address chưa tồn tại, insert không có IP
            $db->execute("
                INSERT INTO seat_selection_logs (user_id, showtime_id, seat_count, seats, is_spam, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ", [
                $user_id,
                $showtime_id,
                $seat_count,
                json_encode($seats),
                $is_spam ? 1 : 0
            ]);
        }
    }
    
    /**
     * Đếm số lần spam trong ngày
     */
    private function getSpamCountToday($user_id) {
        $db = Database::getInstance();
        $result = $db->fetch("
            SELECT COUNT(*) as count
            FROM seat_selection_logs
            WHERE user_id = ?
            AND is_spam = 1
            AND DATE(created_at) = CURDATE()
        ", [$user_id]);
        return $result['count'] ?? 0;
    }
    
    /**
     * Cấm tài khoản người dùng
     */
    private function banUser($user_id, $reason = '') {
        $db = Database::getInstance();
        $db->execute("
            UPDATE users
            SET is_active = 0, status = 'banned'
            WHERE id = ?
        ", [$user_id]);
        
        // Log vào bảng logs nếu có
        try {
            $db->execute("
                INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address, created_at)
                VALUES (?, 'ban_user', 'users', ?, ?, ?, NOW())
            ", [
                $_SESSION['user_id'] ?? null,
                $user_id,
                'User banned: ' . $reason,
                $_SERVER['REMOTE_ADDR'] ?? null
            ]);
        } catch (Exception $e) {
            // Ignore nếu bảng logs không tồn tại
        }
    }
    
    /**
     * Bắt đầu tracking session khi người dùng vào phòng đặt vé
     */
    private function startBookingSession($user_id, $showtime_id) {
        $db = Database::getInstance();
        $bookingModel = new BookingModel();
        
        // Lấy screen_id từ showtime
        $showtime = $bookingModel->getShowtimeById($showtime_id);
        if (!$showtime || !isset($showtime['screen_id'])) {
            return;
        }
        
        $screen_id = $showtime['screen_id'];
        
        // Kiểm tra xem đã có session đang mở chưa
        $existingSession = $db->fetch("
            SELECT id FROM booking_session_tracking
            WHERE user_id = ? AND showtime_id = ? AND screen_id = ? AND session_end IS NULL
            ORDER BY session_start DESC
            LIMIT 1
        ", [$user_id, $showtime_id, $screen_id]);
        
        if (!$existingSession) {
            // Tạo session mới
            $db->execute("
                INSERT INTO booking_session_tracking (user_id, showtime_id, screen_id, session_start, created_at)
                VALUES (?, ?, ?, NOW(), NOW())
            ", [$user_id, $showtime_id, $screen_id]);
        }
    }
    
    /**
     * Kiểm tra thời gian thực và vi phạm
     * Trả về ['allowed' => bool, 'message' => string]
     */
    private function checkBookingTimeAndViolations($user_id, $showtime_id) {
        $db = Database::getInstance();
        $bookingModel = new BookingModel();
        
        // Lấy screen_id từ showtime
        $showtime = $bookingModel->getShowtimeById($showtime_id);
        if (!$showtime || !isset($showtime['screen_id'])) {
            return ['allowed' => true, 'message' => ''];
        }
        
        $screen_id = $showtime['screen_id'];
        
        // Lấy session hiện tại
        $session = $db->fetch("
            SELECT id, session_start, violation_count, is_banned
            FROM booking_session_tracking
            WHERE user_id = ? AND showtime_id = ? AND screen_id = ? AND session_end IS NULL
            ORDER BY session_start DESC
            LIMIT 1
        ", [$user_id, $showtime_id, $screen_id]);
        
        if (!$session) {
            return ['allowed' => true, 'message' => ''];
        }
        
        // Tính thời gian thực (giây)
        $sessionStart = strtotime($session['session_start']);
        $currentTime = time();
        $durationSeconds = $currentTime - $sessionStart;
        
        // Giới hạn 15 phút = 900 giây
        $maxDuration = 15 * 60;
        
        if ($durationSeconds > $maxDuration) {
            // Đã vượt quá 15 phút
            $this->endBookingSession($session['id'], $durationSeconds);
            return [
                'allowed' => false,
                'message' => 'Thời gian đặt vé đã hết! Bạn chỉ có 15 phút để đặt vé cho mỗi suất chiếu.'
            ];
        }
        
        // Kiểm tra vi phạm: quá 10 phút lần 1, quá 5 phút lần 2
        $violationCount = $session['violation_count'] ?? 0;
        
        if ($violationCount == 0 && $durationSeconds > 10 * 60) {
            // Vi phạm lần 1: quá 10 phút
            $db->execute("
                UPDATE booking_session_tracking
                SET violation_count = 1
                WHERE id = ?
            ", [$session['id']]);
            
            return [
                'allowed' => true,
                'message' => 'Cảnh báo: Bạn đã ở quá 10 phút. Lần vi phạm thứ nhất.'
            ];
        } elseif ($violationCount == 1 && $durationSeconds > 5 * 60) {
            // Vi phạm lần 2: quá 5 phút
            $db->execute("
                UPDATE booking_session_tracking
                SET violation_count = 2, is_banned = 1
                WHERE id = ?
            ", [$session['id']]);
            
            $this->endBookingSession($session['id'], $durationSeconds);
            
            return [
                'allowed' => false,
                'message' => 'Bạn đã bị cấm đặt vé phòng này do vi phạm quy định thời gian đặt vé (quá 5 phút lần thứ 2)!'
            ];
        }
        
        return ['allowed' => true, 'message' => ''];
    }
    
    /**
     * Kết thúc session tracking
     */
    private function endBookingSession($session_id, $duration_seconds) {
        $db = Database::getInstance();
        $db->execute("
            UPDATE booking_session_tracking
            SET session_end = NOW(), total_duration_seconds = ?
            WHERE id = ?
        ", [$duration_seconds, $session_id]);
    }
    
    /**
     * Kiểm tra xem người dùng có bị cấm đặt vé phòng này không
     */
    private function isUserBannedFromScreen($user_id, $showtime_id) {
        $db = Database::getInstance();
        $bookingModel = new BookingModel();
        
        // Lấy screen_id từ showtime
        $showtime = $bookingModel->getShowtimeById($showtime_id);
        if (!$showtime || !isset($showtime['screen_id'])) {
            return false;
        }
        
        $screen_id = $showtime['screen_id'];
        
        // Kiểm tra xem có bị cấm không
        $banned = $db->fetch("
            SELECT COUNT(*) as count
            FROM booking_session_tracking
            WHERE user_id = ? AND screen_id = ? AND is_banned = 1
            LIMIT 1
        ", [$user_id, $screen_id]);
        
        return ($banned['count'] ?? 0) > 0;
    }
}
?>

