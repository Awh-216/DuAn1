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

---

# 🔗 Danh Sách Link Sửa Phim và Tập Phim

## 📍 Base URL
```
http://localhost/DuAn1/
```

---

## 🎬 **ADMIN PANEL - QUẢN LÝ PHIM**

### 1️⃣ **Danh Sách Phim (Quản Lý)**
```
?route=admin/movies
```
**Mô tả:** Trang danh sách tất cả phim, có thể tìm kiếm, lọc, xem, sửa, xóa phim.

**Đầy đủ:**
```
http://localhost/DuAn1/?route=admin/movies
```

---

### 2️⃣ **Thêm Phim Mới**
```
?route=admin/movies/create
```
**Mô tả:** Trang form thêm phim mới vào database.

**Đầy đủ:**
```
http://localhost/DuAn1/?route=admin/movies/create
```

---

### 3️⃣ **Sửa Phim** ⭐ (QUAN TRỌNG - ĐỂ SỬA TẬP)
```
?route=admin/movies/edit&id={MOVIE_ID}
```
**Mô tả:** Trang sửa thông tin phim và **quản lý danh sách tập phim**.

**Trong trang này bạn có thể:**
- ✅ Xem danh sách tất cả tập phim hiện có
- ✅ Thêm tập mới (upload video hoặc không upload)
- ✅ Sửa thông tin tập (thông qua form "Thêm tập mới" với cùng số tập)
- ✅ Xóa tập phim
- ✅ Xem link video của từng tập

**Ví dụ với ID = 1:**
```
?route=admin/movies/edit&id=1
```

**Đầy đủ:**
```
http://localhost/DuAn1/?route=admin/movies/edit&id=1
```

**Lưu ý:** Thay `{MOVIE_ID}` bằng ID của phim bạn muốn sửa.

---

### 4️⃣ **Import Tập Từ Folder** ⭐ (NHANH - TỰ ĐỘNG)
```
?route=admin/movies/scanEpisodes
```
**Mô tả:** Trang import tập phim từ folder `data/phim/phimbo/`.

**Cách sử dụng:**
1. Truy cập link này
2. Chọn phim bộ từ dropdown
3. Chọn các file video cần import
4. Kiểm tra số tập tự động nhận diện
5. Click "Import các tập đã chọn"

**Đầy đủ:**
```
http://localhost/DuAn1/?route=admin/movies/scanEpisodes
```

---

### 5️⃣ **Xóa Tập Phim**
```
?route=admin/movies/delete-episode&id={EPISODE_ID}&movie_id={MOVIE_ID}
```
**Mô tả:** Xóa một tập phim cụ thể (sẽ redirect về trang sửa phim).

**Tham số:**
- `{EPISODE_ID}`: ID của tập phim cần xóa
- `{MOVIE_ID}`: ID của phim chứa tập đó

**Ví dụ:**
```
?route=admin/movies/delete-episode&id=5&movie_id=1
```

**Đầy đủ:**
```
http://localhost/DuAn1/?route=admin/movies/delete-episode&id=5&movie_id=1
```

**Lưu ý:** 
- Link này thường được gọi tự động từ nút "Xóa" trong trang sửa phim
- Không nên truy cập trực tiếp trừ khi cần thiết

---

### 6️⃣ **Xóa Phim**
```
?route=admin/movies/delete&id={MOVIE_ID}
```
**Mô tả:** Xóa phim khỏi database (sẽ xóa luôn tất cả tập phim).

**Ví dụ:**
```
?route=admin/movies/delete&id=1
```

**Đầy đủ:**
```
http://localhost/DuAn1/?route=admin/movies/delete&id=1
```

**⚠️ CẢNH BÁO:** Hành động này không thể hoàn tác!

---

## 👤 **USER VIEW - XEM PHIM**

### 7️⃣ **Xem Phim (Trang User)**
```
?route=movie/watch&id={MOVIE_ID}
```
**Mô tả:** Trang xem phim cho người dùng, hiển thị danh sách tập và video player.

**Xem tập cụ thể:**
```
?route=movie/watch&id={MOVIE_ID}&episode_id={EPISODE_ID}
```

**Ví dụ:**
```
?route=movie/watch&id=1
?route=movie/watch&id=1&episode_id=5
```

