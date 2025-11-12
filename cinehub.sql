-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2025 at 03:47 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12
--
-- Database: `cinehub`
-- File này chứa toàn bộ cấu trúc database và dữ liệu mẫu

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cinehub`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `module` varchar(50) NOT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `old_data` text DEFAULT NULL,
  `new_data` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_id`) VALUES
(1, 'Hành động', NULL),
(2, 'Tình cảm', NULL),
(3, 'Hài', NULL),
(4, 'Kinh dị', NULL),
(5, 'Hoạt hình', NULL),
(6, 'Khoa học viễn tưởng', NULL),
(7, 'Phiêu lưu', NULL),
(8, 'Tài liệu', NULL),
(9, 'Chiến tranh', NULL),
(10, 'Thể thao', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','rejected','spam') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `min_amount` decimal(10,2) DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `valid_from` datetime NOT NULL,
  `valid_to` datetime NOT NULL,
  `status` enum('active','inactive','expired') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `level` enum('Free','Silver','Gold','Premium') DEFAULT 'Free',
  `duration` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `director` varchar(100) DEFAULT NULL,
  `actors` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `trailer_url` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `status` enum('Sắp chiếu','Chiếu rạp','Chiếu online') DEFAULT 'Sắp chiếu',
  `rating` float DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_admin` enum('draft','scheduled','published','archived') DEFAULT 'draft',
  `publish_date` datetime DEFAULT NULL,
  `geo_restriction` text DEFAULT NULL,
  `drm_enabled` tinyint(1) DEFAULT 0,
  `banner` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `age_rating` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`title`, `category_id`, `level`, `duration`, `description`, `director`, `actors`, `video_url`, `trailer_url`, `thumbnail`, `status`, `rating`, `status_admin`, `country`, `language`, `age_rating`) VALUES
('Avengers: Endgame', 1, 'Premium', 181, 'Phim siêu anh hùng Marvel, kết thúc của Infinity Saga', 'Anthony Russo, Joe Russo', 'Robert Downey Jr., Chris Evans, Mark Ruffalo', 'https://example.com/avengers.mp4', 'https://example.com/avengers-trailer.mp4', 'avengers.jpg', 'Chiếu online', 9.2, 'published', 'Mỹ', 'Tiếng Anh', 'PG-13'),
('Titanic', 2, 'Gold', 194, 'Câu chuyện tình yêu trên con tàu định mệnh', 'James Cameron', 'Leonardo DiCaprio, Kate Winslet', 'https://example.com/titanic.mp4', 'https://example.com/titanic-trailer.mp4', 'titanic.jpg', 'Chiếu online', 8.8, 'published', 'Mỹ', 'Tiếng Anh', 'PG-13'),
('The Hangover', 3, 'Silver', 100, 'Phim hài về chuyến đi Las Vegas đầy biến cố', 'Todd Phillips', 'Bradley Cooper, Ed Helms, Zach Galifianakis', 'https://example.com/hangover.mp4', 'https://example.com/hangover-trailer.mp4', 'hangover.jpg', 'Chiếu online', 7.7, 'published', 'Mỹ', 'Tiếng Anh', 'R'),
('The Conjuring', 4, 'Gold', 112, 'Phim kinh dị về các nhà điều tra siêu nhiên', 'James Wan', 'Patrick Wilson, Vera Farmiga', 'https://example.com/conjuring.mp4', 'https://example.com/conjuring-trailer.mp4', 'conjuring.jpg', 'Chiếu online', 7.5, 'published', 'Mỹ', 'Tiếng Anh', 'R'),
('Toy Story 4', 5, 'Free', 100, 'Cuộc phiêu lưu mới của Woody và Buzz', 'Josh Cooley', 'Tom Hanks, Tim Allen', 'https://example.com/toystory.mp4', 'https://example.com/toystory-trailer.mp4', 'toystory.jpg', 'Chiếu online', 8.0, 'published', 'Mỹ', 'Tiếng Anh', 'G'),
('Interstellar', 6, 'Premium', 169, 'Cuộc hành trình không gian để cứu nhân loại', 'Christopher Nolan', 'Matthew McConaughey, Anne Hathaway', 'https://example.com/interstellar.mp4', 'https://example.com/interstellar-trailer.mp4', 'interstellar.jpg', 'Chiếu online', 8.6, 'published', 'Mỹ', 'Tiếng Anh', 'PG-13'),
('Indiana Jones', 7, 'Gold', 122, 'Cuộc phiêu lưu tìm kiếm cổ vật', 'Steven Spielberg', 'Harrison Ford', 'https://example.com/indiana.mp4', 'https://example.com/indiana-trailer.mp4', 'indiana.jpg', 'Chiếu online', 8.2, 'published', 'Mỹ', 'Tiếng Anh', 'PG-13');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `module` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `description`, `module`, `created_at`) VALUES
(1, 'users.view', 'Xem danh sách người dùng', 'users', '2025-11-10 16:41:17'),
(2, 'users.create', 'Tạo người dùng mới', 'users', '2025-11-10 16:41:17'),
(3, 'users.edit', 'Sửa thông tin người dùng', 'users', '2025-11-10 16:41:17'),
(4, 'users.delete', 'Xóa người dùng', 'users', '2025-11-10 16:41:17'),
(5, 'users.block', 'Chặn/Mở khóa người dùng', 'users', '2025-11-10 16:41:17'),
(6, 'users.reset_password', 'Reset mật khẩu', 'users', '2025-11-10 16:41:17'),
(7, 'movies.view', 'Xem danh sách phim', 'movies', '2025-11-10 16:41:17'),
(8, 'movies.create', 'Thêm phim mới', 'movies', '2025-11-10 16:41:17'),
(9, 'movies.edit', 'Sửa thông tin phim', 'movies', '2025-11-10 16:41:17'),
(10, 'movies.delete', 'Xóa phim', 'movies', '2025-11-10 16:41:17'),
(11, 'movies.publish', 'Xuất bản phim', 'movies', '2025-11-10 16:41:17'),
(12, 'bookings.view', 'Xem đặt vé', 'bookings', '2025-11-10 16:41:17'),
(13, 'bookings.create', 'Tạo vé thủ công', 'bookings', '2025-11-10 16:41:17'),
(14, 'bookings.edit', 'Sửa vé', 'bookings', '2025-11-10 16:41:17'),
(15, 'bookings.cancel', 'Hủy vé', 'bookings', '2025-11-10 16:41:17'),
(16, 'bookings.refund', 'Hoàn tiền', 'bookings', '2025-11-10 16:41:17'),
(17, 'theaters.view', 'Xem rạp', 'theaters', '2025-11-10 16:41:17'),
(18, 'theaters.create', 'Thêm rạp', 'theaters', '2025-11-10 16:41:17'),
(19, 'theaters.edit', 'Sửa rạp', 'theaters', '2025-11-10 16:41:17'),
(20, 'theaters.delete', 'Xóa rạp', 'theaters', '2025-11-10 16:41:17'),
(21, 'analytics.view', 'Xem báo cáo', 'analytics', '2025-11-10 16:41:17'),
(22, 'analytics.export', 'Xuất báo cáo', 'analytics', '2025-11-10 16:41:17'),
(23, 'system.config', 'Cấu hình hệ thống', 'system', '2025-11-10 16:41:17'),
(24, 'system.logs', 'Xem logs', 'system', '2025-11-10 16:41:17'),
(25, 'support.view', 'Xem ticket', 'support', '2025-11-10 16:41:17'),
(26, 'support.assign', 'Gán ticket', 'support', '2025-11-10 16:41:17'),
(27, 'support.resolve', 'Giải quyết ticket', 'support', '2025-11-10 16:41:17');

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('discount','bundle','free_trial') NOT NULL,
  `discount_value` decimal(10,2) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `status` enum('draft','active','ended') DEFAULT 'draft',
  `target_audience` enum('all','new_users','premium') DEFAULT 'all',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Super Admin', 'Quyền cao nhất, toàn quyền hệ thống', '2025-11-10 16:41:17'),
(2, 'Admin', 'Quản trị viên, quản lý nội dung và người dùng', '2025-11-10 16:41:17'),
(3, 'Moderator', 'Điều hành viên, quản lý bình luận và hỗ trợ', '2025-11-10 16:41:17'),
(4, 'Content Manager', 'Quản lý nội dung phim', '2025-11-10 16:41:17'),
(5, 'Support Staff', 'Nhân viên hỗ trợ khách hàng', '2025-11-10 16:41:17');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `created_at`) VALUES
(1, 1, 22, '2025-11-10 16:41:17'),
(2, 1, 21, '2025-11-10 16:41:17'),
(3, 1, 15, '2025-11-10 16:41:17'),
(4, 1, 13, '2025-11-10 16:41:17'),
(5, 1, 14, '2025-11-10 16:41:17'),
(6, 1, 16, '2025-11-10 16:41:17'),
(7, 1, 12, '2025-11-10 16:41:17'),
(8, 1, 8, '2025-11-10 16:41:17'),
(9, 1, 10, '2025-11-10 16:41:17'),
(10, 1, 9, '2025-11-10 16:41:17'),
(11, 1, 11, '2025-11-10 16:41:17'),
(12, 1, 7, '2025-11-10 16:41:17'),
(13, 1, 26, '2025-11-10 16:41:17'),
(14, 1, 27, '2025-11-10 16:41:17'),
(15, 1, 25, '2025-11-10 16:41:17'),
(16, 1, 23, '2025-11-10 16:41:17'),
(17, 1, 24, '2025-11-10 16:41:17'),
(18, 1, 18, '2025-11-10 16:41:17'),
(19, 1, 20, '2025-11-10 16:41:17'),
(20, 1, 19, '2025-11-10 16:41:17'),
(21, 1, 17, '2025-11-10 16:41:17'),
(22, 1, 5, '2025-11-10 16:41:17'),
(23, 1, 2, '2025-11-10 16:41:17'),
(24, 1, 4, '2025-11-10 16:41:17'),
(25, 1, 3, '2025-11-10 16:41:17'),
(26, 1, 6, '2025-11-10 16:41:17'),
(27, 1, 1, '2025-11-10 16:41:17');

-- --------------------------------------------------------

--
-- Table structure for table `showtimes`
--

CREATE TABLE `showtimes` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `theater_id` int(11) NOT NULL,
  `show_date` date NOT NULL,
  `show_time` time NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `screen_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `benefits` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `name`, `price`, `description`, `benefits`, `created_at`) VALUES
