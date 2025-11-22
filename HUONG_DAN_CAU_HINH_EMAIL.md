# Hướng dẫn cấu hình Email để gửi email thật

## 📋 Mục lục
1. [Tổng quan](#tổng-quan)
2. [Cấu hình với Gmail](#cấu-hình-với-gmail)
3. [Cấu hình với Outlook/Hotmail](#cấu-hình-với-outlookhotmail)
4. [Cấu hình với Yahoo Mail](#cấu-hình-với-yahoo-mail)
5. [Cấu hình với SMTP tùy chỉnh](#cấu-hình-với-smtp-tùy-chỉnh)
6. [Kiểm tra và xử lý lỗi](#kiểm-tra-và-xử-lý-lỗi)

---

## 📌 Tổng quan

Hệ thống CineHub hỗ trợ gửi email thật qua SMTP để phản hồi khách hàng. Bạn cần cấu hình thông tin SMTP trong file `config.php`.

### Các file liên quan:
- `config.php` - File cấu hình chính
- `core/Email.php` - Class xử lý gửi email
- `modules/admin/AdminController.php` - Controller xử lý phản hồi

---

## 📧 Cấu hình với Gmail

### Bước 1: Tạo App Password cho Gmail

1. **Đăng nhập vào Google Account**
   - Truy cập: https://myaccount.google.com/
   - Đăng nhập bằng tài khoản Gmail của bạn

2. **Bật 2-Step Verification (Xác thực 2 bước)**
   - Vào **Security** (Bảo mật)
   - Tìm mục **2-Step Verification** (Xác minh 2 bước)
   - Bật tính năng này nếu chưa bật
   - Làm theo hướng dẫn để hoàn tất

3. **Tạo App Password**
   - Vẫn trong phần **Security**
   - Tìm mục **App passwords** (Mật khẩu ứng dụng)
   - Nếu không thấy, tìm kiếm "App passwords" trong thanh tìm kiếm
   - Chọn **Mail** và **Other (Custom name)**
   - Nhập tên: "CineHub" hoặc tên bất kỳ
   - Click **Generate** (Tạo)
   - **Lưu lại mật khẩu 16 ký tự** (VD: `abcd efgh ijkl mnop`)

### Bước 2: Cấu hình trong config.php

Mở file `config.php` và cập nhật các thông tin sau:

```php
// Cấu hình Email SMTP
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com'); // Email Gmail của bạn
define('SMTP_PASSWORD', 'abcd efgh ijkl mnop'); // App Password (16 ký tự, có thể có khoảng trắng)
define('SMTP_ENCRYPTION', 'tls'); // Gmail dùng TLS
define('SMTP_FROM_EMAIL', 'your-email@gmail.com'); // Email gửi đi
define('SMTP_FROM_NAME', 'CineHub'); // Tên hiển thị
```

**Lưu ý quan trọng:**
- `SMTP_USERNAME`: Email Gmail đầy đủ (VD: `nguyenvana@gmail.com`)
- `SMTP_PASSWORD`: App Password 16 ký tự (có thể có khoảng trắng, hệ thống sẽ tự xử lý)
- **KHÔNG** dùng mật khẩu Gmail thông thường, phải dùng App Password

### Bước 3: Kiểm tra

1. Đăng nhập vào Admin Panel
2. Vào **Hỗ trợ khách hàng**
3. Chọn một ticket và click **Phản hồi**
4. Nhập nội dung và gửi
5. Kiểm tra email của khách hàng xem có nhận được không

---

## 📧 Cấu hình với Outlook/Hotmail

### Bước 1: Lấy mật khẩu ứng dụng

1. **Đăng nhập vào Microsoft Account**
   - Truy cập: https://account.microsoft.com/security
   - Đăng nhập bằng tài khoản Outlook/Hotmail

2. **Tạo App Password**
   - Vào **Security** → **Advanced security options**
   - Tìm mục **App passwords** (Mật khẩu ứng dụng)
   - Click **Create a new app password**
   - Chọn **Mail** và nhập tên: "CineHub"
   - Click **Generate**
   - **Lưu lại mật khẩu** (16 ký tự)

### Bước 2: Cấu hình trong config.php

```php
// Cấu hình Email SMTP cho Outlook
define('SMTP_HOST', 'smtp-mail.outlook.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@outlook.com'); // Email Outlook của bạn
define('SMTP_PASSWORD', 'your-app-password'); // App Password từ Microsoft
define('SMTP_ENCRYPTION', 'tls');
define('SMTP_FROM_EMAIL', 'your-email@outlook.com');
define('SMTP_FROM_NAME', 'CineHub');
```

---

## 📧 Cấu hình với Yahoo Mail

### Bước 1: Tạo App Password

1. **Đăng nhập vào Yahoo Account**
   - Truy cập: https://login.yahoo.com/
   - Đăng nhập bằng tài khoản Yahoo

2. **Tạo App Password**
   - Vào **Account Security** → **Generate app password**
   - Chọn **Mail** và nhập tên: "CineHub"
   - Click **Generate**
   - **Lưu lại mật khẩu**

### Bước 2: Cấu hình trong config.php

```php
// Cấu hình Email SMTP cho Yahoo
define('SMTP_HOST', 'smtp.mail.yahoo.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@yahoo.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_ENCRYPTION', 'tls');
define('SMTP_FROM_EMAIL', 'your-email@yahoo.com');
define('SMTP_FROM_NAME', 'CineHub');
```

---

## 📧 Cấu hình với SMTP tùy chỉnh

Nếu bạn có SMTP server riêng (VD: từ hosting, email công ty), cấu hình như sau:

```php
// Cấu hình Email SMTP tùy chỉnh
define('SMTP_HOST', 'mail.yourdomain.com'); // SMTP server của bạn
define('SMTP_PORT', 587); // Hoặc 465 cho SSL, 25 cho không mã hóa
define('SMTP_USERNAME', 'noreply@yourdomain.com'); // Email SMTP
define('SMTP_PASSWORD', 'your-password'); // Mật khẩu email
define('SMTP_ENCRYPTION', 'tls'); // 'tls', 'ssl', hoặc '' nếu không mã hóa
define('SMTP_FROM_EMAIL', 'noreply@yourdomain.com');
define('SMTP_FROM_NAME', 'CineHub');
```

### Các port phổ biến:
- **587**: TLS (khuyến nghị)
- **465**: SSL
- **25**: Không mã hóa (không khuyến nghị)

---

## 🔍 Kiểm tra và xử lý lỗi

### Kiểm tra cấu hình

1. **Kiểm tra file config.php**
   - Đảm bảo các thông tin SMTP đã được điền đầy đủ
   - Kiểm tra không có dấu ngoặc kép thừa hoặc lỗi cú pháp

2. **Kiểm tra PHP error log**
   - Vị trí thường ở: `C:\xampp\php\logs\php_error_log`
   - Hoặc: `C:\xampp\apache\logs\error.log`
   - Tìm các dòng có chứa "SMTP" hoặc "Email"

### Các lỗi thường gặp và cách xử lý

#### 1. Lỗi "SMTP Connection failed"
**Nguyên nhân:**
- SMTP_HOST hoặc SMTP_PORT sai
- Firewall chặn kết nối
- Internet không ổn định

**Cách xử lý:**
- Kiểm tra lại SMTP_HOST và SMTP_PORT
- Tắt firewall tạm thời để test
- Kiểm tra kết nối internet

#### 2. Lỗi "SMTP Authentication failed"
**Nguyên nhân:**
- SMTP_USERNAME hoặc SMTP_PASSWORD sai
- Với Gmail: chưa tạo App Password, đang dùng mật khẩu thường

**Cách xử lý:**
- Kiểm tra lại username và password
- Với Gmail: đảm bảo đã tạo App Password và dùng App Password
- Kiểm tra có khoảng trắng thừa trong password không

#### 3. Email không đến hộp thư đến
**Nguyên nhân:**
- Email bị đưa vào Spam/Junk
- Email người nhận không tồn tại
- SMTP server từ chối gửi

**Cách xử lý:**
- Kiểm tra thư mục Spam/Junk
- Kiểm tra email người nhận có đúng không
- Kiểm tra error log để xem có lỗi gì

#### 4. Lỗi "Email send failed via mail()"
**Nguyên nhân:**
- SMTP không hoạt động, hệ thống fallback về mail()
- Server không cấu hình mail() function

**Cách xử lý:**
- Cấu hình SMTP đúng cách (khuyến nghị)
- Hoặc cấu hình mail() function trong php.ini

### Test gửi email

Để test xem email có hoạt động không, bạn có thể:

1. **Test trong Admin Panel:**
   - Vào **Hỗ trợ khách hàng**
   - Chọn ticket và gửi phản hồi
   - Kiểm tra email khách hàng

2. **Kiểm tra error log:**
   - Mở error log và tìm dòng "Email sent successfully"
   - Nếu thấy "Email send failed", kiểm tra lỗi cụ thể

---

## 📝 Ví dụ cấu hình hoàn chỉnh

### Ví dụ 1: Gmail

```php
// Cấu hình Email SMTP - Gmail
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'nguyenvana@gmail.com');
define('SMTP_PASSWORD', 'abcd efgh ijkl mnop'); // App Password
define('SMTP_ENCRYPTION', 'tls');
define('SMTP_FROM_EMAIL', 'nguyenvana@gmail.com');
define('SMTP_FROM_NAME', 'CineHub');
```

### Ví dụ 2: Outlook

```php
// Cấu hình Email SMTP - Outlook
define('SMTP_HOST', 'smtp-mail.outlook.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'nguyenvana@outlook.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_ENCRYPTION', 'tls');
define('SMTP_FROM_EMAIL', 'nguyenvana@outlook.com');
define('SMTP_FROM_NAME', 'CineHub');
```

### Ví dụ 3: Email công ty (cPanel)

```php
// Cấu hình Email SMTP - Email công ty
define('SMTP_HOST', 'mail.yourcompany.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'support@yourcompany.com');
define('SMTP_PASSWORD', 'your-email-password');
define('SMTP_ENCRYPTION', 'tls');
define('SMTP_FROM_EMAIL', 'support@yourcompany.com');
define('SMTP_FROM_NAME', 'CineHub Support');
```

---

## 🔒 Bảo mật

### Lưu ý quan trọng:

1. **Không commit file config.php lên Git**
   - Thêm `config.php` vào `.gitignore`
   - Chỉ commit `config.example.php`

2. **Bảo vệ App Password**
   - Không chia sẻ App Password
   - Nếu bị lộ, xóa và tạo App Password mới ngay

3. **Kiểm tra quyền file**
   - Đảm bảo `config.php` có quyền phù hợp (không public read)

---

## 📞 Hỗ trợ

Nếu gặp vấn đề, hãy:

1. Kiểm tra error log để xem lỗi cụ thể
2. Kiểm tra lại các bước cấu hình
3. Thử với email provider khác để xác định vấn đề
4. Kiểm tra firewall và network

---

## ✅ Checklist cấu hình

- [ ] Đã tạo App Password (với Gmail/Outlook/Yahoo)
- [ ] Đã cập nhật `SMTP_HOST` trong config.php
- [ ] Đã cập nhật `SMTP_PORT` trong config.php
- [ ] Đã cập nhật `SMTP_USERNAME` trong config.php
- [ ] Đã cập nhật `SMTP_PASSWORD` trong config.php
- [ ] Đã cập nhật `SMTP_ENCRYPTION` trong config.php
- [ ] Đã cập nhật `SMTP_FROM_EMAIL` trong config.php
- [ ] Đã cập nhật `SMTP_FROM_NAME` trong config.php
- [ ] Đã test gửi email thành công
- [ ] Đã kiểm tra email không bị vào Spam

---

**Chúc bạn cấu hình thành công! 🎉**

