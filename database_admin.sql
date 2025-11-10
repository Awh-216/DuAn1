-- ===========================
-- ADMIN SYSTEM - Bổ sung vào database.sql
-- ===========================

-- Bảng Roles (Vai trò)
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Bảng Permissions (Quyền)
CREATE TABLE IF NOT EXISTS permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    module VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Bảng User Roles (Người dùng - Vai trò)
CREATE TABLE IF NOT EXISTS user_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_role (user_id, role_id)
) ENGINE=InnoDB;

-- Bảng Role Permissions (Vai trò - Quyền)
CREATE TABLE IF NOT EXISTS role_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_role_permission (role_id, permission_id)
) ENGINE=InnoDB;

-- Bảng Admin Logs (Audit Trail)
CREATE TABLE IF NOT EXISTS admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(100) NOT NULL,
    module VARCHAR(50) NOT NULL,
    target_type VARCHAR(50),
    target_id INT,
    old_data TEXT,
    new_data TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_created_at (created_at),
    INDEX idx_module (module)
) ENGINE=InnoDB;

-- Bảng Support Tickets
CREATE TABLE IF NOT EXISTS support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('Mới', 'Đang xử lý', 'Đã giải quyết', 'Đã đóng') DEFAULT 'Mới',
    priority ENUM('Thấp', 'Trung bình', 'Cao', 'Khẩn cấp') DEFAULT 'Trung bình',
    tags VARCHAR(255),
    assigned_to INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Bảng Coupons/Vouchers
CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    type ENUM('percentage', 'fixed') NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    min_amount DECIMAL(10,2) DEFAULT 0,
    max_discount DECIMAL(10,2) DEFAULT NULL,
    usage_limit INT DEFAULT NULL,
    used_count INT DEFAULT 0,
    valid_from DATETIME NOT NULL,
    valid_to DATETIME NOT NULL,
    status ENUM('active', 'inactive', 'expired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Bảng Promotions
CREATE TABLE IF NOT EXISTS promotions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    type ENUM('discount', 'bundle', 'free_trial') NOT NULL,
    discount_value DECIMAL(10,2),
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM('draft', 'active', 'ended') DEFAULT 'draft',
    target_audience ENUM('all', 'new_users', 'premium') DEFAULT 'all',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Bảng Comments (Bình luận)
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    parent_id INT DEFAULT NULL,
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'spam') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE,
    INDEX idx_movie_id (movie_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;

-- Bảng System Config
CREATE TABLE IF NOT EXISTS system_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) NOT NULL UNIQUE,
    config_value TEXT,
    description TEXT,
    updated_by INT DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Cập nhật bảng users để thêm role
ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('user', 'admin', 'moderator') DEFAULT 'user';
ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE;
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login DATETIME DEFAULT NULL;

-- Cập nhật bảng movies để thêm các trường admin cần
ALTER TABLE movies ADD COLUMN IF NOT EXISTS status_admin ENUM('draft', 'scheduled', 'published', 'archived') DEFAULT 'draft';
ALTER TABLE movies ADD COLUMN IF NOT EXISTS publish_date DATETIME DEFAULT NULL;
ALTER TABLE movies ADD COLUMN IF NOT EXISTS geo_restriction TEXT;
ALTER TABLE movies ADD COLUMN IF NOT EXISTS drm_enabled BOOLEAN DEFAULT FALSE;
ALTER TABLE movies ADD COLUMN IF NOT EXISTS banner VARCHAR(255) DEFAULT NULL;
ALTER TABLE movies ADD COLUMN IF NOT EXISTS country VARCHAR(100) DEFAULT NULL;
ALTER TABLE movies ADD COLUMN IF NOT EXISTS language VARCHAR(50) DEFAULT NULL;
ALTER TABLE movies ADD COLUMN IF NOT EXISTS age_rating VARCHAR(10) DEFAULT NULL;

-- Cập nhật bảng theaters để thêm thông tin chi tiết
ALTER TABLE theaters ADD COLUMN IF NOT EXISTS total_screens INT DEFAULT 1;
ALTER TABLE theaters ADD COLUMN IF NOT EXISTS address TEXT;
ALTER TABLE theaters ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE;