(1, 'Free', 0.00, 'Xem trailer, phim miễn phí', 'Giới hạn nội dung, có quảng cáo', '2025-11-09 16:03:14'),
(2, 'Silver', 79000.00, 'Xem phim HD không quảng cáo', 'HD quality, không quảng cáo', '2025-11-09 16:03:14'),
(3, 'Gold', 129000.00, 'Full HD, nội dung độc quyền', 'Full HD, nội dung mới', '2025-11-09 16:03:14'),
(4, 'Premium', 199000.00, '4K, xem sớm, ưu đãi vé rạp', '4K, early access, ưu đãi vé', '2025-11-09 16:03:14'),
(5, 'Basic', 49000.00, 'Gói cơ bản với chất lượng SD', 'SD quality, có quảng cáo', '2025-11-09 16:03:14');

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('Mới','Đang xử lý','Đã giải quyết','Đã đóng') DEFAULT 'Mới',
  `priority` enum('Thấp','Trung bình','Cao','Khẩn cấp') DEFAULT 'Trung bình',
  `tags` varchar(255) DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_config`
--

CREATE TABLE `system_config` (
  `id` int(11) NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `config_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_config`
--

INSERT INTO `system_config` (`id`, `config_key`, `config_value`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'maintenance_mode', '0', 'Chế độ bảo trì (0=off, 1=on)', NULL, '2025-11-10 16:41:17'),
(2, 'max_upload_size', '500', 'Kích thước upload tối đa (MB)', NULL, '2025-11-10 16:41:17'),
(3, 'payment_gateway', 'vnpay', 'Cổng thanh toán mặc định', NULL, '2025-11-10 16:41:17'),
(4, 'default_currency', 'VND', 'Đơn vị tiền tệ', NULL, '2025-11-10 16:41:17'),
(5, 'site_name', 'CineHub', 'Tên website', NULL, '2025-11-10 16:41:17');

-- --------------------------------------------------------

--
-- Table structure for table `theaters`
--

CREATE TABLE `theaters` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_screens` int(11) DEFAULT 1,
  `address` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `theater_screens`
