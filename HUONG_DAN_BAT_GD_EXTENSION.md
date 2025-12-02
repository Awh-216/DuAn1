# Hướng dẫn bật GD Extension cho PHP

## Vấn đề
QR code không hiển thị vì PHP thiếu GD extension (cần thiết để tạo hình ảnh QR code).

## Cách bật GD Extension

### Bước 1: Mở file php.ini
1. Tìm file `php.ini` trong thư mục `C:\xampp\php\`
2. Mở file bằng Notepad hoặc text editor (cần quyền Administrator)

### Bước 2: Tìm và bỏ comment dòng extension=gd
1. Tìm dòng: `;extension=gd` hoặc `;extension=gd2`
2. Bỏ dấu `;` ở đầu dòng để thành: `extension=gd`
3. Lưu file

### Bước 3: Khởi động lại Apache
1. Mở XAMPP Control Panel
2. Stop Apache
3. Start lại Apache

### Bước 4: Kiểm tra
Chạy lệnh sau để kiểm tra GD đã được bật chưa:
```bash
C:\xampp\php\php.exe -m | findstr gd
```

Nếu thấy `gd` trong danh sách thì đã thành công!

## Lưu ý
- Nếu không tìm thấy dòng `extension=gd`, có thể cần thêm dòng mới: `extension=gd`
- Đảm bảo file `php_gd2.dll` tồn tại trong thư mục `C:\xampp\php\ext\`

