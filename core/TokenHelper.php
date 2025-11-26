<?php
/**
 * TokenHelper - Helper class for token management
 * Provides utility functions for creating, hashing, and validating tokens
 */

class TokenHelper {
    
    /**
     * Tạo token ngẫu nhiên an toàn
     * @param int $length Độ dài token (bytes)
     * @return string Token string (hex)
     */
    public static function generateSecureToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Hash token trước khi lưu vào database (tùy chọn)
     * @param string $token Token cần hash
     * @return string Hashed token
     */
    public static function hashToken($token) {
        return hash('sha256', $token);
    }
    
    /**
     * So sánh token với hashed token
     * @param string $token Token gốc
     * @param string $hashedToken Token đã hash
     * @return bool True nếu khớp
     */
    public static function verifyToken($token, $hashedToken) {
        return hash_equals($hashedToken, self::hashToken($token));
    }
    
    /**
     * Lấy thông tin thiết bị từ User Agent
     * @return string Device info
     */
    public static function getDeviceInfo() {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        // Phân tích User Agent để lấy thông tin cơ bản
        $browser = 'Unknown Browser';
        $os = 'Unknown OS';
        
        // Detect Browser
        if (preg_match('/MSIE/i', $userAgent) || preg_match('/Trident/i', $userAgent)) {
            $browser = 'Internet Explorer';
        } elseif (preg_match('/Edge/i', $userAgent)) {
            $browser = 'Microsoft Edge';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Google Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Mozilla Firefox';
        } elseif (preg_match('/Opera/i', $userAgent)) {
            $browser = 'Opera';
        }
        
        // Detect OS
        if (preg_match('/Windows/i', $userAgent)) {
            $os = 'Windows';
        } elseif (preg_match('/Mac/i', $userAgent)) {
            $os = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $os = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $os = 'Android';
        } elseif (preg_match('/iOS|iPhone|iPad/i', $userAgent)) {
            $os = 'iOS';
        }
        
        return "$browser on $os";
    }
    
    /**
     * Lấy IP address của client
     * @return string IP address
     */
    public static function getClientIp() {
        $ipAddress = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        
        // Nếu có nhiều IP (proxy), lấy IP đầu tiên
        if (strpos($ipAddress, ',') !== false) {
            $ipAddress = trim(explode(',', $ipAddress)[0]);
        }
        
        return $ipAddress;
    }
    
    /**
     * Tính toán thời gian hết hạn token
     * @param int $days Số ngày token có hiệu lực
     * @return string Timestamp
     */
    public static function getExpiryTime($days = 30) {
        return date('Y-m-d H:i:s', strtotime("+$days days"));
    }
}
?>
