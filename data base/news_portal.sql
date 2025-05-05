-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2025 at 05:01 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `news_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `description`) VALUES
(1, 'سياسة', 'أخبار سياسية محلية وعالمية'),
(2, 'اقتصاد', 'أخبار اقتصادية ومالية'),
(3, 'رياضة', 'أخبار رياضية'),
(4, 'صحة', 'أخبار صحية وطبية');

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `dateposted` datetime NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `author_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','denied') NOT NULL DEFAULT 'pending',
  `keywords` varchar(255) DEFAULT NULL,
  `views` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `title`, `body`, `image`, `dateposted`, `category_id`, `author_id`, `status`, `keywords`, `views`) VALUES
(1, 'خبر سياسي', 'هذا خبر سياسي للاختبار', 'https://picsum.photos/300/200', '2025-05-04 10:00:00', 1, 3, 'approved', 'سياسة, اختبار', 0),
(2, 'خبر سياسي', 'تفاصيل الخبر السياسي', 'https://picsum.photos/300/200', '2025-05-04 10:00:00', 1, 3, 'approved', 'سياسة', 100),
(3, 'خبر رياضي', 'تفاصيل الخبر الرياضي', 'https://picsum.photos/300/200', '2025-05-04 11:00:00', 3, 3, 'approved', 'رياضة', 50),
(4, 'خبر اقتصادي', 'تفاصيل الخبر الاقتصادي', 'https://picsum.photos/300/200', '2025-05-04 12:00:00', 2, 3, 'approved', 'اقتصاد', 75),
(5, 'خبر صحي', 'تفاصيل الخبر الصحي', 'https://picsum.photos/300/200', '2025-05-04 13:00:00', 4, 3, 'approved', 'صحة', 25),
(6, 'خبر إضافي 1', 'تفاصيل إضافية', 'https://picsum.photos/300/200', '2025-05-04 14:00:00', 1, 3, 'approved', 'سياسة', 80),
(7, 'خبر إضافي 2', 'تفاصيل إضافية', 'https://picsum.photos/300/200', '2025-05-04 15:00:00', 2, 3, 'approved', 'اقتصاد', 60),
(8, 'خبر إضافي 3', 'تفاصيل إضافية', 'https://picsum.photos/300/200', '2025-05-04 16:00:00', 3, 3, 'approved', 'رياضة', 40),
(9, 'خبر إضافي 4', 'تفاصيل إضافية', 'https://picsum.photos/300/200', '2025-05-04 17:00:00', 4, 3, 'approved', 'صحة', 20),
(10, 'خبر إضافي 5', 'تفاصيل إضافية', 'https://picsum.photos/300/200', '2025-05-04 18:00:00', 1, 3, 'approved', 'سياسة', 90),
(11, 'خبر إضافي 6', 'تفاصيل إضافية', 'https://picsum.photos/300/200', '2025-05-04 19:00:00', 2, 3, 'approved', 'اقتصاد', 70);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('author','editor','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'أدمن', 'admin@news.com', 'admin123', 'admin'),
(2, 'محرر', 'editor@news.com', 'editor123', 'editor'),
(3, 'مؤلف', 'author@news.com', 'author123', 'author');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `news_ibfk_2` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
