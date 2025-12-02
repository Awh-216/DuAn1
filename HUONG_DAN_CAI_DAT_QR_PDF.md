# Hướng dẫn cài đặt thư viện QR Code và PDF

## Bước 1: Cài đặt Composer

Nếu bạn chưa có Composer, hãy tải và cài đặt từ: https://getcomposer.org/download/

Hoặc trên Windows, bạn có thể tải file `Composer-Setup.exe` và cài đặt.

### Khi cài đặt Composer, bạn sẽ được hỏi chọn chế độ cài đặt:

**Khuyến nghị: Chọn "Install for me only"** (Chỉ cài cho bạn)
- ✅ Không cần quyền Administrator
- ✅ Đủ cho dự án cá nhân
- ✅ Dễ dàng gỡ cài đặt nếu cần

**Hoặc chọn "Install for all users (recommended)"** nếu:
- Bạn có quyền Administrator
- Muốn nhiều người dùng trên máy có thể sử dụng Composer

## Bước 2: Cài đặt thư viện

### Cách 1: Sử dụng file batch tự động (Khuyến nghị)

1. **Chạy file `install_composer.bat`** trong thư mục dự án
   - Double-click vào file `install_composer.bat`
   - Hoặc mở Command Prompt và chạy: `install_composer.bat`

File này sẽ tự động:
- Tải Composer
- Cài đặt Composer
- Cài đặt các thư viện QR Code và PDF

### Cách 2: Cài đặt thủ công

Nếu cách 1 không hoạt động, làm theo các bước sau:

1. **Mở Command Prompt:**
   - Nhấn `Win + R`
   - Gõ `cmd` và nhấn Enter

2. **Di chuyển đến thư mục dự án:**
   ```bash
   cd C:\xampp\htdocs\DuAn1
   ```

3. **Tải Composer:**
   ```bash
   C:\xampp\php\php.exe -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
   ```

4. **Cài đặt Composer:**
   ```bash
   C:\xampp\php\php.exe composer-setup.php
   ```

5. **Cài đặt thư viện:**
   ```bash
   C:\xampp\php\php.exe composer.phar install
   ```

### Cách 3: Nếu đã cài Composer nhưng chưa có trong PATH

Nếu bạn đã cài Composer nhưng gặp lỗi "composer is not recognized":

1. **Khởi động lại Command Prompt/PowerShell** (sau khi cài Composer)

2. **Hoặc sử dụng đường dẫn đầy đủ:**
   ```bash
   C:\Users\[TenNguoiDung]\AppData\Roaming\Composer\vendor\bin\composer.bat install
   ```
   
   Thay `[TenNguoiDung]` bằng tên user của bạn.

3. **Hoặc thêm Composer vào PATH:**
   - Mở System Properties > Environment Variables
   - Thêm đường dẫn Composer vào PATH

Lệnh này sẽ tự động cài đặt các thư viện cần thiết:
- `endroid/qr-code` - Tạo QR code
- `tecnickcom/tcpdf` - Tạo PDF

## Bước 3: Kiểm tra

Sau khi cài đặt xong, bạn sẽ thấy:
- Thư mục `vendor/` được tạo ra trong thư mục dự án
- Các file thư viện được tải về trong `vendor/`

## Bước 4: Test

Sau khi cài đặt xong, thử đặt vé và thanh toán. Hệ thống sẽ tự động:
- Tạo QR code cho mỗi vé
- Tạo file PDF chứa QR code và thông tin vé
- Gửi link PDF trong thông báo và email

## Lưu ý

- ✅ Đảm bảo PHP version >= 7.4
- ✅ Cần kết nối internet khi chạy `composer install` (Composer cần tải các thư viện từ internet)
- ✅ Nếu gặp lỗi, kiểm tra kết nối internet và đảm bảo PHP đã được thêm vào PATH
- ✅ Thời gian cài đặt có thể mất vài phút tùy vào tốc độ internet

