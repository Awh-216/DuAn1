<?php
/**
 * File test kết nối database đơn giản
 * Chạy file này để kiểm tra kết nối
 */

echo "<h2>Kiểm tra kết nối Database</h2>";
echo "<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
.success { color: green; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0; }
.error { color: red; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0; }
.info { color: #0c5460; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 5px; margin: 10px 0; }
pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
</style>";

// Thông tin kết nối
$host = 'localhost';
$dbname = 'cinehub';
$username = 'root';
$password = ''; // Thử password rỗng trước

echo "<h3>1. Thông tin kết nối:</h3>";
echo "<pre>";
echo "Host: $host\n";
echo "Database: $dbname\n";
echo "Username: $username\n";
echo "Password: " . ($password ?: '(rỗng)') . "\n";
echo "</pre>";

// Test 1: Kiểm tra MySQL đang chạy
echo "<h3>2. Kiểm tra MySQL đang chạy...</h3>";
try {
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div class='success'>✓ MySQL đang chạy và có thể kết nối</div>";
} catch(PDOException $e) {
    echo "<div class='error'>✗ Lỗi kết nối MySQL: " . $e->getMessage() . "</div>";
    echo "<div class='info'>";
    echo "<strong>Giải pháp:</strong><br>";
    echo "1. Kiểm tra XAMPP Control Panel - MySQL đã Start chưa?<br>";
    echo "2. Nếu có password MySQL, cần cập nhật trong file core/Database.php<br>";
    echo "3. Thử các password phổ biến: '', 'root', 'password'<br>";
    echo "</div>";
    die();
}

// Test 2: Kiểm tra database tồn tại
echo "<h3>3. Kiểm tra database '$dbname' tồn tại...</h3>";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<div class='success'>✓ Database '$dbname' tồn tại và có thể kết nối</div>";
} catch(PDOException $e) {
    echo "<div class='error'>✗ Lỗi: " . $e->getMessage() . "</div>";
    
    if (strpos($e->getMessage(), "Unknown database") !== false) {
        echo "<div class='info'>";
        echo "<strong>Database chưa tồn tại!</strong><br>";
        echo "Vui lòng:<br>";
        echo "1. Mở phpMyAdmin: <a href='http://localhost/phpmyadmin' target='_blank'>http://localhost/phpmyadmin</a><br>";
        echo "2. Tạo database tên: <strong>$dbname</strong><br>";
        echo "3. Hoặc chạy file database.sql để tự động tạo<br>";
        echo "</div>";
    }
    die();
}

// Test 3: Kiểm tra các bảng
echo "<h3>4. Kiểm tra các bảng trong database...</h3>";
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $requiredTables = ['users', 'subscriptions', 'categories', 'movies', 'theaters', 'showtimes', 'tickets', 'watch_history', 'reviews'];
    
    echo "<div class='info'>";
    echo "<strong>Các bảng hiện có:</strong><br>";
    if (empty($tables)) {
        echo "Chưa có bảng nào<br>";
    } else {
        foreach ($tables as $table) {
            echo "✓ $table<br>";
        }
    }
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<strong>Các bảng cần thiết:</strong><br>";
    foreach ($requiredTables as $table) {
        $exists = in_array($table, $tables);
        if ($exists) {
            echo "✓ $table (đã có)<br>";
        } else {
            echo "✗ $table (chưa có)<br>";
        }
    }
    echo "</div>";
    
    if (count($tables) < count($requiredTables)) {
        echo "<div class='error'>";
        echo "<strong>Thiếu một số bảng!</strong><br>";
        echo "Vui lòng chạy file database.sql để tạo các bảng còn thiếu.<br>";
        echo "Hướng dẫn:<br>";
        echo "1. Mở phpMyAdmin<br>";
        echo "2. Chọn database '$dbname'<br>";
        echo "3. Vào tab 'SQL'<br>";
        echo "4. Copy nội dung file database.sql và paste vào<br>";
        echo "5. Click 'Go' để chạy<br>";
        echo "</div>";
    }
    
} catch(PDOException $e) {
    echo "<div class='error'>✗ Lỗi: " . $e->getMessage() . "</div>";
}

// Test 4: Kiểm tra kết nối từ Database class
echo "<h3>5. Kiểm tra kết nối từ Database class...</h3>";
try {
    // Autoload
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
    
    $db = Database::getInstance();
    $result = $db->fetch("SELECT 1 as test");
    
    if ($result) {
        echo "<div class='success'>✓ Kết nối từ Database class thành công!</div>";
        echo "<div class='success'>✓ Website có thể hoạt động bình thường</div>";
    }
} catch(Exception $e) {
    echo "<div class='error'>✗ Lỗi: " . $e->getMessage() . "</div>";
    echo "<div class='info'>";
    echo "Có thể do:<br>";
    echo "1. Password MySQL không đúng - cần cập nhật trong core/Database.php<br>";
    echo "2. Database name không đúng<br>";
    echo "3. Host không đúng<br>";
    echo "</div>";
}

// Test 5: Thử các password phổ biến
echo "<h3>6. Thử các password phổ biến (nếu cần)...</h3>";
$passwords_to_try = ['', 'root', 'password', '123456'];
$found_password = null;

foreach ($passwords_to_try as $pwd) {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $pwd);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<div class='success'>✓ Password đúng: <strong>" . ($pwd ?: '(rỗng)') . "</strong></div>";
        $found_password = $pwd;
        break;
    } catch(PDOException $e) {
        // Bỏ qua
    }
}

if ($found_password !== null && $found_password !== $password) {
    echo "<div class='info'>";
    echo "<strong>⚠ Password hiện tại trong core/Database.php không đúng!</strong><br>";
    echo "Password đúng là: <strong>" . ($found_password ?: '(rỗng)') . "</strong><br>";
    echo "Vui lòng mở file <code>core/Database.php</code> và sửa dòng 10:<br>";
    echo "<pre>private \$password = '" . addslashes($found_password) . "';</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<h3>Kết luận:</h3>";
echo "<p>Nếu tất cả các test trên đều thành công, website sẽ hoạt động bình thường.</p>";
echo "<p><a href='index.php' style='display: inline-block; padding: 10px 20px; background: #e50914; color: white; text-decoration: none; border-radius: 5px;'>Thử truy cập website</a></p>";
?>

