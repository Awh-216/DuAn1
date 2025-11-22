<?php
// Cấu hình database
define('DB_HOST', 'localhost');
define('DB_NAME', 'cinehub');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Cấu hình ứng dụng
define('BASE_URL', 'http://localhost/DuAn1/');
define('SITE_NAME', 'CineHub');

// Cấu hình Email SMTP
// Để gửi email thật, cần cấu hình thông tin SMTP dưới đây
// Xem file HUONG_DAN_CAU_HINH_EMAIL.md để biết chi tiết
// Ví dụ với Gmail:
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', ''); // Email đầy đủ của bạn (VD: nguyenvana@gmail.com) - BẮT BUỘC phải có @ và domain
define('SMTP_PASSWORD', ''); // App Password của Gmail (16 ký tự, không phải mật khẩu thường)
define('SMTP_ENCRYPTION', 'tls'); // 'tls' hoặc 'ssl'
define('SMTP_FROM_EMAIL', 'noreply@cinehub.com'); // Email gửi đi (thường giống SMTP_USERNAME)
define('SMTP_FROM_NAME', 'CineHub'); // Tên người gửi

// Khởi động session
session_start();

// Autoload classes
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/models/' . $class . '.php',
        __DIR__ . '/controllers/' . $class . '.php',
        __DIR__ . '/core/' . $class . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Kết nối database
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Hiển thị lỗi chi tiết hơn
    $error_message = "Lỗi kết nối database: " . $e->getMessage();
    
    // Kiểm tra nếu database chưa tồn tại
    if (strpos($e->getMessage(), "Unknown database") !== false) {
        $error_message .= "<br><br><strong>Database '" . DB_NAME . "' chưa tồn tại!</strong>";
        $error_message .= "<br>Vui lòng:";
        $error_message .= "<br>1. Mở phpMyAdmin (http://localhost/phpmyadmin)";
        $error_message .= "<br>2. Tạo database tên 'cinehub'";
        $error_message .= "<br>3. Hoặc chạy file database.sql để tự động tạo";
        $error_message .= "<br>4. Hoặc truy cập <a href='test-db.php'>test-db.php</a> để kiểm tra và tự động tạo";
    }
    
    // Kiểm tra nếu MySQL chưa chạy
    if (strpos($e->getMessage(), "Connection refused") !== false || 
        strpos($e->getMessage(), "Access denied") !== false) {
        $error_message .= "<br><br><strong>Không thể kết nối đến MySQL!</strong>";
        $error_message .= "<br>Vui lòng kiểm tra:";
        $error_message .= "<br>1. XAMPP đã được khởi động chưa?";
        $error_message .= "<br>2. MySQL service đã được bật chưa?";
        $error_message .= "<br>3. Thông tin đăng nhập (username/password) có đúng không?";
    }
    
    die($error_message);
}
?>