**Đầy đủ:**
```
http://localhost/DuAn1/?route=movie/watch&id=1
http://localhost/DuAn1/?route=movie/watch&id=1&episode_id=5
```

---

## 🎯 **HƯỚNG DẪN SỬ DỤNG CÁC LINK**

### 📝 **Cách 1: Sửa Phim và Tập (Trong Admin Panel)**

**Bước 1:** Vào danh sách phim
```
http://localhost/DuAn1/?route=admin/movies
```

**Bước 2:** Tìm phim cần sửa, click nút "Sửa" (icon bút chì)
- Hoặc truy cập trực tiếp: `?route=admin/movies/edit&id={ID_PHIM}`

**Bước 3:** Trong trang sửa phim:
- **Xem danh sách tập:** Cuộn xuống phần "Danh sách tập" (nếu là phim bộ)
- **Thêm tập mới:** Click "Thêm tập" và điền thông tin
- **Sửa tập:** Điền form "Thêm tập mới" với cùng số tập, hệ thống sẽ cập nhật
- **Xóa tập:** Click nút "Xóa" (icon thùng rác) bên cạnh tập

**Bước 4:** Lưu thay đổi
- Click nút "Cập nhật phim" ở cuối form

---

### 🚀 **Cách 2: Import Tập Từ Folder (Nhanh)**

**Bước 1:** Đảm bảo đã có folder và files
- Folder: `data/phim/phimbo/[tên_folder]/`
- Files: `tap1.mp4`, `tap2.mp4`, ...

**Bước 2:** Vào trang import
```
http://localhost/DuAn1/?route=admin/movies/scanEpisodes
```

**Bước 3:** Chọn phim và import
- Chọn phim bộ từ dropdown
- Chọn các file cần import
- Kiểm tra số tập
- Click "Import các tập đã chọn"

---

## 📊 **LẤY ID PHIM/TẬP**

### **Cách 1: Từ Trang Danh Sách Phim**
1. Vào `?route=admin/movies`
2. Cột đầu tiên hiển thị **ID** của phim
3. Click "Sửa" để vào trang sửa, URL sẽ có `id={MOVIE_ID}`

### **Cách 2: Từ Trang Sửa Phim**
- URL: `?route=admin/movies/edit&id={MOVIE_ID}`
- `{MOVIE_ID}` chính là ID của phim

### **Cách 3: Từ Database (phpMyAdmin)**
1. Mở phpMyAdmin: `http://localhost/phpmyadmin`
2. Chọn database `cinehub`
3. Vào bảng `movies`
4. Cột `id` chính là ID của phim

### **Cách 4: Lấy ID Tập Phim**
1. Vào trang sửa phim: `?route=admin/movies/edit&id={MOVIE_ID}`
2. Trong bảng "Danh sách tập", ID tập không hiển thị trực tiếp
3. Hoặc kiểm tra database bảng `episodes`
4. Hoặc dùng Developer Tools (F12) để xem link xóa tập

---

## 💡 **VÍ DỤ THỰC TẾ**

### **Ví dụ với phim "Game of Thrones" có ID = 5:**

#### **1. Danh sách phim:**
```
http://localhost/DuAn1/?route=admin/movies
```

#### **2. Sửa phim (quan trọng nhất):**
```
http://localhost/DuAn1/?route=admin/movies/edit&id=5
```
**→ Trong trang này bạn có thể:**
- Xem tất cả tập: tap1, tap2, tap3, tap4
- Thêm tập mới
- Sửa tập (thêm video cho tập chưa có video)
- Xóa tập

#### **3. Import tập từ folder:**
```
http://localhost/DuAn1/?route=admin/movies/scanEpisodes
```
**→ Chọn phim "Game of Thrones" (ID: 5) và import**

#### **4. Xem phim (user):**
```
http://localhost/DuAn1/?route=movie/watch&id=5
```

#### **5. Xem tập cụ thể (ví dụ tập 1):**
```
http://localhost/DuAn1/?route=movie/watch&id=5&episode_id=1
```

---

## 🔧 **CÁC THAO TÁC THƯỜNG DÙNG**

### ✅ **Thêm Tập Mới**
1. Vào: `?route=admin/movies/edit&id={MOVIE_ID}`
2. Cuộn xuống phần "Thêm tập mới"
3. Click "Thêm tập"
4. Điền số tập, tiêu đề, upload video (tùy chọn)
5. Click "Cập nhật phim"

