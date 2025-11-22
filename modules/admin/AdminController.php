<?php
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/AdminMiddleware.php';
require_once __DIR__ . '/../../core/Database.php';

class AdminController extends Controller {
    
    public function __construct() {
        parent::__construct();
        AdminMiddleware::checkAdmin();
    }
    
    protected function adminView($view, $data = []) {
        extract($data);
        $current_page = $data['current_page'] ?? '';
        $title = $data['title'] ?? 'Admin Panel';
        $user = $data['user'] ?? AdminMiddleware::checkAdmin();
        
        ob_start();
        require_once __DIR__ . '/views/' . $view . '.php';
        $content = ob_get_clean();
        
        require_once __DIR__ . '/views/layout.php';
    }
    
    // Dashboard Overview
    public function index() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        // Thống kê tổng quan
        $stats = [
            'total_users' => $db->fetch("SELECT COUNT(*) as count FROM users")['count'],
            'total_movies' => $db->fetch("SELECT COUNT(*) as count FROM movies")['count'],
            'total_tickets' => $db->fetch("SELECT COUNT(*) as count FROM tickets WHERE status = 'Đã đặt'")['count'],
            'total_revenue' => $db->fetch("SELECT SUM(amount) as total FROM transactions WHERE status = 'Thành công'")['total'] ?? 0,
            'today_revenue' => $db->fetch("SELECT SUM(amount) as total FROM transactions WHERE status = 'Thành công' AND DATE(created_at) = CURDATE()")['total'] ?? 0,
            'week_revenue' => $db->fetch("SELECT SUM(amount) as total FROM transactions WHERE status = 'Thành công' AND WEEK(created_at) = WEEK(NOW())")['total'] ?? 0,
            'month_revenue' => $db->fetch("SELECT SUM(amount) as total FROM transactions WHERE status = 'Thành công' AND MONTH(created_at) = MONTH(NOW())")['total'] ?? 0,
            'pending_tickets' => $db->fetch("SELECT COUNT(*) as count FROM support_tickets WHERE status = 'Mới'")['count'],
            'active_users_today' => $db->fetch("SELECT COUNT(DISTINCT user_id) as count FROM watch_history WHERE DATE(created_at) = CURDATE()")['count'],
        ];
        
        // Doanh thu theo ngày (7 ngày gần nhất) - Đảm bảo có đủ 7 ngày
        $revenueByDay = $db->fetchAll("
            SELECT DATE(created_at) as date, SUM(amount) as revenue, COUNT(*) as transaction_count
            FROM transactions
            WHERE status = 'Thành công' AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            GROUP BY DATE(created_at)
            ORDER BY date ASC
        ");
        
        // Tạo mảng đầy đủ 7 ngày gần nhất
        $allDays = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $allDays[$date] = ['date' => $date, 'revenue' => 0, 'transaction_count' => 0];
        }
        
        // Merge dữ liệu thực tế
        foreach ($revenueByDay as $day) {
            if (isset($allDays[$day['date']])) {
                $allDays[$day['date']] = $day;
            }
        }
        
        $revenueByDay = array_values($allDays);
        
        // Top phim xem nhiều nhất
        $topMovies = $db->fetchAll("
            SELECT m.title, COUNT(wh.id) as view_count
            FROM watch_history wh
            JOIN movies m ON wh.movie_id = m.id
            GROUP BY m.id, m.title
            ORDER BY view_count DESC
            LIMIT 5
        ");
        
        $this->adminView('dashboard', [
            'stats' => $stats,
            'revenueByDay' => $revenueByDay,
            'topMovies' => $topMovies,
            'user' => $user,
            'title' => 'Dashboard',
            'current_page' => 'dashboard'
        ]);
    }
    
    // Users Management
    public function users() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $sql = "SELECT u.*, s.name as subscription_name FROM users u 
                LEFT JOIN subscriptions s ON u.subscription_id = s.id WHERE 1=1";
        $params = [];
        
        if ($search) {
            $sql .= " AND (u.name LIKE ? OR u.email LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($status) {
            $sql .= " AND u.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY u.created_at DESC";
        
        $users = $db->fetchAll($sql, $params);
        
        $this->adminView('users', [
            'users' => $users,
            'search' => $search,
            'status' => $status,
            'user' => $user,
            'title' => 'Quản lý người dùng',
            'current_page' => 'users'
        ]);
    }
    
    /**
     * Cập nhật điểm của user
     */
    public function usersUpdatePoints() {
        try {
            $user = AdminMiddleware::checkAdmin();
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $_SESSION['error'] = 'Phương thức không hợp lệ!';
                $this->redirect('admin/users');
                return;
            }
            
            $userId = $_POST['user_id'] ?? null;
            $action = $_POST['action'] ?? 'set';
            $points = intval($_POST['points'] ?? 0);
            
            // Debug log
            error_log("Update Points Request - User ID: $userId, Action: $action, Points: $points");
            
            if (!$userId) {
                $_SESSION['error'] = 'Thiếu thông tin user ID!';
                $this->redirect('admin/users');
                return;
            }
            
            if ($points < 0) {
                $_SESSION['error'] = 'Số điểm phải lớn hơn hoặc bằng 0!';
                $this->redirect('admin/users');
                return;
            }
            
            // Kiểm tra nếu action là 'add' hoặc 'subtract' nhưng points = 0
            if (($action === 'add' || $action === 'subtract') && $points === 0) {
                $_SESSION['error'] = 'Vui lòng nhập số điểm lớn hơn 0!';
                $this->redirect('admin/users');
                return;
            }
            
            require_once __DIR__ . '/../user/UserModel.php';
            $userModel = new UserModel();
            
            $targetUser = $userModel->getById($userId);
            if (!$targetUser) {
                $_SESSION['error'] = 'Người dùng không tồn tại!';
                $this->redirect('admin/users');
                return;
            }
            
            $currentPoints = intval($targetUser['points'] ?? 0);
            
            switch ($action) {
                case 'set':
                    $newPoints = $points;
                    break;
                case 'add':
                    $newPoints = $currentPoints + $points;
                    break;
                case 'subtract':
                    $newPoints = max(0, $currentPoints - $points);
                    break;
                default:
                    $_SESSION['error'] = 'Thao tác không hợp lệ!';
                    $this->redirect('admin/users');
                    return;
            }
            
            // Log trước khi update
            error_log("Updating points - User ID: $userId, Current: $currentPoints, New: $newPoints, Action: $action");
            
            $userModel->updatePoints($userId, $newPoints);
            
            // Log action
            AdminMiddleware::logAction(
                $user['id'],
                'Cập nhật điểm người dùng',
                'User',
                'user',
                $userId,
                ['points' => $currentPoints],
                ['points' => $newPoints, 'action' => $action, 'points_changed' => $points]
            );
            
            $_SESSION['success'] = "Đã cập nhật điểm thành công! Điểm hiện tại: " . number_format($newPoints) . " (Thay đổi: " . ($action === 'add' ? '+' : ($action === 'subtract' ? '-' : '')) . number_format($points) . ")";
            $this->redirect('admin/users');
        } catch (Exception $e) {
            error_log("Error in usersUpdatePoints: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật điểm: ' . $e->getMessage();
            $this->redirect('admin/users');
        }
    }
    
    // Movies Management
    public function movies() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        require_once __DIR__ . '/../movie/CategoryModel.php';
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->getAll();
        
        $search = $_GET['search'] ?? '';
        $category_id = $_GET['category'] ?? null;
        $status = $_GET['status'] ?? '';
        
        $sql = "SELECT m.*, c.name as category_name FROM movies m 
                LEFT JOIN categories c ON m.category_id = c.id WHERE 1=1";
        $params = [];
        
        if ($search) {
            $sql .= " AND m.title LIKE ?";
            $params[] = "%$search%";
        }
        
        if ($category_id) {
            $sql .= " AND m.category_id = ?";
            $params[] = $category_id;
        }
        
        if (!empty($status)) {
            $sql .= " AND m.status = ?";
            $params[] = trim($status);
        }
        
        $sql .= " ORDER BY m.created_at DESC";
        
        $movies = $db->fetchAll($sql, $params);
        
