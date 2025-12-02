<?php
/**
 * Cấu hình Public URL cho QR Code
 * 
 * Để QR code có thể quét được từ bên ngoài localhost, hãy cấu hình URL public của bạn ở đây.
 * 
 * Các tùy chọn:
 * 1. Sử dụng domain/IP public: 'http://your-domain.com' hoặc 'http://your-ip-address'
 * 2. Sử dụng ngrok hoặc tunnel service: 'https://your-ngrok-url.ngrok.io'
 * 3. Để null để tự động detect từ HTTP_HOST (chỉ hoạt động trong mạng nội bộ)
 */

// Cấu hình public URL - Thay đổi giá trị này thành URL public của bạn
// Ví dụ: 'http://192.168.1.100' hoặc 'https://yourdomain.com' hoặc 'https://abc123.ngrok.io'
define('PUBLIC_BASE_URL', null); // null = auto detect, hoặc đặt URL public của bạn

// Nếu bạn muốn sử dụng ngrok hoặc tunnel service, uncomment và điền URL:
// define('PUBLIC_BASE_URL', 'https://your-ngrok-url.ngrok.io');

// Nếu bạn muốn sử dụng IP public, uncomment và điền IP:
// define('PUBLIC_BASE_URL', 'http://192.168.1.100');

// Nếu bạn có domain, uncomment và điền domain:
// define('PUBLIC_BASE_URL', 'https://yourdomain.com');

