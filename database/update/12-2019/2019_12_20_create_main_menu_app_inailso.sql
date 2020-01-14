-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 20, 2019 at 05:33 AM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.1.33

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
-- Table structure for table `main_menu_app_inailso`
--

CREATE TABLE `main_menu_app_inailso` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `position` int(5) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `main_menu_app_inailso`
--

INSERT INTO `main_menu_app_inailso` (`id`, `name`, `slug`, `position`, `created_at`, `updated_at`, `status`) VALUES
(1, 'Phone Book', 'phone-book', 0, '2019-12-20 04:11:15', '2019-12-20 04:11:15', 1),
(2, 'Website', 'website', 1, '2019-12-20 04:11:15', '2019-12-20 04:11:15', 1),
(3, 'Confirmed', 'confirmed', 2, '2019-12-20 04:11:15', '2019-12-20 04:11:15', 1),
(4, 'Review', 'review', 3, '2019-12-20 04:11:15', '2019-12-20 04:11:15', 1),
(5, 'Coupon', 'coupon', 4, '2019-12-20 04:11:15', '2019-12-20 04:11:15', 1),
(6, 'Promotion', 'promotion', 5, '2019-12-20 04:11:15', '2019-12-20 04:11:15', 1),
(7, 'Waiting List', 'waiting-list', 6, '2019-12-20 04:11:15', '2019-12-20 04:11:15', 1),
(8, 'News', 'news', 7, '2019-12-20 04:11:15', '2019-12-20 04:11:15', 1),
(9, 'Web Style', 'web-style', 8, '2019-12-20 04:11:15', '2019-12-20 04:11:15', 1),
(10, 'Setting', 'setting', 9, '2019-12-20 04:11:15', '2019-12-20 04:11:15', 1),
(11, 'Notification', 'notification', 10, '2019-12-20 04:11:15', '2019-12-20 04:11:15', 1),
(12, 'More', 'more', 11, '2019-12-20 04:11:15', '2019-12-20 04:11:15', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `main_menu_app_inailso`
--
ALTER TABLE `main_menu_app_inailso`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `main_menu_app_inailso`
--
ALTER TABLE `main_menu_app_inailso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
