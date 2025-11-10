<?php
/**
 * Script tạo/cập nhật tài khoản admin
 * Truy cập: http://localhost/DuAn1/create_admin.php
 */

require_once 'config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Tạo/Cập nhật tài khoản Admin</h2>";
    echo "<style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: green; padding: 15px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; margin: 10px 0; }
        .error { color: red; padding: 15px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; margin: 10px 0; }
        .info { color: #0c5460; padding: 15px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 5px; margin: 10px 0; }
        .warning { color: #856404; padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; margin: 10px 0; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .credentials { background: #e7f3ff; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #2196F3; }
    </style>";
    
    echo "<div class='container'>";
    
    $adminEmail = 'admin@cinehub.com';
    $adminPassword = 'admin123';
    $adminName = 'Super Admin';
    
    // Hash password đúng cách
    $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);
    
    // Kiểm tra user đã tồn tại chưa
    $stmt = $pdo->prepare("SELECT id, email, role FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingUser) {
        // Cập nhật user hiện có
        $updateStmt = $pdo->prepare("
            UPDATE users 
            SET name = ?, password = ?, role = 'admin', is_active = TRUE 
            WHERE email = ?
        ");
        $updateStmt->execute([$adminName, $hashedPassword, $adminEmail]);
        
        echo "<div class='success'>";
        echo "✓ <strong>Đã cập nhật tài khoản admin!</strong><br>";
        echo "User ID: " . $existingUser['id'];
        echo "</div>";
    } else {
        // Tạo user mới
        // Kiểm tra xem cột role có tồn tại không
        try {
            $insertStmt = $pdo->prepare("
                INSERT INTO users (name, email, password, role, is_active) 
                VALUES (?, ?, ?, 'admin', TRUE)
            ");
            $insertStmt->execute([$adminName, $adminEmail, $hashedPassword]);
            $userId = $pdo->lastInsertId();
            
            echo "<div class='success'>";
            echo "✓ <strong>Đã tạo tài khoản admin mới!</strong><br>";
            echo "User ID: " . $userId;
            echo "</div>";
        } catch (PDOException $e) {
            // Nếu cột role chưa tồn tại, tạo không có role
            if (strpos($e->getMessage(), 'Unknown column') !== false) {
                $insertStmt = $pdo->prepare("
                    INSERT INTO users (name, email, password) 
                    VALUES (?, ?, ?)
                ");
                $insertStmt->execute([$adminName, $adminEmail, $hashedPassword]);
                $userId = $pdo->lastInsertId();
                
                echo "<div class='warning'>";
                echo "⚠ Đã tạo user nhưng cột 'role' chưa tồn tại. Vui lòng chạy database_admin.sql để thêm cột này.";
                echo "</div>";
                
                echo "<div class='success'>";
                echo "✓ <strong>Đã tạo tài khoản admin!</strong><br>";
                echo "User ID: " . $userId;
                echo "</div>";
            } else {
                throw $e;
            }
        }
    }
    
    // Gán role Super Admin (nếu bảng tồn tại)
    try {
        // Kiểm tra xem có role Super Admin không
        $roleCheck = $pdo->query("SELECT id FROM roles WHERE name = 'Super Admin'")->fetch();
        
        if ($roleCheck) {
            $userId = $existingUser ? $existingUser['id'] : $pdo->lastInsertId();
            
            // Xóa role cũ nếu có
            $pdo->prepare("DELETE FROM user_roles WHERE user_id = ?")->execute([$userId]);
            
            // Gán role Super Admin
            $pdo->prepare("
                INSERT INTO user_roles (user_id, role_id) 
                VALUES (?, ?)
            ")->execute([$userId, $roleCheck['id']]);
            
            echo "<div class='success'>✓ Đã gán role 'Super Admin' cho tài khoản</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='info'>ℹ Bảng roles chưa tồn tại. Chạy database_admin.sql để tạo hệ thống roles.</div>";
    }
    
    // Hiển thị thông tin đăng nhập
    echo "<div class='credentials'>";
    echo "<h3>Thông tin đăng nhập:</h3>";
    echo "<p><strong>Email:</strong> <code>$adminEmail</code></p>";
    echo "<p><strong>Password:</strong> <code>$adminPassword</code></p>";
    echo "<p class='warning'><strong>⚠ Lưu ý:</strong> Đổi mật khẩu ngay sau khi đăng nhập!</p>";
    echo "</div>";
    
    // Test đăng nhập
    echo "<h3>Kiểm tra đăng nhập:</h3>";
    $testStmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $testStmt->execute([$adminEmail]);
    $testUser = $testStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($testUser && password_verify($adminPassword, $testUser['password'])) {
        echo "<div class='success'>✓ Password hash đúng! Có thể đăng nhập được.</div>";
    } else {
        echo "<div class='error'>✗ Password hash không đúng! Vui lòng chạy lại script này.</div>";
    }
    
    echo "<hr>";
    echo "<p><a href='?route=auth/login' class='btn btn-primary'>Đăng nhập ngay</a> | ";
    echo "<a href='?route=admin/index' class='btn btn-success'>Vào Admin Panel</a></p>";
    
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<div class='error'>";
    echo "<strong>Lỗi:</strong> " . $e->getMessage() . "<br>";
    echo "<strong>Mã lỗi:</strong> " . $e->getCode();
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<strong>Giải pháp:</strong><br>";
    echo "1. Kiểm tra database 'cinehub' đã được tạo chưa<br>";
    echo "2. Kiểm tra MySQL đã chạy chưa<br>";
    echo "3. Kiểm tra thông tin kết nối trong config.php<br>";
    echo "4. Chạy file database.sql trước để tạo các bảng cơ bản";
    echo "</div>";
}
?>

