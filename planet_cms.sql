-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2023-11-09 02:37:02
-- 服务器版本： 10.4.28-MariaDB
-- PHP 版本： 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `SolarSystemCMS`
--

-- --------------------------------------------------------

--
-- 表的结构 `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` enum('Terrestrial Planets','Gas Giants','Ice Giants','Dwarf Planets','Exoplanets') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Terrestrial Planets'),
(2, 'Gas Giants'),
(3, 'Ice Giants'),
(4, 'Dwarf Planets');

-- --------------------------------------------------------

--
-- 表的结构 `celestial_bodies`
--

CREATE TABLE `celestial_bodies` (
  `celestial_body_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `celestial_bodies`
--

INSERT INTO `celestial_bodies` (`celestial_body_id`, `name`, `category_id`, `description`, `image_url`) VALUES
(8, 'Mercury', 1, 'The smallest planet in the Solar System and the closest to the Sun. It has a cratered surface and no atmosphere to support life as we know it.', 'uploads/Mercury.jpg'),
(9, '1', 1, '1', NULL),
(10, 'Mercury', 1, 'The smallest planet in the Solar System and the closest to the Sun. It has a cratered surface and no atmosphere to support life as we know it.', 'uploads/Mercury.jpg'),
(11, '1', 1, '1', NULL),
(12, '1', 1, '1', 'uploads/Uranus.png'),
(13, '2', 1, '2', 'uploads/space-background.jpg'),
(14, 'test', 1, '1', 'uploads/Venus.jpg'),
(15, '122', 1, '1', 'uploads/saturn.png');

-- --------------------------------------------------------

--
-- 表的结构 `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `celestial_body_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `creation_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `page_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `images`
--

INSERT INTO `images` (`id`, `page_id`, `file_name`) VALUES
(5, NULL, 'Mercury.jpg'),
(6, NULL, 'Mercury.jpg'),
(7, NULL, 'Uranus.png'),
(8, NULL, 'space-background.jpg'),
(9, 23, 'Venus.jpg'),
(10, NULL, 'saturn.png');

-- --------------------------------------------------------

--
-- 表的结构 `pages`
--

CREATE TABLE `pages` (
  `page_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `creation_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_modified_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `creator_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `pages`
--

INSERT INTO `pages` (`page_id`, `title`, `content`, `creation_time`, `last_modified_time`, `category_id`, `creator_id`) VALUES
(17, 'Mercury', 'The smallest planet in the Solar System and the closest to the Sun. It has a cratered surface and no atmosphere to support life as we know it.', '2023-11-08 20:17:03', '2023-11-08 20:17:03', 1, 1),
(18, '1', '1', '2023-11-08 20:23:10', '2023-11-08 20:23:10', 1, 1),
(19, 'Mercury', 'The smallest planet in the Solar System and the closest to the Sun. It has a cratered surface and no atmosphere to support life as we know it.', '2023-11-08 20:27:54', '2023-11-08 20:27:54', 1, 1),
(20, '1', '1', '2023-11-08 20:28:05', '2023-11-08 20:28:05', 1, 1),
(21, '1', '1', '2023-11-08 20:29:30', '2023-11-08 20:29:30', 1, 1),
(22, '2', '2', '2023-11-08 20:29:49', '2023-11-08 20:29:49', 1, 1),
(23, 'test', '1', '2023-11-08 20:32:29', '2023-11-08 20:32:29', 1, 1),
(24, '122', '1', '2023-11-08 20:42:50', '2023-11-08 20:42:50', 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','visitor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`) VALUES
(1, '18188499883', '$2y$10$hnOxRg/xTNL4Yt6MAcrVnOkqiVgLp/TyqIs2aeYIxeAwPskuyR3ee', 'admin');

--
-- 转储表的索引
--

--
-- 表的索引 `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- 表的索引 `celestial_bodies`
--
ALTER TABLE `celestial_bodies`
  ADD PRIMARY KEY (`celestial_body_id`),
  ADD KEY `category_id` (`category_id`);

--
-- 表的索引 `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `celestial_body_id` (`celestial_body_id`);

--
-- 表的索引 `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `page_id` (`page_id`);

--
-- 表的索引 `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`page_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `creator_id` (`creator_id`);

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `celestial_bodies`
--
ALTER TABLE `celestial_bodies`
  MODIFY `celestial_body_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- 使用表AUTO_INCREMENT `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `pages`
--
ALTER TABLE `pages`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 限制导出的表
--

--
-- 限制表 `celestial_bodies`
--
ALTER TABLE `celestial_bodies`
  ADD CONSTRAINT `celestial_bodies_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- 限制表 `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`celestial_body_id`) REFERENCES `celestial_bodies` (`celestial_body_id`);

--
-- 限制表 `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`page_id`) REFERENCES `pages` (`page_id`);

--
-- 限制表 `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `fk_creator_id` FOREIGN KEY (`creator_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `pages_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
