<?php
/**
 * IPSpamChecker - Helper class để kiểm tra và chặn spam dựa trên IP address
 * Ngăn chặn các hành vi spam từ cùng một IP
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/TokenHelper.php';

class IPSpamChecker {
    
    /**
     * Kiểm tra xem IP có bị chặn không
     * @param string $ipAddress IP address cần kiểm tra
     * @param string $actionType Loại hành động (booking, review, register, login)
     * @return array ['allowed' => bool, 'message' => string, 'remaining_time' => int]
     */
    public static function checkIPSpam($ipAddress = null, $actionType = 'general') {
        if (!$ipAddress) {
            $ipAddress = TokenHelper::getClientIp();
        }
        
        // Bỏ qua kiểm tra cho localhost/127.0.0.1 trong môi trường development
        if (in_array($ipAddress, ['127.0.0.1', '::1', 'localhost'])) {
            return ['allowed' => true, 'message' => '', 'remaining_time' => 0];
        }
        
        $db = Database::getInstance();
        
        // Kiểm tra xem IP có bị block vĩnh viễn không
        $blocked = $db->fetch("
            SELECT * FROM ip_blocks 
            WHERE ip_address = ? AND (expires_at IS NULL OR expires_at > NOW())
        ", [$ipAddress]);
        
        if ($blocked) {
            $expiresAt = $blocked['expires_at'];
            $remainingTime = $expiresAt ? max(0, strtotime($expiresAt) - time()) : 0;
            $message = $expiresAt 
                ? "IP của bạn đã bị chặn tạm thời. Thời gian còn lại: " . self::formatTime($remainingTime)
                : "IP của bạn đã bị chặn vĩnh viễn do vi phạm quy định.";
            
            return [
                'allowed' => false, 
                'message' => $message,
                'remaining_time' => $remainingTime
            ];
        }
        
        // Lấy cấu hình giới hạn cho từng loại hành động
        $limits = self::getActionLimits($actionType);
        
        // Đếm số lần vi phạm trong khoảng thời gian
        $violationCount = self::getViolationCount($ipAddress, $actionType, $limits['time_window']);
        
        // Kiểm tra nếu vượt quá giới hạn
        if ($violationCount >= $limits['max_attempts']) {
            // Đếm tổng số lần vi phạm (không giới hạn thời gian) để xác định lần vi phạm thứ mấy
            $totalViolationCount = self::getTotalViolationCount($ipAddress, $actionType);
            $currentViolation = $totalViolationCount + 1; // +1 vì đây là lần vi phạm sắp xảy ra
            
            // Lần 1: Chỉ cảnh báo, không block
            if ($currentViolation == 1) {
                $message = "Cảnh báo: Bạn đã vi phạm quy định lần đầu tiên. Nếu tiếp tục vi phạm, IP của bạn sẽ bị chặn 20 phút!";
                return [
                    'allowed' => true, // Vẫn cho phép nhưng cảnh báo
                    'message' => $message,
                    'remaining_time' => 0,
                    'warning' => true
                ];
            }
            
            // Lần 2-3: Block 20 phút
            // Lần 4 trở đi: Block vĩnh viễn
            $blockDuration = self::calculateBlockDuration($currentViolation);
            
            if ($blockDuration === null) {
                // Block vĩnh viễn (lần 4+)
                self::blockIP($ipAddress, null, "Spam $actionType - Lần vi phạm thứ $currentViolation ($violationCount lần spam trong time window)");
                $message = "IP của bạn đã bị chặn vĩnh viễn do vi phạm quy định lần thứ $currentViolation!";
            } else {
                // Block tạm thời 20 phút (lần 2-3)
                self::blockIP($ipAddress, $blockDuration, "Spam $actionType - Lần vi phạm thứ $currentViolation ($violationCount lần spam trong time window)");
                $message = "IP của bạn đã bị chặn do vi phạm quy định. Thời gian chặn: " . self::formatTime($blockDuration) . " (Lần vi phạm thứ $currentViolation)";
            }
            
            return [
                'allowed' => false,
                'message' => $message,
                'remaining_time' => $blockDuration ?? 0
            ];
        }
        
        // Kiểm tra rate limiting (số lần thực hiện trong thời gian ngắn)
        $recentAttempts = self::getRecentAttempts($ipAddress, $actionType, $limits['rate_limit_window']);
        
        if ($recentAttempts >= $limits['rate_limit']) {
            $message = "Bạn đang thực hiện quá nhiều thao tác. Vui lòng đợi " . self::formatTime($limits['rate_limit_window']) . " trước khi thử lại.";
            
            return [
                'allowed' => false,
                'message' => $message,
                'remaining_time' => $limits['rate_limit_window']
            ];
        }
        
        return ['allowed' => true, 'message' => '', 'remaining_time' => 0];
    }
    
    /**
     * Log hành động của IP
     * @param string $ipAddress IP address
     * @param string $actionType Loại hành động
     * @param bool $isSpam Có phải spam không
     * @param string $details Chi tiết
     * @param int|null $userId ID của user (nếu có)
     */
    public static function logIPAction($ipAddress = null, $actionType = 'general', $isSpam = false, $details = '', $userId = null) {
        if (!$ipAddress) {
            $ipAddress = TokenHelper::getClientIp();
        }
        
        $db = Database::getInstance();
        
        try {
            $db->execute("
                INSERT INTO ip_spam_logs (ip_address, action_type, is_spam, details, user_id, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ", [
                $ipAddress,
                $actionType,
                $isSpam ? 1 : 0,
                $details,
                $userId
            ]);
        } catch (Exception $e) {
            // Nếu bảng chưa tồn tại, bỏ qua
            error_log("Cannot log IP action: " . $e->getMessage());
        }
    }
    
    /**
     * Đếm số lần vi phạm của IP
     * @param string $ipAddress IP address
     * @param string $actionType Loại hành động
     * @param int $timeWindow Khoảng thời gian (giây)
     * @return int Số lần vi phạm
     */
    private static function getViolationCount($ipAddress, $actionType, $timeWindow) {
        $db = Database::getInstance();
        
        try {
            $result = $db->fetch("
                SELECT COUNT(*) as count
                FROM ip_spam_logs
                WHERE ip_address = ?
                AND action_type = ?
                AND is_spam = 1
                AND created_at >= DATE_SUB(NOW(), INTERVAL ? SECOND)
            ", [$ipAddress, $actionType, $timeWindow]);
            
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Đếm tổng số lần vi phạm của IP (không giới hạn thời gian)
     * @param string $ipAddress IP address
     * @param string $actionType Loại hành động
     * @return int Tổng số lần vi phạm
     */
    private static function getTotalViolationCount($ipAddress, $actionType) {
        $db = Database::getInstance();
        
        try {
            $result = $db->fetch("
                SELECT COUNT(*) as count
                FROM ip_spam_logs
                WHERE ip_address = ?
                AND action_type = ?
                AND is_spam = 1
            ", [$ipAddress, $actionType]);
            
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Đếm số lần thực hiện gần đây
     * @param string $ipAddress IP address
     * @param string $actionType Loại hành động
     * @param int $timeWindow Khoảng thời gian (giây)
     * @return int Số lần thực hiện
     */
    private static function getRecentAttempts($ipAddress, $actionType, $timeWindow) {
        $db = Database::getInstance();
        
        try {
            $result = $db->fetch("
                SELECT COUNT(*) as count
                FROM ip_spam_logs
                WHERE ip_address = ?
                AND action_type = ?
                AND created_at >= DATE_SUB(NOW(), INTERVAL ? SECOND)
            ", [$ipAddress, $actionType, $timeWindow]);
            
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Đếm số lần IP đã bị block (tính cả các block đã hết hạn)
     * @param string $ipAddress IP address
     * @return int Số lần đã bị block
     */
    private static function getIPBlockCount($ipAddress) {
        $db = Database::getInstance();
        
        try {
            $result = $db->fetch("
                SELECT COUNT(*) as count
                FROM ip_blocks
                WHERE ip_address = ?
            ", [$ipAddress]);
            
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Chặn IP
     * @param string $ipAddress IP address
     * @param int|null $duration Thời gian chặn (giây), null = chặn vĩnh viễn
     * @param string $reason Lý do chặn
     */
    private static function blockIP($ipAddress, $duration = null, $reason = '') {
        $db = Database::getInstance();
        
        try {
            $expiresAt = $duration ? date('Y-m-d H:i:s', time() + $duration) : null;
            
            // Kiểm tra xem IP đã bị block vĩnh viễn chưa
            $permanentBlock = $db->fetch("
                SELECT id FROM ip_blocks 
                WHERE ip_address = ? AND expires_at IS NULL
            ", [$ipAddress]);
            
            if ($permanentBlock) {
                // IP đã bị block vĩnh viễn, không cần làm gì
                return;
            }
            
            // Kiểm tra xem IP có đang bị block tạm thời không
            $activeBlock = $db->fetch("
                SELECT id FROM ip_blocks 
                WHERE ip_address = ? AND expires_at IS NOT NULL AND expires_at > NOW()
            ", [$ipAddress]);
            
            if ($activeBlock && $duration === null) {
                // IP đang bị block tạm thời, nhưng lần này là vĩnh viễn -> cập nhật thành vĩnh viễn
                $db->execute("
                    UPDATE ip_blocks 
                    SET expires_at = NULL, reason = ?, updated_at = NOW()
                    WHERE id = ?
                ", [$reason, $activeBlock['id']]);
            } else {
                // Tạo block mới (luôn tạo mới để đếm số lần block)
                $db->execute("
                    INSERT INTO ip_blocks (ip_address, expires_at, reason, created_at, updated_at)
                    VALUES (?, ?, ?, NOW(), NOW())
                ", [$ipAddress, $expiresAt, $reason]);
            }
        } catch (Exception $e) {
            error_log("Cannot block IP: " . $e->getMessage());
        }
    }
    
    /**
     * Lấy cấu hình giới hạn cho từng loại hành động
     * @param string $actionType Loại hành động
     * @return array Cấu hình giới hạn
     */
    private static function getActionLimits($actionType) {
        $limits = [
            'booking' => [
                'max_attempts' => 5,        // Tối đa 5 lần spam trong time_window
                'time_window' => 3600,      // 1 giờ
                'rate_limit' => 10,         // Tối đa 10 lần trong rate_limit_window
                'rate_limit_window' => 60   // 1 phút
            ],
            'review' => [
                'max_attempts' => 10,       // Tối đa 10 lần spam trong time_window
                'time_window' => 3600,      // 1 giờ
                'rate_limit' => 5,          // Tối đa 5 lần trong rate_limit_window
                'rate_limit_window' => 60   // 1 phút
            ],
            'register' => [
                'max_attempts' => 3,        // Tối đa 3 lần spam trong time_window
                'time_window' => 3600,      // 1 giờ
                'rate_limit' => 3,          // Tối đa 3 lần trong rate_limit_window
                'rate_limit_window' => 300  // 5 phút
            ],
            'login' => [
                'max_attempts' => 10,       // Tối đa 10 lần spam trong time_window
                'time_window' => 3600,      // 1 giờ
                'rate_limit' => 5,          // Tối đa 5 lần trong rate_limit_window
                'rate_limit_window' => 60   // 1 phút
            ],
            'general' => [
                'max_attempts' => 20,       // Tối đa 20 lần spam trong time_window
                'time_window' => 3600,      // 1 giờ
                'rate_limit' => 30,         // Tối đa 30 lần trong rate_limit_window
                'rate_limit_window' => 60   // 1 phút
            ]
        ];
        
        return $limits[$actionType] ?? $limits['general'];
    }
    
    /**
     * Tính toán thời gian block dựa trên số lần vi phạm
     * @param int $violationCount Số lần vi phạm (1 = cảnh báo, 2-3 = 20 phút, 4+ = vĩnh viễn)
     * @return int|null Thời gian block (giây), null = vĩnh viễn, 0 = chỉ cảnh báo
     */
    private static function calculateBlockDuration($violationCount) {
        // Vi phạm lần 1: Chỉ cảnh báo (không block) - xử lý ở checkIPSpam
        // Vi phạm lần 2-3: Block 20 phút
        // Vi phạm lần 4 trở đi: Block vĩnh viễn
        if ($violationCount >= 2 && $violationCount <= 3) {
            return 20 * 60; // 20 phút
        } elseif ($violationCount >= 4) {
            return null; // Vĩnh viễn
        } else {
            return 0; // Lần 1: chỉ cảnh báo
        }
    }
    
    /**
     * Format thời gian thành chuỗi dễ đọc
     * @param int $seconds Số giây
     * @return string Chuỗi thời gian
     */
    private static function formatTime($seconds) {
        if ($seconds <= 0) {
            return '0 giây';
        }
        
        if ($seconds < 60) {
            return $seconds . ' giây';
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            return $minutes . ' phút';
        } elseif ($seconds < 86400) {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            return $hours . ' giờ' . ($minutes > 0 ? ' ' . $minutes . ' phút' : '');
        } else {
            $days = floor($seconds / 86400);
            $hours = floor(($seconds % 86400) / 3600);
            return $days . ' ngày' . ($hours > 0 ? ' ' . $hours . ' giờ' : '');
        }
    }
    
    /**
     * Bỏ chặn IP
     * @param string $ipAddress IP address
     * @return bool Thành công hay không
     */
    public static function unblockIP($ipAddress) {
        $db = Database::getInstance();
        
        try {
            $db->execute("
                UPDATE ip_blocks 
                SET expires_at = NOW() - INTERVAL 1 SECOND
                WHERE ip_address = ?
            ", [$ipAddress]);
            
            return true;
        } catch (Exception $e) {
            error_log("Cannot unblock IP: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Lấy danh sách IP bị chặn
     * @param int $limit Số lượng
     * @return array Danh sách IP bị chặn
     */
    public static function getBlockedIPs($limit = 100) {
        $db = Database::getInstance();
        
        try {
            return $db->fetchAll("
                SELECT * FROM ip_blocks 
                WHERE expires_at IS NULL OR expires_at > NOW()
                ORDER BY created_at DESC
                LIMIT ?
            ", [$limit]);
        } catch (Exception $e) {
            return [];
        }
    }
}
?>

