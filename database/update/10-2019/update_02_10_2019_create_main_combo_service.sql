-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 06, 2019 at 05:09 AM
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
-- Table structure for table `main_combo_service`
--

CREATE TABLE `main_combo_service` (
  `id` int(11) NOT NULL,
  `cs_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cs_price` int(11) NOT NULL,
  `cs_expiry_period` int(5) DEFAULT NULL COMMENT 'expiry period to use , unit is month',
  `cs_service_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'id of service in pos_combo_service_template, save with implode ;',
  `cs_menu_id` text COLLATE utf8_unicode_ci COMMENT 'menu_id in pos_merchant_menus--implode ;',
  `cs_description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cs_status` tinyint(1) DEFAULT '1',
  `cs_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1 is combo, 2 is service',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `main_combo_service`
--

INSERT INTO `main_combo_service` (`id`, `cs_name`, `cs_price`, `cs_expiry_period`, `cs_service_id`, `cs_menu_id`, `cs_description`, `cs_status`, `cs_type`, `created_at`, `updated_at`) VALUES
(1, 'Combo Test', 300, NULL, '2;3;4', NULL, NULL, 1, 1, '2019-09-05 07:34:50', '2019-09-05 07:34:50'),
(2, 'POS', 100, 6, NULL, '', NULL, 1, 2, '2019-09-05 07:34:50', '2019-09-05 07:34:50'),
(6, 'Service ', 300, 6, NULL, '12;23;6;14;25', NULL, 1, 2, '2019-09-05 07:34:50', '2019-09-05 07:34:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `main_combo_service`
--
ALTER TABLE `main_combo_service`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `main_combo_service`
--
ALTER TABLE `main_combo_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