### ✅ **Sửa Tập (Thêm Video)**
1. Vào: `?route=admin/movies/edit&id={MOVIE_ID}`
2. Tìm tập chưa có video (có badge "Chưa có video")
3. Click "Thêm tập" với cùng số tập
4. Upload video file
5. Click "Cập nhật phim"

### ✅ **Xóa Tập**
1. Vào: `?route=admin/movies/edit&id={MOVIE_ID}`
2. Tìm tập cần xóa trong bảng "Danh sách tập"
3. Click nút "Xóa" (icon thùng rác)
4. Xác nhận xóa

### ✅ **Import Nhiều Tập Cùng Lúc**
1. Vào: `?route=admin/movies/scanEpisodes`
2. Chọn phim
3. Chọn tất cả files
4. Kiểm tra số tập
5. Click "Import các tập đã chọn"

---

## 📌 **TÓM TẮT LINK QUAN TRỌNG NHẤT**

| Mục đích | Link |
|----------|------|
| **Sửa phim và tập** | `?route=admin/movies/edit&id={MOVIE_ID}` |
| **Import tập từ folder** | `?route=admin/movies/scanEpisodes` |
| **Danh sách phim** | `?route=admin/movies` |
| **Xem phim (user)** | `?route=movie/watch&id={MOVIE_ID}` |

---

**💡 Tip:** 
- Bookmark link sửa phim của phim thường dùng
- Link import tập rất hữu ích khi thêm nhiều tập cùng lúc
- Luôn kiểm tra lại sau khi sửa/xóa

**🎉 Chúc bạn sửa phim thành công!**

---

# 📊 Báo Cáo Thay Đổi Folder Phim Bộ

## 🔍 Tình Trạng Hiện Tại

### 📁 Cấu Trúc Folder

```
data/phim/phimbo/
└── gameofthrones/
    ├── tap1.mp4  (5.2 MB - 11/19/2025 8:23 AM)
    ├── tap2.mp4  (5.2 MB - 11/19/2025 8:23 AM)
    ├── tap3.mp4  (5.2 MB - 11/19/2025 8:23 AM)
    └── tap4.mp4  (5.2 MB - 11/19/2025 8:23 AM)
```

**Tổng:** 4 file video (khoảng 20.8 MB)

---

## 📝 So Sánh Thay Đổi

### ❌ Trước Đây (Cũ)
- **Folder:** `data/phim/phimbo/gameofthrones/`
- **File:** `game_of_thrones_tap1.mp4` (chỉ 1 file)
- **Số tập:** 1 tập

### ✅ Hiện Tại (Mới)
- **Folder:** `data/phim/phimbo/gameofthrones/` (giữ nguyên)
- **Files:** 
  - `tap1.mp4` ✨ (tên file đơn giản hơn)
  - `tap2.mp4` ✨ (mới thêm)
  - `tap3.mp4` ✨ (mới thêm)
  - `tap4.mp4` ✨ (mới thêm)
- **Số tập:** 4 tập

---

## ✨ Những Thay Đổi Chính

### 1. **Đổi Tên File**
- **Cũ:** `game_of_thrones_tap1.mp4`
- **Mới:** `tap1.mp4`
- **Lý do:** Tên file ngắn gọn, dễ quản lý hơn

### 2. **Thêm 3 Tập Mới**
- ✅ Thêm `tap2.mp4`
- ✅ Thêm `tap3.mp4`
- ✅ Thêm `tap4.mp4`

### 3. **Format Tên File Chuẩn**
- Format: `tap[SO]` (ví dụ: `tap1`, `tap2`, `tap3`, `tap4`)
- Hệ thống sẽ tự động nhận diện số tập từ format này ✅

---

## 🔧 Hệ Thống Có Hỗ Trợ Format Này Không?

### ✅ Có! Hệ thống đã hỗ trợ format `tap1.mp4`

Logic nhận diện số tập hiện tại:
- ✅ `tap1.mp4` → Số tập: **1**
- ✅ `tap2.mp4` → Số tập: **2**
- ✅ `tap3.mp4` → Số tập: **3**
- ✅ `tap4.mp4` → Số tập: **4**
- ✅ `tap_1.mp4` → Số tập: **1** (có dấu gạch dưới)
- ✅ `tap 1.mp4` → Số tập: **1** (có khoảng trắng)
- ✅ `episode1.mp4` → Số tập: **1**
- ✅ `ep_5.mp4` → Số tập: **5**

