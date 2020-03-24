-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2019 at 05:02 AM
-- Server version: 10.1.38-MariaDB
-- PHP Version: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `datae`
--

-- --------------------------------------------------------

--
-- Table structure for table `main_user_customer_places`
--

CREATE TABLE `main_user_customer_places` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'main_user',
  `team_id` int(11) NOT NULL COMMENT 'main_team',
  `customer_id` int(11) NOT NULL COMMENT 'main_customer_template',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `place_id` int(11) DEFAULT NULL COMMENT 'pos_places'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `main_user_customer_places`
--

INSERT INTO `main_user_customer_places` (`id`, `user_id`, `team_id`, `customer_id`, `created_at`, `updated_at`, `place_id`) VALUES
(1, 1, 2, 153, '2019-11-12 09:02:01', '2019-11-13 06:18:39', 653),
(2, 14, 3, 153, '2019-11-12 09:02:37', '2019-11-13 08:25:50', 654),
(3, 14, 3, 153, '2019-11-12 09:03:27', '2019-11-13 08:25:50', 654),
(4, 1, 2, 0, '2019-11-13 08:46:11', '2019-11-13 08:46:11', NULL),
(5, 1, 2, 153, '2019-11-13 08:50:25', '2019-11-13 08:50:25', NULL),
(6, 14, 3, 150, '2019-11-14 02:17:15', '2019-11-14 02:17:15', NULL),
(7, 14, 3, 151, '2019-11-14 02:17:22', '2019-11-14 02:17:22', NULL),
(8, 1, 2, 150, '2019-11-14 02:24:32', '2019-11-14 02:24:32', NULL),
(9, 1, 2, 120, '2019-11-14 02:26:53', '2019-11-14 02:26:53', NULL),
(10, 14, 3, 107, '2019-11-14 02:27:08', '2019-11-14 02:27:08', NULL),
(11, 1, 2, 108, '2019-11-14 02:33:02', '2019-11-14 02:33:02', NULL),
(12, 1, 2, 107, '2019-11-14 02:52:06', '2019-11-14 02:52:06', NULL),
(13, 1, 2, 109, '2019-11-14 02:54:09', '2019-11-14 02:54:09', NULL),
(14, 14, 3, 120, '2019-11-14 02:55:20', '2019-11-14 02:55:20', NULL),
(15, 14, 3, 108, '2019-11-14 02:59:44', '2019-11-14 02:59:44', NULL),
(16, 1, 2, 110, '2019-11-14 03:04:30', '2019-11-14 03:04:30', NULL),
(17, 14, 3, 110, '2019-11-14 03:07:50', '2019-11-14 03:07:50', NULL),
(18, 1, 2, 1, '2019-11-14 04:38:34', '2019-11-14 04:38:34', 655),
(19, 1, 2, 1, '2019-11-14 04:48:06', '2019-11-14 04:48:06', 656),
(20, 14, 3, 109, '2019-11-14 04:52:38', '2019-11-14 04:52:38', NULL),
(21, 14, 3, 3, '2019-11-14 04:55:35', '2019-11-14 04:55:35', 657),
(22, 14, 3, 111, '2019-11-14 04:57:21', '2019-11-14 04:57:21', NULL),
(23, 1, 2, 111, '2019-11-14 04:57:53', '2019-11-14 06:24:00', 658),
(24, 1, 2, 112, '2019-11-14 06:30:20', '2019-11-14 06:30:20', NULL),
(25, 1, 2, 113, '2019-11-14 06:30:44', '2019-11-14 06:30:44', NULL),
(26, 14, 3, 113, '2019-11-14 06:31:05', '2019-11-14 06:33:01', 659),
(27, 1, 2, 1, '2019-11-14 08:59:53', '2019-11-14 08:59:53', 660),
(28, 1, 2, 114, '2019-11-15 02:35:38', '2019-11-15 02:35:38', NULL),
(29, 14, 3, 114, '2019-11-15 02:36:34', '2019-11-15 02:36:34', NULL),
(30, 14, 3, 115, '2019-11-15 02:36:53', '2019-11-15 02:36:53', NULL),
(31, 1, 2, 119, '2019-11-15 04:28:42', '2019-11-15 04:28:42', NULL),
(32, 14, 3, 119, '2019-11-15 04:31:38', '2019-11-15 04:31:38', NULL),
(33, 1, 2, 122, '2019-11-15 04:36:12', '2019-11-15 04:36:12', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `main_user_customer_places`
--
ALTER TABLE `main_user_customer_places`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `main_user_customer_places`
--
ALTER TABLE `main_user_customer_places`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
