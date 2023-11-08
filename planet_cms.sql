-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2023-11-07 20:20:47
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
-- 数据库： `planet_cms`
--

-- --------------------------------------------------------

--
-- 表的结构 `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`) VALUES
(1, 'Planet', 'This category covers all content related to planets in our solar system and beyond.'),
(2, 'Galaxies', 'This category includes content about galaxies, including the Milky Way and galaxies far beyond.'),
(3, 'Astronomy News', 'The latest news and discoveries in the field of astronomy.'),
(4, 'Star', 'This category covers all content related to stars in our solar system and beyond.');

-- --------------------------------------------------------

--
-- 表的结构 `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `author_name` varchar(100) DEFAULT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `galaxies`
--

CREATE TABLE `galaxies` (
  `galaxy_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `distance_ly` float NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `discovered_date` date DEFAULT NULL,
  `discovered_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `galaxies`
--

INSERT INTO `galaxies` (`galaxy_id`, `name`, `description`, `distance_ly`, `image_url`, `discovered_date`, `discovered_by`) VALUES
(1, 'Solar System', 'The Solar System is the gravitationally bound system of the Sun and the objects that orbit it.', 1, '1', '2023-11-15', '1');

-- --------------------------------------------------------

--
-- 表的结构 `news`
--

CREATE TABLE `news` (
  `news_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `published_date` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('published','draft','archived') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `planets`
--

CREATE TABLE `planets` (
  `planet_id` int(11) NOT NULL,
  `galaxy_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('terrestrial','gas giant','ice giant','dwarf','exoplanet') NOT NULL,
  `habitable` tinyint(1) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `stars`
--

CREATE TABLE `stars` (
  `star_id` int(11) NOT NULL,
  `galaxy_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('main sequence','giant','white dwarf','neutron','supernova') NOT NULL,
  `magnitude` float DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 转存表中的数据 `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `created_at`, `last_login`) VALUES
(1, '18188499883', '$2y$10$wneJVeiyPIRwZkKSRDoO1uUfRiR3CWB5N2ZV1aFKcfJZJ/NGrvfSK', 'admin', '2023-11-06 20:13:11', NULL),
(2, 'angelina8808', '$2y$10$TqKPB8lyY1gcOWOY999gkuvMg47M/2bQyRK5wfqWqRwaIacMuOD92', 'user', '2023-11-07 16:03:55', NULL);

--
-- 转储表的索引
--

--
-- 表的索引 `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- 表的索引 `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `page_id` (`user_id`);

--
-- 表的索引 `galaxies`
--
ALTER TABLE `galaxies`
  ADD PRIMARY KEY (`galaxy_id`);

--
-- 表的索引 `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`news_id`),
  ADD KEY `author_id` (`user_id`);

--
-- 表的索引 `planets`
--
ALTER TABLE `planets`
  ADD PRIMARY KEY (`planet_id`),
  ADD KEY `galaxy_id` (`galaxy_id`);

--
-- 表的索引 `stars`
--
ALTER TABLE `stars`
  ADD PRIMARY KEY (`star_id`),
  ADD KEY `galaxy_id` (`galaxy_id`);

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `galaxies`
--
ALTER TABLE `galaxies`
  MODIFY `galaxy_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `news`
--
ALTER TABLE `news`
  MODIFY `news_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- 使用表AUTO_INCREMENT `planets`
--
ALTER TABLE `planets`
  MODIFY `planet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- 使用表AUTO_INCREMENT `stars`
--
ALTER TABLE `stars`
  MODIFY `star_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 限制导出的表
--

--
-- 限制表 `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) ;

--
-- 限制表 `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- 限制表 `planets`
--
ALTER TABLE `planets`
  ADD CONSTRAINT `planets_ibfk_1` FOREIGN KEY (`galaxy_id`) REFERENCES `galaxies` (`galaxy_id`);

--
-- 限制表 `stars`
--
ALTER TABLE `stars`
  ADD CONSTRAINT `stars_ibfk_1` FOREIGN KEY (`galaxy_id`) REFERENCES `galaxies` (`galaxy_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