**Regex pattern:** `/(?:tap|episode|ep)[_ ]?(\d+)/i`

---

## 📋 Hành Động Tiếp Theo

### ✅ Đã Hoàn Thành
1. ✅ Tạo folder `data/phim/phimbo/gameofthrones/`
2. ✅ Thêm 4 file video với format chuẩn
3. ✅ Đặt tên file đơn giản (`tap1.mp4`, `tap2.mp4`, etc.)

### 🔄 Cần Làm Tiếp

#### **Cách 1: Sử Dụng Script Demo (Nhanh)**
1. Truy cập: `http://localhost/DuAn1/demo_import_episodes.php`
2. Script sẽ tự động:
   - ✅ Tạo phim "Game of Thrones" (nếu chưa có)
   - ✅ Import 4 tập: `tap1.mp4`, `tap2.mp4`, `tap3.mp4`, `tap4.mp4`
   - ✅ Nhận diện số tập tự động: 1, 2, 3, 4

#### **Cách 2: Import Thủ Công (Chi Tiết)**
1. **Vào Admin Panel:**
   - Truy cập: `http://localhost/DuAn1/?route=admin/movies`

2. **Thêm Phim (Nếu Chưa Có):**
   - Click "Thêm phim mới"
   - Điền thông tin: **Game of Thrones**
   - Chọn loại: **Phim bộ** ⚠️ QUAN TRỌNG
   - Lưu phim

3. **Import Episodes:**
   - Click nút **"Import tập từ folder"** (màu xanh dương)
   - Chọn phim "Game of Thrones" từ dropdown
   - Kiểm tra danh sách 4 file:
     - ✅ `tap1.mp4` → Số tập: 1
     - ✅ `tap2.mp4` → Số tập: 2
     - ✅ `tap3.mp4` → Số tập: 3
     - ✅ `tap4.mp4` → Số tập: 4
   - Click **"Import các tập đã chọn"**

4. **Kiểm Tra Kết Quả:**
   - Admin → Quản lý phim → Sửa phim → Xem danh sách tập
   - User → Xem phim → Xem danh sách tập

---

## 🎯 Kết Luận

### ✅ **Format Tên File Mới Rất Tốt!**

**Ưu điểm:**
- ✅ Ngắn gọn, dễ quản lý
- ✅ Hệ thống tự động nhận diện số tập
- ✅ Dễ thêm tập mới (chỉ cần copy và đổi tên: `tap5.mp4`, `tap6.mp4`, ...)
- ✅ Tránh tên file quá dài

**Format khuyến nghị cho các phim khác:**
- ✅ `tap1.mp4`, `tap2.mp4`, `tap3.mp4`, ...
- ✅ Hoặc: `episode1.mp4`, `episode2.mp4`, ...
- ✅ Hoặc: `ep1.mp4`, `ep2.mp4`, ...

**Tránh:**
- ❌ Tên file quá dài: `game_of_thrones_season_1_episode_1.mp4`
- ❌ Không có số: `introduction.mp4`, `finale.mp4` (không tự động nhận diện)

---

## 📊 Thống Kê

| Thông Tin | Giá Trị |
|-----------|---------|
| **Folder** | `gameofthrones/` |
| **Số file** | 4 file |
| **Tổng dung lượng** | ~20.8 MB |
| **Format tên file** | `tap[SO].mp4` |
| **Trạng thái nhận diện** | ✅ Tự động (1, 2, 3, 4) |
| **Sẵn sàng import** | ✅ Có |

---

**📝 Ghi Chú:** 
- Các file đã được chuẩn bị đúng format
- Hệ thống đã sẵn sàng import
- Chỉ cần chạy script demo hoặc import thủ công để hoàn tất

**🎉 Chúc bạn thành công!**

---

# Hướng dẫn Import Phim Bộ - Ví dụ Game of Thrones

## 📁 Cấu trúc Folder

Đảm bảo bạn đã tạo folder theo cấu trúc sau:

```
data/
└── phim/
    └── phimbo/
        └── gameofthrones/
            ├── game_of_thrones_tap1.mp4
            ├── game_of_thrones_tap2.mp4
            ├── game_of_thrones_tap3.mp4
            └── ...
```

