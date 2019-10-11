-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2019 at 09:19 AM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `deg_merchant`
--

-- --------------------------------------------------------

--
-- Table structure for table `main_activity_log`
--

CREATE TABLE `main_activity_log` (
  `id` bigint(15) NOT NULL,
  `user_id` int(10) NOT NULL,
  `type` varchar(110) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `ip_address` varchar(110) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `main_activity_log`
--

INSERT INTO `main_activity_log` (`id`, `user_id`, `type`, `message`, `ip_address`, `created_at`, `updated_at`) VALUES
(1, 1, 'login', 'login successfully', '1988', '2019-10-06 19:25:50', '2019-10-06 19:25:50'),
(2, 1, 'Create', NULL, NULL, '2019-10-06 19:32:18', '2019-10-06 19:32:18'),
(3, 1, NULL, NULL, '127.0.0.1', '2019-10-06 20:05:46', '2019-10-06 20:05:46'),
(4, 1, 'Create', 'Create promotion', '127.0.0.1', '2019-10-06 20:06:34', '2019-10-06 20:06:34'),
(5, 1, 'Create', 'main_login_background', '127.0.0.1', '2019-10-06 20:32:21', '2019-10-06 20:32:21'),
(6, 1, 'Create', 'main_login_background', '127.0.0.1', '2019-10-06 20:33:20', '2019-10-06 20:33:20'),
(7, 1, 'Create', 'main_login_background', '127.0.0.1', '2019-10-06 20:35:06', '2019-10-06 20:35:06'),
(8, 1, 'Create', 'Created main_login_backgroundsuccessfully', '127.0.0.1', '2019-10-06 21:09:50', '2019-10-06 21:09:50'),
(9, 1, 'Update', 'main_login_background', '127.0.0.1', '2019-10-06 21:20:34', '2019-10-06 21:20:34'),
(10, 1, 'Update', 'main_login_background', '127.0.0.1', '2019-10-06 21:21:51', '2019-10-06 21:21:51'),
(11, 1, 'Update', 'main_login_background', '127.0.0.1', '2019-10-06 21:22:28', '2019-10-06 21:22:28'),
(12, 1, 'Create', 'main_login_background', '127.0.0.1', '2019-10-06 21:24:33', '2019-10-06 21:24:33'),
(13, 1, 'Update', 'main_login_background', '127.0.0.1', '2019-10-06 21:24:42', '2019-10-06 21:24:42'),
(14, 1, 'Create', 'main_login_background', '127.0.0.1', '2019-10-06 21:29:38', '2019-10-06 21:29:38'),
(15, 1, 'Update', 'main_login_background', '127.0.0.1', '2019-10-06 21:29:46', '2019-10-06 21:29:46'),
(16, 1, 'Delete', 'main_login_background', '127.0.0.1', '2019-10-06 21:30:55', '2019-10-06 21:30:55'),
(17, 1, 'Delete', 'main_login_background', '127.0.0.1', '2019-10-06 21:33:17', '2019-10-06 21:33:17'),
(18, 1, 'Delete', 'main_login_background', '127.0.0.1', '2019-10-06 21:33:19', '2019-10-06 21:33:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `main_activity_log`
--
ALTER TABLE `main_activity_log`
  ADD PRIMARY KEY (`id`,`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
