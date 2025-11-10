<?php
/**
 * File kiểm tra và fix lỗi phpMyAdmin
 */
echo "<h2>Kiểm tra kết nối phpMyAdmin</h2>";
echo "<style>
body { font-family: Arial, sans-serif; max-width: 900px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
.success { color: green; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0; }
.error { color: red; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0; }
.warning { color: #856404; padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; margin: 10px 0; }
.info { color: #0c5460; padding: 15px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 5px; margin: 10px 0; }
pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; border: 1px solid #dee2e6; }
code { background: #f8f9fa; padding: 2px 6px; border-radius: 3px; }
</style>";

// Test 1: Kiểm tra MySQL service
echo "<h3>1. Kiểm tra MySQL Service</h3>";
$mysql_running = false;

// Thử kết nối trực tiếp
$host = 'localhost';
$username = 'root';
$passwords_to_try = ['', 'root', 'password', '123456'];

foreach ($passwords_to_try as $password) {
    try {
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<div class='success'>✓ MySQL đang chạy! Password: <strong>" . ($password ?: '(rỗng)') . "</strong></div>";
        $mysql_running = true;
        $working_password = $password;
        break;
    } catch(PDOException $e) {
        // Tiếp tục thử password khác
    }
}

if (!$mysql_running) {
    echo "<div class='error'>✗ MySQL không chạy hoặc không thể kết nối</div>";
    echo "<div class='info'>";
    echo "<strong>Giải pháp:</strong><br>";
    echo "1. Mở <strong>XAMPP Control Panel</strong><br>";
    echo "2. Kiểm tra nút <strong>MySQL</strong> - nếu chưa Start, click <strong>Start</strong><br>";
    echo "3. Nếu Start bị lỗi, có thể port 3306 đã bị chiếm<br>";
    echo "4. Kiểm tra Windows Services: Nhấn Win+R, gõ <code>services.msc</code>, tìm MySQL và Start nếu cần<br>";
    echo "</div>";
    die();
}

// Test 2: Kiểm tra port MySQL
echo "<h3>2. Kiểm tra Port MySQL</h3>";
$port = 3306;
$connection = @fsockopen($host, $port, $errno, $errstr, 1);
if ($connection) {
    echo "<div class='success'>✓ Port $port đang mở và có thể kết nối</div>";
    fclose($connection);
} else {
    echo "<div class='warning'>⚠ Port $port không thể kết nối (có thể bị firewall chặn hoặc port khác)</div>";
}

// Test 3: Kiểm tra phpMyAdmin config
echo "<h3>3. Kiểm tra phpMyAdmin Config</h3>";
$config_file = 'C:\\xampp\\phpMyAdmin\\config.inc.php';
if (file_exists($config_file)) {
    echo "<div class='success'>✓ File config.inc.php tồn tại</div>";
    
    $config_content = file_get_contents($config_file);
    
    // Kiểm tra password trong config
    if (preg_match("/\\\$cfg\\['Servers'\\]\\[1\\]\\['password'\\]\\s*=\\s*['\"](.*?)['\"]/", $config_content, $matches)) {
        $config_password = $matches[1];
        echo "<div class='info'>Password trong config.inc.php: <strong>" . ($config_password ?: '(rỗng)') . "</strong></div>";
        
        if ($config_password !== $working_password) {
            echo "<div class='warning'>⚠ Password trong config.inc.php không khớp với password MySQL!</div>";
            echo "<div class='info'>";
            echo "Cần cập nhật file <code>config.inc.php</code>:<br>";
            echo "1. Mở file: <code>C:\\xampp\\phpMyAdmin\\config.inc.php</code><br>";
            echo "2. Tìm dòng: <code>\$cfg['Servers'][1]['password'] = '';</code><br>";
            echo "3. Thay đổi thành: <code>\$cfg['Servers'][1]['password'] = '" . addslashes($working_password) . "';</code><br>";
            echo "</div>";
        }
    } else {
        echo "<div class='info'>Không tìm thấy password trong config (có thể dùng config.inc.php mặc định)</div>";
    }
} else {
    echo "<div class='error'>✗ File config.inc.php không tồn tại tại: $config_file</div>";
}

// Test 4: Kiểm tra kết nối database
echo "<h3>4. Kiểm tra kết nối Database</h3>";
try {
    $pdo = new PDO("mysql:host=$host", $username, $working_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Kiểm tra database cinehub
    $stmt = $pdo->query("SHOW DATABASES LIKE 'cinehub'");
    $db_exists = $stmt->fetch();
    
    if ($db_exists) {
        echo "<div class='success'>✓ Database 'cinehub' tồn tại</div>";
        
        // Kiểm tra các bảng
        $pdo->exec("USE cinehub");
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<div class='info'>";
        echo "<strong>Các bảng hiện có:</strong> " . count($tables) . " bảng<br>";
        if (!empty($tables)) {
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
            echo "</ul>";
        }
        echo "</div>";
    } else {
        echo "<div class='warning'>⚠ Database 'cinehub' chưa tồn tại</div>";
        echo "<div class='info'>";
        echo "Cần tạo database:<br>";
        echo "1. Mở phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a><br>";
        echo "2. Click 'New' để tạo database mới<br>";
        echo "3. Đặt tên: <strong>cinehub</strong><br>";
        echo "4. Chọn collation: <strong>utf8mb4_unicode_ci</strong><br>";
        echo "5. Click 'Create'<br>";
        echo "Hoặc chạy file database.sql để tự động tạo<br>";
        echo "</div>";
    }
} catch(PDOException $e) {
    echo "<div class='error'>✗ Lỗi: " . $e->getMessage() . "</div>";
}

// Test 5: Kiểm tra phpMyAdmin có thể truy cập
echo "<h3>5. Kiểm tra phpMyAdmin có thể truy cập</h3>";
$phpmyadmin_url = 'http://localhost/phpmyadmin';
$ch = curl_init($phpmyadmin_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_NOBODY, true);
$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code == 200) {
    echo "<div class='success'>✓ phpMyAdmin có thể truy cập: <a href='$phpmyadmin_url' target='_blank'>$phpmyadmin_url</a></div>";
} else {
    echo "<div class='error'>✗ phpMyAdmin không thể truy cập (HTTP Code: $http_code)</div>";
    echo "<div class='info'>";
    echo "<strong>Giải pháp:</strong><br>";
    echo "1. Kiểm tra Apache đã Start trong XAMPP Control Panel chưa?<br>";
    echo "2. Thử truy cập: <a href='http://localhost' target='_blank'>http://localhost</a><br>";
    echo "3. Kiểm tra port Apache (mặc định 80) có bị chiếm không?<br>";
    echo "4. Kiểm tra firewall có chặn không?<br>";
    echo "</div>";
}

// Tổng kết
echo "<hr>";
echo "<h3>Tổng kết và Hướng dẫn</h3>";
echo "<div class='info'>";
echo "<strong>Các bước để fix lỗi kết nối phpMyAdmin:</strong><br><br>";

echo "<strong>Bước 1: Kiểm tra XAMPP Control Panel</strong><br>";
echo "1. Mở XAMPP Control Panel<br>";
echo "2. Kiểm tra <strong>Apache</strong> và <strong>MySQL</strong> đã Start chưa?<br>";
echo "3. Nếu chưa Start, click nút <strong>Start</strong> cho cả 2 service<br>";
echo "4. Nếu Start bị lỗi, có thể port đã bị chiếm (thường là port 80 hoặc 3306)<br><br>";

echo "<strong>Bước 2: Kiểm tra Port</strong><br>";
echo "1. Nếu port 3306 bị chiếm, có thể đổi port MySQL trong file <code>my.ini</code><br>";
echo "2. Hoặc tắt service khác đang dùng port đó<br><br>";

echo "<strong>Bước 3: Kiểm tra Password MySQL</strong><br>";
echo "1. Mở file: <code>C:\\xampp\\phpMyAdmin\\config.inc.php</code><br>";
echo "2. Tìm dòng: <code>\$cfg['Servers'][1]['password']</code><br>";
echo "3. Đảm bảo password khớp với password MySQL (mặc định XAMPP là rỗng)<br><br>";

echo "<strong>Bước 4: Kiểm tra Firewall</strong><br>";
echo "1. Tắt Windows Firewall tạm thời để test<br>";
echo "2. Hoặc thêm exception cho Apache và MySQL<br><br>";

echo "<strong>Bước 5: Truy cập phpMyAdmin</strong><br>";
echo "1. Mở trình duyệt<br>";
echo "2. Truy cập: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a><br>";
echo "3. Nếu vẫn lỗi, kiểm tra log trong XAMPP Control Panel<br>";
echo "</div>";

echo "<hr>";
echo "<p><strong>Thử truy cập:</strong></p>";
echo "<ul>";
echo "<li><a href='http://localhost/phpmyadmin' target='_blank'>phpMyAdmin</a></li>";
echo "<li><a href='http://localhost' target='_blank'>XAMPP Dashboard</a></li>";
echo "<li><a href='test-connection.php'>Test Database Connection</a></li>";
echo "</ul>";
?>

