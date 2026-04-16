-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 16, 2026 at 02:19 AM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `attendance-system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('super_admin','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `branch_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `name`, `email`, `role`, `created_at`, `updated_at`, `branch_code`) VALUES
(1, 'admin', '$2y$10$vmPwtKtbjf5fKta5Ve6YWOW7CtW82qvUgbN5jynSKSoSM315./kb2', 'Super Administrator', 'admin@jajr.com', 'super_admin', '2026-04-14 03:02:42', '2026-04-14 03:12:08', NULL),
(14, 'branch-e', '$2y$10$3ytjyW/KOW/muKN3yUXB9edeoZrtRqVIpHpHk8/JTXADvnT9wRzcC', 'Branch E Device - Main Office', 'branch-e@jajr.local', '', '2026-04-14 08:14:43', '2026-04-14 08:14:43', 'E'),
(13, 'branch-d', '$2y$10$3ytjyW/KOW/muKN3yUXB9edeoZrtRqVIpHpHk8/JTXADvnT9wRzcC', 'Branch D Device - Panicsican', 'branch-d@jajr.local', '', '2026-04-14 08:14:43', '2026-04-14 08:14:43', 'D'),
(12, 'branch-c', '$2y$10$3ytjyW/KOW/muKN3yUXB9edeoZrtRqVIpHpHk8/JTXADvnT9wRzcC', 'Branch C Device - Sundara', 'branch-c@jajr.local', '', '2026-04-14 08:14:43', '2026-04-14 08:14:43', 'C'),
(11, 'branch-b', '$2y$10$3ytjyW/KOW/muKN3yUXB9edeoZrtRqVIpHpHk8/JTXADvnT9wRzcC', 'Branch B Device - BCDA', 'branch-b@jajr.local', '', '2026-04-14 08:14:43', '2026-04-14 08:14:43', 'B'),
(10, 'branch-a', '$2y$10$3ytjyW/KOW/muKN3yUXB9edeoZrtRqVIpHpHk8/JTXADvnT9wRzcC', 'Branch A Device - Sto. Rosario', 'branch-a@jajr.local', '', '2026-04-14 08:14:43', '2026-04-14 08:14:43', 'A'),
(15, 'branch-f', '$2y$10$3ytjyW/KOW/muKN3yUXB9edeoZrtRqVIpHpHk8/JTXADvnT9wRzcC', 'Branch F Device - Capitol', 'branch-f@jajr.local', '', '2026-04-14 08:14:43', '2026-04-14 08:14:43', 'F');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_id` int NOT NULL,
  `branch_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date NOT NULL,
  `check_in` time DEFAULT NULL,
  `check_out` time DEFAULT NULL,
  `status` enum('present','absent','late','half_day','leave') COLLATE utf8mb4_unicode_ci DEFAULT 'present',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_attendance` (`employee_id`,`date`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `branch_code`, `date`, `check_in`, `check_out`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 'B', '2026-04-16', '00:11:23', NULL, 'present', 'Marked via site attendance', '2026-04-16 00:11:23', '2026-04-16 00:11:23'),
(2, 2, 'B', '2026-04-16', '00:11:28', NULL, 'present', 'Marked via site attendance', '2026-04-16 00:11:28', '2026-04-16 00:11:28'),
(3, 4, 'B', '2026-04-16', '00:11:33', NULL, 'present', 'Marked via site attendance', '2026-04-16 00:11:30', '2026-04-16 00:11:33'),
(4, 5, 'B', '2026-04-16', '00:12:17', NULL, 'present', 'Marked via site attendance', '2026-04-16 00:12:17', '2026-04-16 00:12:17'),
(5, 6, 'B', '2026-04-16', '00:13:33', NULL, 'present', 'Marked via site attendance', '2026-04-16 00:13:33', '2026-04-16 00:13:33'),
(6, 3, 'B', '2026-04-16', '08:14:55', NULL, 'present', 'Marked via site attendance', '2026-04-16 00:14:55', '2026-04-16 00:14:55');

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
CREATE TABLE IF NOT EXISTS `branches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `branch_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `contact_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `branch_code` (`branch_code`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `branch_code`, `branch_name`, `address`, `contact_number`, `status`, `created_at`, `updated_at`) VALUES
(1, 'A', 'Sto. Rosario', NULL, NULL, 'Active', '2026-04-14 06:09:01', '2026-04-14 06:09:01'),
(2, 'B', 'BCDA', NULL, NULL, 'Active', '2026-04-14 06:09:01', '2026-04-14 06:09:01'),
(3, 'C', 'Sundara', NULL, NULL, 'Active', '2026-04-14 06:09:01', '2026-04-14 06:09:01'),
(4, 'D', 'Panicsican', NULL, NULL, 'Active', '2026-04-14 06:09:01', '2026-04-14 06:09:01'),
(5, 'E', 'Main Office', NULL, NULL, 'Active', '2026-04-14 06:09:01', '2026-04-14 06:09:01'),
(6, 'F', 'Capitol', NULL, NULL, 'Active', '2026-04-14 06:09:01', '2026-04-14 06:09:01');

-- --------------------------------------------------------

--
-- Table structure for table `branch_users`
--

DROP TABLE IF EXISTS `branch_users`;
CREATE TABLE IF NOT EXISTS `branch_users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `branch_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
CREATE TABLE IF NOT EXISTS `employees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `employee_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `daily_rate` decimal(10,2) DEFAULT '0.00',
  `has_deductions` tinyint(1) DEFAULT '0',
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `default_branch_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_code` (`employee_code`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_code`, `first_name`, `middle_name`, `last_name`, `email`, `department`, `position`, `status`, `daily_rate`, `has_deductions`, `profile_image`, `created_at`, `updated_at`, `default_branch_id`) VALUES
(1, 'EMP001', 'John', NULL, 'Doe', 'john.doe@example.com', 'IT', 'Developer', 'Active', 0.00, 0, NULL, '2026-04-14 02:26:33', '2026-04-14 02:26:33', NULL),
(2, 'EMP002', 'Jane', NULL, 'Smith', 'jane.smith@example.com', 'HR', 'Manager', 'Active', 0.00, 0, NULL, '2026-04-14 02:26:33', '2026-04-14 02:26:33', NULL),
(3, 'EMP003', 'Bob', NULL, 'Johnson', 'bob.johnson@example.com', 'Sales', 'Representative', 'Active', 0.00, 0, NULL, '2026-04-14 02:26:33', '2026-04-14 02:26:33', NULL),
(4, 'W0002', 'test', 'Woker', 'Ito', 'ito@gmail.com', NULL, 'Worker', 'Active', 1000.00, 0, NULL, '2026-04-14 05:13:37', '2026-04-14 05:13:37', NULL),
(5, '', 'Testtt', 'sdfgdsg', 'sfdg', 'sfdg@gmail.com', NULL, 'Worker', 'Active', 100.00, 1, NULL, '2026-04-14 05:14:15', '2026-04-14 05:14:15', NULL),
(6, 'W0003', 'Edward', 'Obaldo', 'Cariaga', 'cariaga@gmail.com', NULL, 'Worker', 'Active', 1499.94, 1, NULL, '2026-04-14 05:20:16', '2026-04-14 05:20:16', NULL);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
