-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2019 at 12:15 PM
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
-- Table structure for table `main_combo_service_bought`
--

CREATE TABLE `main_combo_service_bought` (
  `id` int(128) NOT NULL,
  `csb_customer_id` int(128) NOT NULL,
  `csb_place_id` int(128) DEFAULT NULL COMMENT 'pos_place',
  `csb_combo_service_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'implode ; with id column in main_combo_service',
  `csb_amount` float DEFAULT NULL,
  `csb_charge` float DEFAULT NULL,
  `csb_cashback` float NOT NULL,
  `csb_payment_method` int(1) NOT NULL COMMENT '1 cash, 2 credit card, 3 check',
  `csb_card_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'name of card',
  `csb_amount_deal` float DEFAULT NULL COMMENT 'amount money discount',
  `csb_card_number` varchar(8) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'card number of customer 4 suffixes',
  `routing_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'for e-check',
  `account_number` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'for e-check',
  `bank_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'for e-check',
  `csb_trans_id` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(128) DEFAULT NULL,
  `updated_by` int(128) DEFAULT NULL,
  `csb_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 is check-notpayment ,1 is payment by card',
  `csb_note` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `main_combo_service_bought`
--

INSERT INTO `main_combo_service_bought` (`id`, `csb_customer_id`, `csb_place_id`, `csb_combo_service_id`, `csb_amount`, `csb_charge`, `csb_cashback`, `csb_payment_method`, `csb_card_type`, `csb_amount_deal`, `csb_card_number`, `routing_number`, `account_number`, `bank_name`, `csb_trans_id`, `created_at`, `updated_at`, `created_by`, `updated_by`, `csb_status`, `csb_note`) VALUES
(1, 1, NULL, '1', NULL, 300, 0, 3, 'E-CHECK', NULL, '11111', NULL, NULL, NULL, NULL, '2019-09-13 02:28:58', '2019-09-13 02:28:58', 2, NULL, 0, NULL),
(2, 1, NULL, '1;2', NULL, 360, 0, 3, 'E-CHECK', 40, '56567878', NULL, NULL, NULL, NULL, '2019-09-13 03:15:40', '2019-09-13 03:15:40', 1, NULL, 0, NULL),
(3, 1, NULL, '1;7', 500, 494, 0, 3, 'E-CHECK', 6, '67677974', NULL, NULL, NULL, NULL, '2019-09-13 03:25:58', '2019-09-13 03:25:58', 1, NULL, 0, NULL),
(19, 1, NULL, '1;2', 400, 360, 0, 3, 'E-CHECK', 40, '45447676', NULL, NULL, NULL, NULL, '2019-09-13 05:02:58', '2019-09-13 05:02:58', 1, NULL, 0, NULL),
(20, 1, NULL, '1', 300, 270, 0, 3, 'E-CHECK', 30, '34343434', NULL, NULL, NULL, NULL, '2019-09-13 09:24:11', '2019-09-13 09:24:11', 1, NULL, 0, NULL),
(21, 1, NULL, '1', 300, 296, 0, 3, 'E-CHECK', 4, '43344444', NULL, NULL, NULL, NULL, '2019-09-13 09:25:03', '2019-09-13 09:25:03', 1, NULL, 0, NULL),
(22, 1, NULL, '1', 300, 296, 0, 3, 'E-CHECK', 4, '34444444', NULL, NULL, NULL, NULL, '2019-09-13 09:26:27', '2019-09-13 09:26:27', 1, NULL, 0, NULL),
(31, 616, NULL, '1;7', 500, 497, 0, 3, 'E-CHECK', 3, '33333333', NULL, NULL, NULL, NULL, '2019-09-16 05:00:22', '2019-09-16 05:00:22', 1, NULL, 0, NULL),
(32, 617, NULL, '1', 300, 288, 0, 3, 'E-CHECK', 12, '22222222', NULL, NULL, NULL, NULL, '2019-09-16 07:03:27', '2019-09-16 07:03:27', 1, NULL, 0, NULL),
(33, 618, NULL, '1', 300, 296, 0, 3, 'E-CHECK', 4, '44444444', NULL, NULL, NULL, NULL, '2019-09-16 07:09:37', '2019-09-16 07:09:37', 1, NULL, 0, NULL),
(34, 619, NULL, '1', 300, 296, 0, 3, 'E-CHECK', 4, '44444444', NULL, NULL, NULL, NULL, '2019-09-16 07:47:30', '2019-09-16 07:47:30', 1, NULL, 0, NULL),
(35, 619, NULL, '1', 300, 292, 0, 3, 'E-CHECK', 8, '88888888', NULL, NULL, NULL, NULL, '2019-09-16 07:58:06', '2019-09-16 07:58:06', 1, NULL, 0, NULL),
(36, 619, NULL, '1', 300, 295, 0, 3, 'MasterCard', 5, '41111111', NULL, NULL, NULL, NULL, '2019-09-16 08:21:22', '2019-09-16 08:21:22', 1, NULL, 1, NULL),
(37, 619, NULL, '1', 300, 298, 0, 3, 'MasterCard', 2, '41111111', NULL, NULL, NULL, NULL, '2019-09-16 09:47:46', '2019-09-16 09:47:46', 1, NULL, 1, NULL),
(38, 619, NULL, '1', 300, 270, 0, 3, 'MasterCard', 30, '41111111', NULL, NULL, NULL, '60127273488', '2019-09-16 10:03:24', '2019-09-16 10:03:24', 1, NULL, 1, NULL),
(39, 619, NULL, '1', 300, 297, 0, 3, 'MasterCard', 3, '41111111', NULL, NULL, NULL, '60127306054', '2019-09-17 07:11:45', '2019-09-17 07:11:45', NULL, NULL, 1, NULL),
(40, 619, 639, '1', 300, 296, 0, 3, 'MasterCard', 4, '41111111', NULL, NULL, NULL, '60127306275', '2019-09-17 07:20:55', '2019-09-17 07:20:55', NULL, NULL, 1, NULL),
(41, 619, 639, '2', 100, 95, 0, 3, 'MasterCard', 5, '41111111', NULL, NULL, NULL, '60127306384', '2019-09-17 07:25:32', '2019-09-17 07:25:32', NULL, NULL, 1, NULL),
(42, 619, 639, '2', NULL, NULL, 0, 3, 'MasterCard', 5, '41111111', NULL, NULL, NULL, '60127306457', '2019-09-17 07:27:21', '2019-09-17 07:27:21', NULL, NULL, 1, NULL),
(43, 619, 639, '6', 300, 297, 0, 3, 'MasterCard', 3, '41111111', NULL, NULL, NULL, '60127306526', '2019-09-17 07:28:23', '2019-09-17 07:28:23', NULL, NULL, 1, NULL),
(44, 619, 639, '2;6', 400, 392, 0, 3, 'MasterCard', 8, '41111111', NULL, NULL, NULL, '60127306798', '2019-09-17 07:31:37', '2019-09-17 07:31:37', NULL, NULL, 1, NULL),
(45, 619, 639, '1', 300, 292, 0, 3, 'MasterCard', 8, '41111111', NULL, NULL, NULL, '60127307100', '2019-09-17 07:35:40', '2019-09-17 07:35:40', 1, NULL, 1, NULL),
(47, 619, 639, '1', 300, 297, 0, 3, 'E-CHECK', 3, '', '45454', NULL, NULL, NULL, '2019-09-23 01:52:02', '2019-09-26 23:31:25', 1, NULL, 1, NULL),
(49, 619, 639, '1', 300, 300, 0, 3, 'E-CHECK', NULL, '', NULL, NULL, NULL, NULL, '2019-09-23 01:57:40', '2019-09-23 01:57:40', 1, NULL, 0, NULL),
(52, 619, 639, '1;7', 500, 477, 0, 3, 'E-CHECK', 23, '', '34343434343', '4343####3434', 'acb', NULL, '2019-09-30 19:20:34', '2019-09-30 19:20:34', 1, NULL, 0, NULL),
(54, 619, 639, '1', 300, 296, 0, 3, 'E-CHECK', 4, '', '4444444444444', '4444####4444', 'acb', NULL, '2019-09-30 20:11:18', '2019-09-30 23:47:09', 1, NULL, 1, NULL),
(55, 619, 639, '1', 300, 297, 0, 3, 'E-CHECK', 3, '', '34343434343', '4343####3434', '3333', NULL, '2019-10-01 00:51:49', '2019-10-01 00:51:49', 1, NULL, 0, NULL),
(56, 619, 639, '1', 300, 295, 0, 3, 'E-CHECK', 5, '', '34343434343', '4343####3434', 'acb', NULL, '2019-10-01 01:16:29', '2019-10-01 01:16:29', 1, NULL, 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `main_combo_service_bought`
--
ALTER TABLE `main_combo_service_bought`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `main_combo_service_bought`
--
ALTER TABLE `main_combo_service_bought`
  MODIFY `id` int(128) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
