-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Apr 17, 2026 at 03:48 AM
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
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `branch_code`, `date`, `check_in`, `check_out`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(2, 2, 'B', '2026-04-16', '00:11:28', NULL, 'present', 'Marked via site attendance', '2026-04-16 00:11:28', '2026-04-16 00:11:28'),
(3, 4, 'B', '2026-04-16', '00:11:33', NULL, 'present', 'Marked via site attendance', '2026-04-16 00:11:30', '2026-04-16 00:11:33'),
(4, 5, 'B', '2026-04-16', '00:12:17', NULL, 'present', 'Marked via site attendance', '2026-04-16 00:12:17', '2026-04-16 00:12:17'),
(5, 6, 'B', '2026-04-16', '00:13:33', NULL, 'present', 'Marked via site attendance', '2026-04-16 00:13:33', '2026-04-16 00:13:33'),
(6, 3, 'B', '2026-04-16', '08:14:55', NULL, 'present', 'Marked via site attendance', '2026-04-16 00:14:55', '2026-04-16 00:14:55'),
(31, 8, 'A', '2026-04-17', '02:57:33', '02:58:24', 'present', 'QR Scan at A', '2026-04-17 02:57:33', '2026-04-17 02:58:24'),
(30, 9, 'A', '2026-04-17', '02:57:02', '02:57:06', 'present', 'QR Scan at A', '2026-04-17 02:57:02', '2026-04-17 02:57:06'),
(29, 5, 'B', '2026-04-17', '10:46:16', '10:46:19', 'present', 'Marked via site attendance', '2026-04-17 02:46:16', '2026-04-17 02:46:19'),
(28, 7, 'B', '2026-04-17', '10:46:12', '10:46:14', 'present', 'Marked via site attendance', '2026-04-17 02:46:12', '2026-04-17 02:46:14'),
(32, 9, 'A', '2026-04-17', '03:13:21', '03:13:26', 'present', 'QR Scan at A', '2026-04-17 03:13:21', '2026-04-17 03:13:26'),
(33, 9, 'B', '2026-04-17', '03:14:00', '03:14:06', 'present', 'QR Scan at B', '2026-04-17 03:14:00', '2026-04-17 03:14:06'),
(34, 9, 'B', '2026-04-17', '03:14:22', '03:17:25', 'present', 'QR Scan at B', '2026-04-17 03:14:22', '2026-04-17 03:17:25'),
(35, 9, 'B', '2026-04-17', '03:17:31', NULL, 'present', 'QR Scan at B', '2026-04-17 03:17:31', '2026-04-17 03:17:31'),
(36, 8, 'A', '2026-04-17', '03:38:44', '03:38:59', 'present', 'QR Scan at A', '2026-04-17 03:38:44', '2026-04-17 03:38:59'),
(37, 10, 'A', '2026-04-17', '11:42:16', NULL, 'present', 'QR Scan at A', '2026-04-17 03:42:16', '2026-04-17 03:42:16');

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
  `employee_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middle_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'Active',
  `daily_rate` decimal(10,2) DEFAULT '0.00',
  `has_deductions` tinyint(1) DEFAULT '0',
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `default_branch_id` int DEFAULT NULL,
  `performance_allowance` decimal(10,2) DEFAULT '0.00',
  `has_deduction` tinyint(1) DEFAULT '1',
  `branch_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_code` (`employee_code`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `employee_code`, `first_name`, `middle_name`, `last_name`, `email`, `department`, `position`, `status`, `daily_rate`, `has_deductions`, `profile_image`, `created_at`, `updated_at`, `default_branch_id`, `performance_allowance`, `has_deduction`, `branch_id`) VALUES
(5, 'W0001', 'Testtt', 'sdfgdsg', 'sfdg', 'sfdg@gmail.com', NULL, 'Worker', 'Active', 100.00, 1, 'uploads/employees/69e18cd6c872f_46.png', '2026-04-14 05:14:15', '2026-04-17 01:51:46', NULL, 0.00, 0, NULL),
(7, 'SA001', 'Super', 'Torres', 'Admin', 'admin@jajrconstruction.com', 'Administration', 'Super Admin', 'Active', 600.00, 0, 'uploads/profile_images/profile_6_1771480314.png', '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, 33),
(8, 'W0002', 'AARIZ', '', 'MARLOU', 'aariz.marlou@example.com', 'Operations', 'Worker', 'Active', 700.00, 0, 'profile_69d6006a66bfe6.32302616.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21),
(9, 'W0003', 'CESAR', '', 'ABUBO', 'cesar.abubo@example.com', 'Operations', 'Worker', 'Active', 550.00, 0, 'profile_69d60010402ca2.92249984.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 150.00, 1, 21),
(10, 'W0004', 'MARLON', '', 'AGUILAR', 'marlon.aguilar@example.com', 'Operations', 'Worker', 'Active', 600.00, 0, 'profile_69d600211a0589.35341824.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 100.00, 0, 10),
(11, 'W0005', 'NOEL', NULL, 'ARIZ', 'noel.ariz@example.com', 'Operations', 'Worker', 'Inactive', 550.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, 26),
(12, 'W0006', 'DANIEL', '', 'BACHILLER', 'daniel.bachiller@example.com', 'Operations', 'Worker', 'Active', 600.00, 0, 'profile_69d6002e97f1d3.80387073.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 100.00, 1, 21),
(13, 'W0007', 'ALFREDO', '', 'BAGUIO', 'alfredo.baguio@example.com', 'Operations', 'Worker', 'Active', 550.00, 0, 'profile_69d5ff418361b7.89098507.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 150.00, 0, 21),
(14, 'W0008', 'ROLLY', '', 'BALTAZAR', 'rolly.baltazar@example.com', 'Operations', 'Worker', 'Active', 500.00, 0, 'profile_69d5ff547f48e9.55971784.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21),
(15, 'W0009', 'DONG', NULL, 'BAUTISTA', 'dong.bautista@example.com', 'Operations', 'Worker', 'Inactive', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, 20),
(16, 'W0010', 'JANLY', '', 'BELINO', 'janly.belino@example.com', 'Operations', 'Worker', 'Active', 650.00, 0, 'profile_69d5f8bd3ff0e7.72784110.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 10),
(17, 'W0011', 'MENUEL', '', 'BENITEZ', 'menuel.benitez@example.com', 'Operations', 'Worker', 'Active', 600.00, 0, 'profile_69d5f8d8982db4.66850139.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 100.00, 1, 21),
(18, 'W0012', 'GELMAR', '', 'BARNACHEA', 'gelmar.bernachea@example.com', 'Operations', 'Worker', 'Active', 500.00, 0, 'profile_69d5ff3620afe4.25764722.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21),
(19, 'W0013', 'JOMAR', NULL, 'CABANBAN', 'jomar.cabanban@example.com', 'Operations', 'Worker', 'Inactive', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, 22),
(20, 'W0014', 'MARIO', '', 'CABANBAN', 'mario.cabanban@example.com', 'Operations', 'Worker', 'Active', 600.00, 0, 'profile_69d9bdfcd6a4e1.58343645.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 100.00, 0, 10),
(21, 'W0015', 'KELVIN', NULL, 'CALDERON', 'kelvin.calderon@example.com', 'Operations', 'Worker', 'Inactive', 500.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, 21),
(22, 'W0016', 'FLORANTE', NULL, 'CALUZA', 'florante.caluza@example.com', 'Operations', 'Worker', 'Inactive', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 22),
(23, 'W0017', 'MELVIN', NULL, 'CAMPOS', 'melvin.campos@example.com', 'Operations', 'Worker', 'Inactive', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, 21),
(24, 'W0018', 'JERWIN', '', 'CAMPOS', 'jerwin.campos@example.com', 'Operations', 'Worker', 'Active', 550.00, 0, 'profile_69d5ff06eb31e2.16953567.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 150.00, 1, 21),
(25, 'W0019', 'BENJIE', '', 'CARAS', 'benjie.caras@example.com', 'Operations', 'Worker', 'Active', 700.00, 0, 'profile_69d5ffdbd4db63.91949381.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21),
(26, 'W0020', 'JORELLE BONJO', '', 'DACUMOS', 'bonjo.dacumos@example.com', 'Operations', 'Worker', 'Active', 500.00, 0, 'profile_69d60206afa450.64233705.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 10),
(27, 'W0021', 'RYAN', '', 'DEOCARIS', 'ryan.deocaris@example.com', 'Operations', 'Worker', 'Active', 500.00, 0, 'profile_69d6009b3d7d21.77206328.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21),
(28, 'W0022', 'BEN', '', 'ESTEPA', 'ben.estepa@example.com', 'Operations', 'Worker', 'Active', 600.00, 0, 'profile_69d6007aeb1ce2.19714221.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 600.00, 1, 21),
(29, 'W0023', 'MAR DAVE', '', 'FLORES', 'mardave.flores@example.com', 'Operations', 'Worker', 'Active', 550.00, 0, 'profile_69d5ffa98b1854.65713856.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 150.00, 0, 10),
(30, 'W0024', 'ALBERT', '', 'FONTANILLA', 'albert.fontanilla@example.com', 'Operations', 'Worker', 'Active', 550.00, 0, 'profile_69d600ff0c9b92.81545089.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 150.00, 1, 21),
(31, 'W0025', 'JOHN WILSON', NULL, 'FONTANILLA', 'johnwilson.fontanilla@example.com', 'Operations', 'Worker', 'Inactive', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 20),
(32, 'W0026', 'LEO', '', 'GURTIZA', 'leo.gurtiza@example.com', 'Operations', 'Worker', 'Active', 600.00, 0, 'profile_69d5fec772d144.20772071.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 100.00, 1, 10),
(33, 'W0027', 'JOSE', '', 'IGLECIAS', 'jose.iglecias@example.com', 'Operations', 'Worker', 'Active', 500.00, 0, 'profile_69d9afab0cf298.43125381.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 31),
(34, 'W0028', 'JEFFREY', '', 'JIMENEZ', 'jeffrey.jimenez@example.com', 'Operations', 'Worker', 'Active', 550.00, 0, 'profile_69d6008a7d4189.24345782.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 150.00, 1, 21),
(35, 'W0029', 'WILSON', '', 'LICTAOA', 'wilson.lictaoa@example.com', 'Operations', 'Worker', 'Inactive', 500.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, 21),
(36, 'W0030', 'LORETO', '', 'MABALO', 'loreto.mabalo@example.com', 'Operations', 'Worker', 'Active', 600.00, 0, 'profile_69d9bddccd1619.96311862.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 100.00, 0, 10),
(37, 'W0031', 'ROMEL', '', 'MALLARE', 'romel.mallare@example.com', 'Operations', 'Worker', 'Active', 800.00, 0, 'profile_69d5fea1eb47d3.35526436.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 150.00, 1, 31),
(38, 'W0032', 'SAMUEL SR.', '', 'MARQUEZ', 'samuel.marquez@example.com', 'Operations', 'Worker', 'Active', 500.00, 0, 'profile_69d5fe62cbdd09.62445973.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21),
(39, 'W0033', 'ROLLY', NULL, 'MARZAN', 'rolly.marzan@example.com', 'Operations', 'Worker', 'Inactive', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, 10),
(40, 'W0034', 'RONALD', '', 'MARZAN', 'ronald.marzan@example.com', 'Operations', 'Worker', 'Active', 600.00, 0, 'profile_69d9bdf04c57f8.40601532.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 1000.00, 0, 10),
(41, 'W0035', 'WILSON', '', 'MARZAN', 'wilson.marzan@example.com', 'Operations', 'Worker', 'Active', 600.00, 0, 'profile_69d6004781b584.57723505.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 100.00, 1, 10),
(42, 'W0036', 'MARVIN', NULL, 'MIRANDA', 'marvin.miranda@example.com', 'Operations', 'Worker', 'Inactive', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 22),
(43, 'W0037', 'JOE', '', 'MONTERDE', 'joe.monterde@example.com', 'Operations', 'Worker', 'Active', 700.00, 0, 'profile_69d5ff67b7ece6.83173563.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21),
(44, 'W0038', 'ARNOLD', '', 'NERIDO', 'arnold.nerido@example.com', 'Operations', 'Worker', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 100.00, 0, 31),
(45, 'W0039', 'DANNY', '', 'PADILLA', 'danny.padilla@example.com', 'Operations', 'Worker', 'Active', 500.00, 0, 'profile_69d600ac33ec53.26400528.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 10),
(46, 'W0040', 'EDGAR', NULL, 'PANEDA', 'edgar.paneda@example.com', 'Operations', 'Worker', 'Inactive', 550.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, 26),
(47, 'W0041', 'JEREMY', '', 'PIMENTEL', 'jeremy.pimentel@example.com', 'Operations', 'Worker', 'Active', 550.00, 0, 'profile_69d600d6b1d057.48967611.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21),
(48, 'W0042', 'MIGUEL', NULL, 'PREPOSI', 'miguel.preposi@example.com', 'Operations', 'Worker', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 100.00, 1, 10),
(49, 'W0043', 'JUN', NULL, 'ROAQUIN', 'jun.roaquin@example.com', 'Operations', 'Worker', 'Inactive', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, 26),
(50, 'W0044', 'RICKMAR', '', 'SANTOS', 'rickmar.santos@example.com', 'Operations', 'Worker', 'Active', 500.00, 0, 'profile_69d600eed64931.69263448.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 100.00, 1, 28),
(51, 'W0045', 'RIO', '', 'SILOY', 'rio.siloy@example.com', 'Operations', 'Worker', 'Active', 750.00, 0, 'profile_69d5fe758e89a2.19541693.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 150.00, 1, 32),
(52, 'W0046', 'NORMAN', '', 'TARAPE', 'norman.tarape@example.com', 'Operations', 'Worker', 'Active', 500.00, 0, 'profile_69d5fe90ac00d1.71248253.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 10),
(53, 'W0047', 'HILMAR', '', 'TATUNAY', 'hilmar.tatunay@example.com', 'Operations', 'Worker', 'Active', 500.00, 0, 'profile_69d5ff866f3104.37734210.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 100.00, 1, 21),
(54, 'W0048', 'KENNETH JOHN', '', 'UGAS', 'kennethjohn.ugas@example.com', 'Operations', 'Worker', 'Active', 600.00, 0, 'profile_69d5ff943a6d70.65129657.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 50.00, 1, 10),
(55, 'W0049', 'CLYDE JUSTINE', NULL, 'VASADRE', 'clydejustine.vasadre@example.com', 'Operations', 'Worker', 'Inactive', 500.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, 28),
(56, 'ENG-2026-0005', 'JOYLENE F.', NULL, 'BALANON', 'joylene.balanon@example.com', 'Engineering', 'Engineer', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, 33),
(57, 'ENG-2026-0002', 'John Kennedy', '', 'Lucas', 'lucas@gmail.com', 'Engineering', 'Engineer', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, 10),
(58, 'ENG-2026-0003', 'Julius John', '', 'Echague', 'echague@gmail.com', 'Engineering', 'Engineer', 'Inactive', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 1, 21),
(59, 'PRO-2026-0001', 'Junell', '', 'Tadina', 'tadina@gmail.com', 'Engineering', 'Engineer', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, 33),
(60, 'ENG-2026-0006', 'Winnielyn Kaye', '', 'Olarte', 'olarte@gmail.com', 'Engineering', 'Engineer', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, 33),
(61, 'ADMIN-2026-0002', 'RONALYN', NULL, 'MALLARE', 'ronalyn.mallare@example.com', 'Administration', 'Admin', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, 33),
(62, 'ENG-2026-0001', 'MICHELLE F.', NULL, 'NORIAL', 'michelle.norial@example.com', 'Engineering', 'Engineer', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, 33),
(63, 'ADMIN-2026-0001', 'Elaine', 'Torres', 'Aguilar', 'aguilar@gmail.com', 'Administration', 'Admin', 'Active', 600.00, 0, 'profile_6996a4f55d7335.10207456.png', '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, 33),
(64, 'SA-2026-002', 'Jason', 'Larkin', 'Wong', 'wong@gmail.com', 'Administration', 'Admin', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, NULL),
(65, 'SA-2026-003', 'Lee Aldrich', '', 'Rimando', 'rimando@gmail.com', 'Administration', 'Admin', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, NULL),
(66, 'SA-2026-004', 'Marc Justin', '', 'Arzadon', 'arzadon@gmail.com', 'Administration', 'Admin', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, NULL),
(67, 'W0050', 'JOSHUA', NULL, 'ARQUITOLA', 'joshua.arquitola@example.com', 'Operations', 'Worker', 'Inactive', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, 22),
(68, 'W0051', 'VERGEL', '', 'DACUMOS', 'vergel.dacumos@example.com', 'Operations', 'Worker', 'Inactive', 550.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, 22),
(69, 'W0052', 'REAL RAIN', NULL, 'IVERSON', 'realrain.iverson@example.com', 'Operations', 'Worker', 'Inactive', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, 22),
(70, 'W0053', 'VOHANN', '', 'MIRANDA', 'vohann.miranda@example.com', 'Operations', 'Worker', 'Inactive', 550.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, 22),
(71, 'W0054', 'SONNY', NULL, 'OCCIANO', 'sonny.occiano@example.com', 'Operations', 'Worker', 'Inactive', 1400.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, 21),
(72, 'W0055', 'RANDY', '', 'ATON', 'randy.aton@example.com', 'Operations', 'Worker', 'Active', 600.00, 0, 'profile_69d600c4792567.58068989.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 50.00, 1, 21),
(73, 'W0056', 'JHUNEL', '', 'CANCHO', 'jhunel.cancho@example.com', 'Operations', 'Worker', 'Active', 500.00, 0, 'profile_69d5fe54d05ff6.44033214.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 32),
(74, 'W0057', 'HECTOR', NULL, 'PADICLAS', 'hector.padiclas@example.com', 'Operations', 'Worker', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 100.00, 0, 10),
(75, 'W0058', 'MARIANO', NULL, 'NERIDO', 'mariano.nerido@example.com', 'Operations', 'Worker', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 10),
(76, 'W0059', 'JAYSON KENNETH', NULL, 'PADILLA', 'jaysonkenneth.padilla@example.com', 'Operations', 'Worker', 'Inactive', 500.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, 21),
(77, 'W0060', 'JEFFREY', '', 'ZAMORA', 'jeffrey.zamora@example.com', 'Operations', 'Worker', 'Active', 600.00, 0, 'profile_69d601095e8562.71487068.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 100.00, 0, 31),
(78, 'W0061', 'FRANKIE', NULL, 'PADILLA', 'frankie.padilla@example.com', 'Operations', 'Worker', 'Active', 500.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21),
(79, 'W0062', 'ROMEO', '', 'GURION', 'romeo.gurion@example.com', 'Operations', 'Worker', 'Active', 550.00, 0, 'profile_69d5ff1d4c6693.09123495.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 10),
(80, 'ADMIN-2026-0003', 'Charisse', 'Abaya', 'Laureaga', 'charisse@gmail.com', 'Administration', 'Admin', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, 33),
(81, 'ADMIN-2026-0004', 'Marjorie', '', 'Garcia', 'garcia@gmail.com', 'Administration', 'Admin', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, 33),
(82, 'ENG-2026-0007', 'Earl Cleint', 'Ordono', 'Nisperos', 'nisperos@gmail.com', 'Engineering', 'Engineer', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, 21),
(83, 'IT-2026-01', 'Daniel ', 'Obaldo', 'Rillera', 'danrillera.va@gmail.com', 'IT', 'Developer', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, 33),
(84, 'IT-2026-02', 'Prince Christiane', 'Borja', 'Tolentino', 'tolentinochristian89@gmail.com', 'IT', 'Developer', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, 33),
(85, 'W0063', 'Gilbert', '', 'Avecilla', 'avecilla@gmail.com', 'Operations', 'Worker', 'Inactive', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, NULL),
(86, 'W0064', 'Joseph', '', 'Espanto', 'espanto@gmail.com', 'Operations', 'Worker', 'Active', 550.00, 0, 'profile_69d9af93b4b563.99389483.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21),
(87, 'W0065', 'Ronel', '', 'Noces', 'noces@gmail.com', 'Operations', 'Worker', 'Active', 500.00, 0, 'profile_69d5fe420625c5.31868763.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 10),
(88, 'W0066', 'Fernando', '', 'Rivera', 'rivera@gmail.com', 'Operations', 'Worker', 'Active', 700.00, 0, 'profile_69d600e353fb09.09593138.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21),
(89, 'W0067', 'Darwin', '', 'Gurion', 'gurion1@gmail.com', 'Operations', 'Worker', 'Active', 700.00, 0, 'profile_69d5fed995d947.19342413.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 10),
(90, 'W0068', 'Rey', '', 'Gurion', 'gurion2@gmail.com', 'Operations', 'Worker', 'Active', 700.00, 0, 'profile_69d5feeb0d97b1.11056357.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 10),
(91, 'W0069', 'Santi', '', 'Abubo', 'abubo1@gmail.com', 'Operations', 'Worker', 'Active', 550.00, 0, 'profile_69d5ffe6e6d766.98386818.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21),
(92, 'ADMIN-2026-0005', 'Lyra', '', 'Javonillo', 'javonillo@gmail.com', 'Administration', 'Admin', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:33:09', NULL, 0.00, 0, 33),
(93, 'W0070', 'Sonny', '', 'Pascua', 'sonny@gmail.com', 'Operations', 'Worker', 'Inactive', 500.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, NULL),
(94, 'W0071', 'Edwin', '', 'Laforteza', 'edwin@gmail.com', 'Operations', 'Worker', 'Inactive', 500.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, NULL),
(95, 'W0072', 'Semy', '', 'Abat', 'abat@gmail.com', 'Operations', 'Worker', 'Inactive', 550.00, 0, 'profile_69c72508562873.21033310.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, NULL),
(96, 'W0073', 'Reynaldo', '', 'Gurion', 'gurion@gmail.com', 'Operations', 'Worker', 'Active', 700.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, NULL),
(97, 'W0074', 'Larry', '', 'Gurion', 'larry@gmail.com', 'Operations', 'Worker', 'Active', 700.00, 0, 'profile_69d9aff8f24610.75781313.png', '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 10),
(98, 'W0075', 'Kyle', '', 'Arrieta', 'kyle@gmail.com', 'Operations', 'Worker', 'Active', 550.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21),
(99, 'W0076', 'Rolan', '', 'Estrada', 'estrada@gmail.com', 'Operations', 'Worker', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 31),
(100, 'W0077', 'Ronald', '', 'Estrada', 'ronald@gmail.com', 'Operations', 'Worker', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 31),
(101, 'W0078', 'Arlene', '', 'Catbagan', 'cat@gmail.com', 'Operations', 'Worker', 'Inactive', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 1, NULL),
(102, 'W0079', 'Test', '', 'Worker', 'testworker@gmail.com', 'Operations', 'Worker', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 90.00, 0, 21),
(103, 'W0080', 'Wilben', '', 'Gurion', 'gurion5@gmail.com', 'Operations', 'Worker', 'Active', 500.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 10),
(104, 'W0081', 'Rodel', '', 'Ochoco', 'ochoco@gmail.com', 'Operations', 'Worker', 'Active', 500.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 10),
(105, 'W0082', 'Justine', '', 'Iglesias', 'Iglesias2@gmail.com', 'Operations', 'Worker', 'Active', 500.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21),
(106, 'W0083', 'Jhonrey', '', 'Danao', 'danao@gmail.com', 'Operations', 'Worker', 'Active', 500.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21),
(107, 'W0084', 'Marvin', '', 'Mirandan', 'miranda@gmail.com', 'Operations', 'Worker', 'Active', 600.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 24),
(108, 'W0085', 'SONNY', '', 'OCCIANO', 'occiano@gmail.com', 'Operations', 'Worker', 'Active', 1400.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 24),
(109, 'W0086', 'GIN TYRONE', '', 'AQUINO', 'aquino@gmail.com', 'Operations', 'Worker', 'Active', 500.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21),
(110, 'W0087', 'EFREN JAY', '', 'MORALES', 'morales@gmail.com', 'Operations', 'Worker', 'Active', 500.00, 0, NULL, '2026-04-16 08:33:09', '2026-04-16 08:42:17', NULL, 0.00, 0, 21);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
