-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 28, 2025 lúc 03:13 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `cinehub`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin_logs`
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

--
-- Đang đổ dữ liệu cho bảng `admin_logs`
--

INSERT INTO `admin_logs` (`id`, `user_id`, `action`, `module`, `target_type`, `target_id`, `old_data`, `new_data`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 2, 'create', 'movies', 'movie', 1, NULL, NULL, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2025-11-12 07:41:09'),
(2, 2, 'update', 'users', 'user', 1, NULL, NULL, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2025-11-12 07:41:09'),
(3, 3, 'delete', 'comments', 'comment', 1, NULL, NULL, '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)', '2025-11-12 07:41:09'),
(4, 2, 'publish', 'movies', 'movie', 2, NULL, NULL, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2025-11-12 07:41:09'),
(5, 3, 'update', 'theaters', 'theater', 1, NULL, NULL, '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)', '2025-11-12 07:41:09'),
(6, 2, 'view', 'analytics', NULL, NULL, NULL, NULL, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2025-11-12 07:41:09'),
(7, 3, 'Cập nhật điểm người dùng', 'User', 'user', 9, '{\"points\":0}', '{\"points\":100000,\"action\":\"add\",\"points_changed\":100000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-11-19 01:08:28'),
(8, 3, 'Cập nhật điểm người dùng', 'User', 'user', 9, '{\"points\":100000}', '{\"points\":300000,\"action\":\"add\",\"points_changed\":200000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-11-19 01:08:44'),
(9, 3, 'Cập nhật điểm người dùng', 'User', 'user', 12, '{\"points\":0}', '{\"points\":5000,\"action\":\"add\",\"points_changed\":5000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 02:23:48'),
(10, 3, 'Cập nhật điểm người dùng', 'User', 'user', 12, '{\"points\":5000}', '{\"points\":505000,\"action\":\"add\",\"points_changed\":500000}', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-25 02:25:18');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_food_items`
--

