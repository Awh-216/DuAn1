<?php
/**
 * Helper class để lấy base URL động
 */
class UrlHelper {
    private static $baseUrl = null;
    
    /**
     * Lấy base URL từ request hiện tại hoặc config
     */
    public static function getBaseUrl() {
        if (self::$baseUrl !== null) {
            return self::$baseUrl;
        }
        
        // Load config nếu có
        $configFile = __DIR__ . '/../config.php';
        if (file_exists($configFile)) {
            require_once $configFile;
        }
        
        // Nếu có PUBLIC_BASE_URL trong config và AUTO_DETECT = false, dùng nó
        if (defined('PUBLIC_BASE_URL') && defined('AUTO_DETECT_PUBLIC_URL') && !AUTO_DETECT_PUBLIC_URL) {
            // Nếu PUBLIC_BASE_URL đã chứa appPath, dùng luôn
            $appPath = defined('APP_PATH') ? APP_PATH : '/DuAn1';
            if (strpos(PUBLIC_BASE_URL, $appPath) !== false) {
                self::$baseUrl = rtrim(PUBLIC_BASE_URL, '/');
            } else {
                // Thêm appPath vào PUBLIC_BASE_URL
                self::$baseUrl = rtrim(PUBLIC_BASE_URL, '/') . $appPath;
            }
            return self::$baseUrl;
        }
        
        // Tự động detect từ request hiện tại
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' || 
                     (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) 
                    ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        
        // Lấy thư mục từ script name (ví dụ: /DuAn1/index.php -> /DuAn1)
        $appPath = dirname($scriptName);
        if ($appPath === '/' || $appPath === '\\') {
            $appPath = '';
        }
        
        self::$baseUrl = $protocol . "://" . $host . $appPath;
        return self::$baseUrl;
    }
    
    /**
     * Tạo URL đầy đủ từ route
     */
    public static function route($route) {
        $baseUrl = self::getBaseUrl();
        return $baseUrl . '/?route=' . $route;
    }
    
    /**
     * Tạo URL đầy đủ từ path
     */
    public static function url($path = '') {
        $baseUrl = self::getBaseUrl();
        if (empty($path)) {
            return $baseUrl . '/';
        }
        if (strpos($path, '/') !== 0) {
            $path = '/' . $path;
        }
        return $baseUrl . $path;
    }
}

