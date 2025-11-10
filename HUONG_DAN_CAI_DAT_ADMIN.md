# Hướng dẫn cài đặt hệ thống Admin

## Cách 1: Chạy trong phpMyAdmin (Khuyến nghị)

### Bước 1: Mở phpMyAdmin
1. Truy cập: `http://localhost/phpmyadmin`
2. Chọn database `cinehub` ở sidebar bên trái

### Bước 2: Chạy file SQL
1. Click vào tab **"SQL"** ở thanh menu trên cùng
2. Mở file `database_admin.sql` bằng Notepad hoặc text editor
3. **Copy toàn bộ nội dung** trong file `database_admin.sql`
4. **Paste** vào ô SQL trong phpMyAdmin
5. Click nút **"Go"** hoặc **"Thực thi"** để chạy

### Bước 3: Kiểm tra kết quả
- Nếu thành công, sẽ thấy thông báo "MySQL returned an empty result set" hoặc số dòng đã thực thi
- Nếu có lỗi, sẽ hiển thị thông báo lỗi (thường là do bảng/khóa đã tồn tại - không sao)

## Cách 2: Sử dụng file update tự động

1. Truy cập: `http://localhost/DuAn1/update_database_admin.php`
2. File này sẽ tự động chạy các câu lệnh SQL và báo cáo kết quả
3. Đơn giản và dễ sử dụng hơn

## Cách 3: Chạy từ MySQL Command Line

```bash
# Mở MySQL command line
mysql -u root -p

# Chọn database
USE cinehub;

# Chạy file SQL
SOURCE E:/XAMPP/htdocs/DuAn1/database_admin.sql;
```

## Lưu ý quan trọng

1. **Backup database trước**: Nên backup database `cinehub` trước khi chạy
2. **Lỗi "Duplicate"**: Nếu gặp lỗi "Table already exists" hoặc "Duplicate column", đó là bình thường - các bảng/cột đã tồn tại
3. **Tài khoản Admin**: Sau khi chạy xong, sẽ có tài khoản:
   - Email: `admin@cinehub.com`
   - Password: `admin123`
   - **Nhớ đổi password sau khi đăng nhập!**

## Sau khi cài đặt xong

1. Đăng nhập với tài khoản admin
2. Truy cập: `http://localhost/DuAn1/?route=admin/index`
3. Bắt đầu sử dụng admin panel!

## Troubleshooting

### Lỗi "Table doesn't exist"
- Đảm bảo đã chọn đúng database `cinehub`
- Kiểm tra database đã được tạo chưa

### Lỗi "Access denied"
- Kiểm tra quyền MySQL user
- Đảm bảo user có quyền CREATE, ALTER, INSERT

### Không thể đăng nhập admin
- Kiểm tra user có `role = 'admin'` trong bảng users
- Hoặc user có role "Super Admin" trong bảng user_roles

