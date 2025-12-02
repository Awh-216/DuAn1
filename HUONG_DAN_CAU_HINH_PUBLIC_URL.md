# Hướng dẫn cấu hình Public URL cho QR Code

## Vấn đề
QR code hiện tại chỉ hoạt động trên localhost, không thể quét từ bên ngoài.

## Giải pháp
Cấu hình public URL trong file `config.php` để QR code có thể được quét từ bất kỳ đâu.

## 🚀 Sử dụng NGROK (Khuyến nghị cho Development)

Ngrok là cách đơn giản nhất để expose localhost ra internet mà không cần cấu hình router hay firewall.

### Bước 1: Tải và cài đặt Ngrok
1. Truy cập: https://ngrok.com/download
2. Tải file `ngrok.exe` cho Windows
3. Giải nén vào thư mục bất kỳ (ví dụ: `C:\ngrok\`)

### Bước 2: Chạy Ngrok
1. Mở Command Prompt hoặc PowerShell
2. Chạy lệnh:
   ```bash
   ngrok http 80
   ```
   (Nếu XAMPP dùng port khác, thay 80 bằng port đó, ví dụ: `ngrok http 8080`)

3. Ngrok sẽ hiển thị một URL như:
   ```
   Forwarding: https://abc123-def456.ngrok-free.app -> http://localhost:80
   ```

### Bước 3: Cấu hình trong config.php
Mở file `config.php` và cập nhật:

**Cách 1: Tự động detect (Khuyến nghị)**
```php
define('PUBLIC_BASE_URL', 'http://localhost'); // Có thể để mặc định
define('AUTO_DETECT_PUBLIC_URL', true);
```
Sau đó truy cập website qua ngrok URL (ví dụ: `https://abc123.ngrok-free.app/DuAn1`)

**Cách 2: Cấu hình thủ công**
```php
define('PUBLIC_BASE_URL', 'https://abc123-def456.ngrok-free.app'); // Thay bằng URL ngrok của bạn
define('AUTO_DETECT_PUBLIC_URL', false);
```

### Bước 4: Kiểm tra
1. Đảm bảo Apache đang chạy trên XAMPP
2. Truy cập website qua ngrok URL: `https://your-ngrok-url.ngrok-free.app/DuAn1`
3. Tạo một booking mới
4. Quét QR code từ điện thoại - sẽ hoạt động!

### Lưu ý về Ngrok:
- **Free plan**: URL thay đổi mỗi lần khởi động ngrok (trừ khi dùng ngrok account)
- **Ngrok account**: Có thể có domain cố định (ví dụ: `yourname.ngrok.io`)
- **Ngrok warning page**: Lần đầu truy cập có thể có warning page, click "Visit Site" để tiếp tục
- **HTTPS**: Ngrok tự động cung cấp HTTPS, rất tốt cho QR code

### Tạo Ngrok Account (Tùy chọn - để có domain cố định)
1. Đăng ký tại: https://dashboard.ngrok.com/signup
2. Lấy authtoken từ dashboard
3. Chạy: `ngrok config add-authtoken YOUR_TOKEN`
4. Chạy với domain cố định: `ngrok http 80 --domain=yourname.ngrok.io`

## Các cách cấu hình khác

### Cách 1: Sử dụng Domain (Production)

#### Cách 1: Sử dụng Domain (Khuyến nghị)
Nếu bạn có domain, cập nhật như sau:
```php
define('PUBLIC_BASE_URL', 'https://yourdomain.com');
define('AUTO_DETECT_PUBLIC_URL', false);
```

#### Cách 2: Sử dụng IP Public
Nếu bạn muốn dùng IP public của máy chủ:
```php
define('PUBLIC_BASE_URL', 'http://123.456.789.0'); // Thay bằng IP public của bạn
define('AUTO_DETECT_PUBLIC_URL', false);
```

#### Cách 3: Tự động detect (Chỉ khi truy cập từ public IP)
Nếu bạn muốn tự động detect URL từ request:
```php
define('PUBLIC_BASE_URL', 'http://your-public-ip-or-domain');
define('AUTO_DETECT_PUBLIC_URL', true);
```

### Bước 3: Cấu hình App Path
Nếu ứng dụng của bạn không nằm ở `/DuAn1`, cập nhật:
```php
define('APP_PATH', '/DuAn1'); // Thay đổi nếu cần
```

## Lưu ý quan trọng

### 1. Firewall và Port Forwarding
- Đảm bảo port 80 (HTTP) hoặc 443 (HTTPS) đã được mở trong firewall
- Nếu dùng router, cần cấu hình port forwarding từ router đến máy chủ

### Bước cấu hình Port Forwarding:
1. Đăng nhập vào router (thường là `192.168.1.1` hoặc `192.168.0.1`)
2. Tìm mục "Port Forwarding" hoặc "Virtual Server"
3. Thêm rule:
   - External Port: 80 (hoặc 443)
   - Internal IP: IP local của máy chủ (ví dụ: `192.168.1.100`)
   - Internal Port: 80 (hoặc 443)
   - Protocol: TCP

### 2. Kiểm tra IP Public
Để biết IP public của bạn:
- Truy cập: https://whatismyipaddress.com/
- Hoặc chạy lệnh: `curl ifconfig.me` (trên Linux/Mac)

### 3. Dynamic IP
Nếu IP public của bạn thay đổi (Dynamic IP), bạn có thể:
- Sử dụng dịch vụ Dynamic DNS (như No-IP, DuckDNS)
- Hoặc cập nhật `PUBLIC_BASE_URL` mỗi khi IP thay đổi

### 4. HTTPS (Khuyến nghị cho production)
Để sử dụng HTTPS:
- Cài đặt SSL certificate (Let's Encrypt miễn phí)
- Cập nhật `PUBLIC_BASE_URL` thành `https://yourdomain.com`

## Ví dụ cấu hình

### Ví dụ 1: Sử dụng domain với HTTPS
```php
define('PUBLIC_BASE_URL', 'https://cinehub.example.com');
define('AUTO_DETECT_PUBLIC_URL', false);
define('APP_PATH', '/');
```

### Ví dụ 2: Sử dụng IP public
```php
define('PUBLIC_BASE_URL', 'http://123.456.789.0');
define('AUTO_DETECT_PUBLIC_URL', false);
define('APP_PATH', '/DuAn1');
```

### Ví dụ 3: Development (localhost)
```php
define('PUBLIC_BASE_URL', 'http://localhost');
define('AUTO_DETECT_PUBLIC_URL', true);
define('APP_PATH', '/DuAn1');
```

## Kiểm tra

Sau khi cấu hình:
1. Tạo một booking mới
2. Kiểm tra QR code được tạo
3. Quét QR code từ điện thoại (không cùng mạng WiFi)
4. Nếu thành công, sẽ hiển thị PDF vé

## Troubleshooting

### QR code vẫn không quét được
1. Kiểm tra `PUBLIC_BASE_URL` đã đúng chưa
2. Kiểm tra firewall đã mở port chưa
3. Kiểm tra router đã forward port chưa
4. Thử truy cập trực tiếp URL trong QR code từ trình duyệt

### Lỗi "Connection refused"
- Kiểm tra Apache đang chạy
- Kiểm tra firewall
- Kiểm tra port forwarding

### QR code quét được nhưng không hiển thị PDF
- Kiểm tra route `booking/verify` hoạt động đúng
- Kiểm tra file PDF được tạo thành công
- Kiểm tra quyền truy cập thư mục `data/ticket_pdfs`

