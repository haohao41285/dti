-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 07, 2019 at 04:05 AM
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
-- Table structure for table `main_customer_note`
--

CREATE TABLE `main_customer_note` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL COMMENT 'main_customer_template',
  `user_id` int(11) NOT NULL COMMENT 'main_user',
  `team_id` int(11) NOT NULL COMMENT 'main_team',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `main_customer_note`
--

INSERT INTO `main_customer_note` (`id`, `customer_id`, `user_id`, `team_id`, `created_at`, `updated_at`, `content`) VALUES
(1, 153, 1, 2, '2019-10-04 02:55:49', '2019-10-04 03:00:29', 'note 2'),
(2, 1, 1, 2, '2019-10-06 18:51:27', '2019-10-06 18:51:27', 'something to note');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `main_customer_note`
--
ALTER TABLE `main_customer_note`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `main_customer_note`
--
ALTER TABLE `main_customer_note`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
