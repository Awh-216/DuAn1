<?php
/**
 * File kiểm tra port MySQL
 */
echo "<h2>Kiểm tra Port MySQL</h2>";
echo "<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
.success { color: green; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0; }
.error { color: red; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0; }
.info { color: #0c5460; padding: 15px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 5px; margin: 10px 0; }
pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
</style>";

// Đọc port từ phpMyAdmin config
$config_file = 'C:\\xampp\\phpMyAdmin\\config.inc.php';
$phpmyadmin_port = 3306; // Mặc định

if (file_exists($config_file)) {
    $config_content = file_get_contents($config_file);
    if (preg_match("/\\\$cfg\\['Servers'\\]\\[\\\$i\\]\\['port'\\]\\s*=\\s*(\\d+);/", $config_content, $matches)) {
        $phpmyadmin_port = $matches[1];
        echo "<div class='info'>";
        echo "✓ Tìm thấy port MySQL trong phpMyAdmin config: <strong>$phpmyadmin_port</strong><br>";
        echo "File: <code>$config_file</code>";
        echo "</div>";
    } else {
        echo "<div class='info'>";
        echo "⚠ Không tìm thấy port trong config, sử dụng port mặc định: <strong>3306</strong>";
        echo "</div>";
    }
} else {
    echo "<div class='error'>✗ Không tìm thấy file config.inc.php</div>";
}

// Thử các port phổ biến
$ports_to_try = [3306, 3307, 3308, 3309];
$working_port = null;
$username = 'root';
$password = '';

echo "<h3>Đang thử kết nối với các port...</h3>";

foreach ($ports_to_try as $port) {
    $connection = @fsockopen('localhost', $port, $errno, $errstr, 1);
    if ($connection) {
        echo "<div class='success'>✓ Port $port đang mở</div>";
        fclose($connection);
        
        // Thử kết nối MySQL
        try {
            $pdo = new PDO("mysql:host=localhost;port=$port", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "<div class='success'>✓ Có thể kết nối MySQL trên port <strong>$port</strong></div>";
            $working_port = $port;
            break;
        } catch(PDOException $e) {
            echo "<div class='error'>✗ Không thể kết nối MySQL trên port $port: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='error'>✗ Port $port không mở</div>";
    }
}

if ($working_port) {
    echo "<hr>";
    echo "<h3>Kết quả</h3>";
    echo "<div class='success'>";
    echo "<strong>Port MySQL đang hoạt động: $working_port</strong><br><br>";
    
    if ($working_port != $phpmyadmin_port) {
        echo "⚠ <strong>Lưu ý:</strong> Port trong phpMyAdmin config ($phpmyadmin_port) khác với port đang hoạt động ($working_port)!<br>";
        echo "Cần cập nhật một trong hai:<br><br>";
        
        echo "<strong>Cách 1: Cập nhật core/Database.php</strong><br>";
        echo "Mở file <code>core/Database.php</code> và sửa dòng 8:<br>";
        echo "<pre>private \$port = $working_port; // Port MySQL</pre><br>";
        
        echo "<strong>Cách 2: Cập nhật phpMyAdmin config.inc.php</strong><br>";
        echo "Mở file <code>C:\\xampp\\phpMyAdmin\\config.inc.php</code> và sửa dòng 28:<br>";
        echo "<pre>\$cfg['Servers'][\$i]['port'] = $working_port;</pre>";
    } else {
        echo "✓ Port khớp với phpMyAdmin config!<br>";
        echo "Cần cập nhật file <code>core/Database.php</code> để dùng port $working_port<br>";
        echo "Mở file và sửa dòng 8:<br>";
        echo "<pre>private \$port = $working_port; // Port MySQL</pre>";
    }
    echo "</div>";
} else {
    echo "<hr>";
    echo "<h3>Không tìm thấy port MySQL hoạt động</h3>";
    echo "<div class='error'>";
    echo "Có thể MySQL chưa được khởi động hoặc đang dùng port khác.<br><br>";
    echo "<strong>Giải pháp:</strong><br>";
    echo "1. Mở XAMPP Control Panel<br>";
    echo "2. Kiểm tra MySQL đã Start chưa?<br>";
    echo "3. Nếu chưa, click <strong>Start</strong> cho MySQL<br>";
    echo "4. Nếu Start bị lỗi, kiểm tra log trong XAMPP Control Panel<br>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='test-connection.php'>Test kết nối database</a> | <a href='check-xampp.php'>Kiểm tra XAMPP</a></p>";
?>

