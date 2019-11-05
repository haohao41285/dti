-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2019 at 09:43 AM
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
-- Table structure for table `main_permission_dti`
--

CREATE TABLE `main_permission_dti` (
  `id` int(10) UNSIGNED NOT NULL,
  `permission_slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `main_permission_dti`
--

INSERT INTO `main_permission_dti` (`id`, `permission_slug`, `permission_name`, `menu_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'dashboard-read', 'Dashboard Read', 1, 1, NULL, NULL),
(9, 'all-task-read', 'All Task Read', 3, 1, NULL, NULL),
(10, 'all-task-create', 'All Task Create', 3, 1, NULL, NULL),
(11, 'all-task-update', 'All Task Update', 3, 1, NULL, NULL),
(12, 'all-task-delete', 'All Task Delete', 3, 1, NULL, NULL),
(13, 'my-task-read', 'My Task Read', 4, 1, NULL, NULL),
(14, 'my-task-create', 'My Task Create', 4, 1, NULL, NULL),
(15, 'my-task-update', 'My Task Update', 4, 1, NULL, NULL),
(16, 'my-task-delete', 'My Task Delete', 4, 1, NULL, NULL),
(17, 'create-new-task-read', 'Create New Task Read', 5, 1, NULL, NULL),
(25, 'all-customers-read', 'All Customers Read', 7, 1, NULL, NULL),
(26, 'all-customers-create', 'All Customers Create', 7, 1, NULL, NULL),
(27, 'all-customers-update', 'All Customers Update', 7, 1, NULL, NULL),
(28, 'all-customers-delete', 'All Customers Delete', 7, 1, NULL, NULL),
(29, 'my-customer-read', 'My Customer Read', 8, 1, NULL, NULL),
(30, 'my-customer-create', 'My Customer Create', 8, 1, NULL, NULL),
(31, 'my-customer-update', 'My Customer Update', 8, 1, NULL, NULL),
(32, 'my-customer-delete', 'My Customer Delete', 8, 1, NULL, NULL),
(33, 'create-new-customer-read', 'Create New Customer Read', 9, 1, NULL, NULL),
(34, 'create-new-customer-create', 'Create New Customer Create', 9, 1, NULL, NULL),
(35, 'create-new-customer-update', 'Create New Customer Update', 9, 1, NULL, NULL),
(36, 'create-new-customer-delete', 'Create New Customer Delete', 9, 1, NULL, NULL),
(41, 'send-sms-read', 'Send SMS Read', 11, 1, NULL, NULL),
(42, 'send-sms-create', 'Send SMS Create', 11, 1, NULL, NULL),
(43, 'send-sms-update', 'Send SMS Update', 11, 1, NULL, NULL),
(44, 'send-sms-delete', 'Send SMS Delete', 11, 1, NULL, NULL),
(45, 'tracking-history-read', 'Tracking History Read', 12, 1, NULL, NULL),
(46, 'tracking-history-create', 'Tracking History Create', 12, 1, NULL, NULL),
(47, 'tracking-history-update', 'Tracking History Update', 12, 1, NULL, NULL),
(48, 'tracking-history-delete', 'Tracking History Delete', 12, 1, NULL, NULL),
(49, 'news-read', 'News Read', 13, 1, NULL, NULL),
(50, 'news-create', 'News Create', 13, 1, NULL, NULL),
(51, 'news-update', 'News Update', 13, 1, NULL, NULL),
(52, 'news-delete', 'News Delete', 13, 1, NULL, NULL),
(57, 'seller-read', 'Seller Read', 15, 1, NULL, NULL),
(58, 'seller-create', 'Seller Create', 15, 1, NULL, NULL),
(59, 'seller-update', 'Seller Update', 15, 1, NULL, NULL),
(60, 'seller-delete', 'Seller Delete', 15, 1, NULL, NULL),
(61, 'pos-read', 'POS Read', 16, 1, NULL, NULL),
(62, 'pos-create', 'POS Create', 16, 1, NULL, NULL),
(63, 'pos-update', 'POS Update', 16, 1, NULL, NULL),
(64, 'pos-delete', 'POS Delete', 16, 1, NULL, NULL),
(65, 'website-read', 'Website Read', 17, 1, NULL, NULL),
(66, 'website-create', 'Website Create', 17, 1, NULL, NULL),
(67, 'website-update', 'Website Update', 17, 1, NULL, NULL),
(68, 'website-delete', 'Website Delete', 17, 1, NULL, NULL),
(73, 'website-theme-read', 'Website theme Read', 19, 1, NULL, NULL),
(74, 'website-theme-create', 'Website theme Create', 19, 1, NULL, NULL),
(75, 'website-theme-update', 'Website theme Update', 19, 1, NULL, NULL),
(76, 'website-theme-delete', 'Website theme Delete', 19, 1, NULL, NULL),
(77, 'app-banners-read', 'App banners Read', 20, 1, NULL, NULL),
(78, 'app-banners-create', 'App banners Create', 20, 1, NULL, NULL),
(79, 'app-banners-update', 'App banners Update', 20, 1, NULL, NULL),
(80, 'app-banners-delete', 'App banners Delete', 20, 1, NULL, NULL),
(81, 'places-read', 'Places Read', 21, 1, NULL, NULL),
(82, 'places-create', 'Places Create', 21, 1, NULL, NULL),
(83, 'places-update', 'Places Update', 21, 1, NULL, NULL),
(84, 'places-delete', 'Places Delete', 21, 1, NULL, NULL),
(89, 'my-orders-read', 'My Orders Read', 23, 1, NULL, NULL),
(90, 'my-orders-create', 'My Orders Create', 23, 1, NULL, NULL),
(91, 'my-orders-update', 'My Orders Update', 23, 1, NULL, NULL),
(92, 'my-orders-delete', 'My Orders Delete', 23, 1, NULL, NULL),
(93, 'all-orders-read', 'All Orders Read', 24, 1, NULL, NULL),
(94, 'all-orders-create', 'All Orders Create', 24, 1, NULL, NULL),
(95, 'all-orders-update', 'All Orders Update', 24, 1, NULL, NULL),
(96, 'all-orders-delete', 'All Orders Delete', 24, 1, NULL, NULL),
(97, 'sellers-orders-read', 'Seller\'s Orders Read', 25, 1, NULL, NULL),
(98, 'sellers-orders-create', 'Seller\'s Orders Create', 25, 1, NULL, NULL),
(99, 'sellers-orders-update', 'Seller\'s Orders Update', 25, 1, NULL, NULL),
(100, 'sellers-orders-delete', 'Seller\'s Orders Delete', 25, 1, NULL, NULL),
(102, 'new-order-create', 'New Order Create', 26, 1, NULL, NULL),
(111, 'users-update', 'Users Update', 28, 1, NULL, NULL),
(112, 'users-delete', 'Users Delete', 28, 1, NULL, NULL),
(113, 'roles-read', 'Roles Read', 29, 1, NULL, NULL),
(114, 'roles-create', 'Roles Create', 29, 1, NULL, NULL),
(115, 'roles-update', 'Roles Update', 29, 1, NULL, NULL),
(116, 'roles-delete', 'Roles Delete', 29, 1, NULL, NULL),
(117, 'user-permission-read', 'User Permission Read', 30, 1, NULL, NULL),
(118, 'user-permission-create', 'User Permission Create', 30, 1, NULL, NULL),
(119, 'user-permission-update', 'User Permission Update', 30, 1, NULL, NULL),
(120, 'user-permission-delete', 'User Permission Delete', 30, 1, NULL, NULL),
(121, 'service-permission-read', 'Service Permission Read', 31, 1, NULL, NULL),
(122, 'service-permission-create', 'Service Permission Create', 31, 1, NULL, NULL),
(123, 'service-permission-update', 'Service Permission Update', 31, 1, NULL, NULL),
(124, 'service-permission-delete', 'Service Permission Delete', 31, 1, NULL, NULL),
(129, 'setup-team-read', 'Setup Team Read', 33, 1, NULL, NULL),
(130, 'setup-team-create', 'Setup Team Create', 33, 1, NULL, NULL),
(131, 'setup-team-update', 'Setup Team Update', 33, 1, NULL, NULL),
(132, 'setup-team-delete', 'Setup Team Delete', 33, 1, NULL, NULL),
(133, 'setup-team-type-read', 'Setup Team Type Read', 34, 1, NULL, NULL),
(134, 'setup-team-type-create', 'Setup Team Type Create', 34, 1, NULL, NULL),
(135, 'setup-team-type-update', 'Setup Team Type Update', 34, 1, NULL, NULL),
(136, 'setup-team-type-delete', 'Setup Team Type Delete', 34, 1, NULL, NULL),
(137, 'setup-service-read', 'Setup Service Read', 35, 1, NULL, NULL),
(138, 'setup-service-create', 'Setup Service Create', 35, 1, NULL, NULL),
(139, 'setup-service-update', 'Setup Service Update', 35, 1, NULL, NULL),
(140, 'setup-service-delete', 'Setup Service Delete', 35, 1, NULL, NULL),
(141, 'setup-service-type-read', 'Setup Service Type Read', 36, 1, NULL, NULL),
(142, 'setup-service-type-create', 'Setup Service Type Create', 36, 1, NULL, NULL),
(143, 'setup-service-type-update', 'Setup Service Type Update', 36, 1, NULL, NULL),
(144, 'setup-service-type-delete', 'Setup Service Type Delete', 36, 1, NULL, NULL),
(145, 'setup-template-sms-read', 'Setup Template SMS Read', 37, 1, NULL, NULL),
(146, 'setup-template-sms-create', 'Setup Template SMS Create', 37, 1, NULL, NULL),
(147, 'setup-template-sms-update', 'Setup Template SMS Update', 37, 1, NULL, NULL),
(148, 'setup-template-sms-delete', 'Setup Template SMS Delete', 37, 1, NULL, NULL),
(149, 'setup-login-background-read', 'Setup Login Background Read', 38, 1, NULL, NULL),
(150, 'setup-login-background-create', 'Setup Login Background Create', 38, 1, NULL, NULL),
(151, 'setup-login-background-update', 'Setup Login Background Update', 38, 1, NULL, NULL),
(152, 'setup-login-background-delete', 'Setup Login Background Delete', 38, 1, NULL, NULL),
(153, 'setup-event-holiday-read', 'Setup Event Holiday Read', 39, 1, NULL, NULL),
(154, 'setup-event-holiday-create', 'Setup Event Holiday Create', 39, 1, NULL, NULL),
(155, 'setup-event-holiday-update', 'Setup Event Holiday Update', 39, 1, NULL, NULL),
(156, 'setup-event-holiday-delete', 'Setup Event Holiday Delete', 39, 1, NULL, NULL),
(157, 'setup-menu-read', 'Setup Menu Read', 40, 1, NULL, NULL),
(158, 'setup-menu-create', 'Setup Menu Create', 40, 1, NULL, NULL),
(159, 'setup-menu-update', 'Setup Menu Update', 40, 1, NULL, NULL),
(160, 'setup-menu-delete', 'Setup Menu Delete', 40, 1, NULL, NULL),
(161, 'setup-permission-read', 'Setup Permission Read', 41, 1, NULL, NULL),
(162, 'setup-permission-create', 'Setup Permission Create', 41, 1, NULL, NULL),
(163, 'setup-permission-update', 'Setup Permission Update', 41, 1, NULL, NULL),
(164, 'setup-permission-delete', 'Setup Permission Delete', 41, 1, NULL, NULL),
(165, 'notification-read', 'Notification Read', 42, 1, NULL, NULL),
(166, 'notification-create', 'Notification Create', 42, 1, NULL, NULL),
(167, 'notification-update', 'Notification Update', 42, 1, NULL, NULL),
(168, 'notification-delete', 'Notification Delete', 42, 1, NULL, NULL),
(169, 'recent-logs-read', 'Recent Logs Read', 43, 1, NULL, NULL),
(170, 'recent-logs-create', 'Recent Logs Create', 43, 1, NULL, NULL),
(171, 'recent-logs-update', 'Recent Logs Update', 43, 1, NULL, NULL),
(172, 'recent-logs-delete', 'Recent Logs Delete', 43, 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `main_permission_dti`
--
ALTER TABLE `main_permission_dti`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `main_permission_dti`
--
ALTER TABLE `main_permission_dti`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=173;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
