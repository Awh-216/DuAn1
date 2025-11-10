<?php
/**
 * File hỗ trợ fix lỗi password MySQL
 * Chạy file này để kiểm tra và hướng dẫn fix lỗi password
 */

$host = 'localhost';
$username = 'root';
$passwords_to_try = ['', 'root', 'password', '123456', 'admin'];

echo "<h2>Kiểm tra và Fix lỗi Password MySQL</h2>";

echo "<h3>1. Đang thử kết nối với các password phổ biến...</h3>";

$working_password = null;
foreach ($passwords_to_try as $password) {
    try {
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p style='color: green;'>✓ Kết nối thành công với password: <strong>" . ($password ?: '(rỗng)') . "</strong></p>";
        $working_password = $password;
        break;
    } catch(PDOException $e) {
        echo "<p style='color: red;'>✗ Password '" . ($password ?: '(rỗng)') . "' không đúng</p>";
    }
}

if ($working_password !== null) {
    echo "<hr>";
    echo "<h3>2. Cập nhật file config.php</h3>";
    echo "<p>Password đúng là: <strong>" . ($working_password ?: '(rỗng)') . "</strong></p>";
    echo "<p>Vui lòng mở file <code>config.php</code> và cập nhật dòng:</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
    echo "define('DB_PASS', '" . addslashes($working_password) . "');";
    echo "</pre>";
    
    // Tự động cập nhật config.php
    echo "<h3>3. Tự động cập nhật config.php</h3>";
    $config_file = __DIR__ . '/config.php';
    if (file_exists($config_file) && is_writable($config_file)) {
        $config_content = file_get_contents($config_file);
        $new_config = preg_replace(
            "/define\s*\(\s*['\"]DB_PASS['\"]\s*,\s*['\"].*?['\"]\s*\);/",
            "define('DB_PASS', '" . addslashes($working_password) . "');",
            $config_content
        );
        
        if ($new_config !== $config_content) {
            file_put_contents($config_file, $new_config);
            echo "<p style='color: green;'>✓ Đã tự động cập nhật file config.php</p>";
            echo "<p><a href='index.php' class='btn'>Thử lại kết nối</a></p>";
        } else {
            echo "<p style='color: orange;'>⚠ Không thể tự động cập nhật. Vui lòng cập nhật thủ công.</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ Không thể ghi file config.php. Vui lòng cập nhật thủ công.</p>";
    }
} else {
    echo "<hr>";
    echo "<h3>2. Không tìm thấy password đúng</h3>";
    echo "<p>Bạn cần reset password MySQL hoặc tìm password hiện tại.</p>";
    echo "<h4>Cách 1: Reset password MySQL trong XAMPP</h4>";
    echo "<ol>";
    echo "<li>Mở XAMPP Control Panel</li>";
    echo "<li>Stop MySQL service</li>";
    echo "<li>Mở file: <code>C:\\xampp\\mysql\\bin\\my.ini</code> (hoặc tìm file my.ini trong thư mục mysql)</li>";
    echo "<li>Tìm dòng <code>[mysqld]</code> và thêm dòng: <code>skip-grant-tables</code></li>";
    echo "<li>Start MySQL service</li>";
    echo "<li>Mở Command Prompt và chạy: <code>mysql -u root</code></li>";
    echo "<li>Chạy lệnh: <code>ALTER USER 'root'@'localhost' IDENTIFIED BY '';</code> (để đặt password rỗng)</li>";
    echo "<li>Chạy lệnh: <code>FLUSH PRIVILEGES;</code></li>";
    echo "<li>Stop MySQL, xóa dòng <code>skip-grant-tables</code> trong my.ini</li>";
    echo "<li>Start MySQL lại</li>";
    echo "</ol>";
    
    echo "<h4>Cách 2: Tìm password trong file cấu hình</h4>";
    echo "<p>Kiểm tra các file sau:</p>";
    echo "<ul>";
    echo "<li><code>C:\\xampp\\phpMyAdmin\\config.inc.php</code> - Tìm dòng <code>\$cfg['Servers'][1]['password']</code></li>";
    echo "<li><code>C:\\xampp\\mysql\\data\\mysql\\user.MYD</code> - File này chứa thông tin user (khó đọc)</li>";
    echo "</ul>";
    
    echo "<h4>Cách 3: Tạo user mới không có password</h4>";
    echo "<p>Nếu bạn có quyền admin, có thể tạo user mới:</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
    echo "CREATE USER 'cinehub_user'@'localhost' IDENTIFIED BY '';\n";
    echo "GRANT ALL PRIVILEGES ON cinehub.* TO 'cinehub_user'@'localhost';\n";
    echo "FLUSH PRIVILEGES;";
    echo "</pre>";
    echo "<p>Sau đó cập nhật config.php:</p>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
    echo "define('DB_USER', 'cinehub_user');\n";
    echo "define('DB_PASS', '');";
    echo "</pre>";
}

echo "<hr>";
echo "<h3>Hướng dẫn thủ công</h3>";
echo "<p>Nếu không thể tự động fix, vui lòng:</p>";
echo "<ol>";
echo "<li>Mở file <code>config.php</code></li>";
echo "<li>Tìm dòng: <code>define('DB_PASS', '');</code></li>";
echo "<li>Thay đổi thành password MySQL của bạn, ví dụ: <code>define('DB_PASS', 'your_password');</code></li>";
echo "<li>Lưu file và thử lại</li>";
echo "</ol>";

echo "<p><a href='test-db.php'>Quay lại test database</a> | <a href='index.php'>Thử truy cập website</a></p>";
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #f5f5f5;
}
h2, h3 {
    color: #333;
}
.btn {
    display: inline-block;
    padding: 10px 20px;
    background: #e50914;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    margin-top: 10px;
}
.btn:hover {
    background: #b20710;
}
pre {
    overflow-x: auto;
}
</style>