-- Bảng Theater Screens (Phòng chiếu)
CREATE TABLE IF NOT EXISTS theater_screens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    theater_id INT NOT NULL,
    screen_name VARCHAR(100) NOT NULL,
    total_seats INT NOT NULL,
    seat_layout TEXT,
    screen_type ENUM('2D', '3D', 'IMAX', '4DX') DEFAULT '2D',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (theater_id) REFERENCES theaters(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Cập nhật bảng showtimes để thêm screen_id
ALTER TABLE showtimes ADD COLUMN IF NOT EXISTS screen_id INT DEFAULT NULL;
ALTER TABLE showtimes ADD FOREIGN KEY IF NOT EXISTS (screen_id) REFERENCES theater_screens(id) ON DELETE SET NULL;

-- ===========================
-- INSERT DỮ LIỆU MẪU
-- ===========================

-- Tạo roles
INSERT INTO roles (name, description) VALUES
('Super Admin', 'Quyền cao nhất, toàn quyền hệ thống'),
('Admin', 'Quản trị viên, quản lý nội dung và người dùng'),
('Moderator', 'Điều hành viên, quản lý bình luận và hỗ trợ'),
('Content Manager', 'Quản lý nội dung phim'),
('Support Staff', 'Nhân viên hỗ trợ khách hàng')
ON DUPLICATE KEY UPDATE description=VALUES(description);

-- Tạo permissions
INSERT INTO permissions (name, description, module) VALUES
-- User Management
('users.view', 'Xem danh sách người dùng', 'users'),
('users.create', 'Tạo người dùng mới', 'users'),
('users.edit', 'Sửa thông tin người dùng', 'users'),
('users.delete', 'Xóa người dùng', 'users'),
('users.block', 'Chặn/Mở khóa người dùng', 'users'),
('users.reset_password', 'Reset mật khẩu', 'users'),

-- Content Management
('movies.view', 'Xem danh sách phim', 'movies'),
('movies.create', 'Thêm phim mới', 'movies'),
('movies.edit', 'Sửa thông tin phim', 'movies'),
('movies.delete', 'Xóa phim', 'movies'),
('movies.publish', 'Xuất bản phim', 'movies'),

-- Booking Management
('bookings.view', 'Xem đặt vé', 'bookings'),
('bookings.create', 'Tạo vé thủ công', 'bookings'),
('bookings.edit', 'Sửa vé', 'bookings'),
('bookings.cancel', 'Hủy vé', 'bookings'),
('bookings.refund', 'Hoàn tiền', 'bookings'),

-- Theater Management
('theaters.view', 'Xem rạp', 'theaters'),
('theaters.create', 'Thêm rạp', 'theaters'),
('theaters.edit', 'Sửa rạp', 'theaters'),
('theaters.delete', 'Xóa rạp', 'theaters'),

-- Analytics
('analytics.view', 'Xem báo cáo', 'analytics'),
('analytics.export', 'Xuất báo cáo', 'analytics'),

-- System
('system.config', 'Cấu hình hệ thống', 'system'),
('system.logs', 'Xem logs', 'system'),

-- Support
('support.view', 'Xem ticket', 'support'),
('support.assign', 'Gán ticket', 'support'),
('support.resolve', 'Giải quyết ticket', 'support')
ON DUPLICATE KEY UPDATE description=VALUES(description), module=VALUES(module);

-- Gán quyền cho Super Admin (tất cả quyền)
INSERT INTO role_permissions (role_id, permission_id)
SELECT 1, id FROM permissions
ON DUPLICATE KEY UPDATE role_id=VALUES(role_id);

-- Tạo tài khoản admin mẫu (password: admin123)
-- Password hash cho "admin123" được tạo bằng: password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (name, email, password, role, is_active) VALUES
('Super Admin', 'admin@cinehub.com', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'admin', TRUE)
ON DUPLICATE KEY UPDATE role='admin', is_active=TRUE, password='$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy';

-- Gán role Super Admin cho admin
INSERT INTO user_roles (user_id, role_id)
SELECT u.id, 1 FROM users u WHERE u.email = 'admin@cinehub.com'
ON DUPLICATE KEY UPDATE role_id=1;

-- System Config mẫu
INSERT INTO system_config (config_key, config_value, description) VALUES
('maintenance_mode', '0', 'Chế độ bảo trì (0=off, 1=on)'),
('max_upload_size', '500', 'Kích thước upload tối đa (MB)'),
('payment_gateway', 'vnpay', 'Cổng thanh toán mặc định'),
('default_currency', 'VND', 'Đơn vị tiền tệ'),
('site_name', 'CineHub', 'Tên website')
ON DUPLICATE KEY UPDATE config_value=VALUES(config_value);

