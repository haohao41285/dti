-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2019 at 12:08 PM
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
-- Table structure for table `main_tracking_history`
--

CREATE TABLE `main_tracking_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL COMMENT 'main_combo_service',
  `task_id` int(11) DEFAULT NULL COMMENT 'main_task',
  `subtask_id` int(11) DEFAULT NULL COMMENT 'main_task',
  `customer_id` int(128) DEFAULT NULL COMMENT 'main_customer',
  `created_by` int(11) NOT NULL COMMENT 'main_user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text COLLATE utf8_unicode_ci,
  `email_list` text COLLATE utf8_unicode_ci COMMENT 'implode ;'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `main_tracking_history`
--

INSERT INTO `main_tracking_history` (`id`, `order_id`, `task_id`, `subtask_id`, `customer_id`, `created_by`, `created_at`, `content`, `email_list`) VALUES
(1, 31, 1, NULL, NULL, 1, '2019-09-23 07:32:49', 'test comment', NULL),
(2, 31, 3, NULL, NULL, 1, '2019-09-23 06:32:49', 'test comment 2', NULL),
(3, 47, 1, NULL, NULL, 1, '2019-09-24 08:01:11', '<p><span style=\"background-color: rgb(231, 148, 57);\">only test</span></p>', NULL),
(4, 47, 1, NULL, NULL, 1, '2019-09-24 08:03:04', '<p><span style=\"background-color: rgb(255, 156, 0);\">just test</span></p>', NULL),
(5, 47, 1, NULL, NULL, 1, '2019-09-24 08:03:56', '<p><span style=\"background-color: rgb(255, 198, 156);\">just test</span></p>', NULL),
(6, 47, 1, NULL, NULL, 1, '2019-09-24 08:04:51', '<p>rtrtr</p>', NULL),
(7, 47, 2, NULL, NULL, 1, '2019-09-24 08:11:38', '<p><span style=\"font-family: Helvetica;\">﻿</span><span style=\"font-family: &quot;Comic Sans MS&quot;;\">﻿<span style=\"background-color: rgb(0, 255, 0);\">test again</span></span><br></p>', NULL),
(8, 47, 1, NULL, NULL, 1, '2019-09-24 08:40:48', '<p><span style=\"font-family: &quot;Arial Black&quot;;\">﻿<span style=\"background-color: rgb(255, 0, 0);\">test for comment</span></span><br></p>', NULL),
(9, 47, 2, NULL, NULL, 1, '2019-09-24 08:50:10', '<p><span style=\"background-color: rgb(255, 255, 0);\">tetsting</span></p>', NULL),
(10, 47, 2, NULL, NULL, 1, '2019-09-24 08:51:39', '<p><span style=\"background-color: rgb(0, 255, 0);\">testing</span></p>', NULL),
(17, 47, 1, NULL, NULL, 1, '2019-09-24 09:39:54', '<p><span style=\"background-color: rgb(247, 173, 107);\">test</span></p>', NULL),
(18, 47, 1, NULL, NULL, 1, '2019-09-24 09:42:27', '<p>erer</p>', NULL),
(19, 47, 2, NULL, NULL, 1, '2019-09-24 09:48:30', '<span style=\"background-color: rgb(0, 0, 255); color: rgb(255, 239, 198);\">test for testing</span>', NULL),
(20, 49, 3, NULL, NULL, 1, '2019-09-25 01:37:23', '<p><span style=\"font-family: &quot;Courier New&quot;;\">﻿test more image</span><br></p>', NULL),
(25, 49, 3, NULL, NULL, 1, '2019-09-25 01:47:50', '<p><span style=\"font-family: &quot;Courier New&quot;;\">﻿test more image</span><br></p>', NULL),
(26, 49, 3, NULL, NULL, 1, '2019-09-25 01:48:17', '<p><span style=\"font-family: &quot;Courier New&quot;;\">﻿test more image</span><br></p>', NULL),
(30, 49, 4, NULL, NULL, 1, '2019-09-25 01:52:59', '<p><span style=\"font-family: &quot;Arial Black&quot;;\">ok</span><br></p>', NULL),
(31, 47, 1, NULL, NULL, 1, '2019-09-25 02:56:43', '<span style=\"font-family: &quot;Arial Black&quot;; color: rgb(0, 255, 0);\">test</span>', NULL),
(36, 0, 0, NULL, NULL, 1, '2019-09-25 03:04:11', '<p>tetere</p>', NULL),
(37, 47, 1, NULL, NULL, 1, '2019-09-25 03:08:00', '<p><span style=\"color: rgb(0, 255, 0);\">test now</span></p>', NULL),
(38, 47, NULL, NULL, NULL, 1, '2019-09-25 03:17:15', '<p>thieu</p>', NULL),
(39, 47, 1, NULL, NULL, 1, '2019-09-25 03:20:00', '<p><span style=\"color: rgb(0, 255, 255);\">test blue</span></p>', NULL),
(40, 47, NULL, NULL, NULL, 1, '2019-09-25 03:21:26', '<p><span style=\"color: rgb(0, 255, 0);\">test nhuy</span></p>', NULL),
(41, 47, 1, NULL, NULL, 1, '2019-09-25 03:32:25', '<p>test</p>', NULL),
(42, 47, 1, NULL, NULL, 1, '2019-09-25 03:35:32', '<p>test zip</p>', NULL),
(43, 47, 1, NULL, NULL, 1, '2019-09-25 04:11:37', '<p>test</p>', NULL),
(44, 47, 1, NULL, NULL, 1, '2019-09-25 04:26:41', '<p>test zip</p>', NULL),
(45, 47, 1, NULL, NULL, 1, '2019-09-25 09:13:14', 'nothing to note', NULL),
(46, 47, 1, NULL, NULL, 1, '2019-09-26 01:30:48', '33', NULL),
(47, 47, 1, NULL, NULL, 1, '2019-09-26 01:33:29', NULL, NULL),
(48, 47, 1, NULL, NULL, 1, '2019-09-26 01:36:31', NULL, NULL),
(49, 47, 1, NULL, NULL, 1, '2019-09-26 01:38:50', NULL, NULL),
(50, 47, 2, NULL, NULL, 1, '2019-09-26 01:56:31', 'nothing', NULL),
(51, 47, 5, NULL, NULL, 1, '2019-09-26 02:27:21', NULL, NULL),
(52, 47, 5, NULL, NULL, 1, '2019-09-26 02:33:59', NULL, NULL),
(53, 47, 5, NULL, NULL, 1, '2019-09-26 02:51:37', NULL, NULL),
(54, 47, 5, NULL, NULL, 1, '2019-09-26 02:52:03', NULL, NULL),
(55, 47, 5, NULL, NULL, 1, '2019-09-26 02:53:51', NULL, NULL),
(56, 47, 5, NULL, NULL, 1, '2019-09-26 02:54:33', NULL, NULL),
(57, 47, 5, NULL, NULL, 1, '2019-09-26 02:54:48', NULL, NULL),
(58, 47, 5, NULL, NULL, 1, '2019-09-26 02:56:50', NULL, NULL),
(59, 47, 5, NULL, NULL, 1, '2019-09-26 03:02:09', NULL, NULL),
(60, 47, 5, NULL, NULL, 1, '2019-09-26 03:03:11', NULL, NULL),
(61, 47, 6, NULL, NULL, 1, '2019-09-26 03:15:58', NULL, NULL),
(62, 49, 3, NULL, NULL, 1, '2019-09-26 08:11:41', '<p><span style=\"font-family: undefined;\">﻿</span><span style=\"font-family: &quot;Comic Sans MS&quot;;\">﻿<span style=\"color: rgb(0, 255, 0);\">test for new comment</span></span><br></p>', NULL),
(63, 49, 3, NULL, NULL, 1, '2019-09-26 08:15:30', '<p><span style=\"font-family: Helvetica;\">﻿<span style=\"color: rgb(107, 165, 74);\">test new</span></span><br></p>', NULL),
(64, 49, 3, NULL, NULL, 1, '2019-09-26 08:17:16', '<p>test test</p>', NULL),
(65, 47, 2, NULL, NULL, 1, '2019-09-27 03:54:51', '<span style=\"background-color: rgb(255, 0, 0); color: rgb(255, 231, 206);\">Loveu</span>', NULL),
(66, 47, 2, NULL, NULL, 1, '2019-09-27 03:58:24', '<span style=\"background-color: rgb(255, 0, 0); color: rgb(255, 231, 206);\">Loveu</span>', NULL),
(67, 47, 2, NULL, NULL, 1, '2019-09-27 03:59:30', '<span style=\"background-color: rgb(255, 0, 0); color: rgb(255, 231, 206);\">Loveu</span>', NULL),
(68, 54, 11, NULL, NULL, 1, '2019-10-01 04:53:36', '<span style=\"font-family: &quot;Arial Black&quot;;\">miss you</span>', NULL),
(69, 54, 12, NULL, NULL, 1, '2019-10-01 04:56:58', '<span style=\"font-family: &quot;Arial Black&quot;; color: rgb(255, 255, 0);\">halo</span>', NULL),
(70, 54, 12, NULL, NULL, 1, '2019-10-01 04:59:50', '<span style=\"font-family: &quot;Arial Black&quot;; color: rgb(255, 255, 0);\">halo</span><span style=\"font-family: &quot;Arial Black&quot;; color: rgb(0, 255, 0);\">tttttt</span>', NULL),
(71, 54, 12, NULL, NULL, 1, '2019-10-01 05:01:11', '<span style=\"color: rgb(255, 255, 0); font-family: &quot;Arial Black&quot;;\">ok</span>', NULL),
(74, 54, 12, NULL, NULL, 1, '2019-10-01 05:09:18', '<p>OK BABE</p>', NULL),
(75, 54, 11, NULL, NULL, 1, '2019-10-01 05:10:22', '<p>anh nhớ em</p>', NULL),
(76, 54, 11, NULL, NULL, 1, '2019-10-01 06:58:27', '<p>test multi email</p>', NULL),
(77, 54, 11, NULL, NULL, 1, '2019-10-01 06:59:58', '<p>test multi email</p>', NULL),
(78, 54, 11, NULL, NULL, 1, '2019-10-01 07:00:44', '<p>test multi email</p>', NULL),
(79, 54, 11, NULL, NULL, 1, '2019-10-01 07:03:14', '<p><span style=\"color: rgb(0, 0, 255);\">test multi email</span></p>', 'thieuhao2525@gmail.com'),
(81, 54, 11, NULL, NULL, 1, '2019-10-01 07:19:43', 'test test', 'nguyenthieupro93@gmail.com;thieuhao2525@gmail.com'),
(82, 54, 11, NULL, NULL, 1, '2019-10-01 07:21:42', '<p>test white space</p>', 'nguyenthieupro93@gmail.com;thieuhao2525@gmail.com'),
(83, 54, 11, NULL, NULL, 1, '2019-10-01 07:22:49', '<p>test white space</p>', NULL),
(84, 47, 1, NULL, NULL, 1, '2019-10-01 09:22:25', 'test', 'nguyenthieupro93@gmail.com;thieuhao2525@gmail..com'),
(85, 47, 1, NULL, NULL, 1, '2019-10-01 09:27:14', '<span style=\"color: rgb(255, 255, 0);\">testing</span>', 'nguyenthieupro93@gmail.com;thieuhao2525@gmail.com'),
(86, 47, 1, NULL, NULL, 1, '2019-10-01 09:37:16', '<span style=\"color: rgb(0, 255, 0);\">ok</span>', 'thieuhao2525@gmail.com'),
(87, 56, 17, NULL, NULL, 1, '2019-10-01 10:00:50', NULL, NULL),
(88, 56, 17, NULL, NULL, 1, '2019-10-01 10:01:54', NULL, NULL),
(89, 56, 17, NULL, NULL, 1, '2019-10-01 10:02:22', NULL, NULL),
(90, 56, 16, NULL, NULL, 1, '2019-10-01 10:05:45', NULL, NULL),
(91, NULL, NULL, NULL, 619, 1, '2019-10-01 10:08:04', '887878', NULL),
(92, NULL, NULL, NULL, 619, 1, '2019-10-02 07:48:02', '<p>this for test customer</p>', 'nguyenthieupro93@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `main_tracking_history`
--
ALTER TABLE `main_tracking_history`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `main_tracking_history`
--
ALTER TABLE `main_tracking_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
