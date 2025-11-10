<?php
/**
 * File kiểm tra XAMPP services
 */
echo "<h2>Kiểm tra XAMPP Services</h2>";
echo "<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
.success { color: green; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0; }
.error { color: red; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0; }
.info { color: #0c5460; padding: 15px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 5px; margin: 10px 0; }
pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
</style>";

// Kiểm tra Apache
echo "<h3>1. Kiểm tra Apache (Port 80)</h3>";
$apache_port = 80;
$apache_connection = @fsockopen('localhost', $apache_port, $errno, $errstr, 1);
if ($apache_connection) {
    echo "<div class='success'>✓ Apache đang chạy trên port $apache_port</div>";
    fclose($apache_connection);
} else {
    echo "<div class='error'>✗ Apache không chạy trên port $apache_port</div>";
    echo "<div class='info'>";
    echo "Vui lòng:<br>";
    echo "1. Mở XAMPP Control Panel<br>";
    echo "2. Click <strong>Start</strong> cho Apache<br>";
    echo "3. Nếu lỗi port đã được sử dụng, có thể đổi port trong httpd.conf<br>";
    echo "</div>";
}

// Kiểm tra MySQL
echo "<h3>2. Kiểm tra MySQL (Port 3306)</h3>";
$mysql_port = 3306;
$mysql_connection = @fsockopen('localhost', $mysql_port, $errno, $errstr, 1);
if ($mysql_connection) {
    echo "<div class='success'>✓ MySQL đang chạy trên port $mysql_port</div>";
    fclose($mysql_connection);
    
    // Thử kết nối
    try {
        $pdo = new PDO("mysql:host=localhost", "root", "");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<div class='success'>✓ Có thể kết nối MySQL với user 'root' và password rỗng</div>";
    } catch(PDOException $e) {
        echo "<div class='error'>✗ Không thể kết nối MySQL: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='error'>✗ MySQL không chạy trên port $mysql_port</div>";
    echo "<div class='info'>";
    echo "Vui lòng:<br>";
    echo "1. Mở XAMPP Control Panel<br>";
    echo "2. Click <strong>Start</strong> cho MySQL<br>";
    echo "3. Nếu lỗi port đã được sử dụng, có thể đổi port trong my.ini<br>";
    echo "4. Hoặc tắt service MySQL khác đang chạy<br>";
    echo "</div>";
}

// Kiểm tra phpMyAdmin
echo "<h3>3. Kiểm tra phpMyAdmin</h3>";
$phpmyadmin_url = 'http://localhost/phpmyadmin';
$ch = curl_init($phpmyadmin_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_NOBODY, true);
$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200) {
    echo "<div class='success'>✓ phpMyAdmin có thể truy cập</div>";
    echo "<div class='info'><a href='$phpmyadmin_url' target='_blank'>Mở phpMyAdmin</a></div>";
} else {
    echo "<div class='error'>✗ phpMyAdmin không thể truy cập (HTTP Code: $http_code)</div>";
    echo "<div class='info'>";
    echo "Có thể do:<br>";
    echo "1. Apache chưa Start<br>";
    echo "2. File phpMyAdmin bị thiếu hoặc lỗi<br>";
    echo "3. Port Apache không phải 80<br>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>Hướng dẫn khắc phục</h3>";
echo "<div class='info'>";
echo "<strong>Nếu MySQL không Start:</strong><br>";
echo "1. Mở XAMPP Control Panel<br>";
echo "2. Click <strong>Start</strong> cho MySQL<br>";
echo "3. Nếu báo lỗi port đã được sử dụng:<br>";
echo "   - Mở file: <code>C:\\xampp\\mysql\\bin\\my.ini</code><br>";
echo "   - Tìm dòng <code>port=3306</code> và đổi thành port khác (ví dụ: 3307)<br>";
echo "   - Lưu file và Start lại MySQL<br>";
echo "4. Nếu vẫn lỗi, có thể cần tắt service MySQL khác đang chạy<br>";
echo "<br>";
echo "<strong>Nếu Apache không Start:</strong><br>";
echo "1. Mở XAMPP Control Panel<br>";
echo "2. Click <strong>Start</strong> cho Apache<br>";
echo "3. Nếu báo lỗi port đã được sử dụng:<br>";
echo "   - Mở file: <code>C:\\xampp\\apache\\conf\\httpd.conf</code><br>";
echo "   - Tìm dòng <code>Listen 80</code> và đổi thành port khác (ví dụ: 8080)<br>";
echo "   - Lưu file và Start lại Apache<br>";
echo "</div>";
?>