        $this->adminView('movies', [
            'movies' => $movies,
            'categories' => $categories,
            'search' => $search,
            'category_id' => $category_id,
            'status' => $status,
            'user' => $user,
            'title' => 'Quản lý phim',
            'current_page' => 'movies'
        ]);
    }
    
    // Create Movie (Form)
    public function moviesCreate() {
        $user = AdminMiddleware::checkAdmin();
        $db = Database::getInstance();
        
        require_once __DIR__ . '/../movie/CategoryModel.php';
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->getAll();
        
        // Lấy danh sách rạp
        $theaters = $db->fetchAll("SELECT * FROM theaters WHERE is_active = 1 ORDER BY name");
        
        $this->adminView('movies/create', [
            'categories' => $categories,
            'theaters' => $theaters,
            'user' => $user,
            'title' => 'Thêm phim mới',
            'current_page' => 'movies'
        ]);
    }
    
    // Store Movie
    public function moviesStore() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/movies');
        }
        
        $title = $_POST['title'] ?? '';
        $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
        $level = $_POST['level'] ?? 'Free';
        $duration = !empty($_POST['duration']) ? intval($_POST['duration']) : null;
        $description = $_POST['description'] ?? '';
        $director = $_POST['director'] ?? '';
        $actors = $_POST['actors'] ?? '';
        $video_url = $_POST['video_url'] ?? '';
        $trailer_url = $_POST['trailer_url'] ?? '';
        $thumbnail = $_POST['thumbnail'] ?? '';
        $status = $_POST['status'] ?? 'Sắp chiếu';
        $status_admin = $_POST['status_admin'] ?? 'draft';
        $rating = floatval($_POST['rating'] ?? 0);
        $country = $_POST['country'] ?? '';
        $language = $_POST['language'] ?? '';
        $age_rating = $_POST['age_rating'] ?? '';
        $banner = $_POST['banner'] ?? '';
        $type = $_POST['type'] ?? 'phimle';
        
        if (empty($title)) {
            $_SESSION['error'] = 'Tiêu đề phim không được để trống!';
            $this->redirect('admin/movies/create');
        }
        
        try {
            // Xử lý upload video file
            if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../data/phim/' . ($type === 'phimbo' ? 'phimbo' : 'phimle') . '/';
                
                // Tạo thư mục nếu chưa tồn tại
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $title);
                $fileExtension = pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION);
                $uploadFileName = $fileName . '.' . $fileExtension;
                $uploadPath = $uploadDir . $uploadFileName;
                
                // Kiểm tra nếu file đã tồn tại, thêm số vào tên
                $counter = 1;
                while (file_exists($uploadPath)) {
                    $uploadFileName = $fileName . '_' . $counter . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $uploadFileName;
                    $counter++;
                }
                
                // Upload file
                if (move_uploaded_file($_FILES['video_file']['tmp_name'], $uploadPath)) {
                    // Lưu đường dẫn tương đối
                    $video_url = 'data/phim/' . ($type === 'phimbo' ? 'phimbo' : 'phimle') . '/' . $uploadFileName;
                } else {
                    $_SESSION['error'] = 'Lỗi khi upload file video!';
                    $this->redirect('admin/movies/create');
                    return;
                }
            }
            $db->execute("
                INSERT INTO movies (
                    title, category_id, level, duration, description, director, actors,
                    video_url, trailer_url, thumbnail, status, status_admin, rating,
                    country, language, age_rating, banner, type
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", [
                $title, $category_id, $level, $duration, $description, $director, $actors,
                $video_url, $trailer_url, $thumbnail, $status, $status_admin, $rating,
                $country, $language, $age_rating, $banner, $type
            ]);
            
            $movie_id = $db->lastInsertId();
            
            // Log activity
            AdminMiddleware::logAction(
                $user['id'],
                'Thêm phim',
                'Movie',
                'movie',
                $movie_id,
                null,
                ['title' => $title, 'status' => $status, 'status_admin' => $status_admin]
            );
            
            // Nếu là phim chiếu rạp, tạo các suất chiếu
            if ($status === 'Chiếu rạp') {
                $showtimeCount = 0;
                
                // Kiểm tra dữ liệu từ form mới (khoảng ngày)
                if (!empty($_POST['schedule_theater_id']) && !empty($_POST['from_date']) && !empty($_POST['to_date']) && !empty($_POST['showtimes_time'])) {
                    $theater_id = intval($_POST['schedule_theater_id']);
                    $from_date = $_POST['from_date'];
                    $to_date = $_POST['to_date'];
                    $times = $_POST['showtimes_time'] ?? [];
                    $default_price = floatval($_POST['default_price'] ?? 120000);
                    $screen_id = !empty($_POST['screen_id']) ? intval($_POST['screen_id']) : null;
                    
                    // Tạo suất chiếu cho từng ngày trong khoảng
                    $start = new DateTime($from_date);
                    $end = new DateTime($to_date);
                    $end->modify('+1 day'); // Để bao gồm cả ngày cuối
                    
                    $interval = new DateInterval('P1D');
                    $period = new DatePeriod($start, $interval, $end);
                    
                    foreach ($period as $date) {
                        $show_date = $date->format('Y-m-d');
                        
                        // Tạo suất chiếu cho mỗi khung giờ
                        foreach ($times as $show_time) {
                            if (!empty($show_time)) {
                                $db->execute("
                                    INSERT INTO showtimes (movie_id, theater_id, show_date, show_time, price, screen_id)
                                    VALUES (?, ?, ?, ?, ?, ?)
                                ", [$movie_id, $theater_id, $show_date, $show_time, $default_price, $screen_id]);
                                $showtimeCount++;
                            }
                        }
                    }
                    
                    if ($showtimeCount > 0) {
                        $_SESSION['success'] = 'Thêm phim thành công! Đã tạo ' . $showtimeCount . ' suất chiếu.';
                    } else {
                        $_SESSION['success'] = 'Thêm phim thành công!';
                    }
                } 
                // Fallback: Xử lý dữ liệu cũ (nếu có)
                elseif (isset($_POST['showtimes']) && is_array($_POST['showtimes'])) {
                    foreach ($_POST['showtimes'] as $showtimeData) {
                        if (!empty($showtimeData['theater_id']) && !empty($showtimeData['show_date']) && !empty($showtimeData['show_time'])) {
                            $theater_id = intval($showtimeData['theater_id']);
                            $show_date = $showtimeData['show_date'];
                            $show_time = $showtimeData['show_time'];
                            $price = floatval($showtimeData['price'] ?? 120000);
                            $screen_id = !empty($showtimeData['screen_id']) ? intval($showtimeData['screen_id']) : null;
                            
                            $db->execute("
                                INSERT INTO showtimes (movie_id, theater_id, show_date, show_time, price, screen_id)
                                VALUES (?, ?, ?, ?, ?, ?)
                            ", [$movie_id, $theater_id, $show_date, $show_time, $price, $screen_id]);
                            $showtimeCount++;
                        }
                    }
                    
                    if ($showtimeCount > 0) {
                        $_SESSION['success'] = 'Thêm phim thành công! Đã tạo ' . $showtimeCount . ' suất chiếu.';
                    } else {
                        $_SESSION['success'] = 'Thêm phim thành công!';
                    }
                } else {
                    $_SESSION['success'] = 'Thêm phim thành công! (Chưa có lịch chiếu)';
                }
            } else {
                $_SESSION['success'] = 'Thêm phim thành công!';
            }
            
            // Nếu là phim bộ, lưu các tập
            if ($type === 'phimbo' && isset($_POST['episodes']) && is_array($_POST['episodes'])) {
                $episodeCount = 0;
                $episodeDir = __DIR__ . '/../../data/phim/phimbo/';
                
                // Đảm bảo thư mục tồn tại
                if (!is_dir($episodeDir)) {
                    mkdir($episodeDir, 0755, true);
                }
                
                foreach ($_POST['episodes'] as $index => $episodeData) {
                    if (!empty($episodeData['episode_number'])) {
                        $episode_number = intval($episodeData['episode_number']);
                        $episode_title = !empty($episodeData['title']) ? $episodeData['title'] : null;
                        $episode_thumbnail = !empty($episodeData['thumbnail']) ? $episodeData['thumbnail'] : null;
                        $episode_duration = !empty($episodeData['duration']) ? intval($episodeData['duration']) : null;
                        $episode_description = !empty($episodeData['description']) ? $episodeData['description'] : null;
                        
                        // Xử lý upload file video
                        $episode_video_url = null;
                        if (isset($_FILES['episodes']['name'][$index]['video_file']) && 
                            $_FILES['episodes']['error'][$index]['video_file'] === UPLOAD_ERR_OK) {
                            
                            $uploadedFile = $_FILES['episodes']['tmp_name'][$index]['video_file'];
                            $originalName = $_FILES['episodes']['name'][$index]['video_file'];
                            
                            // Tạo tên file duy nhất
                            $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
                            $fileName = 'movie_' . $movie_id . '_episode_' . $episode_number . '_' . time() . '.' . $fileExtension;
                            $targetPath = $episodeDir . $fileName;
                            
                            // Upload file
                            if (move_uploaded_file($uploadedFile, $targetPath)) {
                                $episode_video_url = 'data/phim/phimbo/' . $fileName;
                            } else {
                                error_log("Failed to upload episode video file: " . $originalName);
                                // Không bỏ qua, vẫn lưu episode nhưng không có video_url
                            }
                        }
                        
                        // Kiểm tra xem episode đã tồn tại chưa
                        try {
                            $existing = $db->fetch("SELECT id, video_url FROM episodes WHERE movie_id = ? AND episode_number = ?", 
                                [$movie_id, $episode_number]);
                            
                            if ($existing) {
                                // Update episode hiện có
                                $updateVideoUrl = $episode_video_url ?: $existing['video_url']; // Giữ video_url cũ nếu không có mới
                                
                                $db->execute("
                                    UPDATE episodes SET title = ?, video_url = ?, thumbnail = ?, duration = ?, description = ?
                                    WHERE movie_id = ? AND episode_number = ?
                                ", [
                                    $episode_title, 
                                    $updateVideoUrl, 
                                    $episode_thumbnail, 
                                    $episode_duration, 
                                    $episode_description,
                                    $movie_id, 
                                    $episode_number
                                ]);
                                $episodeCount++;
                            } else {
                                // Thêm episode mới (cho phép không có video_url ban đầu)
                                $db->execute("
                                    INSERT INTO episodes (movie_id, episode_number, title, video_url, thumbnail, duration, description)
                                    VALUES (?, ?, ?, ?, ?, ?, ?)
                                ", [
                                    $movie_id, 
                                    $episode_number, 
                                    $episode_title, 
                                    $episode_video_url, // Có thể là null
                                    $episode_thumbnail, 
                                    $episode_duration, 
                                    $episode_description
                                ]);
                                $episodeCount++;
                            }
                        } catch (Exception $e) {
                            // Log lỗi để debug
                            error_log("Error inserting/updating episode: " . $e->getMessage());
                            // Nếu lỗi do bảng chưa tồn tại, thông báo rõ ràng
                            if (strpos($e->getMessage(), "doesn't exist") !== false || 
                                strpos($e->getMessage(), "Unknown table") !== false) {
                                throw new Exception("Bảng 'episodes' chưa được tạo. Vui lòng chạy file create_episodes_table.sql trong phpMyAdmin trước!");
                            }
                            // Bỏ qua nếu có lỗi khác
                        }
                    }
                }
                
                if ($episodeCount > 0) {
                    $_SESSION['success'] = ($_SESSION['success'] ?? 'Thêm phim thành công!') . ' Đã thêm ' . $episodeCount . ' tập.';
                }
            }
            
            $this->redirect('admin/movies');
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $this->redirect('admin/movies/create');
        }
    }
    
    // Edit Movie
    public function moviesEdit() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('admin/movies');
        }
        
        $movie = $db->fetch("SELECT * FROM movies WHERE id = ?", [$id]);
        if (!$movie) {
            $_SESSION['error'] = 'Không tìm thấy phim!';
            $this->redirect('admin/movies');
        }
        
        require_once __DIR__ . '/../movie/CategoryModel.php';
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->getAll();
        
        // Lấy danh sách rạp
        $theaters = $db->fetchAll("SELECT * FROM theaters WHERE is_active = 1 ORDER BY name");
        
        // Lấy showtimes hiện tại nếu có
        $existingShowtimes = [];
        if ($movie['status'] === 'Chiếu rạp') {
            $existingShowtimes = $db->fetchAll("SELECT * FROM showtimes WHERE movie_id = ? ORDER BY show_date, show_time", [$id]);
        }
        
        // Lấy episodes nếu là phim bộ
        $episodes = [];
        if (isset($movie['type']) && $movie['type'] === 'phimbo') {
            try {
                $episodes = $db->fetchAll("SELECT * FROM episodes WHERE movie_id = ? ORDER BY episode_number", [$id]);
            } catch (Exception $e) {
                // Nếu bảng episodes chưa tồn tại, bỏ qua
                $episodes = [];
            }
        }
        
        $this->adminView('movies/edit', [
            'movie' => $movie,
            'categories' => $categories,
            'theaters' => $theaters,
            'existingShowtimes' => $existingShowtimes,
            'episodes' => $episodes,
            'user' => $user,
            'title' => 'Sửa phim',
            'current_page' => 'movies'
        ]);
    }
    
    // Update Movie
    public function moviesUpdate() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/movies');
        }
        
        $id = $_POST['id'] ?? null;
        if (!$id) {
            $this->redirect('admin/movies');
        }
        
        $title = $_POST['title'] ?? '';
        $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
        $level = $_POST['level'] ?? 'Free';
        $duration = !empty($_POST['duration']) ? intval($_POST['duration']) : null;
        $description = $_POST['description'] ?? '';
        $director = $_POST['director'] ?? '';
        $actors = $_POST['actors'] ?? '';
        $video_url = $_POST['video_url'] ?? '';
        $trailer_url = $_POST['trailer_url'] ?? '';
        $thumbnail = $_POST['thumbnail'] ?? '';
        $status = $_POST['status'] ?? 'Sắp chiếu';
        $status_admin = $_POST['status_admin'] ?? 'draft';
        $rating = floatval($_POST['rating'] ?? 0);
        $country = $_POST['country'] ?? '';
        $language = $_POST['language'] ?? '';
        $age_rating = $_POST['age_rating'] ?? '';
        $banner = $_POST['banner'] ?? '';
        $type = $_POST['type'] ?? 'phimle';
        
        if (empty($title)) {
            $_SESSION['error'] = 'Tiêu đề phim không được để trống!';
            $this->redirect('admin/movies/edit&id=' . $id);
        }
        
        try {
            // Lấy thông tin phim cũ để log
            $oldMovie = $db->fetch("SELECT * FROM movies WHERE id = ?", [$id]);
            
            if (!$oldMovie) {
                $_SESSION['error'] = 'Không tìm thấy phim để cập nhật!';
                $this->redirect('admin/movies');
                return;
            }
            
            // Xử lý upload video file (ưu tiên file upload hơn URL)
            if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../data/phim/' . ($type === 'phimbo' ? 'phimbo' : 'phimle') . '/';
                
                // Tạo thư mục nếu chưa tồn tại
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                // Xóa file video cũ nếu có (chỉ nếu là file local, không phải URL)
                if (!empty($oldMovie['video_url']) && strpos($oldMovie['video_url'], 'http') !== 0) {
                    $oldFilePath = __DIR__ . '/../../' . $oldMovie['video_url'];
                    if (file_exists($oldFilePath)) {
                        @unlink($oldFilePath);
                    }
                }
                
                $fileName = preg_replace('/[^a-zA-Z0-9._-]/', '_', $title);
                $fileExtension = pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION);
                $uploadFileName = $fileName . '.' . $fileExtension;
                $uploadPath = $uploadDir . $uploadFileName;
                
                // Kiểm tra nếu file đã tồn tại, thêm số vào tên
                $counter = 1;
                while (file_exists($uploadPath)) {
                    $uploadFileName = $fileName . '_' . $counter . '.' . $fileExtension;
                    $uploadPath = $uploadDir . $uploadFileName;
                    $counter++;
                }
                
                // Upload file
                if (move_uploaded_file($_FILES['video_file']['tmp_name'], $uploadPath)) {
                    // Lưu đường dẫn tương đối
                    $video_url = 'data/phim/' . ($type === 'phimbo' ? 'phimbo' : 'phimle') . '/' . $uploadFileName;
                } else {
                    $_SESSION['error'] = 'Lỗi khi upload file video!';
                    $this->redirect('admin/movies/edit&id=' . $id);
                    return;
                }
            } elseif (empty($video_url) && !empty($oldMovie['video_url'])) {
                // Nếu không có file mới và không có URL mới, giữ nguyên video_url cũ
                $video_url = $oldMovie['video_url'];
            }
            
            // Chuẩn bị dữ liệu
            $updateParams = [
                $title, 
                $category_id, 
                $level, 
                $duration, 
                $description, 
                $director, 
                $actors,
                $video_url, 
                $trailer_url, 
                $thumbnail, 
                $status, 
                $status_admin, 
                $rating,
                $country, 
                $language, 
                $age_rating, 
                $banner, 
                $type, 
                $id
            ];
            
            // Đếm số placeholder và số giá trị để debug
            $sql = "
                UPDATE movies SET
                    title = ?, category_id = ?, level = ?, duration = ?, description = ?,
                    director = ?, actors = ?, video_url = ?, trailer_url = ?, thumbnail = ?,
                    status = ?, status_admin = ?, rating = ?, country = ?, language = ?,
                    age_rating = ?, banner = ?, type = ?
                WHERE id = ?
            ";
            
            $placeholderCount = substr_count($sql, '?');
            $paramCount = count($updateParams);
            
            if ($placeholderCount !== $paramCount) {
                throw new Exception("Lỗi SQL: Số placeholder ($placeholderCount) không khớp với số tham số ($paramCount)");
            }
            
            // Thực hiện cập nhật
            $db->execute($sql, $updateParams);
            
            // Log activity
            AdminMiddleware::logAction(
                $user['id'],
                'Cập nhật phim',
                'Movie',
                'movie',
                $id,
                ['title' => $oldMovie['title'] ?? '', 'status' => $oldMovie['status'] ?? '', 'status_admin' => $oldMovie['status_admin'] ?? ''],
                ['title' => $title, 'status' => $status, 'status_admin' => $status_admin]
            );
            
            // Đặt thông báo thành công (nếu chưa có)
            if (!isset($_SESSION['success'])) {
                $_SESSION['success'] = 'Cập nhật phim thành công!';
            }
            
            // Xử lý lịch chiếu rạp
            // Nếu không phải "Chiếu rạp", xóa tất cả showtimes
            if ($status !== 'Chiếu rạp') {
                $db->execute("DELETE FROM showtimes WHERE movie_id = ?", [$id]);
            } elseif ($status === 'Chiếu rạp') {
                // Chỉ cập nhật showtimes nếu có dữ liệu mới từ form
                if (!empty($_POST['schedule_theater_id']) && !empty($_POST['from_date']) && !empty($_POST['to_date']) && !empty($_POST['showtimes_time'])) {
                    // Xóa tất cả showtimes cũ trước khi tạo mới
                    $db->execute("DELETE FROM showtimes WHERE movie_id = ?", [$id]);
                    $showtimeCount = 0;
                    
                    $theater_id = intval($_POST['schedule_theater_id']);
                    $from_date = $_POST['from_date'];
                    $to_date = $_POST['to_date'];
                    $times = $_POST['showtimes_time'] ?? [];
                    $default_price = floatval($_POST['default_price'] ?? 120000);
                    $screen_id = !empty($_POST['screen_id']) ? intval($_POST['screen_id']) : null;
                    
                    // Validate dates
                    if ($from_date > $to_date) {
                        $_SESSION['error'] = 'Ngày bắt đầu không thể lớn hơn ngày kết thúc!';
                        $this->redirect('admin/movies/edit&id=' . $id);
                        return;
                    }
                    
                    // Tạo suất chiếu cho từng ngày trong khoảng
                    try {
                        $start = new DateTime($from_date);
                        $end = new DateTime($to_date);
                        $end->modify('+1 day'); // Để bao gồm cả ngày cuối
                        
                        $interval = new DateInterval('P1D');
                        $period = new DatePeriod($start, $interval, $end);
                        
                        foreach ($period as $date) {
                            $show_date = $date->format('Y-m-d');
                            
                            // Tạo suất chiếu cho mỗi khung giờ
                            foreach ($times as $show_time) {
                                if (!empty($show_time)) {
                                    $db->execute("
                                        INSERT INTO showtimes (movie_id, theater_id, show_date, show_time, price, screen_id)
                                        VALUES (?, ?, ?, ?, ?, ?)
                                    ", [$id, $theater_id, $show_date, $show_time, $default_price, $screen_id]);
                                    $showtimeCount++;
                                }
                            }
                        }
                        
                        if ($showtimeCount > 0) {
                            $_SESSION['success'] = 'Cập nhật phim thành công! Đã cập nhật ' . $showtimeCount . ' suất chiếu.';
                        } else {
                            $_SESSION['success'] = 'Cập nhật phim thành công! (Đã xóa lịch chiếu cũ)';
                        }
                    } catch (Exception $e) {
                        error_log("Error creating showtimes: " . $e->getMessage());
                        $_SESSION['error'] = 'Lỗi khi tạo lịch chiếu: ' . $e->getMessage();
                        $this->redirect('admin/movies/edit&id=' . $id);
                        return;
                    }
                }
                // Nếu không có dữ liệu mới từ form, giữ nguyên showtimes cũ (không làm gì)
            }
            
            // Nếu là phim bộ, xử lý các tập mới
            if ($type === 'phimbo' && isset($_POST['episodes']) && is_array($_POST['episodes']) && count($_POST['episodes']) > 0) {
                try {
                    // Kiểm tra xem bảng episodes có tồn tại không
                    $db->fetch("SELECT 1 FROM episodes LIMIT 1");
                } catch (Exception $e) {
                    // Nếu bảng chưa tồn tại, bỏ qua phần xử lý episodes
                    error_log("Bảng episodes chưa tồn tại: " . $e->getMessage());
                }
                
                $episodeCount = 0;
                $episodeDir = __DIR__ . '/../../data/phim/phimbo/';
                
                // Đảm bảo thư mục tồn tại
                if (!is_dir($episodeDir)) {
                    mkdir($episodeDir, 0755, true);
                }
                
                foreach ($_POST['episodes'] as $index => $episodeData) {
                    if (!empty($episodeData['episode_number'])) {
                        $episode_number = intval($episodeData['episode_number']);
                        $episode_title = !empty($episodeData['title']) ? $episodeData['title'] : null;
                        $episode_thumbnail = !empty($episodeData['thumbnail']) ? $episodeData['thumbnail'] : null;
                        $episode_duration = !empty($episodeData['duration']) ? intval($episodeData['duration']) : null;
                        $episode_description = !empty($episodeData['description']) ? $episodeData['description'] : null;
                        
                        // Xử lý upload file video
                        $episode_video_url = null;
                        $hasNewVideo = false;
                        
                        // Kiểm tra xem có file video mới được upload không
                        if (isset($_FILES['episodes']['name'][$index]['video_file']) && 
                            $_FILES['episodes']['error'][$index]['video_file'] === UPLOAD_ERR_OK) {
                            
                            $uploadedFile = $_FILES['episodes']['tmp_name'][$index]['video_file'];
                            $originalName = $_FILES['episodes']['name'][$index]['video_file'];
                            
                            // Tạo tên file duy nhất
                            $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
                            $fileName = 'movie_' . $id . '_episode_' . $episode_number . '_' . time() . '.' . $fileExtension;
                            $targetPath = $episodeDir . $fileName;
                            
                            // Upload file
                            if (move_uploaded_file($uploadedFile, $targetPath)) {
                                $episode_video_url = 'data/phim/phimbo/' . $fileName;
                                $hasNewVideo = true;
                            } else {
                                error_log("Failed to upload episode video file: " . $originalName);
                                // Không bỏ qua, vẫn lưu episode nhưng không có video_url
                            }
                        }
                        
                        try {
                            // Kiểm tra xem tập đã tồn tại chưa
                            $existing = $db->fetch("SELECT id, video_url FROM episodes WHERE movie_id = ? AND episode_number = ?", [$id, $episode_number]);
                            
                            if ($existing) {
                                // Nếu không có video mới, giữ nguyên video cũ
                                if (!$hasNewVideo) {
                                    $episode_video_url = $existing['video_url'];
                                }
                                
                                // Cập nhật tập đã tồn tại
                                $db->execute("
                                    UPDATE episodes SET 
                                        title = ?, 
                                        video_url = ?, 
                                        thumbnail = ?, 
                                        duration = ?, 
                                        description = ?
                                    WHERE movie_id = ? AND episode_number = ?
                                ", [
                                    $episode_title, 
                                    $episode_video_url, 
                                    $episode_thumbnail, 
                                    $episode_duration, 
                                    $episode_description,
                                    $id, 
                                    $episode_number
                                ]);
                            } else {
                                // Thêm tập mới (cho phép không có video_url ban đầu, có thể upload sau)
                                $db->execute("
                                    INSERT INTO episodes (movie_id, episode_number, title, video_url, thumbnail, duration, description)
                                    VALUES (?, ?, ?, ?, ?, ?, ?)
                                ", [
                                    $id, 
                                    $episode_number, 
                                    $episode_title, 
                                    $episode_video_url, // Có thể là null
                                    $episode_thumbnail, 
                                    $episode_duration, 
                                    $episode_description
                                ]);
                                $episodeCount++;
                            }
                        } catch (Exception $e) {
                            // Log lỗi để debug
                            error_log("Error inserting/updating episode: " . $e->getMessage());
                            // Bỏ qua nếu có lỗi (có thể do bảng chưa tồn tại hoặc tập đã tồn tại)
                        }
                    }
                }
                
                if ($episodeCount > 0) {
                    $_SESSION['success'] = ($_SESSION['success'] ?? 'Cập nhật phim thành công!') . ' Đã thêm ' . $episodeCount . ' tập mới.';
                }
            }
            
            // Redirect về trang edit để người dùng thấy thay đổi ngay
            $this->redirect('admin/movies/edit&id=' . $id);
        } catch (Exception $e) {
            // Log lỗi chi tiết để debug
            error_log("Error updating movie: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            error_log("POST data: " . print_r($_POST, true));
            
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật phim: ' . $e->getMessage();
            $this->redirect('admin/movies/edit&id=' . $id);
        }
    }
    
    // Delete Episode
    public function moviesDeleteEpisode() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $episode_id = $_GET['id'] ?? null;
        $movie_id = $_GET['movie_id'] ?? null;
        
        if (!$episode_id || !$movie_id) {
            $_SESSION['error'] = 'Thông tin không hợp lệ!';
            $this->redirect('admin/movies');
        }
        
        try {
            $db->execute("DELETE FROM episodes WHERE id = ? AND movie_id = ?", [$episode_id, $movie_id]);
            $_SESSION['success'] = 'Xóa tập phim thành công!';
            
            // Log activity
            AdminMiddleware::logAction(
                $user['id'],
                'Xóa tập phim',
                'Episode',
                'episode',
                $episode_id,
                null,
                ['movie_id' => $movie_id]
            );
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        $this->redirect('admin/movies/edit&id=' . $movie_id);
    }
    
    // Delete Movie
    public function moviesDelete() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('admin/movies');
        }
        
        try {
            // Lấy thông tin phim trước khi xóa để log
            $movie = $db->fetch("SELECT * FROM movies WHERE id = ?", [$id]);
            
            if ($movie) {
                // Log activity
                AdminMiddleware::logAction(
                    $user['id'],
                    'Xóa phim',
                    'Movie',
                    'movie',
                    $id,
                    ['title' => $movie['title'], 'status' => $movie['status'], 'status_admin' => $movie['status_admin'] ?? ''],
                    null
                );
            }
            
            $db->execute("DELETE FROM movies WHERE id = ?", [$id]);
            $_SESSION['success'] = 'Xóa phim thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        $this->redirect('admin/movies');
    }
    
    // Tickets Management
    public function tickets() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $tickets = $db->fetchAll("
            SELECT t.*, u.name as user_name, u.email as user_email, 
                   s.show_date, s.show_time, s.price,
                   m.title as movie_title, th.name as theater_name
            FROM tickets t
            JOIN users u ON t.user_id = u.id
            JOIN showtimes s ON t.showtime_id = s.id
            JOIN movies m ON s.movie_id = m.id
            JOIN theaters th ON s.theater_id = th.id
            ORDER BY t.created_at DESC
        ");
        
        $this->adminView('tickets', [
            'tickets' => $tickets,
            'user' => $user,
            'title' => 'Quản lý vé',
            'current_page' => 'tickets'
        ]);
    }
    
    // View Ticket Details
    public function ticketsView() {
        try {
            $db = Database::getInstance();
            $user = AdminMiddleware::checkAdmin();
            
            $ticket_id = $_GET['id'] ?? null;
            
            if (!$ticket_id) {
                $_SESSION['error'] = 'Không tìm thấy vé!';
                $this->redirect('admin/tickets');
                return;
            }
            
            $ticket = $db->fetch("
                SELECT t.*, 
                       u.name as user_name, u.email as user_email, u.phone as user_phone,
                       s.show_date, s.show_time, s.price as showtime_price,
                       m.title as movie_title, m.poster as movie_poster,
                       th.name as theater_name, th.location as theater_location, th.address as theater_address,
                       ts.screen_name, ts.screen_type
                FROM tickets t
                JOIN users u ON t.user_id = u.id
                JOIN showtimes s ON t.showtime_id = s.id
                JOIN movies m ON s.movie_id = m.id
                JOIN theaters th ON s.theater_id = th.id
                LEFT JOIN theater_screens ts ON s.screen_id = ts.id
                WHERE t.id = ?
            ", [$ticket_id]);
            
            if (!$ticket) {
                $_SESSION['error'] = 'Không tìm thấy vé!';
                $this->redirect('admin/tickets');
                return;
            }
            
            // Lấy các báo cáo của người dùng liên quan đến vé
            $supportTickets = [];
            try {
                $supportTickets = $db->fetchAll("
                    SELECT st.*, 
                           u.name as user_name, u.email as user_email,
                           sub.name as subscription_name,
                           admin.name as assigned_name
                    FROM support_tickets st
                    JOIN users u ON st.user_id = u.id
                    LEFT JOIN subscriptions sub ON u.subscription_id = sub.id
                    LEFT JOIN users admin ON st.assigned_to = admin.id
                    WHERE st.user_id = ? 
                    AND (st.tags LIKE '%Mua bán vé%' OR st.tags LIKE '%Đặt vé%' OR st.message LIKE '%vé%' OR st.message LIKE '%ticket%')
                    ORDER BY st.created_at DESC
                ", [$ticket['user_id']]);
            } catch (Exception $e) {
                error_log("Error fetching support tickets: " . $e->getMessage());
                // Tiếp tục với mảng rỗng nếu có lỗi
            }
            
            $this->adminView('tickets/view', [
                'ticket' => $ticket,
                'supportTickets' => $supportTickets,
                'user' => $user,
                'title' => 'Chi tiết vé #' . $ticket_id,
                'current_page' => 'tickets'
            ]);
        } catch (Exception $e) {
            error_log("Error in ticketsView: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['error'] = 'Có lỗi xảy ra khi tải chi tiết vé: ' . $e->getMessage();
            $this->redirect('admin/tickets');
        }
    }
    
    // Complete Ticket Manually
    public function ticketsComplete() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/tickets');
            return;
        }
        
        $ticket_id = $_POST['ticket_id'] ?? null;
        
        if (!$ticket_id) {
            $_SESSION['error'] = 'Không tìm thấy vé!';
            $this->redirect('admin/tickets');
            return;
        }
        
        try {
            // Lấy thông tin vé
            $ticket = $db->fetch("
                SELECT t.*, s.id as showtime_id, u.id as user_id
                FROM tickets t
                JOIN showtimes s ON t.showtime_id = s.id
                JOIN users u ON t.user_id = u.id
                WHERE t.id = ?
            ", [$ticket_id]);
            
            if (!$ticket) {
                $_SESSION['error'] = 'Không tìm thấy vé!';
                $this->redirect('admin/tickets');
                return;
            }
            
            // Kiểm tra xem vé đã có QR code chưa
            if (empty($ticket['qr_code'])) {
                // Tạo QR code mới
                $qr_code = uniqid('TICKET_') . '_' . $ticket['user_id'] . '_' . $ticket['showtime_id'];
                
                // Cập nhật vé với QR code
                $db->execute("
                    UPDATE tickets 
                    SET qr_code = ? 
                    WHERE id = ?
                ", [$qr_code, $ticket_id]);
                
                $_SESSION['success'] = 'Đã hoàn thành vé thủ công thành công! QR code đã được tạo.';
            } else {
                $_SESSION['success'] = 'Vé đã có QR code. Đã xác nhận hoàn thành vé.';
            }
            
            // Log activity
            AdminMiddleware::logAction(
                $user['id'],
                'Hoàn thành vé thủ công',
                'Ticket',
                'ticket',
                $ticket_id,
                null,
                'Admin đã hoàn thành vé thủ công cho khách hàng'
            );
            
        } catch (Exception $e) {
            error_log("Error completing ticket: " . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra khi hoàn thành vé: ' . $e->getMessage();
        }
        
        $this->redirect('admin/tickets/view?id=' . $ticket_id);
    }
    
    // Theaters Management
    public function theaters() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $theaters = $db->fetchAll("SELECT * FROM theaters ORDER BY name");
        
        $this->adminView('theaters', [
            'theaters' => $theaters,
            'user' => $user,
            'title' => 'Quản lý rạp',
            'current_page' => 'theaters'
        ]);
    }
    
    // Create Theater (Form)
    public function theatersCreate() {
        $user = AdminMiddleware::checkAdmin();
        
        $this->adminView('theaters/create', [
            'user' => $user,
            'title' => 'Thêm rạp mới',
            'current_page' => 'theaters'
        ]);
    }
    
    // Store Theater
    public function theatersStore() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/theaters');
        }
        
        $name = $_POST['name'] ?? '';
        $location = $_POST['location'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $total_screens = intval($_POST['total_screens'] ?? 1);
        
        if (empty($name)) {
            $_SESSION['error'] = 'Tên rạp không được để trống!';
            $this->redirect('admin/theaters/create');
        }
        
        try {
            $db->execute("
                INSERT INTO theaters (name, location, phone, address, total_screens, is_active)
                VALUES (?, ?, ?, ?, ?, 1)
            ", [$name, $location, $phone, $address, $total_screens]);
            
            $theater_id = $db->lastInsertId();
            
            // Log activity
            AdminMiddleware::logAction(
                $user['id'],
                'Thêm rạp',
                'Theater',
                'theater',
                $theater_id,
                null,
                ['name' => $name, 'location' => $location]
            );
            
            $_SESSION['success'] = 'Thêm rạp thành công!';
            $this->redirect('admin/theaters');
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $this->redirect('admin/theaters/create');
        }
    }
    
    // Edit Theater
    public function theatersEdit() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('admin/theaters');
        }
        
        $theater = $db->fetch("SELECT * FROM theaters WHERE id = ?", [$id]);
        if (!$theater) {
            $_SESSION['error'] = 'Không tìm thấy rạp!';
            $this->redirect('admin/theaters');
        }
        
        $this->adminView('theaters/edit', [
            'theater' => $theater,
            'user' => $user,
            'title' => 'Sửa rạp',
            'current_page' => 'theaters'
        ]);
    }
    
    // Update Theater
    public function theatersUpdate() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/theaters');
        }
        
        $id = $_POST['id'] ?? null;
        if (!$id) {
            $this->redirect('admin/theaters');
        }
        
        $name = $_POST['name'] ?? '';
        $location = $_POST['location'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $total_screens = intval($_POST['total_screens'] ?? 1);
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        if (empty($name)) {
            $_SESSION['error'] = 'Tên rạp không được để trống!';
            $this->redirect('admin/theaters/edit&id=' . $id);
        }
        
        try {
            // Lấy thông tin rạp cũ để log
            $oldTheater = $db->fetch("SELECT * FROM theaters WHERE id = ?", [$id]);
            
            $db->execute("
                UPDATE theaters 
                SET name = ?, location = ?, phone = ?, address = ?, total_screens = ?, is_active = ?
                WHERE id = ?
            ", [$name, $location, $phone, $address, $total_screens, $is_active, $id]);
            
            // Log activity
            AdminMiddleware::logAction(
                $user['id'],
                'Cập nhật rạp',
                'Theater',
                'theater',
                $id,
                ['name' => $oldTheater['name'] ?? '', 'location' => $oldTheater['location'] ?? ''],
                ['name' => $name, 'location' => $location]
            );
            
            $_SESSION['success'] = 'Cập nhật rạp thành công!';
            $this->redirect('admin/theaters');
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $this->redirect('admin/theaters/edit&id=' . $id);
        }
    }
    
    // Delete Theater
    public function theatersDelete() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->redirect('admin/theaters');
        }
        
        try {
            // Lấy thông tin rạp trước khi xóa để log
            $theater = $db->fetch("SELECT * FROM theaters WHERE id = ?", [$id]);
            
            if ($theater) {
                // Log activity
                AdminMiddleware::logAction(
                    $user['id'],
                    'Xóa rạp',
                    'Theater',
                    'theater',
                    $id,
                    ['name' => $theater['name'], 'location' => $theater['location'] ?? ''],
                    null
                );
            }
            
            $db->execute("DELETE FROM theaters WHERE id = ?", [$id]);
            $_SESSION['success'] = 'Xóa rạp thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
        }
        
        $this->redirect('admin/theaters');
    }
    
    // Analytics
    public function analytics() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $period = $_GET['period'] ?? 'month'; // day, week, month
        
        // Revenue analytics - Tính từ cả transactions và tickets
        $revenueData = [];
        switch ($period) {
            case 'day':
                // Doanh thu theo ngày (30 ngày gần nhất)
                $revenueData = $db->fetchAll("
                    SELECT DATE(created_at) as period, 
                           SUM(amount) as revenue, 
                           COUNT(*) as transaction_count
                    FROM transactions
                    WHERE status = 'Thành công' 
                      AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                    GROUP BY DATE(created_at)
                    ORDER BY period ASC
                ");
                // Format lại period để hiển thị
                foreach ($revenueData as &$item) {
                    $item['period'] = date('d/m', strtotime($item['period']));
                }
                break;
            case 'week':
                // Doanh thu theo tuần (12 tuần gần nhất)
                $revenueData = $db->fetchAll("
                    SELECT CONCAT('Tuần ', WEEK(created_at), '/', YEAR(created_at)) as period,
                           SUM(amount) as revenue,
                           COUNT(*) as transaction_count
                    FROM transactions
                    WHERE status = 'Thành công' 
                      AND created_at >= DATE_SUB(NOW(), INTERVAL 12 WEEK)
                    GROUP BY YEARWEEK(created_at)
                    ORDER BY YEARWEEK(created_at) ASC
                ");
                break;
            case 'month':
                // Doanh thu theo tháng (12 tháng gần nhất)
                $revenueData = $db->fetchAll("
                    SELECT DATE_FORMAT(created_at, '%m/%Y') as period,
                           SUM(amount) as revenue,
                           COUNT(*) as transaction_count
                    FROM transactions
                    WHERE status = 'Thành công' 
                      AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                    ORDER BY DATE_FORMAT(created_at, '%Y-%m') ASC
                ");
                break;
        }
        
        // Top movies by revenue - Tính từ tickets
        $topMoviesByRevenue = $db->fetchAll("
            SELECT m.id, m.title, 
                   SUM(t.price) as revenue, 
                   COUNT(t.id) as ticket_count
            FROM movies m
            JOIN showtimes s ON m.id = s.movie_id
            JOIN tickets t ON s.id = t.showtime_id
            WHERE t.status = 'Đã đặt'
            GROUP BY m.id, m.title
            ORDER BY revenue DESC
            LIMIT 10
        ");
        
        // Thống kê tổng quan
        $summaryStats = [
            'total_revenue' => $db->fetch("SELECT SUM(amount) as total FROM transactions WHERE status = 'Thành công'")['total'] ?? 0,
            'total_transactions' => $db->fetch("SELECT COUNT(*) as count FROM transactions WHERE status = 'Thành công'")['count'],
            'total_tickets' => $db->fetch("SELECT COUNT(*) as count FROM tickets WHERE status = 'Đã đặt'")['count'],
            'avg_ticket_price' => $db->fetch("SELECT AVG(price) as avg FROM tickets WHERE status = 'Đã đặt'")['avg'] ?? 0,
        ];
        
        // Doanh thu theo phương thức thanh toán
        $revenueByMethod = $db->fetchAll("
            SELECT method, 
                   SUM(amount) as revenue,
                   COUNT(*) as count
            FROM transactions
            WHERE status = 'Thành công'
            GROUP BY method
            ORDER BY revenue DESC
        ");
        
        $this->adminView('analytics', [
            'user' => $user,
            'title' => 'Analytics & Báo cáo',
            'current_page' => 'analytics',
            'period' => $period,
            'revenueData' => $revenueData,
            'topMoviesByRevenue' => $topMoviesByRevenue,
            'summaryStats' => $summaryStats,
            'revenueByMethod' => $revenueByMethod
        ]);
    }
    
    // Support Tickets
    public function support() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $status = $_GET['status'] ?? '';
        $category = $_GET['category'] ?? '';
        
        $where = "1=1";
        $params = [];
        
        if ($status) {
            $where .= " AND st.status = ?";
            $params[] = $status;
        }
        
        if ($category) {
            // Filter theo category dựa trên tags
            if ($category === 'Mua bán vé') {
                // Mua bán vé: có "Đặt vé" hoặc "Mua bán vé" nhưng KHÔNG có "Lỗi"
                $where .= " AND ((st.tags LIKE ? OR st.tags LIKE ?) AND st.tags NOT LIKE ? AND st.tags NOT LIKE ?)";
                $params[] = '%Đặt vé%';
                $params[] = '%Mua bán vé%';
                $params[] = '%Lỗi%';
                $params[] = '%Lỗi thanh toán%';
            } elseif ($category === 'Lỗi mua bán vé') {
                // Lỗi mua bán vé: có "Đặt vé" hoặc "Mua bán vé" VÀ có "Lỗi" hoặc các issue liên quan đến lỗi
                $where .= " AND ((st.tags LIKE ? OR st.tags LIKE ?) AND (st.tags LIKE ? OR st.tags LIKE ? OR st.tags LIKE ? OR st.tags LIKE ? OR st.tags LIKE ?))";
                $params[] = '%Đặt vé%';
                $params[] = '%Mua bán vé%';
                $params[] = '%Lỗi%';
                $params[] = '%Lỗi thanh toán%';
                $params[] = '%Không nhận được vé%';
                $params[] = '%Vấn đề về ghế ngồi%';
                $params[] = '%Hủy/Đổi vé%';
            } elseif ($category === 'Lỗi về phim') {
                // Lỗi về phim: có "Phim" và "Lỗi"
                $where .= " AND (st.tags LIKE ? OR st.tags LIKE ?) AND st.tags LIKE ?";
                $params[] = '%Phim%';
                $params[] = '%phim%';
                $params[] = '%Lỗi%';
            } elseif ($category === 'Đăng nhập/Đăng xuất') {
                // Đăng nhập/Đăng xuất
                $where .= " AND (st.tags LIKE ? OR st.tags LIKE ?)";
                $params[] = '%Đăng nhập%';
                $params[] = '%Đăng xuất%';
            }
        }
        
        // Sắp xếp theo subscription level: Premium > Gold > Silver > Basic > Free
        // Sau đó mới sắp xếp theo ID (mới nhất trước)
        $tickets = $db->fetchAll("
            SELECT st.*, 
                   u.name as user_name, 
                   u.email as user_email,
                   u.subscription_id,
                   s.name as subscription_name,
                   CASE 
                       WHEN s.name = 'Premium' THEN 1
                       WHEN s.name = 'Gold' THEN 2
                       WHEN s.name = 'Silver' THEN 3
                       WHEN s.name = 'Basic' THEN 4
                       WHEN s.name = 'Free' THEN 5
                       ELSE 6
                   END as subscription_priority,
                   a.name as assigned_name
            FROM support_tickets st
            JOIN users u ON st.user_id = u.id
            LEFT JOIN subscriptions s ON u.subscription_id = s.id
            LEFT JOIN users a ON st.assigned_to = a.id
            WHERE $where
            ORDER BY subscription_priority ASC, st.id DESC
        ", $params);
        
        $this->adminView('support', [
            'tickets' => $tickets,
            'status' => $status,
            'category' => $category,
            'user' => $user,
            'title' => 'Hỗ trợ khách hàng',
            'current_page' => 'support'
        ]);
    }
    
    // Support View Detail
    public function supportView() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Không tìm thấy ticket!';
            $this->redirect('admin/support');
            return;
        }
        
        $ticket = $db->fetch("
            SELECT st.*, 
                   u.name as user_name, 
                   u.email as user_email,
                   u.subscription_id,
                   s.name as subscription_name,
                   a.name as assigned_name,
                   a.email as assigned_email
            FROM support_tickets st
            JOIN users u ON st.user_id = u.id
            LEFT JOIN subscriptions s ON u.subscription_id = s.id
            LEFT JOIN users a ON st.assigned_to = a.id
            WHERE st.id = ?
        ", [$id]);
        
        if (!$ticket) {
            $_SESSION['error'] = 'Không tìm thấy ticket!';
            $this->redirect('admin/support');
            return;
        }
        
        // Extract category từ tags
        $category = 'Khác';
        $tags = $ticket['tags'] ?? '';
        if (!empty($tags)) {
            if ((stripos($tags, 'Đặt vé') !== false || stripos($tags, 'Mua bán vé') !== false) && 
                (stripos($tags, 'Lỗi') !== false || stripos($tags, 'Lỗi thanh toán') !== false || 
                 stripos($tags, 'Không nhận được vé') !== false || stripos($tags, 'Vấn đề về ghế ngồi') !== false)) {
                $category = 'Lỗi mua bán vé';
            } elseif (stripos($tags, 'Đặt vé') !== false || stripos($tags, 'Mua bán vé') !== false) {
                $category = 'Mua bán vé';
            } elseif (stripos($tags, 'Phim') !== false && stripos($tags, 'Lỗi') !== false) {
                $category = 'Lỗi về phim';
            } elseif (stripos($tags, 'Đăng nhập') !== false || stripos($tags, 'Đăng xuất') !== false) {
                $category = 'Đăng nhập/Đăng xuất';
            }
        }
        $ticket['category'] = $category;
        
        $this->adminView('support/view', [
            'ticket' => $ticket,
            'user' => $user,
            'title' => 'Chi tiết ticket #' . $id,
            'current_page' => 'support'
        ]);
    }
    
    // Support Update Status
    public function supportUpdateStatus() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/support');
            return;
        }
        
        $ticket_id = $_POST['ticket_id'] ?? null;
        $status = trim($_POST['status'] ?? '');
        
        error_log("supportUpdateStatus called - ticket_id: {$ticket_id}, status: '{$status}'");
        
        if (!$ticket_id || !$status) {
            $_SESSION['error'] = 'Thiếu thông tin! Ticket ID: ' . ($ticket_id ?? 'null') . ', Status: ' . ($status ?? 'null');
            error_log("Missing info - ticket_id: " . ($ticket_id ?? 'null') . ", status: " . ($status ?? 'null'));
            $this->redirect('admin/support');
            return;
        }
        
        // Validate status - kiểm tra cả với và không có khoảng trắng
        $validStatuses = ['Mới', 'Đang xử lý', 'Đã giải quyết', 'Đã đóng'];
        if (!in_array($status, $validStatuses)) {
            $_SESSION['error'] = 'Trạng thái không hợp lệ! Status nhận được: "' . $status . '"';
            error_log("Invalid status: '{$status}'");
            $this->redirect('admin/support');
            return;
        }
        
        try {
            // Lấy thông tin ticket cũ
            $oldTicket = $db->fetch("SELECT * FROM support_tickets WHERE id = ?", [$ticket_id]);
            if (!$oldTicket) {
                $_SESSION['error'] = 'Không tìm thấy ticket!';
                $this->redirect('admin/support');
                return;
            }
            
            // Cập nhật status và assigned_to nếu chuyển sang "Đang xử lý"
            $updateSql = "";
            $updateParams = [];
            
            if ($status === 'Đang xử lý' && empty($oldTicket['assigned_to'])) {
                $updateSql = "UPDATE support_tickets SET status = ?, assigned_to = ? WHERE id = ?";
                $updateParams = [$status, $user['id'], $ticket_id];
            } else {
                $updateSql = "UPDATE support_tickets SET status = ? WHERE id = ?";
                $updateParams = [$status, $ticket_id];
            }
            
            // Thực hiện update - sử dụng PDO trực tiếp để có control tốt hơn
            error_log("Executing SQL: {$updateSql} with params: " . json_encode($updateParams));
            
            $pdo = $db->getConnection();
            $stmt = $pdo->prepare($updateSql);
            
            try {
                $result = $stmt->execute($updateParams);
                
                if (!$result) {
                    $errorInfo = $stmt->errorInfo();
                    error_log("Execute returned false. Error info: " . json_encode($errorInfo));
                    throw new Exception("Lỗi khi cập nhật: " . ($errorInfo[2] ?? 'Unknown error'));
                }
                
                $affectedRows = $stmt->rowCount();
                error_log("Update executed. Affected rows: {$affectedRows}");
                
            } catch (PDOException $e) {
                error_log("PDO Exception: " . $e->getMessage());
                error_log("SQL State: " . $e->getCode());
                throw new Exception("Lỗi database: " . $e->getMessage());
            }
            
            // Verify update ngay lập tức - đợi một chút để đảm bảo commit
            sleep(1); // Đợi 1 giây để đảm bảo database đã commit
            
            $updatedTicket = $db->fetch("SELECT status, assigned_to FROM support_tickets WHERE id = ?", [$ticket_id]);
            if (!$updatedTicket) {
                throw new Exception("Không tìm thấy ticket sau khi cập nhật!");
            }
            
            error_log("Ticket after update - Status: '{$updatedTicket['status']}', Expected: '{$status}'");
            error_log("Status comparison - Equal: " . ($updatedTicket['status'] === $status ? 'YES' : 'NO'));
            
            if ($updatedTicket['status'] !== $status) {
                error_log("Status mismatch! Expected: '{$status}' (length: " . strlen($status) . "), Got: '{$updatedTicket['status']}' (length: " . strlen($updatedTicket['status']) . ")");
                error_log("Byte comparison: " . bin2hex($status) . " vs " . bin2hex($updatedTicket['status']));
                throw new Exception("Không thể cập nhật trạng thái ticket! Status hiện tại: " . $updatedTicket['status']);
            }
            
            
            // Log activity
            try {
                AdminMiddleware::logAction(
                    $user['id'],
                    'Cập nhật trạng thái ticket',
                    'Support',
                    'support_ticket',
                    $ticket_id,
                    ['status' => $oldTicket['status']],
                    ['status' => $status]
                );
            } catch (Exception $e) {
                // Log lỗi nhưng không dừng quá trình
                error_log("Error logging action: " . $e->getMessage());
            }
            
            $_SESSION['success'] = 'Đã cập nhật trạng thái ticket thành công!';
            
            // Force reload bằng cách thêm timestamp
            $timestamp = time();
            
            // Redirect về view nếu có referer
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            if (strpos($referer, 'support/view') !== false) {
                $this->redirect('?route=admin/support/view&id=' . $ticket_id . '&_t=' . $timestamp);
            } else {
                // Giữ lại filter nếu có và thêm timestamp để force reload
                $redirectUrl = '?route=admin/support&_t=' . $timestamp;
                $queryParams = [];
                if (!empty($_GET['status'])) {
                    $queryParams[] = 'status=' . urlencode($_GET['status']);
                }
                if (!empty($_GET['category'])) {
                    $queryParams[] = 'category=' . urlencode($_GET['category']);
                }
                if (!empty($queryParams)) {
                    $redirectUrl .= '&' . implode('&', $queryParams);
                }
                $this->redirect($redirectUrl);
            }
        } catch (Exception $e) {
            error_log("Error updating support ticket status: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $_SESSION['error'] = 'Có lỗi xảy ra khi cập nhật trạng thái: ' . $e->getMessage();
            $this->redirect('admin/support');
        }
    }
    
    // Support Reply
    public function supportReply() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/support');
            return;
        }
        
        $ticket_id = $_POST['ticket_id'] ?? null;
        $reply_message = trim($_POST['reply_message'] ?? '');
        
        if (!$ticket_id || empty($reply_message)) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin!';
            $this->redirect('admin/support');
            return;
        }
        
        // Lấy thông tin ticket và user
        $ticket = $db->fetch("
            SELECT st.*, u.name as user_name, u.email as user_email
            FROM support_tickets st
            JOIN users u ON st.user_id = u.id
            WHERE st.id = ?
        ", [$ticket_id]);
        
        if (!$ticket) {
            $_SESSION['error'] = 'Không tìm thấy ticket!';
            $this->redirect('admin/support');
            return;
        }
        
        // Gửi email
        require_once __DIR__ . '/../../core/Email.php';
        $email = new Email();
        
        $to = $ticket['user_email'];
        $subject = "Phản hồi từ CineHub - Ticket #" . $ticket_id;
        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #e50914, #c40812); color: white; padding: 30px 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .header h2 { margin: 0; font-size: 24px; }
                .content { background: #f9f9f9; padding: 30px 20px; border: 1px solid #ddd; border-top: none; }
                .reply-box { background: white; padding: 20px; border-left: 4px solid #e50914; margin: 20px 0; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; padding: 20px; background: #f5f5f5; border-radius: 0 0 10px 10px; }
                .btn { display: inline-block; padding: 12px 30px; background: #e50914; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>🎬 CineHub - Hỗ trợ khách hàng</h2>
                </div>
                <div class='content'>
                    <p>Xin chào <strong>" . htmlspecialchars($ticket['user_name']) . "</strong>,</p>
                    <p>Cảm ơn bạn đã liên hệ với chúng tôi về vấn đề:</p>
                    <p style='font-weight: bold; color: #e50914; font-size: 16px;'>" . htmlspecialchars($ticket['subject']) . "</p>
                    <p>Phản hồi của chúng tôi:</p>
                    <div class='reply-box'>
                        " . nl2br(htmlspecialchars($reply_message)) . "
                    </div>
                    <p>Nếu bạn cần hỗ trợ thêm, vui lòng liên hệ lại với chúng tôi qua hệ thống hỗ trợ.</p>
                    <p>Trân trọng,<br><strong>Đội ngũ CineHub</strong></p>
                </div>
                <div class='footer'>
                    <p><strong>Ticket ID:</strong> #" . $ticket_id . "</p>
                    <p>Email này được gửi tự động từ hệ thống CineHub</p>
                    <p style='margin-top: 10px; font-size: 11px; color: #999;'>© " . date('Y') . " CineHub. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $mailSent = $email->send($to, $subject, $message, true);
        
        if ($mailSent) {
            // Cập nhật status ticket thành "Đang xử lý" nếu đang là "Mới"
            if ($ticket['status'] === 'Mới') {
                $db->execute("UPDATE support_tickets SET status = 'Đang xử lý', assigned_to = ? WHERE id = ?", 
                    [$user['id'], $ticket_id]);
            }
            
            $_SESSION['success'] = 'Đã gửi phản hồi thành công đến email: ' . $ticket['user_email'];
            error_log("Support reply sent successfully to: " . $ticket['user_email']);
        } else {
            $_SESSION['error'] = 'Không thể gửi email. Vui lòng kiểm tra cấu hình email server trong config.php!';
            error_log("Failed to send support reply email to: " . $ticket['user_email']);
        }
        
        $this->redirect('admin/support');
    }
    
    // Logs
    public function logs() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $module = $_GET['module'] ?? '';
        $action_filter = $_GET['action'] ?? '';
        $page = intval($_GET['page'] ?? 1);
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $sql = "SELECT al.*, u.name as user_name, u.email as user_email
                FROM admin_logs al
                LEFT JOIN users u ON al.user_id = u.id
                WHERE 1=1";
        $params = [];
        
        if ($module) {
            $sql .= " AND al.module = ?";
            $params[] = $module;
        }
        
        if ($action_filter) {
            $sql .= " AND al.action LIKE ?";
            $params[] = "%$action_filter%";
        }
        
        $sql .= " ORDER BY al.created_at DESC LIMIT $limit OFFSET $offset";
        
        $logs = $db->fetchAll($sql, $params);
        
        // Đếm tổng số log
        $countSql = "SELECT COUNT(*) as count FROM admin_logs WHERE 1=1";
        $countParams = [];
        
        if ($module) {
            $countSql .= " AND module = ?";
            $countParams[] = $module;
        }
        
        if ($action_filter) {
            $countSql .= " AND action LIKE ?";
            $countParams[] = "%$action_filter%";
        }
        
        $total = $db->fetch($countSql, $countParams)['count'];
        $total_pages = ceil($total / $limit);
        
        $this->adminView('logs', [
            'logs' => $logs,
            'user' => $user,
            'title' => 'Lịch sử hoạt động',
            'current_page' => 'logs',
            'module' => $module,
            'action' => $action_filter,
            'page' => $page,
            'total_pages' => $total_pages,
            'total' => $total,
            'limit' => $limit
        ]);
    }
    
    // Scan episodes from folder
    public function moviesScanEpisodes() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        $episodeDir = __DIR__ . '/../../data/phim/phimbo/';
        
        // Scan folders
        $folders = [];
        if (is_dir($episodeDir)) {
            $items = scandir($episodeDir);
            foreach ($items as $item) {
                if ($item !== '.' && $item !== '..' && is_dir($episodeDir . $item)) {
                    $folderPath = $episodeDir . $item;
                    $files = [];
                    
                    // Scan video files in folder
                    $videoFiles = glob($folderPath . '/*.{mp4,avi,mkv,mov,wmv,flv}', GLOB_BRACE);
                    foreach ($videoFiles as $file) {
                        $files[] = [
                            'name' => basename($file),
                            'path' => 'data/phim/phimbo/' . $item . '/' . basename($file),
                            'size' => filesize($file),
                            'modified' => filemtime($file)
                        ];
                    }
                    
                    if (!empty($files)) {
                        $folders[] = [
                            'name' => $item,
                            'files' => $files,
                            'count' => count($files)
                        ];
                    }
                }
            }
        }
        
        // Lấy danh sách phim bộ
        $movies = $db->fetchAll("SELECT id, title, type FROM movies WHERE type = 'phimbo' ORDER BY title");
        
        $this->adminView('movies/scan-episodes', [
            'folders' => $folders,
            'movies' => $movies,
            'user' => $user,
            'title' => 'Import tập phim từ folder',
            'current_page' => 'movies'
        ]);
    }
    
    // Import episodes from folder
    public function moviesImportEpisodes() {
        $db = Database::getInstance();
        $user = AdminMiddleware::checkAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('admin/movies/scanEpisodes');
        }
        
        $movie_id = intval($_POST['movie_id'] ?? 0);
        $folder_name = $_POST['folder_name'] ?? '';
        $files = $_POST['files'] ?? [];
        
        if (!$movie_id || !$folder_name || empty($files)) {
            $_SESSION['error'] = 'Vui lòng chọn phim và các file cần import!';
            $this->redirect('admin/movies/scanEpisodes');
            return;
        }
        
        // Kiểm tra phim có tồn tại không
        $movie = $db->fetch("SELECT id, title FROM movies WHERE id = ? AND type = 'phimbo'", [$movie_id]);
        if (!$movie) {
            $_SESSION['error'] = 'Không tìm thấy phim bộ!';
            $this->redirect('admin/movies/scanEpisodes');
            return;
        }
        
        $imported = 0;
        $updated = 0;
        
        try {
            foreach ($files as $fileData) {
                // Kiểm tra xem có checkbox import được chọn không
                if (!isset($fileData['import']) || $fileData['import'] != '1') {
                    continue;
                }
                
                if (!isset($fileData['path']) || !isset($fileData['episode_number'])) {
                    continue;
                }
                
                $file_path = $fileData['path'];
                $episode_number = intval($fileData['episode_number']);
                
                // Kiểm tra file có tồn tại không
                $fullPath = __DIR__ . '/../../' . $file_path;
                if (!file_exists($fullPath)) {
                    continue;
                }
                
                // Kiểm tra episode đã tồn tại chưa
                $existing = $db->fetch("SELECT id, video_url FROM episodes WHERE movie_id = ? AND episode_number = ?", 
                    [$movie_id, $episode_number]);
                
                if ($existing) {
                    // Update episode hiện có (chỉ nếu chưa có video_url)
                    if (empty($existing['video_url'])) {
                        $db->execute("
                            UPDATE episodes SET video_url = ? WHERE movie_id = ? AND episode_number = ?
                        ", [$file_path, $movie_id, $episode_number]);
                        $updated++;
                    }
                } else {
                    // Thêm episode mới
                    $episode_title = !empty($fileData['title']) ? $fileData['title'] : "Tập $episode_number";
                    
                    $db->execute("
                        INSERT INTO episodes (movie_id, episode_number, title, video_url)
                        VALUES (?, ?, ?, ?)
                    ", [$movie_id, $episode_number, $episode_title, $file_path]);
                    $imported++;
                }
            }
            
            AdminMiddleware::logAction($user['id'], 'movies', 'import_episodes', 
                "Đã import $imported tập mới và cập nhật $updated tập cho phim: " . $movie['title']);
            
            $_SESSION['success'] = "Đã import $imported tập mới và cập nhật $updated tập cho phim: " . $movie['title'];
            
        } catch (Exception $e) {
            error_log("Error importing episodes: " . $e->getMessage());
            $_SESSION['error'] = 'Có lỗi xảy ra khi import: ' . $e->getMessage();
        }
        
        $this->redirect('admin/movies/scanEpisodes');
    }
}
?>

