-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2019 at 11:58 AM
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
-- Table structure for table `main_task`
--

CREATE TABLE `main_task` (
  `id` int(128) NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `place_id` int(128) DEFAULT NULL COMMENT 'place_id in pos_place',
  `priority` tinyint(1) DEFAULT NULL COMMENT '1-LOW 2-NORMAL 3-HIGHT 4-URGENT 5-IMMEDIATE',
  `status` tinyint(1) DEFAULT NULL COMMENT '1-NEW 2-PROCESSING 3-DONE',
  `date_start` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_end` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `complete_percent` int(3) DEFAULT NULL COMMENT 'percent',
  `assign_to` int(11) DEFAULT NULL COMMENT 'main_user',
  `task_parent_id` int(128) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL COMMENT 'main_combo_service_bought',
  `created_by` int(11) DEFAULT NULL COMMENT 'main_user',
  `updated_by` int(11) DEFAULT NULL COMMENT 'main_user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `service_id` int(11) DEFAULT NULL COMMENT 'main_combo_service',
  `content` text COLLATE utf8_unicode_ci COMMENT 'json format with type form',
  `category` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8_unicode_ci,
  `desription` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `main_task`
--

INSERT INTO `main_task` (`id`, `subject`, `place_id`, `priority`, `status`, `date_start`, `date_end`, `complete_percent`, `assign_to`, `task_parent_id`, `order_id`, `created_by`, `updated_by`, `created_at`, `updated_at`, `service_id`, `content`, `category`, `note`, `desription`) VALUES
(1, 'Service', 639, 2, 1, NULL, NULL, NULL, 2, NULL, 47, 1, 1, '2019-09-23 08:52:02', '2019-09-25 18:38:50', 6, '{\"google_link\":\"facebook.com\",\"worker_name\":null,\"star\":\"12\",\"current_review\":\"21\",\"order_review\":\"1\",\"complete_date\":\"09\\/18\\/2019\",\"order_id\":\"47\"}', '1', 'note hehe', 'this is a note for task'),
(2, 'Nhu y', 639, 3, 3, '09/25/2019', '09/27/2019', 100, 1, NULL, 47, 1, 1, '2019-09-23 08:52:02', '2019-09-26 20:59:30', 2, '{\"product_name\":\"red\",\"style_customer\":\"classiccal\",\"link\":\"thieu.com\",\"website\":\"nhuy.com\",\"order_id\":\"47\",\"main_color\":\"red\"}', '1', '<span style=\"background-color: rgb(255, 0, 0); color: rgb(255, 231, 206);\">Loveu</span>', '<span style=\"background-color: rgb(0, 0, 255); color: rgb(255, 0, 0);\">Blue</span>'),
(3, 'Service', 637, 2, 1, NULL, NULL, NULL, 2, NULL, 49, 1, 1, '2019-09-23 08:57:40', '2019-09-23 08:57:40', 6, NULL, '1', NULL, 'this is a description for test 2'),
(4, 'Nhu y', 637, 2, 1, NULL, NULL, NULL, 2, NULL, 49, 1, 1, '2019-09-23 08:57:40', '2019-09-23 08:57:40', 2, NULL, '1', NULL, 'this is a dicv '),
(5, 'Test Service 1', 639, 2, 1, NULL, NULL, NULL, 2, NULL, 47, 1, 1, '2019-09-23 08:52:02', '2019-09-25 20:03:11', 9, '{\"link\":\"thieu.fa\",\"promotion\":\"tttt\",\"number\":\"4\",\"admin\":\"1\",\"user\":\"thieusumo\",\"password\":\"123456\",\"order_id\":\"47\"}', '1', NULL, NULL),
(6, 'Thieu Service', 639, 2, 1, NULL, NULL, NULL, 2, NULL, 47, 1, 2, '2019-09-23 08:52:02', '2019-09-25 20:15:58', 8, '{\"domain\":\"thieu.com\",\"theme\":\"2\",\"business_name\":\"Thieu Nails\",\"business_phone\":\"0988934262\",\"email\":\"thieuhao2525@gmail.com\",\"address\":\"14 Main Street\",\"order_id\":\"47\"}', '1', 'noting', 'this is a dexcription for 6'),
(7, 'Website', NULL, 2, 1, NULL, NULL, NULL, 2, 1, NULL, 1, 1, '2019-09-26 19:29:02', '2019-09-26 19:29:02', NULL, NULL, '1', NULL, NULL),
(8, 'Website 1', NULL, 2, 1, NULL, NULL, NULL, 2, 2, NULL, 1, 1, '2019-09-26 19:31:58', '2019-09-26 19:31:58', NULL, NULL, '1', NULL, NULL),
(9, 'Nhu y', 639, 2, 1, NULL, NULL, NULL, 1, NULL, 52, 1, 1, '2019-10-01 02:20:34', '2019-10-01 02:20:34', 2, NULL, '1', NULL, NULL),
(10, 'Service', 639, 2, 1, NULL, NULL, NULL, 6, NULL, 52, 1, 1, '2019-10-01 02:20:34', '2019-10-01 02:20:34', 6, NULL, '1', NULL, NULL),
(11, 'Service', 639, 2, 1, NULL, NULL, NULL, 6, NULL, 54, 1, 1, '2019-10-01 03:11:18', '2019-10-01 03:11:18', 6, NULL, '1', NULL, NULL),
(12, 'Nhu y', 639, 2, 1, NULL, NULL, NULL, 1, NULL, 54, 1, 1, '2019-10-01 03:11:18', '2019-10-01 03:11:18', 2, NULL, '1', NULL, NULL),
(13, 'Service', 639, 2, 1, NULL, NULL, NULL, 1, NULL, 55, 1, 1, '2019-10-01 07:51:49', '2019-10-01 07:51:49', 6, NULL, '1', NULL, NULL),
(14, 'Nhu y', 639, 2, 1, NULL, NULL, NULL, 1, NULL, 55, 1, 1, '2019-10-01 07:51:49', '2019-10-01 07:51:49', 2, NULL, '1', NULL, NULL),
(15, 'Website 111', NULL, 2, 1, NULL, NULL, NULL, 2, NULL, NULL, 1, 1, '2019-10-01 01:12:06', '2019-10-01 01:12:06', NULL, NULL, '1', NULL, NULL),
(16, 'Service', 639, 2, 1, NULL, NULL, NULL, 1, NULL, 56, 1, 1, '2019-10-01 08:16:29', '2019-10-01 03:08:04', 6, '{\"google_link\":\"facebook.com\",\"worker_name\":\"thieu\",\"star\":\"3\",\"current_review\":\"3\",\"order_review\":\"3\",\"complete_date\":\"10\\/01\\/2019\",\"order_id\":\"56\"}', '1', NULL, NULL),
(17, 'Nhu y', 639, 2, 1, NULL, NULL, NULL, 1, NULL, 56, 1, 1, '2019-10-01 08:16:29', '2019-10-01 03:02:22', 2, '{\"product_name\":\"bcs\",\"main_color\":\"red\",\"style_customer\":\"c\\u1ed5 \\u0111i\\u1ec3n\",\"link\":\"tthieusumo.fab\",\"website\":\"\\u01b0ewewe\",\"order_id\":\"56\"}', '1', 'tettetete', NULL),
(18, 'testing for send mail task', NULL, 2, 1, NULL, NULL, NULL, 2, 1, NULL, 1, 1, '2019-10-01 01:19:10', '2019-10-01 01:19:10', NULL, NULL, '1', NULL, NULL),
(19, 'testing for send mail task', NULL, 2, 1, NULL, NULL, NULL, 2, 1, NULL, 1, 1, '2019-10-01 01:21:22', '2019-10-01 01:21:22', NULL, NULL, '1', NULL, NULL),
(20, 'new content', NULL, 2, 1, NULL, NULL, NULL, 2, 1, NULL, 1, 1, '2019-10-01 01:37:52', '2019-10-01 01:37:52', NULL, NULL, '1', NULL, NULL),
(21, 'Website 803', NULL, 2, 1, NULL, NULL, NULL, 1, 1, NULL, 1, 1, '2019-10-01 01:44:09', '2019-10-01 01:44:09', NULL, NULL, '1', NULL, NULL),
(22, 'Website6565', NULL, 2, 1, NULL, NULL, NULL, 1, 1, NULL, 1, 1, '2019-10-01 01:50:44', '2019-10-01 01:50:44', NULL, NULL, '1', NULL, '<p><span style=\"color: rgb(0, 255, 0);\">ok man</span></p>');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `main_task`
--
ALTER TABLE `main_task`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `main_task`
--
ALTER TABLE `main_task`
  MODIFY `id` int(128) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
