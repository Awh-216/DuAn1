# Hướng dẫn sử dụng Ngrok cho QR Code

## Ngrok là gì?
Ngrok là công cụ tạo tunnel để expose localhost ra internet, cho phép truy cập website localhost từ bất kỳ đâu.

## Tại sao dùng Ngrok?
✅ Đơn giản, không cần cấu hình router hay firewall  
✅ Tự động có HTTPS  
✅ Hoạt động ngay lập tức  
✅ Miễn phí cho development  

## Cài đặt và sử dụng

### Bước 1: Tải Ngrok
1. Truy cập: https://ngrok.com/download
2. Tải `ngrok.exe` cho Windows
3. Giải nén vào thư mục (ví dụ: `C:\ngrok\`)

### Bước 2: Chạy Ngrok
1. Mở Command Prompt hoặc PowerShell
2. Di chuyển đến thư mục ngrok:
   ```bash
   cd C:\ngrok
   ```
3. Chạy lệnh:
   ```bash
   ngrok http 80
   ```
   
   **Lưu ý**: 
   - Nếu XAMPP dùng port khác (ví dụ: 8080), thay bằng: `ngrok http 8080`
   - Để kiểm tra port XAMPP, xem trong XAMPP Control Panel

4. Ngrok sẽ hiển thị:
   ```
   Session Status: online
   Forwarding: https://abc123-def456.ngrok-free.app -> http://localhost:80
   ```

### Bước 3: Copy URL Ngrok
Copy URL từ dòng "Forwarding" (ví dụ: `https://abc123-def456.ngrok-free.app`)

### Bước 4: Cấu hình trong config.php

Mở file `C:\xampp\htdocs\DuAn1\config.php`:

**Cách 1: Tự động detect (Khuyến nghị)**
```php
define('PUBLIC_BASE_URL', 'http://localhost');
define('AUTO_DETECT_PUBLIC_URL', true);
```
Sau đó truy cập website qua ngrok URL: `https://abc123-def456.ngrok-free.app/DuAn1`

**Cách 2: Cấu hình thủ công**
```php
define('PUBLIC_BASE_URL', 'https://abc123-def456.ngrok-free.app'); // Thay bằng URL ngrok của bạn
define('AUTO_DETECT_PUBLIC_URL', false);
```

### Bước 5: Kiểm tra
1. Đảm bảo Apache đang chạy trong XAMPP
2. Truy cập: `https://your-ngrok-url.ngrok-free.app/DuAn1`
3. Đăng nhập và tạo booking mới
4. Quét QR code từ điện thoại - sẽ hoạt động! 🎉

## Lưu ý quan trọng

### 1. URL thay đổi mỗi lần khởi động
- Free plan: URL ngrok thay đổi mỗi lần chạy lại
- Giải pháp: Tạo ngrok account để có domain cố định (xem bên dưới)

### 2. Ngrok Warning Page
- Lần đầu truy cập có thể có warning page
- Click "Visit Site" để tiếp tục
- Có thể tắt warning bằng ngrok account

### 3. QR Code cũ
- QR code đã tạo trước đó vẫn dùng URL cũ
- Cần tạo booking mới để có QR code với URL mới

### 4. Ngrok phải chạy liên tục
- Ngrok phải chạy khi có người quét QR code
- Nếu tắt ngrok, QR code sẽ không hoạt động

## Tạo Ngrok Account (Tùy chọn)

### Lợi ích:
- Domain cố định (không thay đổi)
- Tắt warning page
- Nhiều tính năng khác

### Cách làm:
1. Đăng ký tại: https://dashboard.ngrok.com/signup
2. Lấy authtoken từ dashboard
3. Chạy lệnh:
   ```bash
   ngrok config add-authtoken YOUR_AUTHTOKEN
   ```
4. Chạy với domain cố định:
   ```bash
   ngrok http 80 --domain=yourname.ngrok.io
   ```

## Troubleshooting

### Lỗi "ngrok: command not found"
- Đảm bảo đã tải và giải nén ngrok.exe
- Hoặc thêm thư mục ngrok vào PATH

### Lỗi "port already in use"
- Kiểm tra port 80 đã được sử dụng chưa
- Thử port khác: `ngrok http 8080`

### QR code vẫn không quét được
1. Kiểm tra ngrok đang chạy
2. Kiểm tra Apache đang chạy
3. Truy cập website qua ngrok URL để đảm bảo hoạt động
4. Tạo booking mới để có QR code với URL mới

### Ngrok bị giới hạn
- Free plan có giới hạn số lượng request
- Nâng cấp lên paid plan nếu cần

## Ví dụ sử dụng

```bash
# Chạy ngrok trên port 80
C:\ngrok> ngrok http 80

# Kết quả:
Forwarding: https://abc123.ngrok-free.app -> http://localhost:80

# Truy cập website:
https://abc123.ngrok-free.app/DuAn1

# QR code sẽ chứa URL:
https://abc123.ngrok-free.app/DuAn1/?route=booking/verify&booking=...
```

