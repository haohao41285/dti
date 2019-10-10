-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2019 at 11:25 AM
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
-- Table structure for table `main_event_holiday`
--

CREATE TABLE `main_event_holiday` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `main_event_holiday`
--

INSERT INTO `main_event_holiday` (`id`, `name`, `date`, `image`, `created_by`, `updated_by`, `created_at`, `updated_at`, `status`) VALUES
(1, 'Valentine', '2019-10-13', 'images/event/10/1570688362valentine.jpg', 1, 3, '2019-10-10 09:24:00', '2019-10-10 09:24:00', 1),
(6, 'Quoc Khanh My', '2019-07-04', 'images/event/10/1570688232qk.jpg', 3, 3, '2019-10-10 06:17:12', '2019-10-10 06:17:12', 1),
(8, 'Lễ Phục Sinh', '2019-04-12', 'images/event/10/1570688534trung-phuc-sinh.jpg', 3, NULL, '2019-10-10 06:22:14', '2019-10-10 06:22:14', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `main_event_holiday`
--
ALTER TABLE `main_event_holiday`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `main_event_holiday`
--
ALTER TABLE `main_event_holiday`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
