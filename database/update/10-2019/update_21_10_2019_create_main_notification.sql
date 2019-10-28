-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2019 at 05:55 AM
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
-- Table structure for table `main_notification`
--

CREATE TABLE `main_notification` (
  `id` int(11) NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `href_to` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `receiver_id` int(11) NOT NULL COMMENT 'main_user',
  `read_not` int(11) NOT NULL COMMENT '0: not, 1: read',
  `created_by` int(11) NOT NULL COMMENT 'main_user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `main_notification`
--

INSERT INTO `main_notification` (`id`, `content`, `href_to`, `receiver_id`, `read_not`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'nhytran created a comment on task #34', 'http://localhost:8000/task/task-detail/34', 1, 0, 14, '2019-10-17 05:04:31', '2019-10-17 09:52:59'),
(2, '1created a task #39', 'http://localhost:8000/task/task-detail/39', 1, 0, 1, '2019-10-17 06:37:56', '2019-10-17 09:52:59'),
(3, '1created a task #39', 'http://localhost:8000/task/task-detail/39', 1, 1, 1, '2019-10-17 06:37:56', '2019-10-17 09:52:59'),
(4, 'Expired Service Notification!', 'http://localhost/orders/add/620', 1, 0, 0, '2019-10-18 03:53:47', '2019-10-18 03:53:47'),
(5, 'Website be expired at 10/21/2019', 'http://localhost/task/task-detail/7', 1, 0, 0, '2019-10-18 09:00:19', '2019-10-18 09:00:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `main_notification`
--
ALTER TABLE `main_notification`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `main_notification`
--
ALTER TABLE `main_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
