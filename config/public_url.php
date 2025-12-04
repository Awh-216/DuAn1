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

// Cấu hình public URL - Domain của website
define('PUBLIC_BASE_URL', 'https://unadmired-estimatingly-amare.ngrok-free.dev');

