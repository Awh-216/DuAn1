-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th10 17, 2025 lúc 04:30 PM
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
(6, 2, 'view', 'analytics', NULL, NULL, NULL, NULL, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2025-11-12 07:41:09');

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
(1, 'Avengers: Endgame', 1, 'Premium', 181, 'Phim siêu anh hùng Marvel, kết thúc của Infinity Saga', 'Anthony Russo, Joe Russo', 'Robert Downey Jr., Chris Evans, Mark Ruffalo', 'data/phim/phimle/Avengers_Endgame.mp4', 'https://example.com/avengers-trailer.mp4', 'data/img/Avengers_Endgame.webp', 'Chiếu rạp', 9.2, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'PG-13', 'phimle'),
(2, 'Titanic', 2, 'Gold', 194, 'Câu chuyện tình yêu trên con tàu định mệnh', 'James Cameron', 'Leonardo DiCaprio, Kate Winslet', 'data/phim/phimle/titanic.mp4\r\n', 'https://example.com/titanic-trailer.mp4', 'data/img/titanic.jpg', 'Chiếu rạp', 8.8, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'PG-13', 'phimle'),
(3, 'The Hangover', 3, 'Silver', 100, 'Phim hài về chuyến đi Las Vegas đầy biến cố', 'Todd Phillips', 'Bradley Cooper, Ed Helms, Zach Galifianakis', 'https://example.com/hangover.mp4', 'https://example.com/hangover-trailer.mp4', 'hangover.jpg', 'Chiếu rạp', 7.7, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'R', 'phimle'),
(4, 'The Conjuring', 4, 'Gold', 112, 'Phim kinh dị về các nhà điều tra siêu nhiên', 'James Wan', 'Patrick Wilson, Vera Farmiga', 'https://example.com/conjuring.mp4', 'https://example.com/conjuring-trailer.mp4', 'conjuring.jpg', 'Chiếu rạp', 7.5, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'R', 'phimle'),
(5, 'Toy Story 4', 5, 'Free', 100, 'Cuộc phiêu lưu mới của Woody và Buzz', 'Josh Cooley', 'Tom Hanks, Tim Allen', 'https://example.com/toystory.mp4', 'https://example.com/toystory-trailer.mp4', 'toystory.jpg', 'Chiếu rạp', 8, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'G', 'phimle'),
(6, 'Interstellar', 6, 'Premium', 169, 'Cuộc hành trình không gian để cứu nhân loại', 'Christopher Nolan', 'Matthew McConaughey, Anne Hathaway', 'data/phim/phimle/Interstellar\r\n.mp4', 'https://example.com/interstellar-trailer.mp4', 'data/img/interstellar.jpg', 'Chiếu online', 8.6, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'PG-13', 'phimle'),
(7, 'Indiana Jones', 7, 'Gold', 122, 'Cuộc phiêu lưu tìm kiếm cổ vật', 'Steven Spielberg', 'Harrison Ford', 'https://example.com/indiana.mp4', 'https://example.com/indiana-trailer.mp4', 'indiana.jpg', 'Chiếu online', 8.2, '2025-11-12 07:41:09', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'PG-13', 'phimle'),
(8, 'Game of Thrones', 7, 'Premium', 60, 'Cuộc chiến giành quyền lực giữa các dòng họ ở vùng đất Westeros. Bộ phim kể về cuộc đấu tranh của các gia đình quý tộc để giành lấy Ngai Sắt Sắt và cai trị bảy vương quốc.', 'David Benioff, D.B. Weiss', 'Emilia Clarke, Kit Harington, Peter Dinklage, Lena Headey', 'data/phim/phimbo/game_of_thrones', 'https://example.com/got-trailer.mp4', 'data/img/game_of_thrones.jpg', 'Chiếu online', 9.3, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'TV-MA', 'phimbo'),
(9, 'Breaking Bad', 1, 'Gold', 47, 'Câu chuyện về giáo viên hóa học trung học Walter White, người bắt đầu sản xuất và bán methamphetamine sau khi được chẩn đoán ung thư phổi giai đoạn cuối.', 'Vince Gilligan', 'Bryan Cranston, Aaron Paul, Anna Gunn, Dean Norris', 'data/phim/phimbo/breaking_bad', 'https://example.com/breaking-bad-trailer.mp4', 'data/img/breaking_bad.jpg', 'Chiếu online', 9.5, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'TV-14', 'phimbo'),
(10, 'The Walking Dead', 4, 'Gold', 45, 'Sheriff Deputy Rick Grimes tỉnh dậy sau một chấn thương và phát hiện ra thế giới đã bị tàn phá bởi đại dịch zombie. Anh phải dẫn dắt nhóm người sống sót tìm nơi trú ẩn.', 'Frank Darabont', 'Andrew Lincoln, Norman Reedus, Melissa McBride, Danai Gurira', 'data/phim/phimbo/the_walking_dead', 'https://example.com/walking-dead-trailer.mp4', 'data/img/the_walking_dead.jpg', 'Chiếu online', 8.2, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'TV-MA', 'phimbo'),
(11, 'Stranger Things', 6, 'Premium', 50, 'Khi một cậu bé 12 tuổi biến mất, một thị trấn nhỏ ở Indiana tiết lộ một bí mật liên quan đến thí nghiệm bí mật, siêu năng lực đáng sợ và một cô gái nhỏ lạ thường.', 'The Duffer Brothers', 'Millie Bobby Brown, Finn Wolfhard, Winona Ryder, David Harbour', 'data/phim/phimbo/stranger_things', 'https://example.com/stranger-things-trailer.mp4', 'data/img/stranger_things.jpg', 'Chiếu online', 8.7, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'TV-14', 'phimbo'),
(12, 'House of Cards', 2, 'Gold', 58, 'Một chính trị gia khôn ngoan và không khoan nhượng làm bất cứ điều gì để giành quyền lực ở Washington D.C.', 'Beau Willimon', 'Kevin Spacey, Robin Wright, Kate Mara, Michael Kelly', 'data/phim/phimbo/house_of_cards', 'https://example.com/house-of-cards-trailer.mp4', 'data/img/house_of_cards.jpg', 'Chiếu online', 8.8, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'TV-MA', 'phimbo'),
(13, 'The Crown', 2, 'Premium', 58, 'Dòng thời gian về triều đại của Nữ hoàng Elizabeth II của Vương quốc Anh, từ những năm 1950 đến những năm 2000.', 'Peter Morgan', 'Claire Foy, Olivia Colman, Matt Smith, Tobias Menzies', 'data/phim/phimbo/the_crown', 'https://example.com/the-crown-trailer.mp4', 'data/img/the_crown.jpg', 'Chiếu online', 8.6, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Anh', 'Tiếng Anh', 'TV-MA', 'phimbo'),
(14, 'Sherlock', 1, 'Gold', 90, 'Phiên bản hiện đại của các câu chuyện điều tra nổi tiếng của Sir Arthur Conan Doyle, với Sherlock Holmes và Dr. John Watson giải quyết các vụ án ở London thế kỷ 21.', 'Mark Gatiss, Steven Moffat', 'Benedict Cumberbatch, Martin Freeman, Rupert Graves, Mark Gatiss', 'data/phim/phimbo/sherlock', 'https://example.com/sherlock-trailer.mp4', 'data/img/sherlock.png', 'Chiếu online', 9.1, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Anh', 'Tiếng Anh', 'TV-14', 'phimbo'),
(15, 'The Office', 3, 'Silver', 22, 'Một mockumentary về nhóm nhân viên văn phòng hàng ngày tại văn phòng chi nhánh Scranton của công ty giấy Dunder Mifflin.', 'Greg Daniels', 'Steve Carell, Rainn Wilson, John Krasinski, Jenna Fischer', 'data/phim/phimbo/the_office', 'https://example.com/the-office-trailer.mp4', 'data/img/the_office.png', 'Chiếu online', 8.9, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'TV-14', 'phimbo'),
(16, 'Friends', 3, 'Silver', 22, 'Cuộc sống và tình yêu của sáu người bạn ở Manhattan, New York, khi họ cố gắng tìm ra con đường của mình trong cuộc sống.', 'David Crane, Marta Kauffman', 'Jennifer Aniston, Courteney Cox, Lisa Kudrow, Matt LeBlanc, Matthew Perry, David Schwimmer', 'data/phim/phimbo/friends', 'https://example.com/friends-trailer.mp4', 'data/img/friends.jpg', 'Chiếu online', 9, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Mỹ', 'Tiếng Anh', 'TV-14', 'phimbo'),
(17, 'The Witcher', 7, 'Premium', 60, 'Geralt of Rivia, một thợ săn quái vật đột biến đi khắp đất liền để tìm nơi thuộc về mình trong một thế giới nơi con người thường tồi tệ hơn quái vật.', 'Lauren Schmidt Hissrich', 'Henry Cavill, Anya Chalotra, Freya Allan, Joey Batey', 'data/phim/phimbo/the_witcher', 'https://example.com/the-witcher-trailer.mp4', 'data/img/the_witcher.jpg', 'Chiếu online', 8.2, '2025-11-17 01:17:59', 'published', NULL, NULL, 0, NULL, 'Mỹ/ Ba Lan', 'Tiếng Anh', 'TV-MA', 'phimbo');

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
(7, 2, 7, 4, 'Cuộc phiêu lưu thú vị với Indiana Jones.', '2025-11-12 07:41:09', 0);

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
(5, 'Support Staff', 'Nhân viên hỗ trợ khách hàng', '2025-11-10 16:41:17');

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
(1, 1, 1, '2025-11-15', '10:00:00', 120000.00, '2025-11-12 07:41:09', 1),
(2, 1, 1, '2025-11-15', '13:30:00', 120000.00, '2025-11-12 07:41:09', 1),
(3, 2, 2, '2025-11-15', '15:00:00', 100000.00, '2025-11-12 07:41:09', 3),
(4, 3, 3, '2025-11-16', '18:00:00', 110000.00, '2025-11-12 07:41:09', 5),
(5, 4, 4, '2025-11-16', '20:30:00', 115000.00, '2025-11-12 07:41:09', 7),
(6, 5, 5, '2025-11-17', '09:30:00', 90000.00, '2025-11-12 07:41:09', 8),
(7, 6, 1, '2025-11-17', '14:00:00', 130000.00, '2025-11-12 07:41:09', 2),
(8, 1, 1, '2025-11-13', '10:00:00', 120000.00, '2025-11-13 13:57:24', 1),
(9, 1, 1, '2025-11-13', '13:30:00', 120000.00, '2025-11-13 13:57:24', 1),
(10, 1, 1, '2025-11-13', '16:00:00', 120000.00, '2025-11-13 13:57:24', 1),
(11, 1, 1, '2025-11-13', '19:00:00', 140000.00, '2025-11-13 13:57:24', 1),
(12, 1, 2, '2025-11-13', '11:00:00', 120000.00, '2025-11-13 13:57:24', 2),
(13, 1, 2, '2025-11-13', '14:30:00', 120000.00, '2025-11-13 13:57:24', 2),
(14, 1, 2, '2025-11-13', '17:30:00', 120000.00, '2025-11-13 13:57:24', 2),
(15, 2, 1, '2025-11-13', '09:30:00', 100000.00, '2025-11-13 13:57:24', 3),
(16, 2, 1, '2025-11-13', '12:00:00', 100000.00, '2025-11-13 13:57:24', 3),
(17, 2, 1, '2025-11-13', '15:00:00', 100000.00, '2025-11-13 13:57:24', 3),
(18, 2, 1, '2025-11-13', '18:30:00', 120000.00, '2025-11-13 13:57:24', 3),
(19, 2, 3, '2025-11-13', '10:30:00', 100000.00, '2025-11-13 13:57:24', 4),
(20, 2, 3, '2025-11-13', '13:00:00', 100000.00, '2025-11-13 13:57:24', 4),
(21, 2, 3, '2025-11-13', '16:30:00', 100000.00, '2025-11-13 13:57:24', 4),
(22, 3, 2, '2025-11-13', '11:30:00', 110000.00, '2025-11-13 13:57:24', 5),
(23, 3, 2, '2025-11-13', '14:00:00', 110000.00, '2025-11-13 13:57:24', 5),
(24, 3, 2, '2025-11-13', '17:00:00', 110000.00, '2025-11-13 13:57:24', 5),
(25, 3, 4, '2025-11-13', '12:30:00', 110000.00, '2025-11-13 13:57:24', 6),
(26, 3, 4, '2025-11-13', '15:30:00', 110000.00, '2025-11-13 13:57:24', 6),
(27, 3, 4, '2025-11-13', '19:30:00', 130000.00, '2025-11-13 13:57:24', 6),
(28, 4, 3, '2025-11-13', '10:00:00', 115000.00, '2025-11-13 13:57:24', 7),
(29, 4, 3, '2025-11-13', '13:30:00', 115000.00, '2025-11-13 13:57:24', 7),
(30, 4, 3, '2025-11-13', '17:00:00', 115000.00, '2025-11-13 13:57:24', 7),
(31, 4, 5, '2025-11-13', '11:00:00', 115000.00, '2025-11-13 13:57:24', 8),
(32, 4, 5, '2025-11-13', '14:30:00', 115000.00, '2025-11-13 13:57:24', 8),
(33, 4, 5, '2025-11-13', '18:00:00', 115000.00, '2025-11-13 13:57:24', 8),
(34, 5, 1, '2025-11-13', '09:00:00', 90000.00, '2025-11-13 13:57:24', 4),
(35, 5, 1, '2025-11-13', '12:30:00', 90000.00, '2025-11-13 13:57:24', 4),
(36, 5, 1, '2025-11-13', '16:00:00', 90000.00, '2025-11-13 13:57:24', 4),
(37, 5, 2, '2025-11-13', '10:00:00', 90000.00, '2025-11-13 13:57:24', 3),
(38, 5, 2, '2025-11-13', '13:00:00', 90000.00, '2025-11-13 13:57:24', 3),
(39, 5, 2, '2025-11-13', '16:30:00', 90000.00, '2025-11-13 13:57:24', 3),
(40, 1, 1, '2025-11-14', '10:00:00', 120000.00, '2025-11-13 13:57:24', 1),
(41, 1, 1, '2025-11-14', '13:30:00', 120000.00, '2025-11-13 13:57:24', 1),
(42, 1, 1, '2025-11-14', '16:00:00', 120000.00, '2025-11-13 13:57:24', 1),
(43, 1, 1, '2025-11-14', '19:00:00', 140000.00, '2025-11-13 13:57:24', 1),
(44, 2, 2, '2025-11-14', '11:00:00', 100000.00, '2025-11-13 13:57:24', 2),
(45, 2, 2, '2025-11-14', '14:30:00', 100000.00, '2025-11-13 13:57:24', 2),
(46, 2, 2, '2025-11-14', '17:30:00', 100000.00, '2025-11-13 13:57:24', 2),
(47, 3, 3, '2025-11-14', '10:30:00', 110000.00, '2025-11-13 13:57:24', 3),
(48, 3, 3, '2025-11-14', '13:00:00', 110000.00, '2025-11-13 13:57:24', 3),
(49, 3, 3, '2025-11-14', '16:30:00', 110000.00, '2025-11-13 13:57:24', 3),
(50, 4, 4, '2025-11-14', '12:00:00', 115000.00, '2025-11-13 13:57:24', 4),
(51, 4, 4, '2025-11-14', '15:00:00', 115000.00, '2025-11-13 13:57:24', 4),
(52, 4, 4, '2025-11-14', '18:30:00', 115000.00, '2025-11-13 13:57:24', 4),
(53, 5, 5, '2025-11-14', '09:30:00', 90000.00, '2025-11-13 13:57:24', 5),
(54, 5, 5, '2025-11-14', '12:00:00', 90000.00, '2025-11-13 13:57:24', 5),
(55, 5, 5, '2025-11-14', '15:30:00', 90000.00, '2025-11-13 13:57:24', 5),
(56, 1, 1, '2025-11-15', '10:00:00', 120000.00, '2025-11-13 13:57:24', 1),
(57, 1, 2, '2025-11-15', '13:30:00', 120000.00, '2025-11-13 13:57:24', 2),
(58, 2, 3, '2025-11-15', '15:00:00', 100000.00, '2025-11-13 13:57:24', 3),
(59, 3, 4, '2025-11-15', '18:00:00', 110000.00, '2025-11-13 13:57:24', 4),
(60, 4, 5, '2025-11-15', '20:30:00', 115000.00, '2025-11-13 13:57:24', 5),
(61, 5, 1, '2025-11-16', '09:30:00', 90000.00, '2025-11-13 13:57:24', 1),
(62, 1, 2, '2025-11-16', '14:00:00', 120000.00, '2025-11-13 13:57:24', 2),
(63, 2, 3, '2025-11-17', '16:00:00', 100000.00, '2025-11-13 13:57:24', 3),
(64, 3, 4, '2025-11-17', '19:00:00', 110000.00, '2025-11-13 13:57:24', 4),
(65, 4, 5, '2025-11-18', '11:00:00', 115000.00, '2025-11-13 13:57:24', 5),
(66, 5, 1, '2025-11-18', '13:30:00', 90000.00, '2025-11-13 13:57:24', 1),
(67, 1, 2, '2025-11-19', '15:30:00', 120000.00, '2025-11-13 13:57:24', 2),
(68, 2, 3, '2025-11-19', '17:30:00', 100000.00, '2025-11-13 13:57:24', 3),
(69, 3, 4, '2025-11-20', '18:30:00', 110000.00, '2025-11-13 13:57:24', 4),
(70, 4, 5, '2025-11-20', '20:00:00', 115000.00, '2025-11-13 13:57:24', 5);

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
-- Cấu trúc bảng cho bảng `theater_screens`
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

--
-- Đang đổ dữ liệu cho bảng `theater_screens`
--

INSERT INTO `theater_screens` (`id`, `theater_id`, `screen_name`, `total_seats`, `seat_layout`, `screen_type`, `is_active`, `created_at`) VALUES
(1, 1, 'Phòng 1', 120, NULL, '2D', 1, '2025-11-12 07:41:09'),
(2, 1, 'Phòng 2', 150, NULL, '3D', 1, '2025-11-12 07:41:09'),
(3, 2, 'Phòng 1', 100, NULL, '2D', 1, '2025-11-12 07:41:09'),
(4, 2, 'Phòng 2', 120, NULL, 'IMAX', 1, '2025-11-12 07:41:09'),
(5, 3, 'Phòng 1', 200, NULL, '4DX', 1, '2025-11-12 07:41:09'),
(6, 3, 'Phòng 2', 180, NULL, '3D', 1, '2025-11-12 07:41:09'),
(7, 4, 'Phòng 1', 110, NULL, '2D', 1, '2025-11-12 07:41:09'),
(8, 5, 'Phòng 1', 130, NULL, '3D', 1, '2025-11-12 07:41:09');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tickets`
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

--
-- Đang đổ dữ liệu cho bảng `tickets`
--

INSERT INTO `tickets` (`id`, `user_id`, `showtime_id`, `seat`, `qr_code`, `price`, `status`, `created_at`) VALUES
(1, 1, 1, 'A5', NULL, 120000.00, 'Đã đặt', '2025-11-12 07:41:09'),
(2, 1, 1, 'A6', NULL, 120000.00, 'Đã đặt', '2025-11-12 07:41:09'),
(3, 2, 2, 'B10', NULL, 120000.00, 'Đã đặt', '2025-11-12 07:41:09'),
(4, 3, 3, 'C15', NULL, 100000.00, 'Đã đặt', '2025-11-12 07:41:09'),
(5, 4, 4, 'D20', NULL, 110000.00, 'Đã đặt', '2025-11-12 07:41:09'),
(6, 5, 5, 'E12', NULL, 115000.00, 'Đã đặt', '2025-11-12 07:41:09');

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
(6, 1, 'ticket', 2, 120000.00, 'ZaloPay', 'Thành công', '2025-11-12 07:41:09');

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
  `role` enum('user','admin','moderator') DEFAULT 'user',
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
(9, 'vanlinh', 'nguyenvanlinh25062006@gmail.com', '$2y$10$RfY4oVxCmmN5s57rhg2WzuD1eWIFh5MZUNhN.Sa3erAbC5Vt01mwC', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-11-14 01:35:37', '2025-11-14 01:35:37', 'user', 1, NULL),
(10, 'Tuan_awh', 'tuanawh@gmail.com', '$2y$10$5NwNHefnp5jwjr1Vls5HG.dnt4SWC1newqSkuV8X4QTcwZ0Ok1JQ.', NULL, NULL, 'Bronze', 0, 1, 'active', 0, '2025-11-14 01:45:51', '2025-11-14 01:45:51', 'user', 1, NULL);

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
(9, 9, 5, 0, NULL, 0, '2025-11-17 08:57:34');

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
  ADD KEY `idx_showtime` (`showtime_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT cho bảng `showtimes`
--
ALTER TABLE `showtimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

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
-- AUTO_INCREMENT cho bảng `theater_screens`
--
ALTER TABLE `theater_screens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `watch_history`
--
ALTER TABLE `watch_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
-- Các ràng buộc cho bảng `watch_history`
--
ALTER TABLE `watch_history`
  ADD CONSTRAINT `watch_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `watch_history_ibfk_2` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
