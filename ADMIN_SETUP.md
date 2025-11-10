# Hướng dẫn cài đặt hệ thống Admin

## 1. Cài đặt Database

Chạy file `database_admin.sql` để tạo các bảng cần thiết cho hệ thống admin:

```sql
-- Chạy file này trong phpMyAdmin hoặc MySQL command line
source database_admin.sql;
```

Hoặc copy nội dung file và chạy trong phpMyAdmin.

## 2. Tạo tài khoản Admin

Sau khi chạy database_admin.sql, bạn sẽ có tài khoản admin mẫu:
- Email: `admin@cinehub.com`
- Password: `admin123` (cần đổi sau khi đăng nhập)

**Lưu ý:** Password trong database đã được hash. Nếu muốn tạo admin mới, hash password bằng:
```php
password_hash('your_password', PASSWORD_DEFAULT)
```

## 3. Truy cập Admin Panel

Sau khi đăng nhập với tài khoản admin, truy cập:
```
http://localhost/DuAn1/?route=admin/index
```

## 4. Các tính năng Admin

### Dashboard
- Tổng quan thống kê: người dùng, phim, vé, doanh thu
- Doanh thu theo ngày/tuần/tháng
- Top phim xem nhiều nhất
- Suất chiếu sắp tới

### Quản lý người dùng
- Xem danh sách người dùng
- Sửa thông tin người dùng
- Chặn/Mở khóa tài khoản
- Reset mật khẩu
- Xem lịch sử giao dịch

### Quản lý phim
- Thêm/Sửa/Xóa phim
- Quản lý metadata (tiêu đề, mô tả, thể loại, đạo diễn, diễn viên)
- Upload video, poster, banner, trailer
- Quản lý trạng thái: draft/scheduled/published/archived
- DRM & geo-blocking

### Quản lý rạp
- Thêm/Sửa/Xóa rạp
- Quản lý phòng chiếu
- Sơ đồ ghế

### Quản lý vé
- Xem danh sách vé
- Hủy vé
- Hoàn tiền
- In vé (QR code)

### Analytics & Báo cáo
- Doanh thu theo ngày/tuần/tháng
- Top phim doanh thu cao
- Xuất báo cáo CSV/PDF

### Hỗ trợ khách hàng
- Xem ticket hỗ trợ
- Gán ticket cho nhân viên
- Cập nhật trạng thái

### System Logs
- Audit trail cho mọi thay đổi
- Xem ai làm gì, khi nào
- Lọc theo module

## 5. Quyền và Roles

Hệ thống hỗ trợ các roles:
- **Super Admin**: Toàn quyền hệ thống
- **Admin**: Quản trị viên
- **Moderator**: Điều hành viên
- **Content Manager**: Quản lý nội dung
- **Support Staff**: Nhân viên hỗ trợ

Mỗi role có các permissions riêng. Super Admin có tất cả quyền.

## 6. Cấu trúc Files

```
DuAn1/
├── core/
│   └── AdminMiddleware.php      # Middleware kiểm tra quyền admin
├── controllers/
│   └── AdminController.php      # Controller xử lý admin
├── views/
│   └── admin/
│       ├── layout.php           # Layout admin
│       ├── dashboard.php        # Dashboard
│       ├── users.php            # Quản lý người dùng
│       ├── movies.php           # Quản lý phim
│       ├── tickets.php          # Quản lý vé
│       ├── theaters.php         # Quản lý rạp
│       ├── analytics.php         # Analytics
│       ├── support.php          # Hỗ trợ
│       └── logs.php             # System logs
└── database_admin.sql           # SQL tạo bảng admin
```

## 7. Các tính năng cần bổ sung (tùy chọn)

- Upload video và transcode
- Quản lý phụ đề (SRT/VTT)
- Tích hợp cổng thanh toán
- Email notifications
- Push notifications
- Advanced analytics với funnels
- A/B testing
- Feature flags

## 8. Bảo mật

- Tất cả routes admin đều yêu cầu đăng nhập và quyền admin
- Audit trail ghi lại mọi thay đổi quan trọng
- IP address và user agent được lưu trong logs
- Password được hash bằng bcrypt

## 9. Troubleshooting

Nếu không thể truy cập admin panel:
1. Kiểm tra đã chạy database_admin.sql chưa
2. Kiểm tra user có role = 'admin' hoặc có role Super Admin
3. Kiểm tra session đã đăng nhập chưa
4. Kiểm tra file AdminController.php và AdminMiddleware.php có tồn tại

