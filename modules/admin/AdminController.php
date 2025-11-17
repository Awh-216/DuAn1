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
                                continue; // Bỏ qua tập này nếu upload thất bại
                            }
                        }
                        
                        // Chỉ lưu nếu có video URL
                        if ($episode_video_url) {
                            try {
                                $db->execute("
                                    INSERT INTO episodes (movie_id, episode_number, title, video_url, thumbnail, duration, description)
                                    VALUES (?, ?, ?, ?, ?, ?, ?)
                                ", [
                                    $movie_id, 
                                    $episode_number, 
                                    $episode_title, 
                                    $episode_video_url, 
                                    $episode_thumbnail, 
                                    $episode_duration, 
                                    $episode_description
                                ]);
                                $episodeCount++;
                            } catch (Exception $e) {
                                // Log lỗi để debug
                                error_log("Error inserting episode: " . $e->getMessage());
                                // Nếu lỗi do bảng chưa tồn tại, thông báo rõ ràng
                                if (strpos($e->getMessage(), "doesn't exist") !== false || 
                                    strpos($e->getMessage(), "Unknown table") !== false) {
                                    throw new Exception("Bảng 'episodes' chưa được tạo. Vui lòng chạy file create_episodes_table.sql trong phpMyAdmin trước!");
                                }
                                // Bỏ qua nếu tập đã tồn tại hoặc có lỗi khác
                            }
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
            }
            
            if ($status === 'Chiếu rạp') {
                // Xóa tất cả showtimes cũ trước khi tạo mới
                $db->execute("DELETE FROM showtimes WHERE movie_id = ?", [$id]);
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
                } else {
                    $_SESSION['success'] = 'Cập nhật phim thành công! (Đã xóa lịch chiếu cũ)';
                }
            } else {
                // Nếu không còn là "Chiếu rạp", xóa tất cả showtimes
                $db->execute("DELETE FROM showtimes WHERE movie_id = ?", [$id]);
                $_SESSION['success'] = 'Cập nhật phim thành công!';
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
                                continue; // Bỏ qua tập này nếu upload thất bại
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
                                // Chỉ thêm tập mới nếu có video
                                if ($episode_video_url) {
                                    $db->execute("
                                        INSERT INTO episodes (movie_id, episode_number, title, video_url, thumbnail, duration, description)
                                        VALUES (?, ?, ?, ?, ?, ?, ?)
                                    ", [
                                        $id, 
                                        $episode_number, 
                                        $episode_title, 
                                        $episode_video_url, 
                                        $episode_thumbnail, 
                                        $episode_duration, 
                                        $episode_description
                                    ]);
                                    $episodeCount++;
                                }
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
            SELECT t.*, u.name as user_name, u.email, 
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
        
        $tickets = $db->fetchAll("
            SELECT st.*, u.name as user_name, u.email
            FROM support_tickets st
            JOIN users u ON st.user_id = u.id
            ORDER BY st.created_at DESC
        ");
        
        $this->adminView('support', [
            'tickets' => $tickets,
            'user' => $user,
            'title' => 'Hỗ trợ',
            'current_page' => 'support'
        ]);
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
}
?>

