<?php
/**
 * File test kết nối database
 * Chạy file này để kiểm tra kết nối database
 */

// Cấu hình database
$host = 'localhost';
$dbname = 'cinehub';
$username = 'root';
$password = '';

// Đọc password từ config.php nếu có
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
    if (defined('DB_PASS')) {
        $password = DB_PASS;
    }
}

echo "<h2>Kiểm tra kết nối database</h2>";

// Test 1: Kiểm tra MySQL đang chạy
echo "<h3>1. Kiểm tra MySQL đang chạy...</h3>";
try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✓ MySQL đang chạy</p>";
} catch(PDOException $e) {
    echo "<p style='color: red;'>✗ Lỗi: " . $e->getMessage() . "</p>";
    echo "<p>Vui lòng kiểm tra:</p>";
    echo "<ul>";
    echo "<li>XAMPP đã được khởi động chưa?</li>";
    echo "<li>MySQL service đã được bật chưa?</li>";
    echo "</ul>";
    die();
}

// Test 2: Kiểm tra database tồn tại
echo "<h3>2. Kiểm tra database '$dbname' tồn tại...</h3>";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✓ Database '$dbname' tồn tại</p>";
} catch(PDOException $e) {
    echo "<p style='color: orange;'>⚠ Database '$dbname' chưa tồn tại</p>";
    echo "<p>Đang tạo database...</p>";
    
    try {
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        echo "<p style='color: green;'>✓ Đã tạo database '$dbname'</p>";
        echo "<p style='color: orange;'>⚠ Bạn cần chạy file database.sql để tạo các bảng</p>";
    } catch(PDOException $e2) {
        echo "<p style='color: red;'>✗ Không thể tạo database: " . $e2->getMessage() . "</p>";
        die();
    }
}

// Test 3: Kiểm tra các bảng
echo "<h3>3. Kiểm tra các bảng...</h3>";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $tables = ['users', 'subscriptions', 'categories', 'movies', 'theaters', 'showtimes', 'tickets', 'watch_history', 'reviews'];
    $existingTables = [];
    
    $stmt = $pdo->query("SHOW TABLES");
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        if (in_array($table, $result)) {
            echo "<p style='color: green;'>✓ Bảng '$table' tồn tại</p>";
            $existingTables[] = $table;
        } else {
            echo "<p style='color: orange;'>⚠ Bảng '$table' chưa tồn tại</p>";
        }
    }
    
    if (count($existingTables) < count($tables)) {
        echo "<p style='color: orange;'><strong>⚠ Bạn cần chạy file database.sql để tạo các bảng còn thiếu</strong></p>";
    } else {
        echo "<p style='color: green;'><strong>✓ Tất cả các bảng đã được tạo</strong></p>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>✗ Lỗi: " . $e->getMessage() . "</p>";
}

// Test 4: Kiểm tra kết nối từ config.php
echo "<h3>4. Kiểm tra kết nối từ config.php...</h3>";
try {
    require_once 'config.php';
    $db = Database::getInstance();
    $connection = $db->getConnection();
    echo "<p style='color: green;'>✓ Kết nối từ config.php thành công</p>";
    
    // Test query
    $result = $db->fetch("SELECT 1 as test");
    if ($result) {
        echo "<p style='color: green;'>✓ Query test thành công</p>";
    }
} catch(Exception $e) {
    echo "<p style='color: red;'>✗ Lỗi: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h3>Hướng dẫn:</h3>";
echo "<ol>";
echo "<li>Nếu database chưa tồn tại, file này đã tự động tạo</li>";
echo "<li>Nếu các bảng chưa tồn tại, bạn cần:</li>";
echo "<ul>";
echo "<li>Mở phpMyAdmin (http://localhost/phpmyadmin)</li>";
echo "<li>Chọn database 'cinehub'</li>";
echo "<li>Vào tab 'SQL'</li>";
echo "<li>Copy toàn bộ nội dung file database.sql và paste vào</li>";
echo "<li>Click 'Go' để chạy</li>";
echo "</ul>";
echo "<li>Sau khi tạo xong database và các bảng, truy cập: <a href='index.php'>index.php</a></li>";
echo "</ol>";

// Kiểm tra lỗi password
if (isset($e) && (strpos($e->getMessage(), "Access denied") !== false)) {
    echo "<hr>";
    echo "<h3 style='color: red;'>⚠ Lỗi Password MySQL!</h3>";
    echo "<p>Bạn đang gặp lỗi: <strong>Access denied for user 'root'@'localhost'</strong></p>";
    echo "<p>Vui lòng truy cập: <a href='fix-mysql-password.php' style='color: #e50914; font-weight: bold;'>fix-mysql-password.php</a> để tự động fix lỗi này.</p>";
    echo "<p>Hoặc cập nhật thủ công password trong file <code>config.php</code></p>";
}
?>

