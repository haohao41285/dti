-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2019 at 05:04 AM
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
-- Table structure for table `main_customer_assigns`
--

CREATE TABLE `main_customer_assigns` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(11) NOT NULL COMMENT 'main_customer_template',
  `business_phone` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `main_customer_assigns`
--

INSERT INTO `main_customer_assigns` (`id`, `customer_id`, `business_phone`, `business_name`, `user_id`, `created_at`, `updated_at`, `address`, `email`, `website`) VALUES
(2, 120, '1111111111', 'Test', 14, '2019-11-14 02:55:20', '2019-11-14 02:55:20', NULL, NULL, ''),
(3, 108, '1111111111', 'test', 14, '2019-11-14 02:59:44', '2019-11-14 02:59:44', NULL, NULL, ''),
(4, 110, '1234567896222', 'test', 14, '2019-11-14 03:07:50', '2019-11-14 03:07:50', NULL, NULL, ''),
(8, 1, '343434232323', 'thieu\'s salon', 1, '2019-11-14 08:34:30', '2019-11-14 08:34:30', NULL, NULL, NULL),
(10, 1, '1212121212121', 'Nhu Y \'s Salon', 1, '2019-11-14 08:43:39', '2019-11-14 08:43:39', 'District 9, Ho Chi Minh city', 'nhuy@gmail.com', 'nhuy.com'),
(11, 119, '1234567896', 'Salon6', 1, '2019-11-15 04:28:41', '2019-11-15 04:28:41', NULL, NULL, NULL),
(12, 119, '121212121212', 'new business', 14, '2019-11-15 04:31:38', '2019-11-15 04:31:38', NULL, NULL, NULL),
(13, 122, '1234567892', 'Salon2', 1, '2019-11-15 04:36:12', '2019-11-15 04:36:12', 'address', 'nguyenthieupro93@gmail.com', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `main_customer_assigns`
--
ALTER TABLE `main_customer_assigns`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `main_customer_assigns`
--
ALTER TABLE `main_customer_assigns`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
