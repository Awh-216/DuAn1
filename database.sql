
CREATE DATABASE IF NOT EXISTS cinehub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cinehub;

-- ===========================
-- 1️⃣ BẢNG NGƯỜI DÙNG
-- ===========================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    birthdate DATE DEFAULT NULL,
    rank ENUM('Bronze', 'Silver', 'Gold', 'Platinum') DEFAULT 'Bronze',
    points INT DEFAULT 0,
    subscription_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id)
) ENGINE=InnoDB;

-- ===========================
-- 2️⃣ BẢNG GÓI XEM PHIM (SUBSCRIPTIONS)
-- ===========================
CREATE TABLE subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) DEFAULT 0,
    description TEXT,
    benefits TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ===========================
-- 3️⃣ BẢNG THỂ LOẠI PHIM
-- ===========================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    parent_id INT DEFAULT NULL,
    FOREIGN KEY (parent_id) REFERENCES categories(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ===========================
-- 4️⃣ BẢNG PHIM
-- ===========================
CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category_id INT DEFAULT NULL,
    level ENUM('Free', 'Silver', 'Gold', 'Premium') DEFAULT 'Free',
    duration INT DEFAULT NULL, -- phút
    description TEXT,
    director VARCHAR(100),
    actors TEXT,
    video_url VARCHAR(255),
    trailer_url VARCHAR(255),
    thumbnail VARCHAR(255),
    status ENUM('Sắp chiếu', 'Chiếu rạp', 'Chiếu online') DEFAULT 'Sắp chiếu',
    rating FLOAT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- ===========================
-- 5️⃣ BẢNG RẠP & SUẤT CHIẾU
-- ===========================
CREATE TABLE theaters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    location VARCHAR(255),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE showtimes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    theater_id INT NOT NULL,
    show_date DATE NOT NULL,
    show_time TIME NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (theater_id) REFERENCES theaters(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===========================
-- 6️⃣ BẢNG VÉ XEM PHIM (TICKET BOOKING)
-- ===========================
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    showtime_id INT NOT NULL,
    seat VARCHAR(10) NOT NULL,
    qr_code VARCHAR(255) DEFAULT NULL,
    price DECIMAL(10,2),
    status ENUM('Đã đặt', 'Đã hủy') DEFAULT 'Đã đặt',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===========================
-- 7️⃣ BẢNG LỊCH SỬ XEM PHIM (ONLINE)
-- ===========================
CREATE TABLE watch_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    last_time INT DEFAULT 0, -- giây đã xem
    rating TINYINT DEFAULT NULL,
    favorite BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_movie (user_id, movie_id)
) ENGINE=InnoDB;

-- ===========================
-- 8️⃣ BẢNG GIAO DỊCH (TRANSACTIONS)
-- ===========================
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('ticket','subscription') NOT NULL,
    related_id INT DEFAULT NULL,
    amount DECIMAL(10,2) NOT NULL,
    method ENUM('Momo','ZaloPay','Stripe','Bank','Cash') DEFAULT 'Momo',
    status ENUM('Thành công','Thất bại','Đang xử lý') DEFAULT 'Thành công',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===========================
-- 9️⃣ BẢNG ĐÁNH GIÁ & PHẢN HỒI
-- ===========================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_id INT NOT NULL,
    rating TINYINT CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===========================
-- 🔟 DỮ LIỆU MẪU
-- ===========================
INSERT INTO subscriptions (name, price, description, benefits) VALUES
('Free', 0, 'Xem trailer, phim miễn phí', 'Giới hạn nội dung, có quảng cáo'),
('Silver', 79000, 'Xem phim HD không quảng cáo', 'HD quality, không quảng cáo'),
('Gold', 129000, 'Full HD, nội dung độc quyền', 'Full HD, nội dung mới'),
('Premium', 199000, '4K, xem sớm, ưu đãi vé rạp', '4K, early access, ưu đãi vé');

INSERT INTO categories (name) VALUES
('Hành động'), ('Tình cảm'), ('Hài'), ('Kinh dị'), ('Hoạt hình');
