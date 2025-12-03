<?php
/**
 * Cấu hình cho ứng dụng CineHub
 */

// ============================================
// CẤU HÌNH PUBLIC URL CHO QR CODE
// ============================================

// Cách 1: Sử dụng Domain Public
// Domain của website: tuanawh.store
define('PUBLIC_BASE_URL', 'https://tuanawh.store');

// Cách 2: Sử dụng Domain/IP Public
// - Nếu dùng domain: 'https://yourdomain.com'
// - Nếu dùng IP public: 'http://123.456.789.0'
// - Nếu dùng localhost: 'http://localhost' (mặc định)

// Tự động detect public URL từ request
// Nếu true, sẽ tự động lấy từ $_SERVER['HTTP_HOST'] (hữu ích khi dùng ngrok)
// Nếu false, sẽ sử dụng PUBLIC_BASE_URL ở trên
// Đặt false để luôn dùng URL ngrok cố định
define('AUTO_DETECT_PUBLIC_URL', false);

// Path của ứng dụng (thường là /DuAn1 hoặc /)
define('APP_PATH', '/');

// ============================================
// HƯỚNG DẪN SỬ DỤNG NGROK
// ============================================
// 1. Tải ngrok từ: https://ngrok.com/download
// 2. Chạy lệnh: ngrok http 80
// 3. Copy URL từ ngrok (ví dụ: https://abc123.ngrok-free.app)
// 4. Cập nhật PUBLIC_BASE_URL ở trên với URL đó
// 5. Hoặc để AUTO_DETECT_PUBLIC_URL = true và truy cập qua ngrok URL
