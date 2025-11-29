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
                $banCheck = $this->isUserBannedFromScreen($user['id'], $selected_showtime_id);
                if ($banCheck['banned']) {
                    $_SESSION['error'] = $banCheck['message'];
                    $this->redirect('booking');
                    return;
                }
                
                // Kiểm tra thời gian thực và vi phạm trước khi bắt đầu session
                $timeCheck = $this->checkBookingTimeAndViolations($user['id'], $selected_showtime_id);
                if (!$timeCheck['allowed']) {
                    $_SESSION['error'] = $timeCheck['message'];
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
                
                // Lấy seat layout nếu có showtime
                $seatLayout = null;
                if ($selected_showtime_id) {
                    try {
                        $showtime = $bookingModel->getShowtimeById($selected_showtime_id);
                        if ($showtime && isset($showtime['screen_id']) && $showtime['screen_id']) {
                            $seatLayout = $bookingModel->getScreenSeatLayout($showtime['screen_id']);
                        }
                    } catch (Exception $e3) {
                        error_log("Error getting seat layout: " . $e3->getMessage());
                    }
                }
                
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
     * Kiểm tra validation khi đặt 1 vé
     */
    private function validateSingleSeat($row, $selectedCol, $groupCols, $minColInGroup, $maxColInGroup, $bookedSeats) {
        // Quy tắc 1: Không được chọn ghế ngay sát ghế ngoài cùng (ghế thứ 2 từ đầu hoặc từ cuối)
        // NHƯNG: Nếu ghế ngoài cùng đã được đặt rồi, thì cho phép đặt ghế ngay sát nó
        
        // Kiểm tra ghế ngoài cùng bên trái đã được đặt chưa
        $leftmostSeat = $row . $minColInGroup;
        $isLeftmostBooked = in_array($leftmostSeat, $bookedSeats);
        
        // Kiểm tra ghế ngoài cùng bên phải đã được đặt chưa
        $rightmostSeat = $row . $maxColInGroup;
        $isRightmostBooked = in_array($rightmostSeat, $bookedSeats);
        
        // Chặn ghế thứ 2 từ đầu (bên trái) - chỉ chặn nếu ghế ngoài cùng bên trái chưa được đặt
        if ($selectedCol == $minColInGroup + 1 && !$isLeftmostBooked) {
            error_log("Row $row: Validation FAILED - Không được chọn ghế ngay sát ghế ngoài cùng bên trái (ghế $selectedCol, minCol=$minColInGroup) vì ghế ngoài cùng chưa được đặt");
            return "Không được chọn ghế ngay sát ghế ngoài cùng bên trái! Vui lòng chọn ghế ngoài cùng hoặc ghế khác.";
        }
        
        // Chặn ghế thứ 2 từ cuối (bên phải) - chỉ chặn nếu ghế ngoài cùng bên phải chưa được đặt
        if ($selectedCol == $maxColInGroup - 1 && !$isRightmostBooked) {
            error_log("Row $row: Validation FAILED - Không được chọn ghế ngay sát ghế ngoài cùng bên phải (ghế $selectedCol, maxCol=$maxColInGroup) vì ghế ngoài cùng chưa được đặt");
            return "Không được chọn ghế ngay sát ghế ngoài cùng bên phải! Vui lòng chọn ghế ngoài cùng hoặc ghế khác.";
        }
        
        // Quy tắc 2: Nếu giữa 2 ghế đã đặt có >= 3 ghế trống, không được đặt ghế ở giữa (cách cả 2 ghế đã đặt ít nhất 1 ghế)
        // Tìm ghế đã đặt gần nhất bên trái
        $nearestBookedLeft = null;
        for ($checkCol = $selectedCol - 1; $checkCol >= $minColInGroup; $checkCol--) {
            if (!in_array($checkCol, $groupCols)) continue;
            $checkSeat = $row . $checkCol;
            if (in_array($checkSeat, $bookedSeats)) {
                $nearestBookedLeft = $checkCol;
                break;
            }
        }
        
        // Tìm ghế đã đặt gần nhất bên phải
        $nearestBookedRight = null;
        for ($checkCol = $selectedCol + 1; $checkCol <= $maxColInGroup; $checkCol++) {
            if (!in_array($checkCol, $groupCols)) continue;
            $checkSeat = $row . $checkCol;
            if (in_array($checkSeat, $bookedSeats)) {
                $nearestBookedRight = $checkCol;
                break;
            }
        }
        
        // Nếu có cả 2 ghế đã đặt ở 2 bên
        if ($nearestBookedLeft !== null && $nearestBookedRight !== null) {
            // Tính khoảng cách giữa 2 ghế đã đặt (số ghế trống)
            $gapBetweenBooked = $nearestBookedRight - $nearestBookedLeft - 1;
            
            // Nếu khoảng cách >= 3 ghế trống
            if ($gapBetweenBooked >= 3) {
                // Kiểm tra xem ghế được chọn có cách cả 2 ghế đã đặt ít nhất 1 ghế không
                $distanceFromLeft = $selectedCol - $nearestBookedLeft;
                $distanceFromRight = $nearestBookedRight - $selectedCol;
                
                // Nếu ghế được chọn cách cả 2 ghế đã đặt ít nhất 1 ghế (không phải ghế ngay sát)
                if ($distanceFromLeft > 1 && $distanceFromRight > 1) {
                    error_log("Row $row: Validation FAILED - Đặt 1 vé (ghế $selectedCol) giữa 2 ghế đã đặt (ghế $nearestBookedLeft và $nearestBookedRight) có $gapBetweenBooked ghế trống, cách cả 2 ghế đã đặt ít nhất 1 ghế");
                    return "Không được đặt ghế ở giữa khi giữa 2 ghế đã đặt có 3 ghế trống trở lên! Vui lòng chọn ghế ngay sát một trong hai ghế đã đặt hoặc chọn ghế khác.";
                }
            }
        }
        
        return null; // OK
    }
    
    /**
     * Validate seat selection rules:
     * 1. Nếu đặt 1 ghế: có thể đặt ở đâu cũng được
     * 2. Nếu đặt từ 2 ghế trở lên:
     *    - Không đặt cách 1 ghế (phải liền kề)
     *    - Không bỏ trống ghế ở giữa các ghế đã chọn
     *    - Bắt buộc phải chọn ít nhất 1 trong 2 ghế ngoài cùng của nhóm
     *    Ví dụ: Hàng C có ghế 1,2,3,4
     *    - Chọn ghế 1,2 → OK (có ghế 1 - ghế ngoài cùng)
     *    - Chọn ghế 3,4 → OK (có ghế 4 - ghế ngoài cùng)
     *    - Chọn ghế 2,3 → Không OK (không có ghế 1 hoặc 4)
     * 3. Nếu đặt từ 3 ghế trở lên:
     *    - Phải để lại ít nhất 2 ghế kể từ ghế ngoài cùng (đầu trái HOẶC đầu phải)
     *    Ví dụ hợp lệ: Chọn ghế 3-5, để lại 1,2 (2 ghế) → OK
     *                  Chọn ghế 9-11, để lại 12,13 (2 ghế) → OK
     *    Ví dụ không hợp lệ: Chọn ghế 3-5, để lại 1 (1 ghế) và 6 (1 ghế) → Không OK
     */
    private function validateSeatSelection($seats, $showtime_id = null) {
        if (empty($seats)) {
            return null; // Không có ghế thì không cần validate
        }
        
        $seatCount = count($seats);
        // Áp dụng validation cho cả trường hợp đặt 1 ghế
        
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
            
            // Kiểm tra không bỏ trống ghế ở giữa - KHÔNG cho phép gap giữa các ghế đã chọn (chỉ khi có >= 2 ghế)
            if (count($cols) > 1) {
                for ($i = 0; $i < count($cols) - 1; $i++) {
                    $gap = $cols[$i + 1] - $cols[$i];
                    if ($gap > 1) {
                        // Luôn báo lỗi nếu có gap giữa các ghế đã chọn
                        return "Không được bỏ trống ghế ở giữa! Các ghế phải liền kề nhau. Vui lòng chọn các ghế liền kề.";
                    }
                }
            }
            
            // Lấy danh sách các nhóm ghế trong hàng này
            $seatGroupsInRow = $this->getSeatGroupsInRow($row, $seatLayout);
            
            if (empty($seatGroupsInRow)) {
                // Nếu không có seat groups, sử dụng toàn bộ hàng như một nhóm
                $allColsInRow = $this->getAllColumnsInRow($row, $seatLayout);
                if (!empty($allColsInRow)) {
                    $seatGroupsInRow = [['cols' => $allColsInRow]];
                }
            }
            
            // Kiểm tra từng nhóm ghế trong hàng
            foreach ($seatGroupsInRow as $group) {
                $groupCols = $group['cols'] ?? [];
                if (empty($groupCols)) continue;
                
                sort($groupCols);
                
                // Lọc các ghế được chọn thuộc nhóm này
                $selectedColsInGroup = array_intersect($cols, $groupCols);
                if (empty($selectedColsInGroup)) continue; // Không có ghế nào được chọn trong nhóm này
                
                $selectedColsInGroup = array_values($selectedColsInGroup);
                sort($selectedColsInGroup);
                $selectedSeatCountInGroup = count($selectedColsInGroup);
                
                // Áp dụng validation cho cả trường hợp đặt 1 ghế
                
                $minColInGroup = min($groupCols);
                $maxColInGroup = max($groupCols);
                $selectedMinCol = min($selectedColsInGroup);
                $selectedMaxCol = max($selectedColsInGroup);
                
                error_log("Row $row, Group [" . implode(',', $groupCols) . "]: selectedCols=" . implode(',', $selectedColsInGroup) . ", selectedSeatCount=$selectedSeatCountInGroup");
                
                // Đếm tổng số ghế AVAILABLE trong nhóm (chưa bị đặt) - cần đếm trước để áp dụng quy tắc
                $totalAvailableInGroup = 0;
                foreach ($groupCols as $col) {
                    $checkSeat = $row . $col;
                    if (!in_array($checkSeat, $bookedSeats)) {
                        $totalAvailableInGroup++;
                    }
                }
                
                // Kiểm tra xem có chọn ít nhất 1 trong 2 ghế ngoài cùng của nhóm không
                $hasFirstSeat = in_array($minColInGroup, $selectedColsInGroup);
                $hasLastSeat = in_array($maxColInGroup, $selectedColsInGroup);
                
                error_log("Row $row, Group: minCol=$minColInGroup, maxCol=$maxColInGroup, hasFirstSeat=$hasFirstSeat, hasLastSeat=$hasLastSeat, totalAvailableInGroup=$totalAvailableInGroup");
                
                // Tìm ghế đã đặt gần nhất bên trái của selectedMinCol (hoặc ghế ngoài cùng nếu không có)
                $nearestBookedSeatLeft = null;
                for ($checkCol = $selectedMinCol - 1; $checkCol >= $minColInGroup; $checkCol--) {
                    if (!in_array($checkCol, $groupCols)) continue;
                    $checkSeat = $row . $checkCol;
                    if (in_array($checkSeat, $bookedSeats)) {
                        $nearestBookedSeatLeft = $checkCol;
                        break;
                    }
                }
                $startPoint = ($nearestBookedSeatLeft !== null) ? $nearestBookedSeatLeft : $minColInGroup;
                
                // Đếm số ghế AVAILABLE từ điểm đầu (ghế đã đặt gần nhất hoặc ghế ngoài cùng) đến ghế được chọn đầu tiên
                // Lưu ý: Trong khoảng này phải không có ghế nào đã đặt
                $availableSeatsAtStart = 0;
                // Nếu startPoint là ghế đã đặt, bắt đầu đếm từ ghế tiếp theo
                $countStart = ($nearestBookedSeatLeft !== null) ? $nearestBookedSeatLeft + 1 : $minColInGroup;
                error_log("Row $row, Group: Đếm availableSeatsAtStart từ $countStart đến " . ($selectedMinCol - 1) . " (selectedMinCol=$selectedMinCol)");
                for ($checkCol = $countStart; $checkCol < $selectedMinCol; $checkCol++) {
                    if (!in_array($checkCol, $groupCols)) continue;
                    $checkSeat = $row . $checkCol;
                    // Nếu gặp ghế đã đặt trong khoảng này, dừng đếm
                    if (in_array($checkSeat, $bookedSeats)) {
                        error_log("Row $row, Group: Gặp ghế đã đặt $checkSeat, dừng đếm availableSeatsAtStart");
                        break;
                    }
                    // Chỉ đếm nếu ghế này available
                    $availableSeatsAtStart++;
                    error_log("Row $row, Group: Found available seat at start: $checkSeat (từ điểm đầu $startPoint đến ghế được chọn $selectedMinCol), availableSeatsAtStart=$availableSeatsAtStart");
                }
                
                // Tìm ghế đã đặt gần nhất bên phải của selectedMaxCol (hoặc ghế ngoài cùng nếu không có)
                $nearestBookedSeatRight = null;
                for ($checkCol = $selectedMaxCol + 1; $checkCol <= $maxColInGroup; $checkCol++) {
                    if (!in_array($checkCol, $groupCols)) continue;
                    $checkSeat = $row . $checkCol;
                    if (in_array($checkSeat, $bookedSeats)) {
                        $nearestBookedSeatRight = $checkCol;
                        break;
                    }
                }
                $endPoint = ($nearestBookedSeatRight !== null) ? $nearestBookedSeatRight : $maxColInGroup;
                
                // Đếm số ghế AVAILABLE từ ghế được chọn cuối cùng đến điểm cuối (ghế đã đặt gần nhất hoặc ghế ngoài cùng)
                // Lưu ý: Trong khoảng này phải không có ghế nào đã đặt
                $availableSeatsAtEnd = 0;
                // Nếu endPoint là ghế đã đặt, kết thúc đếm trước ghế đó
                $countEnd = ($nearestBookedSeatRight !== null) ? $nearestBookedSeatRight - 1 : $maxColInGroup;
                for ($checkCol = $selectedMaxCol + 1; $checkCol <= $countEnd; $checkCol++) {
                    if (!in_array($checkCol, $groupCols)) continue;
                    $checkSeat = $row . $checkCol;
                    // Nếu gặp ghế đã đặt trong khoảng này, dừng đếm
                    if (in_array($checkSeat, $bookedSeats)) {
                        break;
                    }
                    // Chỉ đếm nếu ghế này available
                    $availableSeatsAtEnd++;
                    error_log("Row $row, Group: Found available seat at end: $checkSeat (từ ghế được chọn $selectedMaxCol đến điểm cuối $endPoint)");
                }
                
                // Debug log
                error_log("Row $row, Group: totalColsInGroup=" . count($groupCols) . ", totalAvailableInGroup=$totalAvailableInGroup, selectedSeatCount=$selectedSeatCountInGroup");
                error_log("Row $row, Group: nearestBookedSeatLeft=" . ($nearestBookedSeatLeft !== null ? $nearestBookedSeatLeft : 'null') . ", nearestBookedSeatRight=" . ($nearestBookedSeatRight !== null ? $nearestBookedSeatRight : 'null'));
                error_log("Row $row, Group: availableSeatsAtStart=$availableSeatsAtStart, availableSeatsAtEnd=$availableSeatsAtEnd");
                
                // QUY TẮC: Công thức tổng quát cho nhóm có X ghế available
                // - Nếu một trong hai điểm bắt đầu có ghế đã đặt, thì có thể đặt ngay sau ghế đó (bỏ qua kiểm tra)
                // - Nếu đặt số ghế >= X/2 và không có ghế đã đặt ở hai đầu: Bắt buộc phải đặt từ đầu hàng
                // - Nếu đặt số ghế < X/2 và không đặt từ đầu hàng: Phải để lại >= 2 ghế ở đầu trái HOẶC đầu phải
                
                $halfOfAvailable = floor($totalAvailableInGroup / 2);
                
                // Kiểm tra nếu đặt từ đầu hàng (chọn ít nhất 1 trong 2 ghế ngoài cùng) - OK
                if ($hasFirstSeat || $hasLastSeat) {
                    error_log("Row $row, Group: Validation OK - Đặt từ đầu hàng (hasFirstSeat=$hasFirstSeat, hasLastSeat=$hasLastSeat)");
                    continue; // Bỏ qua validation cho nhóm này
                }
                
                // Kiểm tra riêng cho trường hợp đặt 1 vé
                if ($selectedSeatCountInGroup == 1) {
                    $singleSeatError = $this->validateSingleSeat($row, $selectedMinCol, $groupCols, $minColInGroup, $maxColInGroup, $bookedSeats);
                    if ($singleSeatError) {
                        return $singleSeatError;
                    }
                    // Nếu pass validation cho 1 vé, tiếp tục kiểm tra các quy tắc khác
                }
                
                // Không đặt từ đầu hàng, kiểm tra các trường hợp khác (áp dụng cho cả 1 ghế)
                // Nếu có ghế đã đặt ở một trong hai đầu, chỉ cho phép đặt NGAY SAU ghế đó (không có ghế ở giữa)
                $isAdjacentToBookedLeft = ($nearestBookedSeatLeft !== null && $selectedMinCol == $nearestBookedSeatLeft + 1);
                $isAdjacentToBookedRight = ($nearestBookedSeatRight !== null && $selectedMaxCol == $nearestBookedSeatRight - 1);
                
                if ($isAdjacentToBookedLeft || $isAdjacentToBookedRight) {
                    error_log("Row $row, Group: Validation OK - Đặt ngay sau ghế đã đặt (trái: " . ($isAdjacentToBookedLeft ? "ghế $nearestBookedSeatLeft" : 'no') . ", phải: " . ($isAdjacentToBookedRight ? "ghế $nearestBookedSeatRight" : 'no') . ")");
                    continue; // Bỏ qua validation cho nhóm này
                }
                
                // Kiểm tra khi đặt 2 ghế: Không được đặt nếu có ghế đã đặt cách 2 ô (bên trái hoặc phải)
                // Trừ khi bên cạnh ghế được chọn đã có ghế đặt rồi (đã xử lý ở trên)
                if ($selectedSeatCountInGroup == 2) {
                    // Kiểm tra ghế đã đặt cách 2 ô về bên trái (từ ghế được chọn đầu tiên)
                    $seatTwoAwayLeft = $selectedMinCol - 2;
                    if ($seatTwoAwayLeft >= $minColInGroup && in_array($seatTwoAwayLeft, $groupCols)) {
                        $checkSeatLeft = $row . $seatTwoAwayLeft;
                        if (in_array($checkSeatLeft, $bookedSeats)) {
                            // Kiểm tra xem bên cạnh ghế được chọn có ghế đã đặt không
                            $seatAdjacentLeft = $selectedMinCol - 1;
                            if ($seatAdjacentLeft >= $minColInGroup && in_array($seatAdjacentLeft, $groupCols)) {
                                $checkSeatAdjacentLeft = $row . $seatAdjacentLeft;
                                // Nếu bên cạnh không có ghế đã đặt, thì không được đặt
                                if (!in_array($checkSeatAdjacentLeft, $bookedSeats)) {
                                    error_log("Row $row, Group: Validation FAILED - Đặt 2 ghế nhưng có ghế đã đặt cách 2 ô về bên trái (ghế $checkSeatLeft) và bên cạnh không có ghế đã đặt");
                                    return "Không được đặt ghế khi có ghế đã đặt cách 2 ô! Vui lòng chọn ghế khác.";
                                }
                            }
                        }
                    }
                    
                    // Kiểm tra ghế đã đặt cách 2 ô về bên phải (từ ghế được chọn cuối cùng)
                    $seatTwoAwayRight = $selectedMaxCol + 2;
                    if ($seatTwoAwayRight <= $maxColInGroup && in_array($seatTwoAwayRight, $groupCols)) {
                        $checkSeatRight = $row . $seatTwoAwayRight;
                        if (in_array($checkSeatRight, $bookedSeats)) {
                            // Kiểm tra xem bên cạnh ghế được chọn có ghế đã đặt không
                            $seatAdjacentRight = $selectedMaxCol + 1;
                            if ($seatAdjacentRight <= $maxColInGroup && in_array($seatAdjacentRight, $groupCols)) {
                                $checkSeatAdjacentRight = $row . $seatAdjacentRight;
                                // Nếu bên cạnh không có ghế đã đặt, thì không được đặt
                                if (!in_array($checkSeatAdjacentRight, $bookedSeats)) {
                                    error_log("Row $row, Group: Validation FAILED - Đặt 2 ghế nhưng có ghế đã đặt cách 2 ô về bên phải (ghế $checkSeatRight) và bên cạnh không có ghế đã đặt");
                                    return "Không được đặt ghế khi có ghế đã đặt cách 2 ô! Vui lòng chọn ghế khác.";
                                }
                            }
                        }
                    }
                }
                
                // Không đặt từ đầu hàng và không đặt ngay sau ghế đã đặt, áp dụng quy tắc bình thường
                if ($selectedSeatCountInGroup >= $halfOfAvailable) {
                    // Đặt >= X/2 ghế: Bắt buộc phải đặt từ đầu hàng
                    error_log("Row $row, Group: Validation FAILED - Nhóm có $totalAvailableInGroup ghế available, đặt $selectedSeatCountInGroup vé (>= $halfOfAvailable) nhưng không đặt từ đầu hàng");
                    return "Khi đặt từ $halfOfAvailable vé trở lên trong nhóm có $totalAvailableInGroup ghế trống, bắt buộc phải đặt từ đầu hàng (chọn ít nhất 1 trong 2 ghế ngoài cùng)!";
                } else {
                    // Đặt < X/2 ghế (bao gồm cả 1 ghế): Phải để lại >= 2 ghế ở cả hai đầu (nếu không đặt ngay sau ghế đã đặt)
                    // NHƯNG: Nếu đặt 1 ghế, chỉ cần để lại >= 1 ghế ở mỗi đầu (nới lỏng hơn)
                    $minRequiredAtEnds = ($selectedSeatCountInGroup == 1) ? 1 : 2;
                    
                    error_log("Row $row, Group: Kiểm tra quy tắc 'để lại >= $minRequiredAtEnds ghế ở cả hai đầu': availableSeatsAtStart=$availableSeatsAtStart, availableSeatsAtEnd=$availableSeatsAtEnd");
                    if ($availableSeatsAtStart < $minRequiredAtEnds || $availableSeatsAtEnd < $minRequiredAtEnds) {
                        // Nếu đặt 1 ghế và chỉ thiếu 1 ghế ở một đầu, vẫn cho phép nếu đầu kia có >= 2 ghế
                        if ($selectedSeatCountInGroup == 1 && ($availableSeatsAtStart >= 2 || $availableSeatsAtEnd >= 2)) {
                            error_log("Row $row, Group: Validation OK - Đặt 1 ghế, một đầu có >= 2 ghế (đầu trái: $availableSeatsAtStart, đầu phải: $availableSeatsAtEnd)");
                        } else {
                            error_log("Row $row, Group: Validation FAILED - Nhóm có $totalAvailableInGroup ghế available, đặt $selectedSeatCountInGroup vé (< $halfOfAvailable) nhưng không đặt từ đầu hàng và không để lại ít nhất $minRequiredAtEnds ghế ở cả hai đầu (đầu trái: $availableSeatsAtStart, đầu phải: $availableSeatsAtEnd)");
                            return "Khi đặt $selectedSeatCountInGroup vé trong nhóm có $totalAvailableInGroup ghế trống mà không đặt từ đầu hàng, phải để lại ít nhất $minRequiredAtEnds ghế kể từ ghế ngoài cùng ở cả hai đầu hàng!";
                        }
                    } else {
                        error_log("Row $row, Group: Validation OK - Đã để lại >= $minRequiredAtEnds ghế ở cả hai đầu (đầu trái: $availableSeatsAtStart, đầu phải: $availableSeatsAtEnd)");
                    }
                }
            }
        }
        
        return null; // Valid
    }
    
    /**
     * Lấy danh sách các nhóm ghế trong một hàng từ seat layout
     */
    private function getSeatGroupsInRow($row, $seatLayout) {
        if (!$seatLayout) {
            return [];
        }
        
        $groups = [];
        
        // Nếu có seat_groups (layout phức tạp)
        if (isset($seatLayout['seat_groups']) && is_array($seatLayout['seat_groups'])) {
            foreach ($seatLayout['seat_groups'] as $group) {
                $groupRows = $group['rows'] ?? [];
                $groupCols = $group['cols'] ?? [];
                
                if (in_array($row, $groupRows) && !empty($groupCols)) {
                    $groups[] = ['cols' => $groupCols];
                }
            }
        } elseif (isset($seatLayout['cols']) && is_array($seatLayout['cols'])) {
            // Layout tiêu chuẩn - coi toàn bộ hàng là một nhóm
            $groups[] = ['cols' => $seatLayout['cols']];
        }
        
        return $groups;
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
        $user = $this->getCurrentUser();
        
        $showtime_id = $_GET['showtime'] ?? null;
        
        if (!$showtime_id) {
            $this->redirect('booking');
        }
        
        $showtime = $bookingModel->getShowtimeById($showtime_id);
        
        if (!$showtime) {
            $this->redirect('booking');
        }
        
        // Kiểm tra xem người dùng có bị cấm đặt vé phòng này không
        $banCheck = $this->isUserBannedFromScreen($user['id'], $showtime_id);
        if ($banCheck['banned']) {
            $_SESSION['error'] = $banCheck['message'];
            $this->redirect('booking');
            return;
        }
        
        // Kiểm tra thời gian thực và vi phạm
        $timeCheck = $this->checkBookingTimeAndViolations($user['id'], $showtime_id);
        if (!$timeCheck['allowed']) {
            $_SESSION['error'] = $timeCheck['message'];
            $this->redirect('booking');
            return;
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
        error_log("=== VALIDATION START ===");
        error_log("Seats to validate: " . implode(', ', $seats));
        error_log("Showtime ID: " . $showtime_id);
        $validationError = $this->validateSeatSelection($seats, $showtime_id);
        error_log("Validation result: " . ($validationError ? $validationError : "PASSED"));
        error_log("=== VALIDATION END ===");
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
        
        // Tính tổng tiền
        $totalAmount = 0;
        foreach ($seats as $seat) {
            $seat_type = $bookingModel->getSeatType($seat, $seatLayout);
            $seat_price = $bookingModel->getSeatPrice($seat, $seatLayout, $showtime['price']);
            $totalAmount += $seat_price;
        }
        
        // Tính tiền food items
        if (!empty($food_items)) {
            foreach ($food_items as $food_item_id => $quantity) {
                if ($quantity > 0) {
                    $foodItem = $bookingModel->getFoodItemById($food_item_id);
                    if ($foodItem) {
                        $totalAmount += $foodItem['price'] * $quantity;
                    }
                }
            }
        }
        
        // Tạo mã giao dịch VNPay
        $vnp_TxnRef = 'BOOKING_' . $user['id'] . '_' . $showtime_id . '_' . time() . '_' . rand(1000, 9999);
        
        // Tạo pending booking
        error_log("=== Creating pending booking ===");
        error_log("User ID: " . $user['id']);
        error_log("Showtime ID: " . $showtime_id);
        error_log("Seats: " . json_encode($seats));
        error_log("Food items: " . json_encode($food_items));
        error_log("Total amount: " . $totalAmount);
        error_log("Txn ref: " . $vnp_TxnRef);
        
        $pendingBookingId = $bookingModel->createPendingBooking([
            'user_id' => $user['id'],
            'showtime_id' => $showtime_id,
            'seats' => $seats,
            'food_items' => $food_items,
            'customer_email' => $customer_email,
            'total_amount' => $totalAmount,
            'vnp_txn_ref' => $vnp_TxnRef,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+10 minutes'))
        ]);
        
        error_log("Pending booking ID result: " . ($pendingBookingId ? $pendingBookingId : 'FALSE'));
        
        if (!$pendingBookingId) {
            error_log("Failed to create pending booking");
            $_SESSION['error'] = 'Có lỗi xảy ra khi tạo đơn hàng! Vui lòng thử lại. Lỗi đã được ghi vào log.';
            $redirectUrl = '?route=booking/index&showtime_id=' . urlencode($showtime_id);
            $this->redirect($redirectUrl);
            return;
        }
        
        error_log("Pending booking created successfully with ID: " . $pendingBookingId);
        
        // Xóa reservations
        $bookingModel->releaseSeats($showtime_id, $seats, $user['id']);
        
        // Chuyển hướng đến VNPay
        $this->redirectToVNPay($vnp_TxnRef, $totalAmount, $showtime, $seats);
        return;
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
        
        // Lấy vé đã thanh toán
        $tickets = $bookingModel->getUserTickets($user['id']);
        
        // Lấy vé chưa thanh toán (pending bookings)
        $pendingBookings = $bookingModel->getUserPendingBookings($user['id']);
        
        // Gộp lại và sắp xếp: pending trước, completed sau
        $allTickets = array_merge($pendingBookings, $tickets);
        
        // Sắp xếp theo thời gian tạo (mới nhất trước)
        usort($allTickets, function($a, $b) {
            $timeA = strtotime($a['created_at']);
            $timeB = strtotime($b['created_at']);
            return $timeB - $timeA;
        });
        
        $this->view('booking/my-tickets', [
            'tickets' => $allTickets,
            'user' => $user
        ]);
    }
    
    /**
     * Tiếp tục thanh toán cho pending booking
     */
    public function payment() {
        $this->requireLogin();
        
        $txn_ref = $_GET['txn_ref'] ?? null;
        
        if (!$txn_ref) {
            $_SESSION['error'] = 'Không tìm thấy mã giao dịch!';
            $this->redirect('booking/my-tickets');
            return;
        }
        
        $bookingModel = new BookingModel();
        $user = $this->getCurrentUser();
        
        // Lấy pending booking
        $pendingBooking = $bookingModel->getPendingBookingByTxnRef($txn_ref);
        
        if (!$pendingBooking) {
            $_SESSION['error'] = 'Không tìm thấy đơn hàng chờ thanh toán!';
            $this->redirect('booking/my-tickets');
            return;
        }
        
        // Kiểm tra xem booking có thuộc về user này không
        if ($pendingBooking['user_id'] != $user['id']) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập đơn hàng này!';
            $this->redirect('booking/my-tickets');
            return;
        }
        
        // Kiểm tra xem booking còn hiệu lực không
        $now = date('Y-m-d H:i:s');
        $expiresAt = $pendingBooking['expires_at'] ?? date('Y-m-d H:i:s', strtotime($pendingBooking['created_at'] . ' +10 minutes'));
        
        if (strtotime($expiresAt) < strtotime($now)) {
            // Booking đã hết hạn, xóa và thông báo
            $bookingModel->updatePendingBookingStatus($pendingBooking['id'], 'cancelled');
            $_SESSION['error'] = 'Đơn hàng đã hết hạn thanh toán! Vui lòng đặt vé lại.';
            $this->redirect('booking/my-tickets');
            return;
        }
        
        // Kiểm tra status
        if ($pendingBooking['status'] !== 'pending') {
            $_SESSION['error'] = 'Đơn hàng này đã được xử lý!';
            $this->redirect('booking/my-tickets');
            return;
        }
        
        // Lấy thông tin showtime
        $showtime = $bookingModel->getShowtimeById($pendingBooking['showtime_id']);
        if (!$showtime) {
            $_SESSION['error'] = 'Không tìm thấy thông tin suất chiếu!';
            $this->redirect('booking/my-tickets');
            return;
        }
        
        // Parse seats
        $seats = json_decode($pendingBooking['seats'], true) ?? [];
        if (empty($seats)) {
            $_SESSION['error'] = 'Không tìm thấy thông tin ghế!';
            $this->redirect('booking/my-tickets');
            return;
        }
        
        // Redirect đến VNPay
        $this->redirectToVNPay($txn_ref, floatval($pendingBooking['total_amount']), $showtime, $seats);
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
     * Logic: 
     * - Vi phạm lần 1: quá 10 phút → đưa ra khỏi trang
     * - Vi phạm lần 2: quá 10 phút → cấm 10 phút
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
            SELECT id, session_start, violation_count, is_banned, ban_until
            FROM booking_session_tracking
            WHERE user_id = ? AND showtime_id = ? AND screen_id = ? AND session_end IS NULL
            ORDER BY session_start DESC
            LIMIT 1
        ", [$user_id, $showtime_id, $screen_id]);
        
        if (!$session) {
            return ['allowed' => true, 'message' => ''];
        }
        
        // Tính thời gian thực (giây) - thời gian không bị reset khi load lại trang
        $sessionStart = strtotime($session['session_start']);
        $currentTime = time();
        $durationSeconds = $currentTime - $sessionStart;
        
        // Đếm tổng số vi phạm trước đó của user cho screen này (từ các session đã kết thúc)
        $previousViolations = $db->fetch("
            SELECT COUNT(*) as total_violations
            FROM booking_session_tracking
            WHERE user_id = ? AND screen_id = ? AND violation_count > 0 AND id != ?
        ", [$user_id, $screen_id, $session['id']]);
        
        $previousViolationCount = $previousViolations['total_violations'] ?? 0;
        $currentViolationCount = $session['violation_count'] ?? 0;
        
        // Tổng số vi phạm = vi phạm từ các session trước + vi phạm của session hiện tại
        $totalViolationCount = $previousViolationCount + $currentViolationCount;
        
        $maxDuration = 10 * 60; // 10 phút = 600 giây
        
        // Kiểm tra vi phạm: quá 10 phút lần 1 → đưa ra khỏi trang, quá 10 phút lần 2 → cấm 10 phút
        if ($totalViolationCount == 0 && $durationSeconds > $maxDuration) {
            // Vi phạm lần 1: quá 10 phút → đưa ra khỏi trang
            $db->execute("
                UPDATE booking_session_tracking
                SET violation_count = 1, session_end = NOW(), total_duration_seconds = ?
                WHERE id = ?
            ", [$durationSeconds, $session['id']]);
            
            return [
                'allowed' => false,
                'message' => 'Thời gian đặt vé đã hết! Bạn đã ở quá 10 phút. Lần vi phạm thứ nhất. Vui lòng chọn suất chiếu khác.'
            ];
        } elseif ($totalViolationCount == 1 && $durationSeconds > $maxDuration) {
            // Vi phạm lần 2: quá 10 phút → cấm 10 phút
            $banUntil = date('Y-m-d H:i:s', $currentTime + (10 * 60)); // Cấm 10 phút
            
            $db->execute("
                UPDATE booking_session_tracking
                SET violation_count = 2, is_banned = 1, ban_until = ?, session_end = NOW(), total_duration_seconds = ?
                WHERE id = ?
            ", [$banUntil, $durationSeconds, $session['id']]);
            
            return [
                'allowed' => false,
                'message' => 'Bạn đã bị cấm đặt vé phòng này trong 10 phút do vi phạm quy định thời gian đặt vé lần thứ 2!'
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
     * Trả về ['banned' => bool, 'message' => string]
     */
    private function isUserBannedFromScreen($user_id, $showtime_id) {
        $db = Database::getInstance();
        $bookingModel = new BookingModel();
        
        // Lấy screen_id từ showtime
        $showtime = $bookingModel->getShowtimeById($showtime_id);
        if (!$showtime || !isset($showtime['screen_id'])) {
            return ['banned' => false, 'message' => ''];
        }
        
        $screen_id = $showtime['screen_id'];
        
        // Kiểm tra xem có bị cấm không và thời gian cấm còn hiệu lực không
        $bannedSession = $db->fetch("
            SELECT ban_until, violation_count
            FROM booking_session_tracking
            WHERE user_id = ? AND screen_id = ? AND is_banned = 1
            ORDER BY created_at DESC
            LIMIT 1
        ", [$user_id, $screen_id]);
        
        if ($bannedSession) {
            $currentTime = time();
            $banUntil = $bannedSession['ban_until'] ? strtotime($bannedSession['ban_until']) : 0;
            
            // Nếu thời gian cấm còn hiệu lực
            if ($banUntil > $currentTime) {
                $remainingMinutes = ceil(($banUntil - $currentTime) / 60);
                return [
                    'banned' => true,
                    'message' => "Bạn đã bị cấm đặt vé phòng này do vi phạm quy định thời gian đặt vé. Thời gian cấm còn lại: {$remainingMinutes} phút."
                ];
            } else {
                // Thời gian cấm đã hết, xóa trạng thái cấm
                $db->execute("
                    UPDATE booking_session_tracking
                    SET is_banned = 0, ban_until = NULL
                    WHERE user_id = ? AND screen_id = ? AND is_banned = 1
                ", [$user_id, $screen_id]);
            }
        }
        
        return ['banned' => false, 'message' => ''];
    }
    
    /**
     * Chuyển hướng đến VNPay để thanh toán
     */
    private function redirectToVNPay($vnp_TxnRef, $amount, $showtime, $seats) {
        require_once __DIR__ . '/../../vnpay_php/config.php';
        
        $vnp_Amount = $amount * 100; // VNPay yêu cầu số tiền nhân 100
        $vnp_Locale = 'vn';
        $vnp_BankCode = '';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        
        $movieTitle = isset($showtime['movie_title']) ? $showtime['movie_title'] : 'Phim';
        $orderInfo = "Dat ve: " . $movieTitle . " - " . implode(', ', $seats);
        
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $orderInfo,
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $expire
        );
        
        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        
        header('Location: ' . $vnp_Url);
        die();
    }
    
    /**
     * Xử lý callback từ VNPay sau khi thanh toán
     */
    public function vnpayReturn() {
        $this->requireLogin();
        
        require_once __DIR__ . '/../../vnpay_php/config.php';
        
        $vnp_SecureHash = isset($_GET['vnp_SecureHash']) ? $_GET['vnp_SecureHash'] : '';
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $vnp_TxnRef = isset($_GET['vnp_TxnRef']) ? $_GET['vnp_TxnRef'] : '';
        $vnp_ResponseCode = isset($_GET['vnp_ResponseCode']) ? $_GET['vnp_ResponseCode'] : '';
        $vnp_Amount = isset($_GET['vnp_Amount']) ? $_GET['vnp_Amount'] : 0;
        
        $bookingModel = new BookingModel();
        $pendingBooking = $bookingModel->getPendingBookingByTxnRef($vnp_TxnRef);
        
        if (!$pendingBooking) {
            $_SESSION['error'] = 'Không tìm thấy thông tin đơn hàng!';
            $this->redirect('booking');
            return;
        }
        
        // Kiểm tra chữ ký
        if ($secureHash == $vnp_SecureHash) {
            if ($vnp_ResponseCode == '00') {
                // Thanh toán thành công
                $this->completeBooking($pendingBooking, $vnp_TxnRef, $vnp_Amount);
            } else {
                // Thanh toán thất bại
                $bookingModel->updatePendingBookingStatus($pendingBooking['id'], 'cancelled');
                $_SESSION['error'] = 'Thanh toán thất bại! Mã lỗi: ' . $vnp_ResponseCode;
                $redirectUrl = '?route=booking/index&showtime_id=' . $pendingBooking['showtime_id'];
                $this->redirect($redirectUrl);
            }
        } else {
            $_SESSION['error'] = 'Chữ ký không hợp lệ!';
            $redirectUrl = '?route=booking/index&showtime_id=' . $pendingBooking['showtime_id'];
            $this->redirect($redirectUrl);
        }
    }
    
    /**
     * Hoàn tất booking sau khi thanh toán thành công
     */
    private function completeBooking($pendingBooking, $vnp_TxnRef, $vnp_Amount) {
        $bookingModel = new BookingModel();
        $user = $this->getCurrentUser();
        
        $seats = json_decode($pendingBooking['seats'], true);
        $food_items = !empty($pendingBooking['food_items']) ? json_decode($pendingBooking['food_items'], true) : [];
        $showtime_id = $pendingBooking['showtime_id'];
        
        $showtime = $bookingModel->getShowtimeWithScreen($showtime_id);
        if (!$showtime) {
            $_SESSION['error'] = 'Không tìm thấy thông tin suất chiếu!';
            $this->redirect('booking');
            return;
        }
        
        // Lấy seat layout để tính giá
        $seatLayout = null;
        if (isset($showtime['screen_id']) && $showtime['screen_id']) {
            $seatLayout = $bookingModel->getScreenSeatLayout($showtime['screen_id']);
        }
        
        $db = Database::getInstance()->getConnection();
        $createdTickets = [];
        
        try {
            $db->beginTransaction();
            
            // Kiểm tra lại ghế đã được đặt chưa
            $existingTickets = $bookingModel->getBookedSeats($showtime_id);
            $existingSeats = array_column($existingTickets, 'seat');
            
            // Tạo tất cả tickets trước
            foreach ($seats as $seat) {
                if (in_array($seat, $existingSeats)) {
                    throw new Exception("Ghế $seat đã được đặt bởi người khác!");
                }
                
                $seat_type = $bookingModel->getSeatType($seat, $seatLayout);
                $seat_price = $bookingModel->getSeatPrice($seat, $seatLayout, $showtime['price']);
                
                $qr_code = uniqid('TICKET_') . '_' . $user['id'] . '_' . $showtime_id . '_' . time() . '_' . $seat;
                
                $ticket_id = $bookingModel->createTicket([
                    'user_id' => $user['id'],
                    'showtime_id' => $showtime_id,
                    'booking_pending_id' => $pendingBooking['id'],
                    'seat' => $seat,
                    'seat_type' => $seat_type,
                    'price' => $seat_price,
                    'qr_code' => $qr_code
                ]);
                
                if (!$ticket_id) {
                    throw new Exception("Không thể tạo vé cho ghế $seat!");
                }
                
                $createdTickets[] = [
                    'id' => $ticket_id,
                    'seat' => $seat,
                    'seat_type' => $seat_type,
                    'qr_code' => $qr_code,
                    'price' => $seat_price
                ];
                
                $existingSeats[] = $seat;
            }
            
            // Tạo food items 1 lần cho toàn bộ booking (gắn với booking_pending_id)
            if (!empty($food_items)) {
                foreach ($food_items as $food_item_id => $quantity) {
                    if ($quantity > 0) {
                        $foodItem = $bookingModel->getFoodItemById($food_item_id);
                        if ($foodItem) {
                            // Gắn với booking_pending_id thay vì ticket_id
                            $bookingModel->createBookingFoodItem(
                                null, // ticket_id = null vì food items thuộc về booking, không phải ticket cụ thể
                                $food_item_id,
                                $quantity,
                                $foodItem['price'],
                                $pendingBooking['id'] // booking_pending_id
                            );
                        }
                    }
                }
            }
            
            // Tạo transaction record
            require_once __DIR__ . '/../user/TransactionModel.php';
            $transactionModel = new TransactionModel();
            $transactionModel->create([
                'user_id' => $user['id'],
                'type' => 'ticket',
                'related_id' => $pendingBooking['id'],
                'amount' => $pendingBooking['total_amount'],
                'method' => 'VNPay',
                'status' => 'Thành công'
            ]);
            
            // Cập nhật trạng thái pending booking
            $bookingModel->updatePendingBookingStatus($pendingBooking['id'], 'completed');
            
            $db->commit();
            
            // Gửi email
            try {
                $this->sendTicketEmail($pendingBooking['customer_email'], $showtime, $createdTickets, $user);
            } catch (Exception $e) {
                error_log("Error sending ticket email: " . $e->getMessage());
            }
            
            $_SESSION['success'] = 'Đặt vé thành công! Vé và QR code đã được gửi đến email ' . htmlspecialchars($pendingBooking['customer_email']);
            
            // Redirect về trang booking
            $redirectUrl = '?route=booking/index&showtime_id=' . $showtime_id . '&_t=' . time();
            $this->redirect($redirectUrl);
            
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Error completing booking: " . $e->getMessage());
            $bookingModel->updatePendingBookingStatus($pendingBooking['id'], 'cancelled');
            $_SESSION['error'] = 'Có lỗi xảy ra khi hoàn tất đặt vé: ' . $e->getMessage();
            $redirectUrl = '?route=booking/index&showtime_id=' . $showtime_id;
            $this->redirect($redirectUrl);
        }
    }
}
?>

