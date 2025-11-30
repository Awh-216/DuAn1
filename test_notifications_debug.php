<?php
session_start();
require_once 'core/Database.php';

echo "<h2>Kiểm tra hệ thống thông báo</h2>";

$db = Database::getInstance();

// 1. Kiểm tra bảng notifications
echo "<h3>1. Kiểm tra bảng notifications:</h3>";
try {
    $pdo = $db->getConnection();
    $stmt = $pdo->query("SHOW TABLES LIKE 'notifications'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Bảng notifications tồn tại</p>";
        
        // Kiểm tra cấu trúc
        $stmt = $pdo->query("DESCRIBE notifications");
        echo "<table border='1' cellpadding='5'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            foreach ($row as $col) {
                echo "<td>" . htmlspecialchars($col) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>✗ Bảng notifications không tồn tại</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Lỗi: " . $e->getMessage() . "</p>";
}

// 2. Kiểm tra thông báo trong database
echo "<h3>2. Thông báo trong database:</h3>";
try {
    $notifications = $db->fetchAll("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 20");
    echo "<p>Tổng số thông báo: <strong>" . count($notifications) . "</strong></p>";
    
    if (count($notifications) > 0) {
        echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>User ID</th><th>Type</th><th>Title</th><th>Message</th><th>Is Read</th><th>Created At</th></tr>";
        foreach ($notifications as $n) {
            echo "<tr>";
            echo "<td>{$n['id']}</td>";
            echo "<td>{$n['user_id']}</td>";
            echo "<td>{$n['type']}</td>";
            echo "<td>" . htmlspecialchars($n['title']) . "</td>";
            echo "<td>" . htmlspecialchars(substr($n['message'], 0, 50)) . "...</td>";
            echo "<td>" . ($n['is_read'] ? 'Đã đọc' : 'Chưa đọc') . "</td>";
            echo "<td>{$n['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠ Không có thông báo nào trong database</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Lỗi: " . $e->getMessage() . "</p>";
}

// 3. Kiểm tra user hiện tại
echo "<h3>3. User hiện tại:</h3>";
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    echo "<p>User ID: <strong>{$userId}</strong></p>";
    
    // Kiểm tra thông báo của user này
    try {
        $userNotifications = $db->fetchAll("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC", [$userId]);
        echo "<p>Thông báo của user này: <strong>" . count($userNotifications) . "</strong></p>";
        
        $unreadCount = $db->fetch("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0", [$userId])['count'] ?? 0;
        echo "<p>Thông báo chưa đọc: <strong>{$unreadCount}</strong></p>";
        
        if (count($userNotifications) > 0) {
            echo "<table border='1' cellpadding='5'><tr><th>ID</th><th>Title</th><th>Message</th><th>Is Read</th><th>Created At</th></tr>";
            foreach ($userNotifications as $n) {
                echo "<tr>";
                echo "<td>{$n['id']}</td>";
                echo "<td>" . htmlspecialchars($n['title']) . "</td>";
                echo "<td>" . htmlspecialchars(substr($n['message'], 0, 50)) . "...</td>";
                echo "<td>" . ($n['is_read'] ? 'Đã đọc' : 'Chưa đọc') . "</td>";
                echo "<td>{$n['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Lỗi: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠ Chưa đăng nhập. Vui lòng đăng nhập để kiểm tra.</p>";
}

// 4. Test API endpoint
echo "<h3>4. Test API endpoint:</h3>";
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    echo "<p>Test URL: <a href='?route=notifications/getNotifications&limit=10' target='_blank'>?route=notifications/getNotifications&limit=10</a></p>";
    echo "<p>Mở Developer Tools (F12) và kiểm tra Network tab khi click vào nút thông báo.</p>";
} else {
    echo "<p style='color: orange;'>⚠ Cần đăng nhập để test API</p>";
}

// 5. Tạo thông báo test (nếu chưa có)
echo "<h3>5. Tạo thông báo test:</h3>";
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    try {
        // Kiểm tra xem đã có thông báo test chưa
        $testNotif = $db->fetch("SELECT * FROM notifications WHERE user_id = ? AND title LIKE '%Test%' LIMIT 1", [$userId]);
        if (!$testNotif) {
            $db->execute("
                INSERT INTO notifications (user_id, type, title, message, link, is_read)
                VALUES (?, 'info', 'Thông báo test', 'Đây là thông báo test để kiểm tra hệ thống', '?route=notifications/index', 0)
            ", [$userId]);
            echo "<p style='color: green;'>✓ Đã tạo thông báo test</p>";
        } else {
            echo "<p style='color: blue;'>ℹ Đã có thông báo test rồi</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Lỗi: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠ Cần đăng nhập để tạo thông báo test</p>";
}

echo "<hr>";
echo "<p><a href='?route=home/index'>← Về trang chủ</a></p>";
?>

