-- Tạo bảng episodes để lưu các tập phim bộ
CREATE TABLE IF NOT EXISTS `episodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movie_id` int(11) NOT NULL,
  `episode_number` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_movie_episode` (`movie_id`, `episode_number`),
  KEY `idx_movie_id` (`movie_id`),
  CONSTRAINT `fk_episodes_movie` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

