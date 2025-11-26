# Migrations Directory

## Hướng Dẫn

Thư mục này chứa các file migration cho database.

## Lưu Ý

**Bảng `user_tokens` đã được tích hợp vào file `cinehub.sql` chính.**

Không cần import riêng migration cho bảng này nữa. Chỉ cần import file `cinehub.sql` là đủ.

## Import Database

### Cách 1: phpMyAdmin

1. Mở phpMyAdmin: `http://localhost/phpmyadmin`
2. Tạo database `cinehub` (nếu chưa có)
3. Chọn database `cinehub`
4. Click tab "Import"
5. Chọn file `cinehub.sql` từ thư mục gốc dự án
6. Click "Go"

### Cách 2: Command Line

```bash
mysql -u root -p cinehub < cinehub.sql
```

## Kiểm Tra

Sau khi import, kiểm tra các bảng:

```sql
SHOW TABLES;
```

Bạn sẽ thấy bảng `user_tokens` trong danh sách.