CREATE TABLE `booking_food_items` (
  `id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `food_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_pending`
--

CREATE TABLE `booking_pending` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `showtime_id` int(11) NOT NULL,
  `seats` text NOT NULL,
  `food_items` text DEFAULT NULL,
  `customer_email` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `vnp_txn_ref` varchar(100) DEFAULT NULL,
  `status` enum('pending','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_pending`
--

INSERT INTO `booking_pending` (`id`, `user_id`, `showtime_id`, `seats`, `food_items`, `customer_email`, `total_amount`, `vnp_txn_ref`, `status`, `created_at`, `expires_at`) VALUES
(1, 9, 357, '[\"E9\",\"E10\"]', '{\"1\":\"0\",\"2\":\"0\",\"3\":\"0\",\"4\":\"0\",\"5\":\"0\",\"8\":\"0\",\"6\":\"0\",\"7\":\"0\"}', 'nguyenvanlinh25062006@gmail.com', 400000.00, 'BOOKING_9_357_1764295710_8141', 'completed', '2025-11-28 02:08:30', '2025-11-27 20:23:30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `booking_session_tracking`
--

CREATE TABLE `booking_session_tracking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `showtime_id` int(11) NOT NULL,
  `screen_id` int(11) NOT NULL,
  `session_start` datetime NOT NULL,
  `session_end` datetime DEFAULT NULL,
  `total_duration_seconds` int(11) DEFAULT 0,
  `violation_count` int(11) DEFAULT 0,
  `is_banned` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `booking_session_tracking`
--

INSERT INTO `booking_session_tracking` (`id`, `user_id`, `showtime_id`, `screen_id`, `session_start`, `session_end`, `total_duration_seconds`, `violation_count`, `is_banned`, `created_at`) VALUES
(1, 9, 351, 11, '2025-11-28 08:03:42', NULL, 0, 0, 0, '2025-11-28 01:03:42'),
(2, 9, 354, 12, '2025-11-28 08:03:46', NULL, 0, 0, 0, '2025-11-28 01:03:46'),
(3, 9, 355, 12, '2025-11-28 08:28:23', NULL, 0, 0, 0, '2025-11-28 01:28:23'),
(4, 9, 352, 11, '2025-11-28 08:29:20', NULL, 0, 0, 0, '2025-11-28 01:29:20'),
(5, 9, 353, 11, '2025-11-28 08:29:41', NULL, 0, 0, 0, '2025-11-28 01:29:41'),
(6, 3, 354, 12, '2025-11-28 08:31:52', NULL, 0, 0, 0, '2025-11-28 01:31:52'),
(7, 9, 356, 11, '2025-11-28 08:46:36', NULL, 0, 0, 0, '2025-11-28 01:46:36'),
(8, 9, 357, 12, '2025-11-28 08:46:38', NULL, 0, 0, 0, '2025-11-28 01:46:38'),
(9, 9, 191, 1, '2025-11-28 09:12:14', NULL, 0, 0, 0, '2025-11-28 02:12:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
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
-- Cấu trúc bảng cho bảng `comments`
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

--
-- Đang đổ dữ liệu cho bảng `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `movie_id`, `parent_id`, `content`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, 'Phim này thật sự đáng xem!', 'approved', '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(2, 2, 2, NULL, 'Cảm động quá, tôi đã khóc.', 'approved', '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(3, 3, 3, NULL, 'Hài quá, cười không ngừng.', 'approved', '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(4, 4, 4, NULL, 'Sợ quá, không dám xem một mình.', 'approved', '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(5, 5, 5, NULL, 'Phim hay cho trẻ em.', 'approved', '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(6, 1, 6, NULL, 'Khoa học viễn tưởng đỉnh cao!', 'approved', '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(7, 2, 7, NULL, 'Cuộc phiêu lưu thú vị.', 'approved', '2025-11-12 07:41:09', '2025-11-12 07:41:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `coupons`
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

--
-- Đang đổ dữ liệu cho bảng `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `name`, `type`, `value`, `min_amount`, `max_discount`, `usage_limit`, `used_count`, `valid_from`, `valid_to`, `status`, `created_at`) VALUES
(1, 'WELCOME10', 'Giảm 10% cho khách hàng mới', 'percentage', 10.00, 50000.00, 50000.00, 100, 0, '2025-11-01 00:00:00', '2025-12-31 23:59:59', 'active', '2025-11-12 07:41:09'),
(2, 'SAVE50K', 'Giảm 50.000đ', 'fixed', 50000.00, 200000.00, NULL, 200, 0, '2025-11-01 00:00:00', '2025-12-31 23:59:59', 'active', '2025-11-12 07:41:09'),
(3, 'VIP20', 'Giảm 20% cho thành viên VIP', 'percentage', 20.00, 100000.00, 100000.00, 50, 0, '2025-11-01 00:00:00', '2025-12-31 23:59:59', 'active', '2025-11-12 07:41:09'),
(4, 'FLASH30', 'Giảm 30% trong ngày', 'percentage', 30.00, 150000.00, 150000.00, 30, 0, '2025-11-15 00:00:00', '2025-11-15 23:59:59', 'active', '2025-11-12 07:41:09'),
(5, 'NEWUSER', 'Giảm 25.000đ cho người dùng mới', 'fixed', 25000.00, 100000.00, NULL, 500, 0, '2025-11-01 00:00:00', '2026-01-31 23:59:59', 'active', '2025-11-12 07:41:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `episodes`
--

CREATE TABLE `episodes` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `episode_number` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `episodes`
--

INSERT INTO `episodes` (`id`, `movie_id`, `episode_number`, `title`, `video_url`, `thumbnail`, `duration`, `description`, `created_at`, `updated_at`) VALUES
(1, 8, 1, 'Tập 1', 'data/phim/phimbo/gameofthrones', NULL, NULL, NULL, '2025-11-19 02:21:55', '2025-11-19 02:21:55'),
(2, 8, 2, 'Tập 2', 'data/phim/phimbo/gameofthrones', NULL, NULL, NULL, '2025-11-19 02:21:55', '2025-11-19 02:21:55'),
(3, 8, 3, 'Tập 3', 'data/phim/phimbo/gameofthrones', NULL, NULL, NULL, '2025-11-19 02:21:55', '2025-11-19 02:21:55'),
(4, 8, 4, 'Tập 4', 'data/phim/phimbo/gameofthrones', NULL, NULL, NULL, '2025-11-19 02:21:55', '2025-11-19 02:21:55'),
(5, 29, 1, 'tập 1', 'data/phim/phimbo/venhadicon', NULL, NULL, NULL, '2025-11-25 03:19:46', '2025-11-24 03:28:37');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `food_items`
--

CREATE TABLE `food_items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `type` enum('combo','snack','drink') DEFAULT 'combo',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `food_items`
--

INSERT INTO `food_items` (`id`, `name`, `description`, `price`, `image`, `type`, `is_active`, `created_at`) VALUES
(1, 'Combo 1 - Bỏng + Nước', '1 bỏng ngô lớn + 1 nước ngọt lớn', 85000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(2, 'Combo 2 - Bỏng + Nước + Snack', '1 bỏng ngô lớn + 1 nước ngọt lớn + 1 snack', 120000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(3, 'Combo 3 - Đôi', '2 bỏng ngô lớn + 2 nước ngọt lớn', 150000.00, NULL, 'combo', 1, '2025-11-28 01:03:19'),
(4, 'Bỏng ngô lớn', 'Bỏng ngô size lớn', 55000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(5, 'Bỏng ngô vừa', 'Bỏng ngô size vừa', 40000.00, NULL, 'snack', 1, '2025-11-28 01:03:19'),
(6, 'Nước ngọt lớn', 'Nước ngọt size lớn', 35000.00, NULL, 'drink', 1, '2025-11-28 01:03:19'),
(7, 'Nước ngọt vừa', 'Nước ngọt size vừa', 25000.00, NULL, 'drink', 1, '2025-11-28 01:03:19'),
(8, 'Snack mix', 'Hỗn hợp snack', 45000.00, NULL, 'snack', 1, '2025-11-28 01:03:19');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ip_blocks`
--

CREATE TABLE `ip_blocks` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `expires_at` datetime DEFAULT NULL COMMENT 'NULL = chặn vĩnh viễn',
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ip_spam_logs`
--

CREATE TABLE `ip_spam_logs` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `action_type` varchar(50) NOT NULL DEFAULT 'general',
  `is_spam` tinyint(1) DEFAULT 0,
  `details` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `ip_spam_logs`
--

INSERT INTO `ip_spam_logs` (`id`, `ip_address`, `action_type`, `is_spam`, `details`, `user_id`, `created_at`) VALUES
(1, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:07:57'),
(2, '::1', 'booking', 0, 'Đặt vé thành công: 4 vé', 9, '2025-11-28 01:09:16'),
(3, '::1', 'booking', 0, 'Đặt vé thành công: 1 vé', 9, '2025-11-28 01:10:11'),
(4, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:10:35'),
(5, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:11:56'),
(6, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:15:31'),
(7, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:16:16'),
(8, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:16:52'),
(9, '::1', 'booking', 0, 'Đặt vé thành công: 4 vé', 9, '2025-11-28 01:18:55'),
(10, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:20:31'),
(11, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:22:32'),
(12, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:23:11'),
(13, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:23:45'),
(14, '::1', 'booking', 0, 'Đặt vé thành công: 2 vé', 9, '2025-11-28 01:26:33'),
(15, '::1', 'booking', 0, 'Đặt vé thành công: 1 vé', 9, '2025-11-28 01:28:40'),
(16, '::1', 'login', 1, 'Đăng nhập thất bại: admin2@cinehub.com', NULL, '2025-11-28 01:31:44');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `movies`
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
  `age_rating` varchar(10) DEFAULT NULL,
  `type` enum('phimle','phimbo') DEFAULT 'phimle'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `movies`
--

INSERT INTO `movies` (`id`, `title`, `category_id`, `level`, `duration`, `description`, `director`, `actors`, `video_url`, `trailer_url`, `thumbnail`, `status`, `rating`, `created_at`, `status_admin`, `publish_date`, `geo_restriction`, `drm_enabled`, `banner`, `country`, `language`, `age_rating`, `type`) VALUES
(1, 'Avengers: Endgame', 1, 'Premium', 181, 'Phim siêu anh hùng Marvel, kết thúc của Infinity Saga', 'Anthony Russo, Joe Russo', 'Robert Downey Jr., Chris Evans, Mark Ruffalo', 'data/phim/phimle/Avengers_Endgame.mp4', 'https://example.com/avengers-trailer.mp4', 'data/img/avengers_end_game_img.jpg', 'Chiếu rạp', 9.2, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, 'data/img/avengers_end_game.jpg', 'Mỹ', 'Tiếng Anh', 'PG-13', 'phimle'),
(2, 'Titanic', 2, 'Gold', 194, 'Câu chuyện tình yêu trên con tàu định mệnh', 'James Cameron', 'Leonardo DiCaprio, Kate Winslet', 'data/phim/phimle/titanic.mp4\r\n', 'https://example.com/titanic-trailer.mp4', 'data/img/titanic.jpg', 'Chiếu rạp', 8.8, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'PG-13', 'phimle'),
(3, 'The Hangover', 3, 'Silver', 100, 'Phim hài về chuyến đi Las Vegas đầy biến cố', 'Todd Phillips', 'Bradley Cooper, Ed Helms, Zach Galifianakis', 'https://example.com/hangover.mp4', 'https://example.com/hangover-trailer.mp4', 'data/img/the_hangover_img.jpg', 'Chiếu rạp', 7.7, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, 'data/img/the_hangover.jpg', 'Mỹ', 'Tiếng Anh', 'R', 'phimle'),
(4, 'The Conjuring', 4, 'Gold', 112, 'Phim kinh dị về các nhà điều tra siêu nhiên', 'James Wan', 'Patrick Wilson, Vera Farmiga', 'https://example.com/conjuring.mp4', 'https://example.com/conjuring-trailer.mp4', 'data/img/the_conjuring_img.jpg', 'Chiếu rạp', 7.5, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, 'data/img/the_conjuring.jpg', 'Mỹ', 'Tiếng Anh', 'R', 'phimle'),
(5, 'Toy Story 4', 5, 'Free', 100, 'Cuộc phiêu lưu mới của Woody và Buzz', 'Josh Cooley', 'Tom Hanks, Tim Allen', 'https://example.com/toystory.mp4', 'https://example.com/toystory-trailer.mp4', 'data/img/toy_story_img.jpg', 'Chiếu rạp', 8, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, 'data/img/toy_story.jpg', 'Mỹ', 'Tiếng Anh', 'G', 'phimle'),
(6, 'Interstellar', 6, 'Premium', 169, 'Cuộc hành trình không gian để cứu nhân loại', 'Christopher Nolan', 'Matthew McConaughey, Anne Hathaway', 'data/phim/phimle/Interstellar\r\n.mp4', 'https://example.com/interstellar-trailer.mp4', 'data/img/interstellar.jpg', 'Chiếu online', 8.6, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'PG-13', 'phimle'),
(7, 'Indiana Jones', 7, 'Gold', 122, 'Cuộc phiêu lưu tìm kiếm cổ vật', 'Steven Spielberg', 'Harrison Ford', 'https://example.com/indiana.mp4', 'https://example.com/indiana-trailer.mp4', 'data/img/indiana_jones_img.jpg', 'Chiếu online', 8.2, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, 'data/img/indiana_jones.jpg', 'Mỹ', 'Tiếng Anh', 'PG-13', 'phimle'),
(8, 'Game of Thrones', 7, 'Premium', 60, 'Cuộc chiến giành quyền lực giữa các dòng họ ở vùng đất Westeros. Bộ phim kể về cuộc đấu tranh của các gia đình quý tộc để giành lấy Ngai Sắt Sắt và cai trị bảy vương quốc.', 'David Benioff, D.B. Weiss', 'Emilia Clarke, Kit Harington, Peter Dinklage, Lena Headey', 'data/phim/phimbo/gameofthrones', 'https://example.com/got-trailer.mp4', 'data/img/game_of_thrones_img.webp', 'Chiếu online', 9.3, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, 'data/img/game_of_thrones.jpg', 'Mỹ', 'Tiếng Anh', 'TV-MA', 'phimbo'),
(9, 'Breaking Bad', 1, 'Gold', 47, 'Câu chuyện về giáo viên hóa học trung học Walter White, người bắt đầu sản xuất và bán methamphetamine sau khi được chẩn đoán ung thư phổi giai đoạn cuối.', 'Vince Gilligan', 'Bryan Cranston, Aaron Paul, Anna Gunn, Dean Norris', 'data/phim/phimbo/breaking_bad', 'https://example.com/breaking-bad-trailer.mp4', 'data/img/breaking_bad_img.jpg', 'Chiếu online', 9.5, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, 'data/img/breaking_bad.jpg', 'Mỹ', 'Tiếng Anh', 'TV-14', 'phimbo'),
(10, 'The Walking Dead', 4, 'Gold', 45, 'Sheriff Deputy Rick Grimes tỉnh dậy sau một chấn thương và phát hiện ra thế giới đã bị tàn phá bởi đại dịch zombie. Anh phải dẫn dắt nhóm người sống sót tìm nơi trú ẩn.', 'Frank Darabont', 'Andrew Lincoln, Norman Reedus, Melissa McBride, Danai Gurira', 'data/phim/phimbo/the_walking_dead', 'https://example.com/walking-dead-trailer.mp4', 'data/img/the_walking_dead_img.jpg', 'Chiếu online', 8.2, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, 'data/img/the_walking_dead.jpg', 'Mỹ', 'Tiếng Anh', 'TV-MA', 'phimbo'),
(11, 'Stranger Things', 6, 'Premium', 50, 'Khi một cậu bé 12 tuổi biến mất, một thị trấn nhỏ ở Indiana tiết lộ một bí mật liên quan đến thí nghiệm bí mật, siêu năng lực đáng sợ và một cô gái nhỏ lạ thường.', 'The Duffer Brothers', 'Millie Bobby Brown, Finn Wolfhard, Winona Ryder, David Harbour', 'data/phim/phimbo/stranger_things', 'https://example.com/stranger-things-trailer.mp4', 'data/img/stranger_things_img.jpg', 'Chiếu online', 8.7, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, 'data/img/stranger_things.jpg', 'Mỹ', 'Tiếng Anh', 'TV-14', 'phimbo'),
(12, 'House of Cards', 2, 'Gold', 58, 'Một chính trị gia khôn ngoan và không khoan nhượng làm bất cứ điều gì để giành quyền lực ở Washington D.C.', 'Beau Willimon', 'Kevin Spacey, Robin Wright, Kate Mara, Michael Kelly', 'data/phim/phimbo/house_of_cards', 'https://example.com/house-of-cards-trailer.mp4', 'data/img/house_of_cards_img.png', 'Chiếu online', 8.8, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, 'data/img/house_of_cards.jpg', 'Mỹ', 'Tiếng Anh', 'TV-MA', 'phimbo'),
(13, 'The Crown', 2, 'Premium', 58, 'Dòng thời gian về triều đại của Nữ hoàng Elizabeth II của Vương quốc Anh, từ những năm 1950 đến những năm 2000.', 'Peter Morgan', 'Claire Foy, Olivia Colman, Matt Smith, Tobias Menzies', 'data/phim/phimbo/the_crown', 'https://example.com/the-crown-trailer.mp4', 'data/img/the_crown.jpg', 'Chiếu online', 8.6, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Anh', 'Tiếng Anh', 'TV-MA', 'phimbo'),
(14, 'Sherlock', 1, 'Gold', 90, 'Phiên bản hiện đại của các câu chuyện điều tra nổi tiếng của Sir Arthur Conan Doyle, với Sherlock Holmes và Dr. John Watson giải quyết các vụ án ở London thế kỷ 21.', 'Mark Gatiss, Steven Moffat', 'Benedict Cumberbatch, Martin Freeman, Rupert Graves, Mark Gatiss', 'data/phim/phimbo/sherlock', 'https://example.com/sherlock-trailer.mp4', 'data/img/Sherlock_img.jpg', 'Chiếu online', 9.1, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, 'data/img/sherlock.jpg', 'Anh', 'Tiếng Anh', 'TV-14', 'phimbo'),
(15, 'The Office', 3, 'Silver', 22, 'Một mockumentary về nhóm nhân viên văn phòng hàng ngày tại văn phòng chi nhánh Scranton của công ty giấy Dunder Mifflin.', 'Greg Daniels', 'Steve Carell, Rainn Wilson, John Krasinski, Jenna Fischer', 'data/phim/phimbo/the_office', 'https://example.com/the-office-trailer.mp4', 'data/img/the_office.png', 'Chiếu online', 8.9, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'TV-14', 'phimbo'),
(16, 'Friends', 3, 'Silver', 22, 'Cuộc sống và tình yêu của sáu người bạn ở Manhattan, New York, khi họ cố gắng tìm ra con đường của mình trong cuộc sống.', 'David Crane, Marta Kauffman', 'Jennifer Aniston, Courteney Cox, Lisa Kudrow, Matt LeBlanc, Matthew Perry, David Schwimmer', 'data/phim/phimbo/friends', 'https://example.com/friends-trailer.mp4', 'data/img/friends.jpg', 'Chiếu online', 9, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'TV-14', 'phimbo'),
(17, 'The Witcher', 7, 'Premium', 60, 'Geralt of Rivia, một thợ săn quái vật đột biến đi khắp đất liền để tìm nơi thuộc về mình trong một thế giới nơi con người thường tồi tệ hơn quái vật.', 'Lauren Schmidt Hissrich', 'Henry Cavill, Anya Chalotra, Freya Allan, Joey Batey', 'data/phim/phimbo/the_witcher', 'https://example.com/the-witcher-trailer.mp4', 'data/img/the_witcher.jpg', 'Chiếu online', 8.2, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Mỹ/ Ba Lan', 'Tiếng Anh', 'TV-MA', 'phimbo'),
(18, 'Hai Phượng', 1, 'Premium', 98, 'Mẹ đơn thân từng là dân giang hồ phải chiến đấu với băng nhóm bắt cóc con gái mình.', 'Lê Văn Kiệt', 'Ngô Thanh Vân, Mai Cát Vi', 'data/phim/phimle/hai_phuong.mp4', 'https://example.com/hai-phuong-trailer.mp4', 'data/img/hai_phuong_img.jpg', 'Chiếu rạp', 7.5, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, 'data/img/hai_phuong.jpg', 'Việt Nam', 'Tiếng Việt', 'C16', 'phimle'),
(19, 'Mắt Biếc', 2, 'Gold', 117, 'Câu chuyện tình đơn phương lãng mạn và đầy hoài niệm ở thập niên 70.', 'Victor Vũ', 'Trần Nghĩa, Trúc Anh, Trần Phong', 'data/phim/phimle/mat_biec.mp4', 'https://example.com/mat-biec-trailer.mp4', 'data/img/mat_biec_img.jpg', 'Chiếu rạp', 8, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, 'data/img/mat_biec.webp', 'Việt Nam', 'Tiếng Việt', 'C13', 'phimle'),
(20, 'Bố Già', 3, 'Premium', 128, 'Phim về tình cha con đầy cảm xúc và những mâu thuẫn trong gia đình.', 'Trấn Thành, Vũ Ngọc Đãng', 'Trấn Thành, Lê Giang, Tuấn Trần', 'data/phim/phimle/bo_gia.mp4', 'https://example.com/bo-gia-trailer.mp4', 'data/img/bo_gia.jpg', 'Chiếu rạp', 8.5, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, NULL, 'Việt Nam', 'Tiếng Việt', 'C13', 'phimle'),
(21, 'Tiệc Trăng Máu', 3, 'Gold', 100, 'Bảy người bạn cùng chơi một trò chơi công khai tin nhắn và cuộc gọi điện thoại, dẫn đến những bí mật bị phanh phui.', 'Nguyễn Quang Dũng', 'Thái Hòa, Thu Trang, Hồng Ánh, Hứa Vĩ Văn', 'data/phim/phimle/tiec_trang_mau.mp4', 'https://example.com/tiec-trang-mau-trailer.mp4', 'data/img/tiec_trang_mau.jpg', 'Chiếu rạp', 7.8, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, NULL, 'Việt Nam', 'Tiếng Việt', 'C16', 'phimle'),
(22, 'Lật Mặt 4: Nhà Có Khách', 4, 'Silver', 90, 'Phim hài kinh dị với những tình huống dở khóc dở cười và yếu tố ma quái.', 'Lý Hải', 'Lý Hải, Katleen Phan Võ, Huy Khánh', 'data/phim/phimle/lat_mat_4.mp4', 'https://example.com/lat-mat-4-trailer.mp4', 'data/img/lat_mat_4.jpg', 'Chiếu rạp', 7, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, NULL, 'Việt Nam', 'Tiếng Việt', 'C13', 'phimle'),
(23, 'Em Chưa 18', 3, 'Free', 95, 'Chuyện tình hài hước giữa một cô gái tuổi teen và một chàng trai đã trưởng thành.', 'Lê Thanh Sơn', 'Kaity Nguyễn, Kiều Minh Tuấn', 'data/phim/phimle/em_chua_18.mp4', 'https://example.com/em-chua-18-trailer.mp4', 'data/img/em_chua_18.jpg', 'Chiếu rạp', 7.2, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, NULL, 'Việt Nam', 'Tiếng Việt', 'C16', 'phimle'),
(24, 'Chị Trợ Lý Của Anh', 2, 'Gold', 105, 'Giám đốc trẻ phải thuê một cô trợ lý bí ẩn để cứu công ty của mình.', 'Lý Minh Thắng', 'Mỹ Tâm, Mai Tài Phến', 'data/phim/phimle/chi_tro_ly_cua_anh.mp4', 'https://example.com/chi-tro-ly-cua-anh-trailer.mp4', 'data/img/chi_tro_ly_cua_anh_img.jpg', 'Chiếu online', 6.8, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, 'data/img/chi_tro_ly_cua_anh.jpg', 'Việt Nam', 'Tiếng Việt', 'C13', 'phimle'),
(25, 'Gái Già Lắm Chiêu 3', 3, 'Premium', 108, 'Cuộc chiến mẹ chồng nàng dâu đầy xa hoa và kịch tính ở Huế.', 'Nam Cito, Bảo Nhân', 'Ninh Dương Lan Ngọc, Lê Khanh, Hồng Vân', 'data/phim/phimle/gai_gia_lam_chieu_3.mp4', 'https://example.com/gai-gia-lam-chieu-3-trailer.mp4', 'data/img/gai_gia_lam_chieu_3.jpg', 'Chiếu rạp', 7.4, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, NULL, 'Việt Nam', 'Tiếng Việt', 'C16', 'phimle'),
(26, 'Quỳnh Hoa Nhất Dạ', 7, 'Premium', 120, 'Phim cổ trang, dã sử về cuộc đời đầy sóng gió của Thái hậu Dương Vân Nga.', 'Lý Minh Thắng', 'Nhã Phương, Thuý Ngân, Lương Thế Thành', 'data/phim/phimle/quynh_hoa_nhat_da.mp4', 'https://example.com/quynh-hoa-nhat-da-trailer.mp4', 'data/img/quynh_hoa_nhat_da.jpg', 'Chiếu rạp', 7.7, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, NULL, 'Việt Nam', 'Tiếng Việt', 'C13', 'phimle'),
(27, 'Tấm Cám: Chuyện Chưa Kể', 5, 'Gold', 116, 'Phiên bản cải biên của truyện cổ tích Tấm Cám, kết hợp yếu tố giả tưởng và hành động.', 'Ngô Thanh Vân', 'Hạ Vi, Isaac, Ngô Thanh Vân', 'data/phim/phimle/tam_cam.mp4', 'https://example.com/tam-cam-trailer.mp4', 'data/img/tam_cam.jpg', 'Chiếu rạp', 7.1, '2025-11-24 09:19:09', 'published', NULL, NULL, 0, NULL, 'Việt Nam', 'Tiếng Việt', 'C13', 'phimle'),
(28, 'Hương Vị Tình Thân (Phần 1)', 2, 'Free', 45, 'Phim truyền hình về cuộc đời đầy thử thách của Phương Nam, người luôn khát khao tình cảm gia đình.', 'Nguyễn Danh Dũng', 'Phương Oanh, Mạnh Trường, Công Lý', 'data/phim/phimbo/huong_vi_tinh_than_p1', 'https://example.com/hvtt-trailer.mp4', 'data/img/huong_vi_tinh_than_p1_img.jpg', 'Chiếu online', 8.4, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/huong_vi_tinh_than_p1.jpg', 'Việt Nam', 'Tiếng Việt', 'P', 'phimbo'),
(29, 'Về Nhà Đi Con', 2, 'Gold', 45, 'Phim về tình cảm gia đình, đặc biệt là tình cha và ba cô con gái có tính cách khác nhau.', 'Nguyễn Danh Dũng', 'NSND Hoàng Dũng, Thu Quỳnh, Bảo Thanh, Bảo Hân', 'data/phim/phimbo/venhadicon', 'https://example.com/vndc-trailer.mp4', 'data/img/venhadicon_img.webp', 'Chiếu online', 9, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/venhadicon.jpg', 'Việt Nam', 'Tiếng Việt', 'P', 'phimbo'),
(30, 'Người Phán Xử', 1, 'Premium', 45, 'Ông trùm Phan Quân và những cuộc chiến tranh giành quyền lực trong thế giới ngầm.', 'Nguyễn Mai Hiền, Nguyễn Khải Anh, Bùi Quốc Việt', 'NSND Hoàng Dũng, Việt Anh, Hồng Đăng', 'data/phim/phimbo/nguoi_phan_xu', 'https://example.com/npx-trailer.mp4', 'data/img/nguoi_phan_xu_img.jpg', 'Chiếu online', 10, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/nguoi_phan_xu.webp', 'Việt Nam', 'Tiếng Việt', 'C18', 'phimbo'),
(31, 'Sống Chung Với Mẹ Chồng', 2, 'Gold', 45, 'Những mâu thuẫn nảy sinh khi nàng dâu và mẹ chồng sống chung dưới một mái nhà.', 'Vũ Trường Khoa', 'NSND Lan Hương, Bảo Thanh, Anh Dũng', 'data/phim/phimbo/song_chung_voi_me_chong', 'https://example.com/scvmc-trailer.mp4', 'data/img/song_chung_voi_me_chong_img.jpg', 'Chiếu online', 8.1, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/song_chung_voi_me_chong.jpg', 'Việt Nam', 'Tiếng Việt', 'P', 'phimbo'),
(32, 'Thương Ngày Nắng Về (Phần 2)', 2, 'Premium', 45, 'Câu chuyện về bà mẹ đơn thân cùng ba cô con gái, xoay quanh tình yêu, sự nghiệp và những mâu thuẫn.', 'Bùi Tiến Huy', 'NSƯT Thanh Quý, Phan Minh Huyền, Lan Phương', 'data/phim/phimbo/thuong_ngay_nang_ve_p2', 'https://example.com/tnnv-trailer.mp4', 'data/img/thuong_ngay_nang_ve_img.jpg', 'Chiếu online', 8.5, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/thuong_ngay_nang_ve.jpg', 'Việt Nam', 'Tiếng Việt', 'P', 'phimbo'),
(33, 'Phim ngắn: 20 Năm 20 Món Ăn', 5, 'Free', 15, 'Loạt phim ngắn ẩm thực về hành trình tìm kiếm hương vị đã mất sau 20 năm xa quê.', 'Nguyễn Hoàng Điệp', 'Nhiều diễn viên', 'data/phim/phimle/20_nam_20_mon_an.mp4', 'https://example.com/20nam-trailer.mp4', 'data/img/20_nam_20_mon_an.jpg', 'Chiếu online', 7, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, NULL, 'Việt Nam', 'Tiếng Việt', 'P', 'phimle'),
(34, 'Series: Ai Là Hung Thủ (Mùa 1)', 1, 'Gold', 40, 'Series trinh thám điều tra các vụ án mạng phức tạp tại thành phố Hồ Chí Minh.', 'Lý Hải (Đóng vai trò sản xuất)', 'Trương Thế Vinh, Nhan Phúc Vinh', 'data/phim/phimbo/ai_la_hung_thu_s1', 'https://example.com/alht-trailer.mp4', 'data/img/ai_la_hung_thu_s1.jpg', 'Chiếu online', 7.9, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, NULL, 'Việt Nam', 'Tiếng Việt', 'C16', 'phimbo'),
(35, 'Phim lẻ: Cô Ba Sài Gòn', 2, 'Premium', 94, 'Phim lãng mạn, giả tưởng về thời trang áo dài và câu chuyện xuyên không giữa hai thế hệ.', 'Trần Bửu Lộc, Nguyễn Lê Minh', 'Ninh Dương Lan Ngọc, Diễm My 9x, Ngô Thanh Vân', 'data/phim/phimle/co_ba_sai_gon.mp4', 'https://example.com/cbsg-trailer.mp4', 'data/img/co_ba_sai_gon_img.jpg', 'Chiếu online', 7.6, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/co_ba_sai_gon.jpg', 'Việt Nam', 'Tiếng Việt', 'C13', 'phimle'),
(36, 'Series: Tình Yêu và Tham Vọng', 7, 'Gold', 45, 'Phim thương trường với những cuộc chiến khốc liệt giữa các tập đoàn và câu chuyện tình yêu phức tạp.', 'Bùi Tiến Huy', 'Nhan Phúc Vinh, Diễm My 9x, Lã Thanh Huyền', 'data/phim/phimbo/tinh_yeu_va_tham_vong', 'https://example.com/tyvtc-trailer.mp4', 'data/img/tinh_yeu_va_tham_vong_img.jpg', 'Chiếu online', 7.8, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/tinh_yeu_va_tham_vong.jpg', 'Việt Nam', 'Tiếng Việt', 'P', 'phimbo'),
(37, 'Phim ngắn: Ngã Ba Đồng Lộc', 7, 'Free', 30, 'Phim tài liệu/chiến tranh về sự hy sinh anh dũng của 10 cô gái thanh niên xung phong.', 'Nguyễn Minh Chung', 'Nhiều diễn viên', 'data/phim/phimle/nga_ba_dong_loc.mp4', 'https://example.com/nbdl-trailer.mp4', 'data/img/nga_ba_dong_loc_img.jpg', 'Chiếu online', 8.2, '2025-11-25 09:25:05', 'published', NULL, NULL, 0, 'data/img/nga_ba_dong_loc.jpg', 'Việt Nam', 'Tiếng Việt', 'P', 'phimle');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `module` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `permissions`
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
-- Cấu trúc bảng cho bảng `promotions`
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

--
-- Đang đổ dữ liệu cho bảng `promotions`
--

INSERT INTO `promotions` (`id`, `name`, `description`, `type`, `discount_value`, `start_date`, `end_date`, `status`, `target_audience`, `created_at`) VALUES
(1, 'Khuyến mãi Black Friday', 'Giảm giá lớn nhân dịp Black Friday', 'discount', 30.00, '2025-11-20 00:00:00', '2025-11-30 23:59:59', 'draft', 'all', '2025-11-12 07:41:09'),
(2, 'Gói Premium ưu đãi', 'Mua gói Premium được tặng thêm 1 tháng', 'bundle', 0.00, '2025-11-01 00:00:00', '2025-12-31 23:59:59', 'active', 'all', '2025-11-12 07:41:09'),
(3, 'Dùng thử miễn phí', '7 ngày dùng thử miễn phí cho người dùng mới', 'free_trial', 0.00, '2025-11-01 00:00:00', '2026-01-31 23:59:59', 'active', 'new_users', '2025-11-12 07:41:09'),
(4, 'Giảm giá cuối tuần', 'Giảm 15% cho tất cả gói dịch vụ cuối tuần', 'discount', 15.00, '2025-11-15 00:00:00', '2025-12-31 23:59:59', 'active', 'all', '2025-11-12 07:41:09'),
(5, 'Ưu đãi thành viên Premium', 'Thành viên Premium được giảm thêm 10%', 'discount', 10.00, '2025-11-01 00:00:00', '2026-12-31 23:59:59', 'active', 'premium', '2025-11-12 07:41:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `rating` tinyint(4) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_pinned` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `movie_id`, `rating`, `comment`, `created_at`, `is_pinned`) VALUES
(1, 1, 1, 5, 'Phim tuyệt vời! Diễn xuất xuất sắc và cốt truyện hấp dẫn.', '2025-11-12 07:41:09', 0),
(2, 2, 2, 5, 'Titanic là một kiệt tác điện ảnh, tình yêu vĩnh cửu.', '2025-11-12 07:41:09', 0),
(3, 3, 3, 4, 'Phim hài rất vui nhộn, giải trí tốt.', '2025-11-12 07:41:09', 0),
(4, 4, 4, 4, 'Kinh dị đúng nghĩa, rùng rợn từ đầu đến cuối.', '2025-11-12 07:41:09', 0),
(5, 5, 5, 5, 'Hoạt hình hay, phù hợp cho cả gia đình.', '2025-11-12 07:41:09', 0),
(6, 1, 6, 5, 'Interstellar là một tác phẩm khoa học viễn tưởng xuất sắc.', '2025-11-12 07:41:09', 0),
(7, 2, 7, 4, 'Cuộc phiêu lưu thú vị với Indiana Jones.', '2025-11-12 07:41:09', 0),
(8, 9, 8, 1, 'phim hay qua\r\n', '2025-11-19 03:15:34', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Super Admin', 'Quyền cao nhất, toàn quyền hệ thống', '2025-11-10 16:41:17'),
(2, 'Admin', 'Quản trị viên, quản lý nội dung và người dùng', '2025-11-10 16:41:17'),
(3, 'Moderator', 'Điều hành viên, quản lý bình luận và hỗ trợ', '2025-11-10 16:41:17'),
(4, 'Content Manager', 'Quản lý nội dung phim', '2025-11-10 16:41:17'),
(5, 'Support Staff', 'Nhân viên hỗ trợ khách hàng', '2025-11-10 16:41:17'),
(6, 'Theater Manager', 'Quản lý rạp, quản lý lịch chiếu, bán vé và phim của rạp', '2025-11-10 16:41:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `role_permissions`
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
-- Cấu trúc bảng cho bảng `seat_reservations`
--

CREATE TABLE `seat_reservations` (
  `id` int(11) NOT NULL,
  `showtime_id` int(11) NOT NULL,
  `seat` varchar(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `reserved_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `seat_selection_logs`
--

CREATE TABLE `seat_selection_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `showtime_id` int(11) NOT NULL,
  `seat_count` int(11) NOT NULL,
  `seats` text DEFAULT NULL,
  `is_spam` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `seat_selection_logs`
--

INSERT INTO `seat_selection_logs` (`id`, `user_id`, `ip_address`, `showtime_id`, `seat_count`, `seats`, `is_spam`, `created_at`) VALUES
(1, 9, '::1', 354, 7, '[\"A5\",\"B6\",\"C1\",\"C3\",\"C5\",\"D4\",\"D6\"]', 0, '2025-11-28 01:07:25'),
(2, 9, '::1', 354, 2, '[\"C1\",\"D1\"]', 0, '2025-11-28 01:07:57'),
(3, 9, '::1', 354, 4, '[\"D2\",\"D6\",\"D7\",\"D16\"]', 0, '2025-11-28 01:08:36'),
(4, 9, '::1', 354, 2, '[\"D2\",\"D4\"]', 0, '2025-11-28 01:08:52'),
(5, 9, '::1', 354, 4, '[\"A4\",\"B4\",\"C4\",\"D4\"]', 0, '2025-11-28 01:09:16'),
(6, 9, '::1', 354, 7, '[\"A3\",\"A9\",\"B3\",\"C3\",\"C9\",\"D3\",\"D9\"]', 0, '2025-11-28 01:09:59'),
(7, 9, '::1', 354, 1, '[\"D3\"]', 0, '2025-11-28 01:10:11'),
(8, 9, '::1', 354, 2, '[\"B6\",\"C6\"]', 0, '2025-11-28 01:10:35'),
(9, 9, '::1', 354, 2, '[\"C5\",\"D6\"]', 0, '2025-11-28 01:11:56'),
(10, 9, '::1', 354, 2, '[\"B2\",\"C3\"]', 0, '2025-11-28 01:13:17'),
(11, 9, '::1', 354, 8, '[\"B3\",\"C2\",\"C10\",\"C12\",\"C14\",\"D9\",\"D11\",\"D13\"]', 0, '2025-11-28 01:14:29'),
(12, 9, '::1', 354, 2, '[\"B7\",\"B8\"]', 0, '2025-11-28 01:15:31'),
(13, 9, '::1', 354, 2, '[\"B5\",\"C9\"]', 0, '2025-11-28 01:16:16'),
(14, 9, '::1', 354, 2, '[\"C12\",\"D11\"]', 0, '2025-11-28 01:16:51'),
(15, 9, '::1', 354, 1, '[\"C18\"]', 0, '2025-11-28 01:18:31'),
(16, 9, '::1', 354, 4, '[\"C18\",\"C19\",\"D17\",\"D18\"]', 0, '2025-11-28 01:18:55'),
(17, 9, '::1', 354, 2, '[\"B14\",\"B15\"]', 0, '2025-11-28 01:19:19'),
(18, 9, '::1', 354, 2, '[\"B11\",\"C10\"]', 0, '2025-11-28 01:20:31'),
(19, 9, '::1', 354, 5, '[\"C14\",\"C20\",\"C21\",\"D9\",\"D10\"]', 0, '2025-11-28 01:22:18'),
(20, 9, '::1', 354, 2, '[\"C20\",\"C21\"]', 0, '2025-11-28 01:22:32'),
(21, 9, '::1', 354, 2, '[\"C24\",\"C25\"]', 0, '2025-11-28 01:23:11'),
(22, 9, '::1', 354, 2, '[\"B23\",\"C22\"]', 0, '2025-11-28 01:23:45'),
(23, 9, '::1', 354, 3, '[\"B21\",\"B25\",\"C23\"]', 0, '2025-11-28 01:24:17'),
(24, 9, '::1', 354, 2, '[\"A25\",\"B24\"]', 0, '2025-11-28 01:25:53'),
(25, 9, '::1', 354, 2, '[\"B14\",\"B15\"]', 0, '2025-11-28 01:26:13'),
(26, 9, '::1', 354, 2, '[\"A11\",\"B10\"]', 0, '2025-11-28 01:26:33'),
(27, 9, '::1', 355, 1, '[\"D11\"]', 0, '2025-11-28 01:28:40'),
(28, 9, '::1', 357, 2, '[\"D8\",\"D9\"]', 0, '2025-11-28 02:06:18'),
(29, 9, '::1', 357, 2, '[\"E9\",\"E10\"]', 0, '2025-11-28 02:06:26'),
(30, 9, '::1', 357, 2, '[\"E10\",\"E11\"]', 0, '2025-11-28 02:06:47'),
(31, 9, '::1', 357, 2, '[\"E9\",\"E10\"]', 0, '2025-11-28 02:08:30'),
(32, 9, '::1', 191, 4, '[\"I2\",\"I3\",\"I4\",\"I5\"]', 0, '2025-11-28 02:12:24');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `showtimes`
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

--
-- Đang đổ dữ liệu cho bảng `showtimes`
--

INSERT INTO `showtimes` (`id`, `movie_id`, `theater_id`, `show_date`, `show_time`, `price`, `created_at`, `screen_id`) VALUES
(71, 1, 1, '2025-11-24', '10:00:00', 120000.00, '2025-11-24 09:32:00', 1),
(72, 2, 2, '2025-11-24', '10:00:00', 100000.00, '2025-11-24 09:32:00', 2),
(73, 3, 3, '2025-11-24', '10:00:00', 110000.00, '2025-11-24 09:32:00', 3),
(74, 4, 4, '2025-11-24', '10:00:00', 115000.00, '2025-11-24 09:32:00', 4),
(75, 5, 5, '2025-11-24', '10:00:00', 90000.00, '2025-11-24 09:32:00', 5),
(76, 1, 2, '2025-11-24', '12:30:00', 130000.00, '2025-11-24 09:32:00', 6),
(77, 2, 3, '2025-11-24', '12:30:00', 110000.00, '2025-11-24 09:32:00', 7),
(78, 3, 4, '2025-11-24', '12:30:00', 120000.00, '2025-11-24 09:32:00', 8),
(79, 4, 5, '2025-11-24', '12:30:00', 125000.00, '2025-11-24 09:32:00', 1),
(80, 5, 1, '2025-11-24', '12:30:00', 100000.00, '2025-11-24 09:32:00', 2),
(81, 1, 3, '2025-11-24', '15:00:00', 120000.00, '2025-11-24 09:32:00', 3),
(82, 2, 4, '2025-11-24', '15:00:00', 100000.00, '2025-11-24 09:32:00', 4),
(83, 3, 5, '2025-11-24', '15:00:00', 110000.00, '2025-11-24 09:32:00', 5),
(84, 4, 1, '2025-11-24', '15:00:00', 115000.00, '2025-11-24 09:32:00', 6),
(85, 5, 2, '2025-11-24', '15:00:00', 90000.00, '2025-11-24 09:32:00', 7),
(86, 1, 4, '2025-11-24', '17:30:00', 130000.00, '2025-11-24 09:32:00', 8),
(87, 2, 5, '2025-11-24', '17:30:00', 110000.00, '2025-11-24 09:32:00', 1),
(88, 3, 1, '2025-11-24', '17:30:00', 120000.00, '2025-11-24 09:32:00', 2),
(89, 4, 2, '2025-11-24', '17:30:00', 125000.00, '2025-11-24 09:32:00', 3),
(90, 5, 3, '2025-11-24', '17:30:00', 100000.00, '2025-11-24 09:32:00', 4),
(91, 1, 5, '2025-11-24', '19:00:00', 120000.00, '2025-11-24 09:32:00', 5),
(92, 2, 1, '2025-11-24', '19:00:00', 100000.00, '2025-11-24 09:32:00', 6),
(93, 3, 2, '2025-11-24', '19:00:00', 110000.00, '2025-11-24 09:32:00', 7),
(94, 4, 3, '2025-11-24', '19:00:00', 115000.00, '2025-11-24 09:32:00', 8),
(95, 5, 4, '2025-11-24', '19:00:00', 90000.00, '2025-11-24 09:32:00', 1),
(96, 1, 1, '2025-11-24', '21:30:00', 130000.00, '2025-11-24 09:32:00', 2),
(97, 2, 2, '2025-11-24', '21:30:00', 110000.00, '2025-11-24 09:32:00', 3),
(98, 3, 3, '2025-11-24', '21:30:00', 120000.00, '2025-11-24 09:32:00', 4),
(99, 4, 4, '2025-11-24', '21:30:00', 125000.00, '2025-11-24 09:32:00', 5),
(100, 5, 5, '2025-11-24', '21:30:00', 100000.00, '2025-11-24 09:32:00', 6),
(101, 1, 1, '2025-11-25', '10:00:00', 120000.00, '2025-11-24 09:32:00', 1),
(102, 2, 2, '2025-11-25', '10:00:00', 100000.00, '2025-11-24 09:32:00', 2),
(103, 3, 3, '2025-11-25', '10:00:00', 110000.00, '2025-11-24 09:32:00', 3),
(104, 4, 4, '2025-11-25', '10:00:00', 115000.00, '2025-11-24 09:32:00', 4),
(105, 5, 5, '2025-11-25', '10:00:00', 90000.00, '2025-11-24 09:32:00', 5),
(106, 1, 2, '2025-11-25', '12:30:00', 130000.00, '2025-11-24 09:32:00', 6),
(107, 2, 3, '2025-11-25', '12:30:00', 110000.00, '2025-11-24 09:32:00', 7),
(108, 3, 4, '2025-11-25', '12:30:00', 120000.00, '2025-11-24 09:32:00', 8),
(109, 4, 5, '2025-11-25', '12:30:00', 125000.00, '2025-11-24 09:32:00', 1),
(110, 5, 1, '2025-11-25', '12:30:00', 100000.00, '2025-11-24 09:32:00', 2),
(111, 1, 3, '2025-11-25', '15:00:00', 120000.00, '2025-11-24 09:32:00', 3),
(112, 2, 4, '2025-11-25', '15:00:00', 100000.00, '2025-11-24 09:32:00', 4),
(113, 3, 5, '2025-11-25', '15:00:00', 110000.00, '2025-11-24 09:32:00', 5),
(114, 4, 1, '2025-11-25', '15:00:00', 115000.00, '2025-11-24 09:32:00', 6),
(115, 5, 2, '2025-11-25', '15:00:00', 90000.00, '2025-11-24 09:32:00', 7),
(116, 1, 4, '2025-11-25', '17:30:00', 130000.00, '2025-11-24 09:32:00', 8),
(117, 2, 5, '2025-11-25', '17:30:00', 110000.00, '2025-11-24 09:32:00', 1),
(118, 3, 1, '2025-11-25', '17:30:00', 120000.00, '2025-11-24 09:32:00', 2),
(119, 4, 2, '2025-11-25', '17:30:00', 125000.00, '2025-11-24 09:32:00', 3),
(120, 5, 3, '2025-11-25', '17:30:00', 100000.00, '2025-11-24 09:32:00', 4),
(121, 1, 5, '2025-11-25', '19:00:00', 120000.00, '2025-11-24 09:32:00', 5),
(122, 2, 1, '2025-11-25', '19:00:00', 100000.00, '2025-11-24 09:32:00', 6),
(123, 3, 2, '2025-11-25', '19:00:00', 110000.00, '2025-11-24 09:32:00', 7),
(124, 4, 3, '2025-11-25', '19:00:00', 115000.00, '2025-11-24 09:32:00', 8),
(125, 5, 4, '2025-11-25', '19:00:00', 90000.00, '2025-11-24 09:32:00', 1),
(126, 1, 1, '2025-11-25', '21:30:00', 130000.00, '2025-11-24 09:32:00', 2),
(127, 2, 2, '2025-11-25', '21:30:00', 110000.00, '2025-11-24 09:32:00', 3),
(128, 3, 3, '2025-11-25', '21:30:00', 120000.00, '2025-11-24 09:32:00', 4),
(129, 4, 4, '2025-11-25', '21:30:00', 125000.00, '2025-11-24 09:32:00', 5),
(130, 5, 5, '2025-11-25', '21:30:00', 100000.00, '2025-11-24 09:32:00', 6),
(131, 1, 1, '2025-11-26', '10:00:00', 120000.00, '2025-11-24 09:32:00', 1),
(132, 2, 2, '2025-11-26', '10:00:00', 100000.00, '2025-11-24 09:32:00', 2),
(133, 3, 3, '2025-11-26', '10:00:00', 110000.00, '2025-11-24 09:32:00', 3),
(134, 4, 4, '2025-11-26', '10:00:00', 115000.00, '2025-11-24 09:32:00', 4),
(135, 5, 5, '2025-11-26', '10:00:00', 90000.00, '2025-11-24 09:32:00', 5),
(136, 1, 2, '2025-11-26', '12:30:00', 130000.00, '2025-11-24 09:32:00', 6),
(137, 2, 3, '2025-11-26', '12:30:00', 110000.00, '2025-11-24 09:32:00', 7),
(138, 3, 4, '2025-11-26', '12:30:00', 120000.00, '2025-11-24 09:32:00', 8),
(139, 4, 5, '2025-11-26', '12:30:00', 125000.00, '2025-11-24 09:32:00', 1),
(140, 5, 1, '2025-11-26', '12:30:00', 100000.00, '2025-11-24 09:32:00', 2),
(141, 1, 3, '2025-11-26', '15:00:00', 120000.00, '2025-11-24 09:32:00', 3),
(142, 2, 4, '2025-11-26', '15:00:00', 100000.00, '2025-11-24 09:32:00', 4),
(143, 3, 5, '2025-11-26', '15:00:00', 110000.00, '2025-11-24 09:32:00', 5),
(144, 4, 1, '2025-11-26', '15:00:00', 115000.00, '2025-11-24 09:32:00', 6),
(145, 5, 2, '2025-11-26', '15:00:00', 90000.00, '2025-11-24 09:32:00', 7),
(146, 1, 4, '2025-11-26', '17:30:00', 130000.00, '2025-11-24 09:32:00', 8),
(147, 2, 5, '2025-11-26', '17:30:00', 110000.00, '2025-11-24 09:32:00', 1),
(148, 3, 1, '2025-11-26', '17:30:00', 120000.00, '2025-11-24 09:32:00', 2),
(149, 4, 2, '2025-11-26', '17:30:00', 125000.00, '2025-11-24 09:32:00', 3),
(150, 5, 3, '2025-11-26', '17:30:00', 100000.00, '2025-11-24 09:32:00', 4),
(151, 1, 5, '2025-11-26', '19:00:00', 120000.00, '2025-11-24 09:32:00', 5),
(152, 2, 1, '2025-11-26', '19:00:00', 100000.00, '2025-11-24 09:32:00', 6),
(153, 3, 2, '2025-11-26', '19:00:00', 110000.00, '2025-11-24 09:32:00', 7),
(154, 4, 3, '2025-11-26', '19:00:00', 115000.00, '2025-11-24 09:32:00', 8),
(155, 5, 4, '2025-11-26', '19:00:00', 90000.00, '2025-11-24 09:32:00', 1),
(156, 1, 1, '2025-11-26', '21:30:00', 130000.00, '2025-11-24 09:32:00', 2),
(157, 2, 2, '2025-11-26', '21:30:00', 110000.00, '2025-11-24 09:32:00', 3),
(158, 3, 3, '2025-11-26', '21:30:00', 120000.00, '2025-11-24 09:32:00', 4),
(159, 4, 4, '2025-11-26', '21:30:00', 125000.00, '2025-11-24 09:32:00', 5),
(160, 5, 5, '2025-11-26', '21:30:00', 100000.00, '2025-11-24 09:32:00', 6),
(161, 1, 1, '2025-11-27', '10:00:00', 120000.00, '2025-11-24 09:32:00', 1),
(162, 2, 2, '2025-11-27', '10:00:00', 100000.00, '2025-11-24 09:32:00', 2),
(163, 3, 3, '2025-11-27', '10:00:00', 110000.00, '2025-11-24 09:32:00', 3),
(164, 4, 4, '2025-11-27', '10:00:00', 115000.00, '2025-11-24 09:32:00', 4),
(165, 5, 5, '2025-11-27', '10:00:00', 90000.00, '2025-11-24 09:32:00', 5),
(166, 1, 2, '2025-11-27', '12:30:00', 130000.00, '2025-11-24 09:32:00', 6),
(167, 2, 3, '2025-11-27', '12:30:00', 110000.00, '2025-11-24 09:32:00', 7),
(168, 3, 4, '2025-11-27', '12:30:00', 120000.00, '2025-11-24 09:32:00', 8),
(169, 4, 5, '2025-11-27', '12:30:00', 125000.00, '2025-11-24 09:32:00', 1),
(170, 5, 1, '2025-11-27', '12:30:00', 100000.00, '2025-11-24 09:32:00', 2),
(171, 1, 3, '2025-11-27', '15:00:00', 120000.00, '2025-11-24 09:32:00', 3),
(172, 2, 4, '2025-11-27', '15:00:00', 100000.00, '2025-11-24 09:32:00', 4),
(173, 3, 5, '2025-11-27', '15:00:00', 110000.00, '2025-11-24 09:32:00', 5),
(174, 4, 1, '2025-11-27', '15:00:00', 115000.00, '2025-11-24 09:32:00', 6),
(175, 5, 2, '2025-11-27', '15:00:00', 90000.00, '2025-11-24 09:32:00', 7),
(176, 1, 4, '2025-11-27', '17:30:00', 130000.00, '2025-11-24 09:32:00', 8),
(177, 2, 5, '2025-11-27', '17:30:00', 110000.00, '2025-11-24 09:32:00', 1),
(178, 3, 1, '2025-11-27', '17:30:00', 120000.00, '2025-11-24 09:32:00', 2),
(179, 4, 2, '2025-11-27', '17:30:00', 125000.00, '2025-11-24 09:32:00', 3),
(180, 5, 3, '2025-11-27', '17:30:00', 100000.00, '2025-11-24 09:32:00', 4),
(181, 1, 5, '2025-11-27', '19:00:00', 120000.00, '2025-11-24 09:32:00', 5),
(182, 2, 1, '2025-11-27', '19:00:00', 100000.00, '2025-11-24 09:32:00', 6),
(183, 3, 2, '2025-11-27', '19:00:00', 110000.00, '2025-11-24 09:32:00', 7),
(184, 4, 3, '2025-11-27', '19:00:00', 115000.00, '2025-11-24 09:32:00', 8),
(185, 5, 4, '2025-11-27', '19:00:00', 90000.00, '2025-11-24 09:32:00', 1),
(186, 1, 1, '2025-11-27', '21:30:00', 130000.00, '2025-11-24 09:32:00', 2),
(187, 2, 2, '2025-11-27', '21:30:00', 110000.00, '2025-11-24 09:32:00', 3),
(188, 3, 3, '2025-11-27', '21:30:00', 120000.00, '2025-11-24 09:32:00', 4),
(189, 4, 4, '2025-11-27', '21:30:00', 125000.00, '2025-11-24 09:32:00', 5),
(190, 5, 5, '2025-11-27', '21:30:00', 100000.00, '2025-11-24 09:32:00', 6),
(191, 1, 1, '2025-11-28', '10:00:00', 120000.00, '2025-11-24 09:32:00', 1),
(192, 2, 2, '2025-11-28', '10:00:00', 100000.00, '2025-11-24 09:32:00', 2),
(193, 3, 3, '2025-11-28', '10:00:00', 110000.00, '2025-11-24 09:32:00', 3),
(194, 4, 4, '2025-11-28', '10:00:00', 115000.00, '2025-11-24 09:32:00', 4),
(195, 5, 5, '2025-11-28', '10:00:00', 90000.00, '2025-11-24 09:32:00', 5),
(196, 1, 2, '2025-11-28', '12:30:00', 130000.00, '2025-11-24 09:32:00', 6),
(197, 2, 3, '2025-11-28', '12:30:00', 110000.00, '2025-11-24 09:32:00', 7),
(198, 3, 4, '2025-11-28', '12:30:00', 120000.00, '2025-11-24 09:32:00', 8),
(199, 4, 5, '2025-11-28', '12:30:00', 125000.00, '2025-11-24 09:32:00', 1),
(200, 5, 1, '2025-11-28', '12:30:00', 100000.00, '2025-11-24 09:32:00', 2),
(201, 1, 3, '2025-11-28', '15:00:00', 120000.00, '2025-11-24 09:32:00', 3),
(202, 2, 4, '2025-11-28', '15:00:00', 100000.00, '2025-11-24 09:32:00', 4),
(203, 3, 5, '2025-11-28', '15:00:00', 110000.00, '2025-11-24 09:32:00', 5),
(204, 4, 1, '2025-11-28', '15:00:00', 115000.00, '2025-11-24 09:32:00', 6),
(205, 5, 2, '2025-11-28', '15:00:00', 90000.00, '2025-11-24 09:32:00', 7),
(206, 1, 4, '2025-11-28', '17:30:00', 130000.00, '2025-11-24 09:32:00', 8),
(207, 2, 5, '2025-11-28', '17:30:00', 110000.00, '2025-11-24 09:32:00', 1),
(208, 3, 1, '2025-11-28', '17:30:00', 120000.00, '2025-11-24 09:32:00', 2),
(209, 4, 2, '2025-11-28', '17:30:00', 125000.00, '2025-11-24 09:32:00', 3),
(210, 5, 3, '2025-11-28', '17:30:00', 100000.00, '2025-11-24 09:32:00', 4),
(211, 1, 5, '2025-11-28', '19:00:00', 120000.00, '2025-11-24 09:32:00', 5),
(212, 2, 1, '2025-11-28', '19:00:00', 100000.00, '2025-11-24 09:32:00', 6),
(213, 3, 2, '2025-11-28', '19:00:00', 110000.00, '2025-11-24 09:32:00', 7),
(214, 4, 3, '2025-11-28', '19:00:00', 115000.00, '2025-11-24 09:32:00', 8),
(215, 5, 4, '2025-11-28', '19:00:00', 90000.00, '2025-11-24 09:32:00', 1),
(216, 1, 1, '2025-11-28', '21:30:00', 130000.00, '2025-11-24 09:32:00', 2),
(217, 2, 2, '2025-11-28', '21:30:00', 110000.00, '2025-11-24 09:32:00', 3),
(218, 3, 3, '2025-11-28', '21:30:00', 120000.00, '2025-11-24 09:32:00', 4),
(219, 4, 4, '2025-11-28', '21:30:00', 125000.00, '2025-11-24 09:32:00', 5),
(220, 5, 5, '2025-11-28', '21:30:00', 100000.00, '2025-11-24 09:32:00', 6),
(221, 1, 1, '2025-11-29', '10:00:00', 120000.00, '2025-11-24 09:32:00', 1),
(222, 2, 2, '2025-11-29', '10:00:00', 100000.00, '2025-11-24 09:32:00', 2),
(223, 3, 3, '2025-11-29', '10:00:00', 110000.00, '2025-11-24 09:32:00', 3),
(224, 4, 4, '2025-11-29', '10:00:00', 115000.00, '2025-11-24 09:32:00', 4),
(225, 5, 5, '2025-11-29', '10:00:00', 90000.00, '2025-11-24 09:32:00', 5),
(226, 1, 2, '2025-11-29', '12:30:00', 130000.00, '2025-11-24 09:32:00', 6),
(227, 2, 3, '2025-11-29', '12:30:00', 110000.00, '2025-11-24 09:32:00', 7),
(228, 3, 4, '2025-11-29', '12:30:00', 120000.00, '2025-11-24 09:32:00', 8),
(229, 4, 5, '2025-11-29', '12:30:00', 125000.00, '2025-11-24 09:32:00', 1),
(230, 5, 1, '2025-11-29', '12:30:00', 100000.00, '2025-11-24 09:32:00', 2),
(231, 1, 3, '2025-11-29', '15:00:00', 120000.00, '2025-11-24 09:32:00', 3),
(232, 2, 4, '2025-11-29', '15:00:00', 100000.00, '2025-11-24 09:32:00', 4),
(233, 3, 5, '2025-11-29', '15:00:00', 110000.00, '2025-11-24 09:32:00', 5),
(234, 4, 1, '2025-11-29', '15:00:00', 115000.00, '2025-11-24 09:32:00', 6),
(235, 5, 2, '2025-11-29', '15:00:00', 90000.00, '2025-11-24 09:32:00', 7),
(236, 1, 4, '2025-11-29', '17:30:00', 130000.00, '2025-11-24 09:32:00', 8),
(237, 2, 5, '2025-11-29', '17:30:00', 110000.00, '2025-11-24 09:32:00', 1),
(238, 3, 1, '2025-11-29', '17:30:00', 120000.00, '2025-11-24 09:32:00', 2),
(239, 4, 2, '2025-11-29', '17:30:00', 125000.00, '2025-11-24 09:32:00', 3),
(240, 5, 3, '2025-11-29', '17:30:00', 100000.00, '2025-11-24 09:32:00', 4),
(241, 1, 5, '2025-11-29', '19:00:00', 120000.00, '2025-11-24 09:32:00', 5),
(242, 2, 1, '2025-11-29', '19:00:00', 100000.00, '2025-11-24 09:32:00', 6),
(243, 3, 2, '2025-11-29', '19:00:00', 110000.00, '2025-11-24 09:32:00', 7),
(244, 4, 3, '2025-11-29', '19:00:00', 115000.00, '2025-11-24 09:32:00', 8),
(245, 5, 4, '2025-11-29', '19:00:00', 90000.00, '2025-11-24 09:32:00', 1),
(246, 1, 1, '2025-11-29', '21:30:00', 130000.00, '2025-11-24 09:32:00', 2),
(247, 2, 2, '2025-11-29', '21:30:00', 110000.00, '2025-11-24 09:32:00', 3),
(248, 3, 3, '2025-11-29', '21:30:00', 120000.00, '2025-11-24 09:32:00', 4),
(249, 4, 4, '2025-11-29', '21:30:00', 125000.00, '2025-11-24 09:32:00', 5),
(250, 5, 5, '2025-11-29', '21:30:00', 100000.00, '2025-11-24 09:32:00', 6),
(251, 1, 1, '2025-11-30', '10:00:00', 120000.00, '2025-11-24 09:32:00', 1),
(252, 2, 2, '2025-11-30', '10:00:00', 100000.00, '2025-11-24 09:32:00', 2),
(253, 3, 3, '2025-11-30', '10:00:00', 110000.00, '2025-11-24 09:32:00', 3),
(254, 4, 4, '2025-11-30', '10:00:00', 115000.00, '2025-11-24 09:32:00', 4),
(255, 5, 5, '2025-11-30', '10:00:00', 90000.00, '2025-11-24 09:32:00', 5),
(256, 1, 2, '2025-11-30', '12:30:00', 130000.00, '2025-11-24 09:32:00', 6),
(257, 2, 3, '2025-11-30', '12:30:00', 110000.00, '2025-11-24 09:32:00', 7),
(258, 3, 4, '2025-11-30', '12:30:00', 120000.00, '2025-11-24 09:32:00', 8),
(259, 4, 5, '2025-11-30', '12:30:00', 125000.00, '2025-11-24 09:32:00', 1),
(260, 5, 1, '2025-11-30', '12:30:00', 100000.00, '2025-11-24 09:32:00', 2),
(261, 1, 3, '2025-11-30', '15:00:00', 120000.00, '2025-11-24 09:32:00', 3),
(262, 2, 4, '2025-11-30', '15:00:00', 100000.00, '2025-11-24 09:32:00', 4),
(263, 3, 5, '2025-11-30', '15:00:00', 110000.00, '2025-11-24 09:32:00', 5),
(264, 4, 1, '2025-11-30', '15:00:00', 115000.00, '2025-11-24 09:32:00', 6),
(265, 5, 2, '2025-11-30', '15:00:00', 90000.00, '2025-11-24 09:32:00', 7),
(266, 1, 4, '2025-11-30', '17:30:00', 130000.00, '2025-11-24 09:32:00', 8),
(267, 2, 5, '2025-11-30', '17:30:00', 110000.00, '2025-11-24 09:32:00', 1),
(268, 3, 1, '2025-11-30', '17:30:00', 120000.00, '2025-11-24 09:32:00', 2),
(269, 4, 2, '2025-11-30', '17:30:00', 125000.00, '2025-11-24 09:32:00', 3),
(270, 5, 3, '2025-11-30', '17:30:00', 100000.00, '2025-11-24 09:32:00', 4),
(319, 18, 1, '2025-11-25', '10:00:00', 85000.00, '2025-11-24 02:58:07', 1),
(320, 18, 2, '2025-11-25', '14:30:00', 85000.00, '2025-11-24 02:58:07', 3),
(321, 18, 3, '2025-11-26', '18:00:00', 95000.00, '2025-11-24 02:58:07', 5),
(322, 18, 5, '2025-11-26', '21:00:00', 90000.00, '2025-11-24 02:58:07', 8),
(323, 19, 1, '2025-11-25', '13:00:00', 90000.00, '2025-11-24 02:58:07', 2),
(324, 19, 3, '2025-11-25', '16:30:00', 100000.00, '2025-11-24 02:58:07', 6),
(325, 19, 4, '2025-11-26', '10:30:00', 80000.00, '2025-11-24 02:58:07', 7),
(326, 19, 5, '2025-11-26', '15:00:00', 90000.00, '2025-11-24 02:58:07', 8),
(327, 20, 2, '2025-11-25', '19:00:00', 95000.00, '2025-11-24 02:58:07', 4),
(328, 20, 3, '2025-11-25', '22:00:00', 105000.00, '2025-11-24 02:58:07', 5),
(329, 20, 4, '2025-11-26', '18:00:00', 90000.00, '2025-11-24 02:58:07', 7),
(330, 21, 5, '2025-11-25', '12:00:00', 90000.00, '2025-11-24 02:58:07', 8),
(331, 21, 1, '2025-11-26', '14:00:00', 80000.00, '2025-11-24 02:58:07', 1),
(332, 21, 2, '2025-11-26', '20:30:00', 90000.00, '2025-11-24 02:58:07', 3),
(333, 22, 1, '2025-11-25', '17:00:00', 80000.00, '2025-11-24 02:58:07', 2),
(334, 22, 5, '2025-11-26', '19:30:00', 90000.00, '2025-11-24 02:58:07', 8),
(335, 23, 2, '2025-11-25', '21:00:00', 85000.00, '2025-11-24 02:58:07', 4),
(336, 23, 3, '2025-11-26', '16:00:00', 95000.00, '2025-11-24 02:58:07', 6),
(337, 25, 3, '2025-11-25', '15:00:00', 100000.00, '2025-11-24 02:58:07', 5),
(338, 25, 4, '2025-11-26', '20:30:00', 85000.00, '2025-11-24 02:58:07', 7),
(339, 26, 1, '2025-11-25', '09:30:00', 75000.00, '2025-11-24 02:58:07', 1),
(340, 26, 5, '2025-11-26', '13:00:00', 88000.00, '2025-11-24 02:58:07', 8),
(341, 27, 2, '2025-11-25', '11:00:00', 75000.00, '2025-11-24 02:58:07', 4),
(342, 27, 4, '2025-11-26', '12:30:00', 78000.00, '2025-11-24 02:58:07', 7),
(351, 18, 3, '2025-11-29', '14:00:00', 120000.00, '2025-11-28 01:03:25', 11),
(352, 18, 3, '2025-11-29', '17:00:00', 120000.00, '2025-11-28 01:03:25', 11),
(353, 18, 3, '2025-11-29', '20:00:00', 120000.00, '2025-11-28 01:03:25', 11),
(354, 18, 3, '2025-11-29', '15:30:00', 130000.00, '2025-11-28 01:03:25', 12),
(355, 18, 3, '2025-11-29', '19:00:00', 130000.00, '2025-11-28 01:03:25', 12),
(356, 18, 3, '2025-11-28', '03:00:00', 120000.00, '2025-11-28 01:03:25', 11),
(357, 18, 3, '2025-11-28', '04:00:00', 130000.00, '2025-11-28 01:03:25', 12);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `subscriptions`
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
-- Đang đổ dữ liệu cho bảng `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `name`, `price`, `description`, `benefits`, `created_at`) VALUES
(1, 'Free', 0.00, 'Xem trailer, phim miễn phí', 'Giới hạn nội dung, có quảng cáo', '2025-11-09 16:03:14'),
(2, 'Silver', 79000.00, 'Xem phim HD không quảng cáo', 'HD quality, không quảng cáo', '2025-11-09 16:03:14'),
(3, 'Gold', 129000.00, 'Full HD, nội dung độc quyền', 'Full HD, nội dung mới', '2025-11-09 16:03:14'),
(4, 'Premium', 199000.00, '4K, xem sớm, ưu đãi vé rạp', '4K, early access, ưu đãi vé', '2025-11-09 16:03:14'),
(5, 'Basic', 49000.00, 'Gói cơ bản với chất lượng SD', 'SD quality, có quảng cáo', '2025-11-12 07:41:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `support_tickets`
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

--
-- Đang đổ dữ liệu cho bảng `support_tickets`
--

INSERT INTO `support_tickets` (`id`, `user_id`, `subject`, `message`, `status`, `priority`, `tags`, `assigned_to`, `created_at`, `updated_at`) VALUES
(1, 1, 'Không thể đăng nhập', 'Tôi không thể đăng nhập vào tài khoản của mình', 'Mới', 'Cao', NULL, NULL, '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(2, 2, 'Vấn đề thanh toán', 'Giao dịch của tôi bị lỗi khi thanh toán', 'Đang xử lý', 'Trung bình', NULL, NULL, '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(3, 3, 'Yêu cầu hoàn tiền', 'Tôi muốn hoàn tiền cho vé đã mua', 'Mới', 'Cao', NULL, NULL, '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(4, 4, 'Câu hỏi về gói dịch vụ', 'Tôi muốn biết thêm về gói Premium', 'Đã giải quyết', 'Thấp', NULL, NULL, '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(5, 5, 'Lỗi phát video', 'Video không phát được trên trình duyệt của tôi', 'Đang xử lý', 'Trung bình', NULL, NULL, '2025-11-12 07:41:09', '2025-11-12 07:41:09'),
(6, 1, 'Thay đổi thông tin tài khoản', 'Tôi muốn thay đổi email đăng nhập', 'Mới', 'Thấp', NULL, NULL, '2025-11-12 07:41:09', '2025-11-12 07:41:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `system_config`
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
-- Đang đổ dữ liệu cho bảng `system_config`
--

INSERT INTO `system_config` (`id`, `config_key`, `config_value`, `description`, `updated_by`, `updated_at`) VALUES
(1, 'maintenance_mode', '0', 'Chế độ bảo trì (0=off, 1=on)', NULL, '2025-11-10 16:41:17'),
(2, 'max_upload_size', '500', 'Kích thước upload tối đa (MB)', NULL, '2025-11-10 16:41:17'),
(3, 'payment_gateway', 'vnpay', 'Cổng thanh toán mặc định', NULL, '2025-11-10 16:41:17'),
(4, 'default_currency', 'VND', 'Đơn vị tiền tệ', NULL, '2025-11-10 16:41:17'),
(5, 'site_name', 'CineHub', 'Tên website', NULL, '2025-11-10 16:41:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `theaters`
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

--
-- Đang đổ dữ liệu cho bảng `theaters`
--

INSERT INTO `theaters` (`id`, `name`, `location`, `phone`, `created_at`, `total_screens`, `address`, `is_active`) VALUES
(1, 'CGV Vincom Center', 'Hà Nội', '0241234567', '2025-11-12 07:41:09', 8, '72 Lê Thánh Tôn, Hoàn Kiếm, Hà Nội', 1),
(2, 'CGV Landmark', 'Hà Nội', '0242345678', '2025-11-12 07:41:09', 6, '72A Nguyễn Trãi, Thanh Xuân, Hà Nội', 1),
(3, 'Lotte Cinema', 'Hồ Chí Minh', '0283456789', '2025-11-12 07:41:09', 10, '469 Nguyễn Hữu Thọ, Quận 7, TP.HCM', 1),
(4, 'Galaxy Cinema', 'Đà Nẵng', '0236456789', '2025-11-12 07:41:09', 7, '910A Ngô Quyền, Sơn Trà, Đà Nẵng', 1),
(5, 'BHD Star Cineplex', 'Hồ Chí Minh', '0284567890', '2025-11-12 07:41:09', 9, 'L3-Vincom Center, 72 Lê Thánh Tôn, Quận 1, TP.HCM', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `theater_managers`
--

CREATE TABLE `theater_managers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `theater_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `theater_screens`
--

CREATE TABLE `theater_screens` (
  `id` int(11) NOT NULL,
  `theater_id` int(11) NOT NULL,
  `screen_name` varchar(100) NOT NULL,
  `total_seats` int(11) NOT NULL,
  `seat_layout` text DEFAULT NULL,
  `seat_layout_config` text DEFAULT NULL COMMENT 'JSON config for seat layout: rows, cols, vip_rows, couple_rows, etc.',
  `screen_type` enum('2D','3D','IMAX','4DX') DEFAULT '2D',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `theater_screens`
--

INSERT INTO `theater_screens` (`id`, `theater_id`, `screen_name`, `total_seats`, `seat_layout`, `seat_layout_config`, `screen_type`, `is_active`, `created_at`) VALUES
(1, 1, 'Phòng 1', 120, NULL, '{\"rows\":[\"A\",\"B\",\"C\",\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\",\"L\"],\"cols\":[1,2,3,4,5,6,7,8,9,10,11,12],\"vip_rows\":[\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\"],\"couple_rows\":[\"L\"],\"normal_price\":120000,\"vip_price\":180000,\"couple_price\":240000,\"layout_type\":\"standard\"}', '2D', 1, '2025-11-12 07:41:09'),
(2, 1, 'Phòng 2', 150, NULL, '{\"rows\":[\"A\",\"B\",\"C\",\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\",\"L\",\"M\"],\"cols\":[1,2,3,4,5,6,7,8,9,10,11,12,13,14],\"vip_rows\":[\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\",\"L\"],\"couple_rows\":[\"M\"],\"normal_price\":130000,\"vip_price\":200000,\"couple_price\":260000,\"layout_type\":\"standard\"}', '3D', 1, '2025-11-12 07:41:09'),
(3, 2, 'Phòng 1', 100, NULL, '{\"rows\":[\"A\",\"B\",\"C\",\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\"],\"cols\":[1,2,3,4,5,6,7,8,9,10],\"vip_rows\":[\"D\",\"E\",\"F\",\"G\",\"H\",\"I\"],\"couple_rows\":[\"J\"],\"normal_price\":110000,\"vip_price\":170000,\"couple_price\":220000,\"layout_type\":\"standard\"}', '2D', 1, '2025-11-12 07:41:09'),
(4, 2, 'Phòng 2', 120, NULL, '{\"rows\":[\"A\",\"B\",\"C\",\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\",\"L\"],\"cols\":[1,2,3,4,5,6,7,8,9,10],\"vip_rows\":[\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\"],\"couple_rows\":[\"L\"],\"normal_price\":150000,\"vip_price\":220000,\"couple_price\":300000,\"layout_type\":\"imax\"}', 'IMAX', 1, '2025-11-12 07:41:09'),
(5, 3, 'Phòng 1', 200, NULL, '{\"rows\":[\"A\",\"B\",\"C\",\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\",\"L\",\"M\",\"N\"],\"cols\":[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15],\"vip_rows\":[\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\",\"L\",\"M\"],\"couple_rows\":[\"N\"],\"normal_price\":140000,\"vip_price\":210000,\"couple_price\":280000,\"layout_type\":\"4dx\"}', '4DX', 1, '2025-11-12 07:41:09'),
(6, 3, 'Phòng 2', 180, NULL, '{\"rows\":[\"A\",\"B\",\"C\",\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\",\"L\",\"M\"],\"cols\":[1,2,3,4,5,6,7,8,9,10,11,12,13,14],\"vip_rows\":[\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\",\"L\"],\"couple_rows\":[\"M\"],\"normal_price\":130000,\"vip_price\":200000,\"couple_price\":260000,\"layout_type\":\"standard\"}', '3D', 1, '2025-11-12 07:41:09'),
(7, 4, 'Phòng 1', 110, NULL, '{\"rows\":[\"A\",\"B\",\"C\",\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\"],\"cols\":[1,2,3,4,5,6,7,8,9,10],\"vip_rows\":[\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\"],\"couple_rows\":[\"K\"],\"normal_price\":115000,\"vip_price\":175000,\"couple_price\":230000,\"layout_type\":\"standard\"}', '2D', 1, '2025-11-12 07:41:09'),
(8, 5, 'Phòng 1', 130, NULL, '{\"rows\":[\"A\",\"B\",\"C\",\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\",\"L\"],\"cols\":[1,2,3,4,5,6,7,8,9,10,11],\"vip_rows\":[\"D\",\"E\",\"F\",\"G\",\"H\",\"I\",\"J\",\"K\"],\"couple_rows\":[\"L\"],\"normal_price\":125000,\"vip_price\":190000,\"couple_price\":250000,\"layout_type\":\"standard\"}', '3D', 1, '2025-11-12 07:41:09'),
(9, 1, 'Phòng 3', 200, NULL, '{\"rows\":[\"A\",\"B\",\"C\",\"D\",\"E\",\"F\",\"G\",\"H\",\"I\"],\"cols\":[1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17],\"vip_rows\":[\"D\",\"E\",\"F\",\"G\",\"H\",\"I\"],\"couple_rows\":[],\"normal_price\":120000,\"vip_price\":180000,\"couple_price\":240000,\"layout_type\":\"complex\",\"seat_groups\":[{\"rows\":[\"A\",\"B\",\"C\",\"D\",\"E\",\"F\",\"G\",\"H\",\"I\"],\"cols\":[1,2]},{\"rows\":[\"A\",\"B\",\"C\",\"D\",\"E\",\"F\",\"G\",\"H\",\"I\"],\"cols\":[3,4,5,6,7,8,9,10,11,12,13,14,15]},{\"rows\":[\"H\",\"I\"],\"cols\":[16,17]}]}', 'IMAX', 1, '2025-11-12 07:41:09'),
(11, 3, 'Phòng 3', 180, NULL, '{\r\n  \"layout_type\": \"grouped\",\r\n  \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\"],\r\n  \"seat_groups\": [\r\n    {\r\n      \"name\": \"Khối trái\",\r\n      \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\"],\r\n      \"cols\": [1, 2, 3, 4]\r\n    },\r\n    {\r\n      \"name\": \"Khối giữa\",\r\n      \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\"],\r\n      \"cols\": [5, 6, 7, 8]\r\n    },\r\n    {\r\n      \"name\": \"Khối phải\",\r\n      \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\", \"H\", \"I\"],\r\n      \"cols\": [9, 10, 11, 12]\r\n    },\r\n    {\r\n      \"name\": \"Ghế riêng lẻ\",\r\n      \"rows\": [\"H\", \"I\"],\r\n      \"cols\": [13, 14]\r\n    }\r\n  ],\r\n  \"vip_rows\": [\"D\", \"E\", \"F\", \"G\", \"H\"],\r\n  \"couple_rows\": [],\r\n  \"normal_price\": 120000,\r\n  \"vip_price\": 180000,\r\n  \"couple_price\": 240000\r\n}', '2D', 1, '2025-11-28 01:03:25'),
(12, 3, 'Phòng 4', 224, NULL, '{\r\n  \"layout_type\": \"grouped\",\r\n  \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\"],\r\n  \"seat_groups\": [\r\n    {\r\n      \"name\": \"Khối 1\",\r\n      \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\"],\r\n      \"cols\": [1, 2, 3, 4, 5, 6]\r\n    },\r\n    {\r\n      \"name\": \"Khối 2\",\r\n      \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\"],\r\n      \"cols\": [7, 8, 9, 10, 11, 12, 13, 14, 15, 16]\r\n    },\r\n    {\r\n      \"name\": \"Khối 3\",\r\n      \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\"],\r\n      \"cols\": [17, 18, 19]\r\n    },\r\n    {\r\n      \"name\": \"Khối 4\",\r\n      \"rows\": [\"A\", \"B\", \"C\", \"D\", \"E\", \"F\", \"G\"],\r\n      \"cols\": [20, 21, 22, 23, 24, 25]\r\n    }\r\n  ],\r\n  \"vip_rows\": [\"E\", \"F\", \"G\"],\r\n  \"couple_rows\": [],\r\n  \"normal_price\": 130000,\r\n  \"vip_price\": 200000,\r\n  \"couple_price\": 260000\r\n}', '3D', 1, '2025-11-28 01:03:25');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `showtime_id` int(11) NOT NULL,
  `seat` varchar(10) NOT NULL,
  `seat_type` enum('normal','vip','couple') DEFAULT 'normal',
  `qr_code` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `status` enum('Đã đặt','Đã hủy') DEFAULT 'Đã đặt',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `tickets`
--

INSERT INTO `tickets` (`id`, `user_id`, `showtime_id`, `seat`, `seat_type`, `qr_code`, `price`, `status`, `created_at`) VALUES
(1, 1, 71, 'A5', 'normal', NULL, 120000.00, 'Đã đặt', '2025-11-12 07:41:09'),
(2, 1, 71, 'A6', 'normal', NULL, 120000.00, 'Đã đặt', '2025-11-12 07:41:09'),
(3, 2, 72, 'B10', 'normal', NULL, 120000.00, 'Đã đặt', '2025-11-12 07:41:09'),
(4, 3, 73, 'C15', 'normal', NULL, 100000.00, 'Đã đặt', '2025-11-12 07:41:09'),
(5, 4, 74, 'D20', 'normal', NULL, 110000.00, 'Đã đặt', '2025-11-12 07:41:09'),
(6, 5, 75, 'E12', 'normal', NULL, 115000.00, 'Đã đặt', '2025-11-12 07:41:09'),
(7, 9, 81, 'A6', 'normal', 'TICKET_69240f4b89ccf_9_81_1763970891_A6', 120000.00, 'Đã đặt', '2025-11-24 07:54:51'),
(8, 9, 81, 'G7', 'normal', 'TICKET_69240f61d71ab_9_81_1763970913_G7', 120000.00, 'Đã đặt', '2025-11-24 07:55:13'),
(9, 9, 81, 'G8', 'normal', 'TICKET_69240f61d7831_9_81_1763970913_G8', 120000.00, 'Đã đặt', '2025-11-24 07:55:13'),
(10, 9, 81, 'G9', 'normal', 'TICKET_69240f61d7bcf_9_81_1763970913_G9', 120000.00, 'Đã đặt', '2025-11-24 07:55:13'),
(11, 9, 121, 'G4', 'normal', 'TICKET_692506ab7cef0_9_121_1764034219_G4', 120000.00, 'Đã đặt', '2025-11-25 01:30:19'),
(12, 9, 121, 'G5', 'normal', 'TICKET_692506ab7d8a8_9_121_1764034219_G5', 120000.00, 'Đã đặt', '2025-11-25 01:30:19'),
(13, 9, 121, 'H4', 'normal', 'TICKET_692506ab7e237_9_121_1764034219_H4', 120000.00, 'Đã đặt', '2025-11-25 01:30:19'),
(14, 9, 121, 'H5', 'normal', 'TICKET_692506ab7ea13_9_121_1764034219_H5', 120000.00, 'Đã đặt', '2025-11-25 01:30:19'),
(15, 9, 121, 'H6', 'normal', 'TICKET_692506ab7f17a_9_121_1764034219_H6', 120000.00, 'Đã đặt', '2025-11-25 01:30:19'),
(16, 9, 121, 'H7', 'normal', 'TICKET_692506ab7f857_9_121_1764034219_H7', 120000.00, 'Đã đặt', '2025-11-25 01:30:19'),
(17, 9, 121, 'I4', 'normal', 'TICKET_692506ab7ff3e_9_121_1764034219_I4', 120000.00, 'Đã đặt', '2025-11-25 01:30:19'),
(18, 9, 121, 'I5', 'normal', 'TICKET_692506ab80651_9_121_1764034219_I5', 120000.00, 'Đã đặt', '2025-11-25 01:30:19'),
(19, 9, 121, 'I6', 'normal', 'TICKET_692506ab80dbd_9_121_1764034219_I6', 120000.00, 'Đã đặt', '2025-11-25 01:30:19'),
(20, 9, 121, 'I7', 'normal', 'TICKET_692506ab81559_9_121_1764034219_I7', 120000.00, 'Đã đặt', '2025-11-25 01:30:19'),
(21, 9, 116, 'I5', 'normal', 'TICKET_692512378240a_9_116_1764037175_I5', 130000.00, 'Đã đặt', '2025-11-25 02:19:35'),
(22, 9, 116, 'I6', 'normal', 'TICKET_6925123783725_9_116_1764037175_I6', 130000.00, 'Đã đặt', '2025-11-25 02:19:35'),
(23, 9, 151, 'K5', 'normal', 'TICKET_69266a0629905_9_151_1764125190_K5', 120000.00, 'Đã đặt', '2025-11-26 02:46:30'),
(24, 9, 354, 'C1', 'normal', 'TICKET_6928f5ed22549_9_354_1764292077_C1', 130000.00, 'Đã đặt', '2025-11-28 01:07:57'),
(25, 9, 354, 'D1', 'normal', 'TICKET_6928f5ed25b7b_9_354_1764292077_D1', 130000.00, 'Đã đặt', '2025-11-28 01:07:57'),
(26, 9, 354, 'A4', 'normal', 'TICKET_6928f63cb1d3c_9_354_1764292156_A4', 130000.00, 'Đã đặt', '2025-11-28 01:09:16'),
(27, 9, 354, 'B4', 'normal', 'TICKET_6928f63cb2506_9_354_1764292156_B4', 130000.00, 'Đã đặt', '2025-11-28 01:09:16'),
(28, 9, 354, 'C4', 'normal', 'TICKET_6928f63cb2e75_9_354_1764292156_C4', 130000.00, 'Đã đặt', '2025-11-28 01:09:16'),
(29, 9, 354, 'D4', 'normal', 'TICKET_6928f63cb342c_9_354_1764292156_D4', 130000.00, 'Đã đặt', '2025-11-28 01:09:16'),
(30, 9, 354, 'D3', 'normal', 'TICKET_6928f6735fbc6_9_354_1764292211_D3', 130000.00, 'Đã đặt', '2025-11-28 01:10:11'),
(31, 9, 354, 'B6', 'normal', 'TICKET_6928f68bda27a_9_354_1764292235_B6', 130000.00, 'Đã đặt', '2025-11-28 01:10:35'),
(32, 9, 354, 'C6', 'normal', 'TICKET_6928f68bda8f8_9_354_1764292235_C6', 130000.00, 'Đã đặt', '2025-11-28 01:10:35'),
(33, 9, 354, 'C5', 'normal', 'TICKET_6928f6dc1494e_9_354_1764292316_C5', 130000.00, 'Đã đặt', '2025-11-28 01:11:56'),
(34, 9, 354, 'D6', 'normal', 'TICKET_6928f6dc14edb_9_354_1764292316_D6', 130000.00, 'Đã đặt', '2025-11-28 01:11:56'),
(35, 9, 354, 'B7', 'normal', 'TICKET_6928f7b37254a_9_354_1764292531_B7', 130000.00, 'Đã đặt', '2025-11-28 01:15:31'),
(36, 9, 354, 'B8', 'normal', 'TICKET_6928f7b3731f2_9_354_1764292531_B8', 130000.00, 'Đã đặt', '2025-11-28 01:15:31'),
(37, 9, 354, 'B5', 'normal', 'TICKET_6928f7e0831f8_9_354_1764292576_B5', 130000.00, 'Đã đặt', '2025-11-28 01:16:16'),
(38, 9, 354, 'C9', 'normal', 'TICKET_6928f7e083b07_9_354_1764292576_C9', 130000.00, 'Đã đặt', '2025-11-28 01:16:16'),
(39, 9, 354, 'C12', 'normal', 'TICKET_6928f803f2edd_9_354_1764292611_C12', 130000.00, 'Đã đặt', '2025-11-28 01:16:51'),
(40, 9, 354, 'D11', 'normal', 'TICKET_6928f803f3548_9_354_1764292611_D11', 130000.00, 'Đã đặt', '2025-11-28 01:16:52'),
(41, 9, 354, 'C18', 'normal', 'TICKET_6928f87f60dcb_9_354_1764292735_C18', 130000.00, 'Đã đặt', '2025-11-28 01:18:55'),
(42, 9, 354, 'C19', 'normal', 'TICKET_6928f87f6185a_9_354_1764292735_C19', 130000.00, 'Đã đặt', '2025-11-28 01:18:55'),
(43, 9, 354, 'D17', 'normal', 'TICKET_6928f87f61eab_9_354_1764292735_D17', 130000.00, 'Đã đặt', '2025-11-28 01:18:55'),
(44, 9, 354, 'D18', 'normal', 'TICKET_6928f87f6251b_9_354_1764292735_D18', 130000.00, 'Đã đặt', '2025-11-28 01:18:55'),
(45, 9, 354, 'B11', 'normal', 'TICKET_6928f8df6b12b_9_354_1764292831_B11', 130000.00, 'Đã đặt', '2025-11-28 01:20:31'),
(46, 9, 354, 'C10', 'normal', 'TICKET_6928f8df6c15a_9_354_1764292831_C10', 130000.00, 'Đã đặt', '2025-11-28 01:20:31'),
(47, 9, 354, 'C20', 'normal', 'TICKET_6928f958850fe_9_354_1764292952_C20', 130000.00, 'Đã đặt', '2025-11-28 01:22:32'),
(48, 9, 354, 'C21', 'normal', 'TICKET_6928f95885a36_9_354_1764292952_C21', 130000.00, 'Đã đặt', '2025-11-28 01:22:32'),
(49, 9, 354, 'C24', 'normal', 'TICKET_6928f97fbc4c5_9_354_1764292991_C24', 130000.00, 'Đã đặt', '2025-11-28 01:23:11'),
(50, 9, 354, 'C25', 'normal', 'TICKET_6928f97fbcdc9_9_354_1764292991_C25', 130000.00, 'Đã đặt', '2025-11-28 01:23:11'),
(51, 9, 354, 'B23', 'normal', 'TICKET_6928f9a1b6715_9_354_1764293025_B23', 130000.00, 'Đã đặt', '2025-11-28 01:23:45'),
(52, 9, 354, 'C22', 'normal', 'TICKET_6928f9a1b6ec4_9_354_1764293025_C22', 130000.00, 'Đã đặt', '2025-11-28 01:23:45'),
(53, 9, 354, 'A11', 'normal', 'TICKET_6928fa497ab6e_9_354_1764293193_A11', 130000.00, 'Đã đặt', '2025-11-28 01:26:33'),
(54, 9, 354, 'B10', 'normal', 'TICKET_6928fa497b6f2_9_354_1764293193_B10', 130000.00, 'Đã đặt', '2025-11-28 01:26:33'),
(55, 9, 355, 'D11', 'normal', 'TICKET_6928fac8e037b_9_355_1764293320_D11', 130000.00, 'Đã đặt', '2025-11-28 01:28:40'),
(56, 9, 357, 'E9', 'vip', 'TICKET_69290459ea985_9_357_1764295769_E9', 200000.00, 'Đã đặt', '2025-11-28 02:09:29'),
(57, 9, 357, 'E10', 'vip', 'TICKET_69290459eb7b9_9_357_1764295769_E10', 200000.00, 'Đã đặt', '2025-11-28 02:09:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('ticket','subscription','deposit') NOT NULL,
  `related_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('Momo','ZaloPay','Stripe','Bank','Cash') DEFAULT 'Momo',
  `status` enum('Thành công','Thất bại','Đang xử lý') DEFAULT 'Thành công',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `type`, `related_id`, `amount`, `method`, `status`, `created_at`) VALUES
(1, 1, 'subscription', 2, 79000.00, 'Momo', 'Thành công', '2025-11-12 07:41:09'),
(2, 2, 'subscription', 3, 129000.00, 'ZaloPay', 'Thành công', '2025-11-12 07:41:09'),
(3, 3, 'ticket', 1, 240000.00, 'Momo', 'Thành công', '2025-11-12 07:41:09'),
(4, 4, 'subscription', 4, 199000.00, 'Bank', 'Thành công', '2025-11-12 07:41:09'),
(5, 5, 'ticket', 5, 110000.00, 'Momo', 'Thành công', '2025-11-12 07:41:09'),
(6, 1, 'ticket', 2, 120000.00, 'ZaloPay', 'Thành công', '2025-11-12 07:41:09'),
(7, 9, 'subscription', 4, 199000.00, '', 'Thành công', '2025-11-19 01:09:14'),
(8, 12, 'subscription', 2, 79000.00, '', 'Thành công', '2025-11-25 02:26:14'),
(9, 12, 'subscription', 3, 129000.00, '', 'Thành công', '2025-11-25 02:27:04'),
(10, 9, 'ticket', 1, 400000.00, '', 'Thành công', '2025-11-28 02:09:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
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
  `role` enum('user','admin','moderator','manager') DEFAULT 'user',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `avatar`, `birthdate`, `rank`, `points`, `subscription_id`, `status`, `email_verified`, `created_at`, `updated_at`, `role`, `is_active`, `last_login`) VALUES
(1, 'Tuan Anh', 'noble.toad.nict@letterguard.net', '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-11-10 15:10:16', '2025-11-10 15:10:16', 'user', 1, NULL),
(2, 'Super Admin', 'admin@cinehub.com', '$2y$10$Q516uBkFiAAoP9sABaJJRebPWUFZjqKI9370ZLqFxlhtFE1L1r9ba', NULL, NULL, 'Bronze', 0, NULL, 'active', 0, '2025-11-10 16:41:17', '2025-11-10 16:45:54', 'admin', 1, NULL),
(3, 'Admin Mới', 'admin2@cinehub.com', '$2y$10$DcmIe4LT6ByLRbWkKLRrE.r4fPNWpOtQylE4ISfTbP6TeCs/J5T2a', NULL, NULL, 'Bronze', 0, NULL, 'active', 0, '2025-11-12 02:39:06', '2025-11-12 02:39:39', 'admin', 1, NULL),
(4, 'Nguyễn Văn A', 'nguyenvana@example.com', '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Silver', 500, 2, 'active', 0, '2025-11-12 07:41:09', '2025-11-12 07:41:09', 'user', 1, NULL),
(5, 'Trần Thị B', 'tranthib@example.com', '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Gold', 1200, 3, 'active', 0, '2025-11-12 07:41:09', '2025-11-12 07:41:09', 'user', 1, NULL),
(6, 'Lê Văn C', 'levanc@example.com', '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Bronze', 100, 1, 'active', 0, '2025-11-12 07:41:09', '2025-11-12 07:41:09', 'user', 1, NULL),
(7, 'Phạm Thị D', 'phamthid@example.com', '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Platinum', 2500, 4, 'active', 0, '2025-11-12 07:41:09', '2025-11-12 07:41:09', 'user', 1, NULL),
(8, 'Hoàng Văn E', 'hoangvane@example.com', '$2y$10$lOJtx0GSp2xgBlX1cKw1LuTf90z0qfuXcrVlz6fiGQn1QM3kwl.fW', NULL, NULL, 'Silver', 800, 2, 'active', 0, '2025-11-12 07:41:09', '2025-11-12 07:41:09', 'user', 1, NULL),
(9, 'vanlinh', 'nguyenvanlinh25062006@gmail.com', '$2y$10$RfY4oVxCmmN5s57rhg2WzuD1eWIFh5MZUNhN.Sa3erAbC5Vt01mwC', NULL, NULL, 'Bronze', 101000, 4, 'active', 0, '2025-11-14 01:35:37', '2025-11-19 01:09:14', 'user', 1, NULL),
(10, 'Tuan_awh', 'tuanawh@gmail.com', '$2y$10$5NwNHefnp5jwjr1Vls5HG.dnt4SWC1newqSkuV8X4QTcwZ0Ok1JQ.', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-11-14 01:45:51', '2025-11-14 01:45:51', 'user', 1, NULL),
(11, 'Hoang Son', 'hsson97805@gmail.com', '$2y$10$4OBk1HA71jEhbVPP7FA7VueQ8B30EgEy9eB9tAHRFmUvA8I7lwAPe', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-11-24 08:52:25', '2025-11-24 08:52:25', 'user', 1, NULL),
(12, 'jack', 'jack@gmail.com', '$2y$10$4OPMx0NC7sXIg23/hWQt1u0t52jEDgc5grk/LZAOmmFw8a3DAy.BW', NULL, NULL, 'Bronze', 297000, 3, 'active', 0, '2025-11-25 02:20:46', '2025-11-25 02:27:04', 'user', 1, NULL),
(13, 'huung', 'nguyenconghung954@gmail.com', '$2y$10$0aCzLlyOsSw4IZeDM8Vr8uC.1zWUY/F0SZTjwU8hrS9jxzvUvTgeG', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-11-25 12:43:00', '2025-11-25 12:43:00', 'user', 1, NULL),
(14, 'bom', 'vlinh25062006@gmail.com', '$2y$10$SGQNRO1gcjuJy76tKCWx7e/9boVMyK2kkgK5D4PMepeswkveVa2qa', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-11-26 04:03:42', '2025-11-26 04:03:42', 'user', 1, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `created_at`) VALUES
(4, 2, 1, '2025-11-10 16:45:54'),
(7, 3, 1, '2025-11-12 02:39:39'),
(8, 4, 3, '2025-11-12 07:41:09'),
(9, 5, 4, '2025-11-12 07:41:09'),
(10, 1, 5, '2025-11-12 07:41:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_tokens`
--

CREATE TABLE `user_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `device_info` varchar(500) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_tokens`
--

INSERT INTO `user_tokens` (`id`, `user_id`, `token`, `device_info`, `ip_address`, `expires_at`, `created_at`) VALUES
(0, 14, '7b32aadef21999c3720213862d37bc018ae475e3e822f3e22b4dab43af082cd6', 'Google Chrome on Windows', '::1', '2025-12-25 22:03:42', '2025-11-26 04:03:42');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `watch_history`
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
-- Đang đổ dữ liệu cho bảng `watch_history`
--

INSERT INTO `watch_history` (`id`, `user_id`, `movie_id`, `last_time`, `rating`, `favorite`, `created_at`) VALUES
(1, 1, 1, 3600, 5, 1, '2025-11-12 07:41:09'),
(2, 2, 2, 7200, 5, 1, '2025-11-12 07:41:09'),
(3, 3, 3, 1800, 4, 0, '2025-11-12 07:41:09'),
(4, 4, 4, 2400, 4, 0, '2025-11-12 07:41:09'),
(5, 5, 5, 3000, 5, 1, '2025-11-12 07:41:09'),
(6, 1, 6, 5400, 5, 1, '2025-11-12 07:41:09'),
(7, 2, 7, 2100, 4, 0, '2025-11-12 07:41:09'),
(8, 9, 2, 0, NULL, 0, '2025-11-14 01:37:54'),
(9, 9, 5, 0, NULL, 0, '2025-11-17 08:57:34'),
(10, 9, 10, 0, NULL, 0, '2025-11-19 01:13:47'),
(11, 9, 12, 0, NULL, 1, '2025-11-19 01:16:38'),
(12, 9, 1, 0, NULL, 1, '2025-11-19 01:17:46'),
(14, 9, 14, 0, NULL, 0, '2025-11-19 02:24:15'),
(15, 9, 8, 0, NULL, 0, '2025-11-25 00:57:55'),
(27, 3, 8, 0, NULL, 0, '2025-11-19 01:47:02'),
(61, 9, 29, 0, NULL, 0, '2025-11-25 01:32:09'),
(70, 11, 37, 0, NULL, 0, '2025-11-25 12:31:14'),
(75, 12, 14, 0, NULL, 0, '2025-11-25 02:53:22'),
(76, 12, 29, 0, NULL, 0, '2025-11-25 02:53:34'),
(79, 13, 37, 0, NULL, 0, '2025-11-25 12:43:08'),
(80, 3, 30, 0, NULL, 0, '2025-11-25 13:17:35'),
(81, 3, 35, 0, NULL, 0, '2025-11-25 13:30:54'),
(83, 3, 37, 0, NULL, 0, '2025-11-25 14:13:19'),
(84, 3, 33, 0, NULL, 0, '2025-11-26 01:51:37'),
(85, 3, 34, 0, NULL, 0, '2025-11-25 14:20:48'),
(87, 3, 36, 0, NULL, 0, '2025-11-25 14:33:55'),
(89, 3, 11, 0, NULL, 0, '2025-11-25 14:56:06'),
(92, 9, 28, 0, NULL, 0, '2025-11-26 02:47:13'),
(93, 9, 6, 0, NULL, 0, '2025-11-28 02:09:56');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_module` (`module`);

--
-- Chỉ mục cho bảng `booking_food_items`
--
ALTER TABLE `booking_food_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ticket_id` (`ticket_id`),
  ADD KEY `food_item_id` (`food_item_id`);

--
-- Chỉ mục cho bảng `booking_pending`
--
ALTER TABLE `booking_pending`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vnp_txn_ref` (`vnp_txn_ref`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_showtime` (`showtime_id`),
  ADD KEY `idx_txn_ref` (`vnp_txn_ref`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `booking_session_tracking`
--
ALTER TABLE `booking_session_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `showtime_id` (`showtime_id`),
  ADD KEY `screen_id` (`screen_id`),
  ADD KEY `session_start` (`session_start`),
  ADD KEY `is_banned` (`is_banned`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Chỉ mục cho bảng `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `idx_movie_id` (`movie_id`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_status` (`status`);

--
-- Chỉ mục cho bảng `episodes`
--
ALTER TABLE `episodes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_movie_episode` (`movie_id`,`episode_number`),
  ADD KEY `idx_movie_id` (`movie_id`);

--
-- Chỉ mục cho bảng `food_items`
--
ALTER TABLE `food_items`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `ip_blocks`
--
ALTER TABLE `ip_blocks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_ip` (`ip_address`),
  ADD KEY `idx_expires_at` (`expires_at`),
  ADD KEY `idx_ip_expires` (`ip_address`,`expires_at`);

--
-- Chỉ mục cho bảng `ip_spam_logs`
--
ALTER TABLE `ip_spam_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ip_address` (`ip_address`),
  ADD KEY `idx_action_type` (`action_type`),
  ADD KEY `idx_is_spam` (`is_spam`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_ip_action_spam` (`ip_address`,`action_type`,`is_spam`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Chỉ mục cho bảng `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category_id`);

--
-- Chỉ mục cho bảng `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Chỉ mục cho bảng `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rev_user` (`user_id`),
  ADD KEY `idx_rev_movie` (`movie_id`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Chỉ mục cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_permission` (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Chỉ mục cho bảng `seat_reservations`
--
ALTER TABLE `seat_reservations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_seat_reservation` (`showtime_id`,`seat`),
  ADD KEY `idx_showtime_seat` (`showtime_id`,`seat`),
  ADD KEY `idx_expires_at` (`expires_at`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_active_reservations` (`showtime_id`,`expires_at`);

--
-- Chỉ mục cho bảng `seat_selection_logs`
--
ALTER TABLE `seat_selection_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_ip_address_seat_logs` (`ip_address`),
  ADD KEY `showtime_id` (`showtime_id`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `is_spam` (`is_spam`);

--
-- Chỉ mục cho bảng `showtimes`
--
ALTER TABLE `showtimes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_movie` (`movie_id`),
  ADD KEY `idx_theater` (`theater_id`),
  ADD KEY `screen_id` (`screen_id`);

--
-- Chỉ mục cho bảng `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Chỉ mục cho bảng `system_config`
--
ALTER TABLE `system_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_key` (`config_key`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Chỉ mục cho bảng `theaters`
--
ALTER TABLE `theaters`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `theater_managers`
--
ALTER TABLE `theater_managers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_theater` (`user_id`,`theater_id`),
  ADD KEY `theater_id` (`theater_id`);

--
-- Chỉ mục cho bảng `theater_screens`
--
ALTER TABLE `theater_screens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `theater_id` (`theater_id`);

--
-- Chỉ mục cho bảng `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_showtime` (`showtime_id`),
  ADD KEY `idx_showtime_status` (`showtime_id`,`status`),
  ADD KEY `idx_user_showtime` (`user_id`,`showtime_id`),
  ADD KEY `idx_showtime_seat_status` (`showtime_id`,`seat`,`status`);

--
-- Chỉ mục cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tx_user` (`user_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_subscription` (`subscription_id`);

--
-- Chỉ mục cho bảng `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_role` (`user_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- Chỉ mục cho bảng `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `expires_at` (`expires_at`);

--
-- Chỉ mục cho bảng `watch_history`
--
ALTER TABLE `watch_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_movie` (`user_id`,`movie_id`),
  ADD KEY `idx_wh_user` (`user_id`),
  ADD KEY `idx_wh_movie` (`movie_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `booking_food_items`
--
ALTER TABLE `booking_food_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `booking_pending`
--
ALTER TABLE `booking_pending`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `booking_session_tracking`
--
ALTER TABLE `booking_session_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `episodes`
--
ALTER TABLE `episodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `food_items`
--
ALTER TABLE `food_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `ip_blocks`
--
ALTER TABLE `ip_blocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `ip_spam_logs`
--
ALTER TABLE `ip_spam_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT cho bảng `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT cho bảng `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `seat_reservations`
--
ALTER TABLE `seat_reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;

--
-- AUTO_INCREMENT cho bảng `seat_selection_logs`
--
ALTER TABLE `seat_selection_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT cho bảng `showtimes`
--
ALTER TABLE `showtimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=358;

--
-- AUTO_INCREMENT cho bảng `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `system_config`
--
ALTER TABLE `system_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `theaters`
--
ALTER TABLE `theaters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `theater_managers`
--
ALTER TABLE `theater_managers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `theater_screens`
--
ALTER TABLE `theater_screens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT cho bảng `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `watch_history`
--
ALTER TABLE `watch_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `booking_food_items`
--
ALTER TABLE `booking_food_items`
  ADD CONSTRAINT `fk_booking_food_item` FOREIGN KEY (`food_item_id`) REFERENCES `food_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_booking_food_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `booking_pending`
--
ALTER TABLE `booking_pending`
  ADD CONSTRAINT `booking_pending_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_pending_ibfk_2` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `booking_session_tracking`
--
ALTER TABLE `booking_session_tracking`
  ADD CONSTRAINT `booking_session_tracking_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_session_tracking_ibfk_2` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_session_tracking_ibfk_3` FOREIGN KEY (`screen_id`) REFERENCES `theater_screens` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `episodes`
--
ALTER TABLE `episodes`
  ADD CONSTRAINT `fk_episodes_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `movies`
--
ALTER TABLE `movies`
  ADD CONSTRAINT `movies_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `seat_reservations`
--
ALTER TABLE `seat_reservations`
  ADD CONSTRAINT `fk_seat_reservations_showtime` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_seat_reservations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `seat_selection_logs`
--
ALTER TABLE `seat_selection_logs`
  ADD CONSTRAINT `seat_selection_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seat_selection_logs_ibfk_2` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `showtimes`
--
ALTER TABLE `showtimes`
  ADD CONSTRAINT `showtimes_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `showtimes_ibfk_2` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `showtimes_ibfk_3` FOREIGN KEY (`screen_id`) REFERENCES `theater_screens` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `showtimes_ibfk_4` FOREIGN KEY (`screen_id`) REFERENCES `theater_screens` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `showtimes_ibfk_5` FOREIGN KEY (`screen_id`) REFERENCES `theater_screens` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `showtimes_ibfk_6` FOREIGN KEY (`screen_id`) REFERENCES `theater_screens` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD CONSTRAINT `support_tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `support_tickets_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `system_config`
--
ALTER TABLE `system_config`
  ADD CONSTRAINT `system_config_ibfk_1` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `theater_managers`
--
ALTER TABLE `theater_managers`
  ADD CONSTRAINT `theater_managers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `theater_managers_ibfk_2` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `theater_screens`
--
ALTER TABLE `theater_screens`
  ADD CONSTRAINT `theater_screens_ibfk_1` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `watch_history`
--
ALTER TABLE `watch_history`
  ADD CONSTRAINT `watch_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `watch_history_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