--

CREATE TABLE `theater_screens` (
  `id` int(11) NOT NULL,
  `theater_id` int(11) NOT NULL,
  `screen_name` varchar(100) NOT NULL,
  `total_seats` int(11) NOT NULL,
  `seat_layout` text DEFAULT NULL,
  `screen_type` enum('2D','3D','IMAX','4DX') DEFAULT '2D',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `showtime_id` int(11) NOT NULL,
  `seat` varchar(10) NOT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('Đã đặt','Đã hủy') DEFAULT 'Đã đặt',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('ticket','subscription') NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('Momo','ZaloPay','Stripe','Bank','Cash') DEFAULT 'Momo',
  `status` enum('Thành công','Thất bại','Đang xử lý') DEFAULT 'Thành công',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `rank` enum('Bronze','Silver','Gold','Platinum') DEFAULT 'Bronze',
  `points` int(11) DEFAULT 0,
  `subscription_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive','banned') DEFAULT 'active',
  `email_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `role` enum('user','admin','moderator') DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `avatar`, `birthdate`, `rank`, `points`, `subscription_id`, `status`, `email_verified`, `created_at`, `updated_at`, `role`, `is_active`, `last_login`) VALUES
(1, 'Tuan Anh', 'noble.toad.nict@letterguard.net', '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-11-10 15:10:16', '2025-11-10 15:10:16', 'user', 1, NULL),
(2, 'Super Admin', 'admin@cinehub.com', '$2y$10$Q516uBkFiAAoP9sABaJJRebPWUFZjqKI9370ZLqFxlhtFE1L1r9ba', NULL, NULL, 'Bronze', 0, NULL, 'active', 0, '2025-11-10 16:41:17', '2025-11-10 16:45:54', 'admin', 1, NULL),
(3, 'Admin Mới', 'admin2@cinehub.com', '$2y$10$DcmIe4LT6ByLRbWkKLRrE.r4fPNWpOtQylE4ISfTbP6TeCs/J5T2a', NULL, NULL, 'Bronze', 0, NULL, 'active', 0, '2025-11-12 02:39:06', '2025-11-12 02:39:39', 'admin', 1, NULL),
(4, 'Nguyễn Văn A', 'nguyenvana@example.com', '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Silver', 500, 2, 'active', 0, '2025-11-10 15:10:16', '2025-11-10 15:10:16', 'user', 1, NULL),
(5, 'Trần Thị B', 'tranthib@example.com', '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Gold', 1200, 3, 'active', 0, '2025-11-10 15:10:16', '2025-11-10 15:10:16', 'user', 1, NULL),
(6, 'Lê Văn C', 'levanc@example.com', '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Bronze', 100, 1, 'active', 0, '2025-11-10 15:10:16', '2025-11-10 15:10:16', 'user', 1, NULL),
(7, 'Phạm Thị D', 'phamthid@example.com', '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Platinum', 2500, 4, 'active', 0, '2025-11-10 15:10:16', '2025-11-10 15:10:16', 'user', 1, NULL),
(8, 'Hoàng Văn E', 'hoangvane@example.com', '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Silver', 800, 2, 'active', 0, '2025-11-10 15:10:16', '2025-11-10 15:10:16', 'user', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`) VALUES
(1, 2, 1, '2025-11-10 16:45:54'),
(2, 3, 1, '2025-11-12 02:39:39'),
(3, 4, 3, '2025-11-10 16:45:54'),
(4, 5, 4, '2025-11-10 16:45:54'),
(5, 1, 5, '2025-11-10 16:45:54');

-- --------------------------------------------------------

--
-- Table structure for table `watch_history`
--

CREATE TABLE `watch_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `last_time` int(11) DEFAULT 0,
  `rating` tinyint(4) DEFAULT NULL,
  `favorite` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theaters`
--

INSERT INTO `theaters` (`name`, `location`, `phone`, `address`, `total_screens`, `is_active`) VALUES
('CGV Vincom Center', 'Hà Nội', '0241234567', '72 Lê Thánh Tôn, Hoàn Kiếm, Hà Nội', 8, 1),
('CGV Landmark', 'Hà Nội', '0242345678', '72A Nguyễn Trãi, Thanh Xuân, Hà Nội', 6, 1),
('Lotte Cinema', 'Hồ Chí Minh', '0283456789', '469 Nguyễn Hữu Thọ, Quận 7, TP.HCM', 10, 1),
('Galaxy Cinema', 'Đà Nẵng', '0236456789', '910A Ngô Quyền, Sơn Trà, Đà Nẵng', 7, 1),
('BHD Star Cineplex', 'Hồ Chí Minh', '0284567890', 'L3-Vincom Center, 72 Lê Thánh Tôn, Quận 1, TP.HCM', 9, 1);

--
-- Dumping data for table `theater_screens`
--

INSERT INTO `theater_screens` (`theater_id`, `screen_name`, `total_seats`, `screen_type`, `is_active`) VALUES
(1, 'Phòng 1', 120, '2D', 1),
(1, 'Phòng 2', 150, '3D', 1),
(2, 'Phòng 1', 100, '2D', 1),
(2, 'Phòng 2', 120, 'IMAX', 1),
(3, 'Phòng 1', 200, '4DX', 1),
(3, 'Phòng 2', 180, '3D', 1),
(4, 'Phòng 1', 110, '2D', 1),
(5, 'Phòng 1', 130, '3D', 1);

--
-- Dumping data for table `showtimes`
--

INSERT INTO `showtimes` (`movie_id`, `theater_id`, `show_date`, `show_time`, `price`, `screen_id`) VALUES
(1, 1, '2025-11-15', '10:00:00', 120000.00, 1),
(1, 1, '2025-11-15', '13:30:00', 120000.00, 1),
(2, 2, '2025-11-15', '15:00:00', 100000.00, 3),
(3, 3, '2025-11-16', '18:00:00', 110000.00, 5),
(4, 4, '2025-11-16', '20:30:00', 115000.00, 7),
(5, 5, '2025-11-17', '09:30:00', 90000.00, 8),
(6, 1, '2025-11-17', '14:00:00', 130000.00, 2);

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`user_id`, `showtime_id`, `seat`, `price`, `status`) VALUES
(4, 1, 'A5', 120000.00, 'Đã đặt'),
(4, 1, 'A6', 120000.00, 'Đã đặt'),
(5, 2, 'B10', 120000.00, 'Đã đặt'),
(6, 3, 'C15', 100000.00, 'Đã đặt'),
(7, 4, 'D20', 110000.00, 'Đã đặt'),
(8, 5, 'E12', 115000.00, 'Đã đặt');

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`user_id`, `type`, `related_id`, `amount`, `method`, `status`) VALUES
(4, 'subscription', 2, 79000.00, 'Momo', 'Thành công'),
(5, 'subscription', 3, 129000.00, 'ZaloPay', 'Thành công'),
(6, 'ticket', 1, 240000.00, 'Momo', 'Thành công'),
(7, 'subscription', 4, 199000.00, 'Bank', 'Thành công'),
(8, 'ticket', 5, 110000.00, 'Momo', 'Thành công'),
(4, 'ticket', 2, 120000.00, 'ZaloPay', 'Thành công');

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`user_id`, `movie_id`, `rating`, `comment`) VALUES
(4, 1, 5, 'Phim tuyệt vời! Diễn xuất xuất sắc và cốt truyện hấp dẫn.'),
(5, 2, 5, 'Titanic là một kiệt tác điện ảnh, tình yêu vĩnh cửu.'),
(6, 3, 4, 'Phim hài rất vui nhộn, giải trí tốt.'),
(7, 4, 4, 'Kinh dị đúng nghĩa, rùng rợn từ đầu đến cuối.'),
(8, 5, 5, 'Hoạt hình hay, phù hợp cho cả gia đình.'),
(4, 6, 5, 'Interstellar là một tác phẩm khoa học viễn tưởng xuất sắc.'),
(5, 7, 4, 'Cuộc phiêu lưu thú vị với Indiana Jones.');

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`user_id`, `movie_id`, `content`, `status`) VALUES
(4, 1, 'Phim này thật sự đáng xem!', 'approved'),
(5, 2, 'Cảm động quá, tôi đã khóc.', 'approved'),
(6, 3, 'Hài quá, cười không ngừng.', 'approved'),
(7, 4, 'Sợ quá, không dám xem một mình.', 'approved'),
(8, 5, 'Phim hay cho trẻ em.', 'approved'),
(4, 6, 'Khoa học viễn tưởng đỉnh cao!', 'approved'),
(5, 7, 'Cuộc phiêu lưu thú vị.', 'approved');

--
-- Dumping data for table `watch_history`
--

INSERT INTO `watch_history` (`user_id`, `movie_id`, `last_time`, `rating`, `favorite`) VALUES
(4, 1, 3600, 5, 1),
(5, 2, 7200, 5, 1),
(6, 3, 1800, 4, 0),
(7, 4, 2400, 4, 0),
(8, 5, 3000, 5, 1),
(4, 6, 5400, 5, 1),
(5, 7, 2100, 4, 0);

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`code`, `name`, `type`, `value`, `min_amount`, `max_discount`, `usage_limit`, `valid_from`, `valid_to`, `status`) VALUES
('WELCOME10', 'Giảm 10% cho khách hàng mới', 'percentage', 10.00, 50000.00, 50000.00, 100, '2025-11-01 00:00:00', '2025-12-31 23:59:59', 'active'),
('SAVE50K', 'Giảm 50.000đ', 'fixed', 50000.00, 200000.00, NULL, 200, '2025-11-01 00:00:00', '2025-12-31 23:59:59', 'active'),
('VIP20', 'Giảm 20% cho thành viên VIP', 'percentage', 20.00, 100000.00, 100000.00, 50, '2025-11-01 00:00:00', '2025-12-31 23:59:59', 'active'),
('FLASH30', 'Giảm 30% trong ngày', 'percentage', 30.00, 150000.00, 150000.00, 30, '2025-11-15 00:00:00', '2025-11-15 23:59:59', 'active'),
('NEWUSER', 'Giảm 25.000đ cho người dùng mới', 'fixed', 25000.00, 100000.00, NULL, 500, '2025-11-01 00:00:00', '2026-01-31 23:59:59', 'active');

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`name`, `description`, `type`, `discount_value`, `start_date`, `end_date`, `status`, `target_audience`) VALUES
('Khuyến mãi Black Friday', 'Giảm giá lớn nhân dịp Black Friday', 'discount', 30.00, '2025-11-20 00:00:00', '2025-11-30 23:59:59', 'draft', 'all'),
('Gói Premium ưu đãi', 'Mua gói Premium được tặng thêm 1 tháng', 'bundle', 0.00, '2025-11-01 00:00:00', '2025-12-31 23:59:59', 'active', 'all'),
('Dùng thử miễn phí', '7 ngày dùng thử miễn phí cho người dùng mới', 'free_trial', 0.00, '2025-11-01 00:00:00', '2026-01-31 23:59:59', 'active', 'new_users'),
('Giảm giá cuối tuần', 'Giảm 15% cho tất cả gói dịch vụ cuối tuần', 'discount', 15.00, '2025-11-15 00:00:00', '2025-12-31 23:59:59', 'active', 'all'),
('Ưu đãi thành viên Premium', 'Thành viên Premium được giảm thêm 10%', 'discount', 10.00, '2025-11-01 00:00:00', '2026-12-31 23:59:59', 'active', 'premium');

--
-- Dumping data for table `support_tickets`
--

INSERT INTO `support_tickets` (`user_id`, `subject`, `message`, `status`, `priority`) VALUES
(4, 'Không thể đăng nhập', 'Tôi không thể đăng nhập vào tài khoản của mình', 'Mới', 'Cao'),
(5, 'Vấn đề thanh toán', 'Giao dịch của tôi bị lỗi khi thanh toán', 'Đang xử lý', 'Trung bình'),
(6, 'Yêu cầu hoàn tiền', 'Tôi muốn hoàn tiền cho vé đã mua', 'Mới', 'Cao'),
(7, 'Câu hỏi về gói dịch vụ', 'Tôi muốn biết thêm về gói Premium', 'Đã giải quyết', 'Thấp'),
(8, 'Lỗi phát video', 'Video không phát được trên trình duyệt của tôi', 'Đang xử lý', 'Trung bình'),
(4, 'Thay đổi thông tin tài khoản', 'Tôi muốn thay đổi email đăng nhập', 'Mới', 'Thấp');

--
-- Dumping data for table `admin_logs`
--

INSERT INTO `admin_logs` (`user_id`, `action`, `module`, `target_type`, `target_id`, `ip_address`, `user_agent`) VALUES
(2, 'create', 'movies', 'movie', 1, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'),
(2, 'update', 'users', 'user', 1, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'),
(3, 'delete', 'comments', 'comment', 1, '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)'),
(2, 'publish', 'movies', 'movie', 2, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'),
(3, 'update', 'theaters', 'theater', 1, '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)'),
(2, 'view', 'analytics', NULL, NULL, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_module` (`module`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `idx_movie_id` (`movie_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rev_user` (`user_id`),
  ADD KEY `idx_rev_movie` (`movie_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_permission` (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_movie` (`movie_id`),
  ADD KEY `idx_theater` (`theater_id`),
  ADD KEY `screen_id` (`screen_id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Indexes for table `system_config`
--
ALTER TABLE `system_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_key` (`config_key`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `theaters`
--
ALTER TABLE `theaters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `theater_screens`
--
ALTER TABLE `theater_screens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `theater_id` (`theater_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_showtime` (`showtime_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tx_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_subscription` (`subscription_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_role` (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `watch_history`
--
ALTER TABLE `watch_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_movie` (`user_id`,`movie_id`),
  ADD KEY `idx_wh_user` (`user_id`),
  ADD KEY `idx_wh_movie` (`movie_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `showtimes`
--
ALTER TABLE `showtimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `system_config`
--
ALTER TABLE `system_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `theaters`
--
ALTER TABLE `theaters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `theater_screens`
--
ALTER TABLE `theater_screens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `watch_history`
--
ALTER TABLE `watch_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `movies`
--
ALTER TABLE `movies`
  ADD CONSTRAINT `movies_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD CONSTRAINT `showtimes_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `showtimes_ibfk_2` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `showtimes_ibfk_3` FOREIGN KEY (`screen_id`) REFERENCES `theater_screens` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `showtimes_ibfk_4` FOREIGN KEY (`screen_id`) REFERENCES `theater_screens` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `showtimes_ibfk_5` FOREIGN KEY (`screen_id`) REFERENCES `theater_screens` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `showtimes_ibfk_6` FOREIGN KEY (`screen_id`) REFERENCES `theater_screens` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_tickets_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `system_config`
--
ALTER TABLE `system_config`
  ADD CONSTRAINT `system_config_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `theater_screens`
--
ALTER TABLE `theater_screens`
  ADD CONSTRAINT `theater_screens_ibfk_1` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `watch_history`
--
ALTER TABLE `watch_history`
  ADD CONSTRAINT `watch_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `watch_history_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

