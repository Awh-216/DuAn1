# CineHub - Hệ thống Xem Phim Online & Đặt Vé

## Hướng dẫn cài đặt

### 1. Yêu cầu hệ thống
- XAMPP (hoặc WAMP/LAMP) với PHP 7.4+
- MySQL/MariaDB
- Web server (Apache)

### 2. Cài đặt database

#### Cách 1: Tự động (Khuyến nghị)
1. Truy cập: `http://localhost/DuAn1/test-db.php`
2. File này sẽ tự động kiểm tra và tạo database nếu chưa có
3. Sau đó bạn cần chạy file `database.sql` để tạo các bảng

#### Cách 2: Thủ công
1. Mở phpMyAdmin: `http://localhost/phpmyadmin`
2. Tạo database mới tên: `cinehub`
3. Chọn database `cinehub`
4. Vào tab "SQL"
5. Copy toàn bộ nội dung file `database.sql` và paste vào
6. Click "Go" để chạy

### 3. Cấu hình

Mở file `config.php` và kiểm tra thông tin kết nối:

```php
define('DB_HOST', 'localhost');      // Host database
define('DB_NAME', 'cinehub');        // Tên database
define('DB_USER', 'root');           // Username MySQL
define('DB_PASS', '');               // Password MySQL (mặc định XAMPP là rỗng)
```

Nếu bạn đã đổi password MySQL, hãy cập nhật `DB_PASS`.

### 4. Kiểm tra kết nối

Truy cập: `http://localhost/DuAn1/test-db.php`

File này sẽ:
- Kiểm tra MySQL đang chạy
- Kiểm tra database tồn tại
- Kiểm tra các bảng đã được tạo
- Kiểm tra kết nối từ config.php

### 5. Truy cập website

Sau khi cài đặt xong, truy cập:
- Trang chủ: `http://localhost/DuAn1/`
- Test database: `http://localhost/DuAn1/test-db.php`

## Cấu trúc dự án (MVC)

```
DuAn1/
├── config.php              # Cấu hình database và autoload
├── index.php               # Router chính
├── test-db.php             # File test kết nối database
├── database.sql            # File SQL tạo database và bảng
├── style.css               # CSS chính
├── core/                   # Core classes
│   ├── Database.php        # Database singleton
│   └── Controller.php      # Base Controller
├── models/                 # Models (Business Logic)
│   ├── UserModel.php
│   ├── MovieModel.php
│   ├── CategoryModel.php
│   ├── BookingModel.php
│   ├── ReviewModel.php
│   └── WatchHistoryModel.php
├── controllers/            # Controllers (Request Handling)
│   ├── HomeController.php
│   ├── MovieController.php
│   ├── BookingController.php
│   ├── AuthController.php
│   ├── ReviewController.php
│   └── ProfileController.php
└── views/                  # Views (Templates)
    ├── layout/
    │   ├── header.php
    │   └── footer.php
    ├── home/
    ├── movie/
    ├── booking/
    ├── auth/
    └── profile/
```

## Tính năng

### 1. Xem phim online
- Danh sách phim
- Tìm kiếm và lọc theo thể loại
- Xem phim với video player
- Đánh giá và bình luận
- Lịch sử xem phim

### 2. Đặt vé online
- Chọn phim, rạp, ngày chiếu
- Chọn suất chiếu
- Chọn ghế (sơ đồ ghế)
- Xem vé đã đặt

### 3. Quản lý tài khoản
- Đăng ký/Đăng nhập
- Cập nhật thông tin cá nhân
- Xem lịch sử xem phim
- Xem vé đã đặt

## Xử lý lỗi

Nếu gặp lỗi kết nối database:
1. Kiểm tra XAMPP đã khởi động chưa
2. Kiểm tra MySQL service đã bật chưa
3. Chạy file `test-db.php` để kiểm tra chi tiết
4. Kiểm tra thông tin đăng nhập trong `config.php`

## Lưu ý

- Mặc định XAMPP không có password cho MySQL
- Nếu bạn đã đặt password, cần cập nhật trong `config.php`
- Đảm bảo database `cinehub` đã được tạo trước khi chạy website

