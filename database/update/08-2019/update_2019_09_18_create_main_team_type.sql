-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 11, 2019 at 06:56 AM
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
-- Table structure for table `main_team_type`
--

CREATE TABLE `main_team_type` (
  `id` int(11) NOT NULL,
  `team_type_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `team_type_status` tinyint(1) NOT NULL DEFAULT '1',
  `team_type_description` text COLLATE utf8_unicode_ci NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `main_team_type`
--

INSERT INTO `main_team_type` (`id`, `team_type_name`, `team_type_status`, `team_type_description`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'Sale', 1, 'test', 1, 1, '2019-09-04 03:25:37', '2019-09-09 02:56:37'),
(2, 'Website', 1, '', 1, 1, '2019-09-04 03:25:37', '2019-09-09 02:40:31'),
(3, 'Sale', 1, 'test', 1, NULL, '2019-09-09 09:55:45', '2019-09-09 09:55:45'),
(4, 'Website', 1, 'test', 1, NULL, '2019-09-09 09:55:58', '2019-09-09 09:55:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `main_team_type`
--
ALTER TABLE `main_team_type`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `main_team_type`
--
ALTER TABLE `main_team_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
