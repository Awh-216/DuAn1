<?php
/**
 * Script cập nhật database để thêm hệ thống admin
 * Chạy file này một lần để cập nhật database hiện có
 */

require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Cập nhật Database cho hệ thống Admin</h2>";
    echo "<style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0; }
        .error { color: red; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0; }
        .info { color: #0c5460; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 5px; margin: 10px 0; }
    </style>";
    
    // Đọc file database_admin.sql
    $sqlFile = __DIR__ . '/database_admin.sql';
    if (!file_exists($sqlFile)) {
        die("<div class='error'>Không tìm thấy file database_admin.sql</div>");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Tách các câu lệnh SQL
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        function($stmt) {
            return !empty($stmt) && !preg_match('/^--/', $stmt) && !preg_match('/^\/\*/', $stmt);
        }
    );
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        if (empty(trim($statement))) continue;
        
        try {
            // Bỏ qua các câu lệnh IF NOT EXISTS trong ALTER TABLE
            if (stripos($statement, 'ALTER TABLE') !== false && stripos($statement, 'IF NOT EXISTS') !== false) {
                // Thử thêm cột, nếu lỗi thì bỏ qua
                $statement = str_ireplace('IF NOT EXISTS', '', $statement);
                try {
                    $pdo->exec($statement);
                    echo "<div class='success'>✓ " . substr($statement, 0, 50) . "...</div>";
                    $successCount++;
                } catch (PDOException $e) {
                    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                        echo "<div class='info'>⚠ Cột đã tồn tại: " . substr($statement, 0, 50) . "...</div>";
                    } else {
                        echo "<div class='error'>✗ Lỗi: " . $e->getMessage() . "</div>";
                        $errorCount++;
                    }
                }
            } else {
                $pdo->exec($statement);
                echo "<div class='success'>✓ " . substr($statement, 0, 50) . "...</div>";
                $successCount++;
            }
        } catch (PDOException $e) {
            // Bỏ qua lỗi nếu bảng/khóa đã tồn tại
            if (strpos($e->getMessage(), 'already exists') !== false || 
                strpos($e->getMessage(), 'Duplicate') !== false) {
                echo "<div class='info'>⚠ Đã tồn tại: " . substr($statement, 0, 50) . "...</div>";
            } else {
                echo "<div class='error'>✗ Lỗi: " . $e->getMessage() . "</div>";
                $errorCount++;
            }
        }
    }
    
    echo "<hr>";
    echo "<h3>Kết quả</h3>";
    echo "<div class='success'>Thành công: $successCount câu lệnh</div>";
    if ($errorCount > 0) {
        echo "<div class='error'>Lỗi: $errorCount câu lệnh</div>";
    }
    
    // Tạo tài khoản admin nếu chưa có
    echo "<hr><h3>Tạo tài khoản Admin</h3>";
    $adminEmail = 'admin@cinehub.com';
    $checkAdmin = $pdo->query("SELECT id FROM users WHERE email = '$adminEmail'")->fetch();
    
    if (!$checkAdmin) {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (name, email, password, role, is_active) VALUES ('Super Admin', '$adminEmail', '$hashedPassword', 'admin', TRUE)");
        echo "<div class='success'>✓ Đã tạo tài khoản admin: $adminEmail / admin123</div>";
    } else {
        // Cập nhật role cho admin hiện có
        $pdo->exec("UPDATE users SET role = 'admin', is_active = TRUE WHERE email = '$adminEmail'");
        echo "<div class='info'>⚠ Tài khoản admin đã tồn tại, đã cập nhật role</div>";
    }
    
    echo "<hr>";
    echo "<div class='success'><strong>Hoàn tất!</strong> Bạn có thể truy cập admin panel tại: <a href='?route=admin/index'>?route=admin/index</a></div>";
    
} catch(PDOException $e) {
    echo "<div class='error'>Lỗi kết nối database: " . $e->getMessage() . "</div>";
}
?>