**Lưu ý:** 
- Folder phải nằm trong `data/phim/phimbo/`
- Tên folder nên viết thường, không dấu (ví dụ: `gameofthrones`, `strangerthings`, `breakingbad`)
- Tên file có thể chứa số tập: `tap1`, `tap_1`, `episode_1`, `ep1`, etc.

---

## 🎬 Bước 1: Thêm Phim Mới Vào Database

### 1.1. Đăng nhập Admin Panel
- Truy cập: `http://localhost/DuAn1/?route=admin`
- Đăng nhập với tài khoản admin

### 1.2. Thêm Phim Mới
1. Vào **Quản lý phim** → Click **"Thêm phim mới"**
2. Điền thông tin:
   - **Tiêu đề phim:** `Game of Thrones` (hoặc tên phim của bạn)
   - **Loại phim:** Chọn `Phim bộ` ⚠️ **QUAN TRỌNG: Phải chọn "Phim bộ"**
   - **Thể loại:** Chọn thể loại (ví dụ: Hành động, Khoa học viễn tưởng)
   - **Cấp độ:** Free/Silver/Gold/Premium
   - **Trạng thái:** Chiếu online
   - **Rating:** (ví dụ: 9.3)
   - **Mô tả:** Mô tả về phim
   - **Đạo diễn, Diễn viên, Quốc gia, Ngôn ngữ:** (tùy chọn)
   - **Poster/Thumbnail:** Upload ảnh poster

3. **KHÔNG CẦN** thêm tập ở bước này (có thể bỏ qua phần "Thêm tập")
4. Click **"Lưu phim"**

**Kết quả:** Phim đã được thêm vào database với ID (ví dụ: ID = 5)

---

## 📂 Bước 2: Chuẩn Bị File Video

### 2.1. Tạo Folder
Tạo folder với tên dễ nhớ (không dấu, viết thường):

```
C:\xampp\htdocs\DuAn1\data\phim\phimbo\gameofthrones\
```

### 2.2. Copy File Video
Copy các file video vào folder, đặt tên theo format:
- `game_of_thrones_tap1.mp4`
- `game_of_thrones_tap2.mp4`
- `game_of_thrones_tap3.mp4`
- Hoặc: `tap1.mp4`, `episode_1.mp4`, `ep_01.mp4`, etc.

**Lưu ý:** Hệ thống sẽ tự động nhận diện số tập từ tên file!

---

## 📥 Bước 3: Import Episodes Từ Folder

### 3.1. Vào Trang Import
1. Vào **Quản lý phim** 
2. Click nút **"Import tập từ folder"** (màu xanh dương, có icon folder)
   - Hoặc truy cập: `http://localhost/DuAn1/?route=admin/movies/scanEpisodes`

### 3.2. Chọn Phim
1. Tìm folder `gameofthrones` trong danh sách
2. Trong dropdown **"Chọn phim bộ"**, chọn phim **"Game of Thrones"** (hoặc ID phim bạn vừa tạo)
3. Hệ thống sẽ hiển thị danh sách file video tìm thấy

### 3.3. Kiểm Tra Số Tập
- Hệ thống tự động nhận diện số tập từ tên file
- Ví dụ:
  - `game_of_thrones_tap1.mp4` → Số tập: **1** ✅
  - `game_of_thrones_tap2.mp4` → Số tập: **2** ✅
- Nếu không đúng, bạn có thể sửa trực tiếp trong ô "Số tập"

### 3.4. Tùy Chỉnh (Nếu Cần)
- **Bỏ chọn** các file không muốn import (uncheck checkbox)
- **Sửa tên tập:** Click nút "Sửa" để đặt tên tập tùy chỉnh (ví dụ: "Tập 1 - Khởi đầu")
- **Chọn tất cả / Bỏ chọn tất cả:** Dùng các nút ở dưới

### 3.5. Import
1. Click **"Import các tập đã chọn"**
2. Hệ thống sẽ import vào database
3. Thông báo thành công: "Đã import X tập mới và cập nhật Y tập cho phim: Game of Thrones"

---

## ✅ Bước 4: Kiểm Tra Kết Quả

