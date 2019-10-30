-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2019 at 09:42 AM
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
-- Table structure for table `main_menu_dti`
--

CREATE TABLE `main_menu_dti` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `main_menu_dti`
--

INSERT INTO `main_menu_dti` (`id`, `name`, `icon`, `link`, `parent_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard', 'fas fa-tachometer-alt', 'dashboard', 0, 1, NULL, NULL),
(2, 'Task', 'fas fa-users', 'task', 0, 1, NULL, NULL),
(3, 'All Task', '', 'task/all-task', 2, 1, NULL, NULL),
(4, 'My Task', '', 'task', 2, 1, NULL, NULL),
(5, 'Create New Task', '', 'task/add', 2, 1, NULL, NULL),
(6, 'Customers', 'fas fa-users', 'customer', 0, 1, NULL, NULL),
(7, 'All Customers', '', 'customer/customers', 6, 1, NULL, NULL),
(8, 'My Customer', '', 'customer/my-customers', 6, 1, NULL, NULL),
(9, 'Create New Customer', '', 'customer/add', 6, 1, NULL, NULL),
(10, 'Marketing', 'fas fa-lightbulb', 'marketing', 0, 1, NULL, NULL),
(11, 'Send SMS', '', 'marketing/sendsms', 10, 1, NULL, NULL),
(12, 'Tracking History', '', 'marketing/tracking-history', 10, 1, NULL, NULL),
(13, 'News', '', 'marketing/news', 10, 1, NULL, NULL),
(14, 'Statistic', 'fas fa-chart-bar', 'statistic', 0, 1, NULL, NULL),
(15, 'Seller', '', 'statistic/seller', 14, 1, NULL, NULL),
(16, 'POS', '', 'statistic/pos', 14, 1, NULL, NULL),
(17, 'Website', '', 'statistic/website', 14, 1, NULL, NULL),
(18, 'IT Tools', 'fas fa-toolbox', 'tools', 0, 1, NULL, NULL),
(19, 'Website theme', '', 'tools/website-themes', 18, 1, NULL, NULL),
(20, 'App banners', '', 'tools/app-banners', 18, 1, NULL, NULL),
(21, 'Places', '', 'tools/places', 18, 1, NULL, NULL),
(22, 'Orders', 'fas fa-shopping-cart', 'orders', 0, 1, NULL, NULL),
(23, 'My Orders', '', 'orders/my-orders', 22, 1, NULL, NULL),
(24, 'All Orders', '', 'orders/all', 22, 1, NULL, NULL),
(25, 'Seller\'s Orders', '', 'orders/sellers', 22, 1, NULL, NULL),
(26, 'New Order', '', 'orders/add', 22, 1, NULL, NULL),
(27, 'Users', 'fas fa-user-cog', 'user', 0, 1, NULL, NULL),
(28, 'Users', '', 'user/list', 27, 1, NULL, NULL),
(29, 'Roles', '', 'user/roles', 27, 1, NULL, NULL),
(30, 'User Permission', '', 'user/user-permission', 27, 1, NULL, NULL),
(31, 'Service Permission', '', 'user/service-permission', 27, 1, NULL, NULL),
(32, 'Settings', 'fas fa-cog', 'setting', 0, 1, NULL, NULL),
(33, 'Setup Team', '', 'setting/setup-team', 32, 1, NULL, NULL),
(34, 'Setup Team Type', '', 'setting/setup-team-type', 32, 1, NULL, NULL),
(35, 'Setup Service', '', 'setting/setup-service', 32, 1, NULL, NULL),
(36, 'Setup Service Type', '', 'setting/setup-service-type', 32, 1, NULL, NULL),
(37, 'Setup Template SMS', '', 'setting/setup-template-sms', 32, 1, NULL, NULL),
(38, 'Setup Login Background', '', 'setting/login-background', 32, 1, NULL, NULL),
(39, 'Setup Event Holiday', '', 'setting/setup-event-holiday', 32, 1, NULL, NULL),
(40, 'Setup Menu', '', 'setting/menu', 32, 1, NULL, NULL),
(41, 'Setup Permission', '', 'setting/setup-permission', 32, 1, NULL, NULL),
(42, 'Notification', 'fas fa-sms', 'notification', 0, 1, NULL, NULL),
(43, 'Recent Logs', 'fas fa-list-alt', 'recentlog', 0, 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `main_menu_dti`
--
ALTER TABLE `main_menu_dti`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `main_menu_dti`
--
ALTER TABLE `main_menu_dti`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