### 4.1. Xem Danh Sách Tập Trong Admin
1. Vào **Quản lý phim** → Click **"Sửa"** phim Game of Thrones
2. Cuộn xuống phần **"Danh sách tập"**
3. Bạn sẽ thấy các tập đã được import:
   - Tập 1: `data/phim/phimbo/gameofthrones/game_of_thrones_tap1.mp4`
   - Tập 2: `data/phim/phimbo/gameofthrones/game_of_thrones_tap2.mp4`
   - ...

### 4.2. Xem Phim Từ Trang User
1. Vào trang chủ hoặc danh sách phim
2. Tìm và click vào phim "Game of Thrones"
3. Trang xem phim sẽ hiển thị:
   - Video player (tự động chọn tập đầu tiên có video)
   - **Danh sách tập** bên dưới với các tập đã import
   - Click vào tập bất kỳ để xem

---

## 🎯 Ví Dụ Cụ Thể

### Ví Dụ 1: Tên File Có "tap"
**File:** `game_of_thrones_tap1.mp4`
- ✅ Tự động nhận diện: Số tập = **1**

### Ví Dụ 2: Tên File Có "episode"
**File:** `breaking_bad_episode_5.mp4`
- ✅ Tự động nhận diện: Số tập = **5**

### Ví Dụ 3: Tên File Đơn Giản
**File:** `tap3.mp4`
- ✅ Tự động nhận diện: Số tập = **3**

### Ví Dụ 4: Tên File Không Có Số
**File:** `intro.mp4`
- ⚠️ Hệ thống sẽ gán số tập theo thứ tự: **1, 2, 3, ...**
- Bạn nên sửa lại số tập cho đúng

---

## ❓ Câu Hỏi Thường Gặp

### Q: Tôi đã thêm file vào folder nhưng không thấy trong danh sách import?
**A:** 
- Kiểm tra folder có đúng đường dẫn: `data/phim/phimbo/[tên_folder]/`
- Đảm bảo file là video (.mp4, .avi, .mkv, .mov, .wmv, .flv)
- Refresh lại trang import

### Q: Số tập tự động nhận diện sai, làm sao?
**A:** 
- Bạn có thể sửa trực tiếp trong ô "Số tập" trước khi import
- Hoặc đổi tên file để có số tập rõ ràng hơn

### Q: Import xong nhưng không thấy tập trong trang xem phim?
**A:**
- Kiểm tra lại trong Admin → Sửa phim → Xem danh sách tập
- Đảm bảo phim đã chọn loại "Phim bộ" (type = 'phimbo')
- Kiểm tra file video có tồn tại không

### Q: Có thể import nhiều lần không?
**A:**
- ✅ Có! Bạn có thể import thêm tập mới bất cứ lúc nào
- Nếu tập đã tồn tại và đã có video, hệ thống sẽ bỏ qua
- Nếu tập đã tồn tại nhưng chưa có video, hệ thống sẽ cập nhật

### Q: Tôi muốn thêm video cho tập đã có trong database?
**A:**
- Vào Admin → Quản lý phim → Sửa phim
- Ở phần "Thêm tập mới", nhập số tập giống tập đã có
- Upload video file
- Hệ thống sẽ cập nhật video_url cho tập đó

---

## 📝 Tóm Tắt Quy Trình

1. ✅ **Thêm phim** vào database (chọn "Phim bộ")
2. ✅ **Tạo folder** trong `data/phim/phimbo/[tên_folder]/`
3. ✅ **Copy file video** vào folder
4. ✅ **Import** từ trang Admin → Import tập từ folder
5. ✅ **Kiểm tra** danh sách tập trong Admin và trang xem phim

---

## 🚀 Demo Tự Động (Nhanh)

Bạn có thể sử dụng script demo tự động để xem ví dụ:

1. **Đảm bảo** đã có:
   - Folder: `data/phim/phimbo/gameofthrones/`
   - File: `game_of_thrones_tap1.mp4` trong folder đó

2. **Truy cập:** `http://localhost/DuAn1/demo_import_episodes.php`

3. **Script sẽ tự động:**
   - ✅ Kiểm tra folder và files
   - ✅ Tạo phim "Game of Thrones" (nếu chưa có)
   - ✅ Import tất cả episodes từ folder
   - ✅ Hiển thị kết quả và link xem phim

**Lưu ý:** Script này chỉ để demo. Để import thêm tập, hãy dùng tính năng "Import tập từ folder" trong Admin Panel.

---

**Chúc bạn thành công! 🎉**

---

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

---

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
