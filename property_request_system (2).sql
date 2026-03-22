-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2026 at 08:04 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `property_request_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `approvals`
--

CREATE TABLE `approvals` (
  `approval_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `approver_id` int(11) NOT NULL,
  `department` varchar(50) NOT NULL,
  `action` enum('approved','rejected') NOT NULL,
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `approvals`
--

INSERT INTO `approvals` (`approval_id`, `request_id`, `approver_id`, `department`, `action`, `feedback`, `created_at`) VALUES
(1, 1, 2, 'requester_main_dept', 'approved', NULL, '2026-03-18 16:06:13'),
(2, 1, 3, 'property_mgmt_main_dept', 'approved', NULL, '2026-03-18 16:12:54'),
(3, 1, 4, 'property_mgmt_dept', 'approved', NULL, '2026-03-18 16:14:05'),
(4, 1, 5, 'registry_office', 'approved', NULL, '2026-03-18 16:15:27'),
(9, 2, 2, 'requester_main_dept', 'approved', NULL, '2026-03-19 18:31:34'),
(10, 2, 3, 'property_mgmt_main_dept', 'approved', NULL, '2026-03-19 18:31:48'),
(11, 2, 4, 'property_mgmt_dept', 'approved', NULL, '2026-03-19 18:32:02'),
(12, 2, 5, 'registry_office', 'approved', NULL, '2026-03-19 18:32:27');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `log_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`log_id`, `request_id`, `user_id`, `action`, `details`, `created_at`) VALUES
(1, 1, 1, 'request_created', 'Request created with Form 20', '2026-03-18 16:05:14'),
(2, 1, 2, 'approved', 'Approved by requester_main_dept', '2026-03-18 16:06:13'),
(3, 1, 3, 'approved', 'Approved by property_mgmt_main_dept', '2026-03-18 16:12:54'),
(4, 1, 4, 'item_registered', 'Item registered: zszs', '2026-03-18 16:14:05'),
(5, 1, 4, 'approved', 'Approved by property_mgmt_dept', '2026-03-18 16:14:05'),
(6, 1, 5, 'backup_created', 'Backup record created', '2026-03-18 16:15:27'),
(7, 1, 5, 'approved', 'Approved by registry_office', '2026-03-18 16:15:27'),
(8, 1, 6, 'release_permission_issued', 'Release permission RP-20260318-9214 issued for item ßè«ßêØßìÆßïìßë░ßê¡ ßï┤ßêÁßè¡ßëÂßìò (Code: COMP-001)', '2026-03-18 17:10:39'),
(9, 1, 6, 'item_released', 'Item ßè«ßêØßìÆßïìßë░ßê¡ ßï┤ßêÁßè¡ßëÂßìò (Code: COMP-001) released to requester with permission RP-20260318-9214', '2026-03-18 17:10:39'),
(10, 2, 1, 'request_created', 'Request created with Form 20', '2026-03-19 18:31:08'),
(11, 2, 2, 'approved', 'Approved by requester_main_dept', '2026-03-19 18:31:34'),
(12, 2, 3, 'approved', 'Approved by property_mgmt_main_dept', '2026-03-19 18:31:48'),
(13, 2, 4, 'item_registered', 'Item registered: zsx', '2026-03-19 18:32:02'),
(14, 2, 4, 'approved', 'Approved by property_mgmt_dept', '2026-03-19 18:32:02'),
(15, 2, 5, 'backup_created', 'Backup record created', '2026-03-19 18:32:27'),
(16, 2, 5, 'approved', 'Approved by registry_office', '2026-03-19 18:32:27'),
(17, 2, 6, 'item_assigned_from_store', 'Item ßè«ßêØßìÆßïìßë░ßê¡ ßï┤ßêÁßè¡ßëÂßìò (Code: COMP-001) assigned - Quantity: 1', '2026-03-19 18:32:52'),
(18, 2, 6, 'release_permission_issued', 'Release permission RP-20260319-3726 issued for item ßè«ßêØßìÆßïìßë░ßê¡ ßï┤ßêÁßè¡ßëÂßìò (Code: COMP-001)', '2026-03-19 18:34:18'),
(19, 2, 6, 'item_released', 'Item ßè«ßêØßìÆßïìßë░ßê¡ ßï┤ßêÁßè¡ßëÂßìò (Code: COMP-001) released to requester with permission RP-20260319-3726', '2026-03-19 18:34:18');

-- --------------------------------------------------------

--
-- Table structure for table `backup_records`
--

CREATE TABLE `backup_records` (
  `backup_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `backup_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`backup_data`)),
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `backup_records`
--

INSERT INTO `backup_records` (`backup_id`, `request_id`, `backup_data`, `created_by`, `created_at`) VALUES
(1, 1, '{\"request\":{},\"form20_data\":{\"item_description\":\"swdsc\",\"quantity\":\"908\",\"reason\":\"uhkj\"},\"registration\":{}}', 5, '2026-03-18 16:15:27'),
(2, 2, '{\"request\":{},\"form20_data\":{\"item_description\":\"hh\",\"quantity\":\"8\",\"reason\":\"tt\"},\"registration\":{}}', 5, '2026-03-19 18:32:27');

-- --------------------------------------------------------

--
-- Table structure for table `ict_support_requests`
--

CREATE TABLE `ict_support_requests` (
  `support_id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
  `category` enum('hardware','software','network','account','other') DEFAULT 'other',
  `status` enum('pending','in_progress','resolved','closed') DEFAULT 'pending',
  `assigned_to` int(11) DEFAULT NULL,
  `resolution` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resolved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ict_support_requests`
--

INSERT INTO `ict_support_requests` (`support_id`, `requester_id`, `subject`, `description`, `priority`, `category`, `status`, `assigned_to`, `resolution`, `created_at`, `updated_at`, `resolved_at`) VALUES
(1, 3, 'kj,h', 'utjfnv', 'medium', 'hardware', 'resolved', 7, 'guhfyhtfh', '2026-03-20 06:09:31', '2026-03-20 06:25:39', '2026-03-20 06:25:39'),
(2, 1, 'jghuj', 'khhhj', 'medium', 'other', 'in_progress', 7, 'አይሲቲ ሙያተኛ ወደ ቦታው በመሄድ ላይ ነው...', '2026-03-20 06:58:22', '2026-03-20 06:59:57', NULL),
(3, 7, 'jghuj', 'khhhj', 'medium', 'other', 'closed', 7, 'አይሲቲ ሙያተኛ ወደ ቦታው በመሄድ ላይ ነው...', '2026-03-20 07:00:32', '2026-03-20 07:02:12', '2026-03-20 07:02:12');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `item_id` int(11) NOT NULL,
  `item_code` varchar(50) NOT NULL,
  `item_name` varchar(200) NOT NULL,
  `item_description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `quantity_in_stock` int(11) NOT NULL DEFAULT 0,
  `unit_of_measure` varchar(50) DEFAULT NULL,
  `location_in_store` varchar(100) DEFAULT NULL,
  `minimum_stock_level` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_items`
--

INSERT INTO `inventory_items` (`item_id`, `item_code`, `item_name`, `item_description`, `category`, `quantity_in_stock`, `unit_of_measure`, `location_in_store`, `minimum_stock_level`, `created_at`, `updated_at`) VALUES
(1, 'COMP-001', 'ßè«ßêØßìÆßïìßë░ßê¡ ßï┤ßêÁßè¡ßëÂßìò', 'Dell OptiPlex 7090 Desktop Computer', 'ßè«ßêØßìÆßïìßë░ßê¡', 14, 'ßëüßîÑßê¡', 'ßêÿßï░ßê¡ßï░ßê¬ßï½ A-1', 0, '2026-03-18 16:26:41', '2026-03-19 18:32:52'),
(2, 'COMP-002', 'ßêïßìòßëÂßìò', 'HP EliteBook 840 G8 Laptop', 'ßè«ßêØßìÆßïìßë░ßê¡', 10, 'ßëüßîÑßê¡', 'ßêÿßï░ßê¡ßï░ßê¬ßï½ A-2', 0, '2026-03-18 16:26:41', '2026-03-18 16:26:41'),
(3, 'PRNT-001', 'ßìòßê¬ßèòßë░ßê¡', 'HP LaserJet Pro M404dn Printer', 'ßìòßê¬ßèòßë░ßê¡', 8, 'ßëüßîÑßê¡', 'ßêÿßï░ßê¡ßï░ßê¬ßï½ B-1', 0, '2026-03-18 16:26:41', '2026-03-18 16:26:41'),
(4, 'DESK-001', 'ßï¿ßëóßê« ßîáßê¿ßî┤ßïø', 'Office Desk 120x60cm', 'ßï¿ßëóßê« ßèÑßëâßïÄßë¢', 20, 'ßëüßîÑßê¡', 'ßêÿßï░ßê¡ßï░ßê¬ßï½ C-1', 0, '2026-03-18 16:26:41', '2026-03-18 16:26:41'),
(5, 'CHAIR-001', 'ßï¿ßëóßê« ßïêßèòßëáßê¡', 'Ergonomic Office Chair', 'ßï¿ßëóßê« ßèÑßëâßïÄßë¢', 25, 'ßëüßîÑßê¡', 'ßêÿßï░ßê¡ßï░ßê¬ßï½ C-2', 0, '2026-03-18 16:26:41', '2026-03-18 16:26:41'),
(6, 'PHONE-001', 'ßêÁßêìßè¡', 'Cisco IP Phone 7841', 'ßêÁßêìßè¡', 12, 'ßëüßîÑßê¡', 'ßêÿßï░ßê¡ßï░ßê¬ßï½ D-1', 0, '2026-03-18 16:26:41', '2026-03-18 16:26:41'),
(7, 'PROJ-001', 'ßìòßê«ßîÇßè¡ßë░ßê¡', 'Epson EB-X41 Projector', 'ßèñßêîßè¡ßëÁßê«ßèÆßè¡ßêÁ', 5, 'ßëüßîÑßê¡', 'ßêÿßï░ßê¡ßï░ßê¬ßï½ E-1', 0, '2026-03-18 16:26:41', '2026-03-18 16:26:41'),
(8, 'SCAN-001', 'ßêÁßè½ßèÉßê¡', 'Canon imageFORMULA DR-C225', 'ßèñßêîßè¡ßëÁßê«ßèÆßè¡ßêÁ', 6, 'ßëüßîÑßê¡', 'ßêÿßï░ßê¡ßï░ßê¬ßï½ B-2', 0, '2026-03-18 16:26:41', '2026-03-18 16:26:41'),
(9, 'CAB-001', 'ßï¿ßìïßï¡ßêì ßè½ßëóßèößëÁ', 'Filing Cabinet 4 Drawer', 'ßï¿ßëóßê« ßèÑßëâßïÄßë¢', 15, 'ßëüßîÑßê¡', 'ßêÿßï░ßê¡ßï░ßê¬ßï½ C-3', 0, '2026-03-18 16:26:41', '2026-03-18 16:26:41'),
(10, 'WHIT-001', 'ßïïßï¡ßëÁ ßëªßê¡ßïÁ', 'Whiteboard 120x90cm', 'ßï¿ßëóßê« ßèÑßëâßïÄßë¢', 10, 'ßëüßîÑßê¡', 'ßêÿßï░ßê¡ßï░ßê¬ßï½ F-1', 0, '2026-03-18 16:26:41', '2026-03-18 16:26:41');

-- --------------------------------------------------------

--
-- Table structure for table `item_assignments`
--

CREATE TABLE `item_assignments` (
  `assignment_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `requester_identification` varchar(50) NOT NULL,
  `quantity_assigned` int(11) NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item_assignments`
--

INSERT INTO `item_assignments` (`assignment_id`, `request_id`, `item_id`, `requester_id`, `requester_identification`, `quantity_assigned`, `assigned_by`, `assigned_at`, `notes`) VALUES
(4, 1, 1, 1, 'REQ001', 1, 6, '2026-03-18 17:07:40', 'Demo assignment'),
(5, 2, 1, 1, 'REQ001', 1, 6, '2026-03-19 18:32:52', '');

-- --------------------------------------------------------

--
-- Table structure for table `item_registrations`
--

CREATE TABLE `item_registrations` (
  `registration_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `item_description` text NOT NULL,
  `requester_identification` varchar(50) NOT NULL,
  `registered_by` int(11) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `item_registrations`
--

INSERT INTO `item_registrations` (`registration_id`, `request_id`, `item_description`, `requester_identification`, `registered_by`, `registered_at`) VALUES
(1, 1, 'zszs', 'REQ001', 4, '2026-03-18 16:14:05'),
(2, 2, 'zsx', 'REQ001', 4, '2026-03-19 18:32:02');

-- --------------------------------------------------------

--
-- Table structure for table `item_returns`
--

CREATE TABLE `item_returns` (
  `return_id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `quantity_returned` int(11) NOT NULL,
  `return_reason` text DEFAULT NULL,
  `returned_by` int(11) DEFAULT NULL,
  `returned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `request_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `request_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 1, 'ጥያቄዎ ተጠናቋል። የመልቀቅ ፍቃድ ቁጥር: RP-20260318-9214', 1, '2026-03-18 17:10:39'),
(2, 1, 2, 'ጥያቄዎ ተጠናቋል። የመልቀቅ ፍቃድ ቁጥር: RP-20260319-3726', 1, '2026-03-19 18:34:18'),
(3, 3, NULL, 'የአይሲቲ ድጋፍ ጥያቄዎ \"kj,h\" ሁኔታ ወደ በሂደት ላይ ተቀይሯል። መፍትሄ: guhfyhtfh...', 1, '2026-03-20 06:14:51'),
(4, 3, NULL, 'የአይሲቲ ድጋፍ ጥያቄዎ \"kj,h\" ሁኔታ ወደ ተፈትቷል ተቀይሯል።', 0, '2026-03-20 06:25:39'),
(5, 1, NULL, 'የአይሲቲ ድጋፍ ጥያቄዎ \"jghuj\" ሁኔታ ወደ በሂደት ላይ ተቀይሯል። መፍትሄ: አይሲቲ ሙያተኛ ወደ ቦታው በመሄድ ላይ ነው......', 0, '2026-03-20 06:59:57'),
(6, 7, NULL, 'አዲስ የአይሲቲ ድጋፍ ጥያቄ: \"jghuj\" - ቅድሚያ: መካከለኛ, ምድብ: ሌላ', 1, '2026-03-20 07:00:32'),
(7, 7, NULL, 'የአይሲቲ ድጋፍ ጥያቄዎ \"jghuj\" ሁኔታ ወደ በሂደት ላይ ተቀይሯል። መፍትሄ: አይሲቲ ሙያተኛ ወደ ቦታው በመሄድ ላይ ነው......', 1, '2026-03-20 07:00:42'),
(8, 7, NULL, 'የአይሲቲ ድጋፍ ጥያቄዎ \"jghuj\" ሁኔታ ወደ ተዘግቷል ተቀይሯል።', 1, '2026-03-20 07:02:12');

-- --------------------------------------------------------

--
-- Table structure for table `release_permissions`
--

CREATE TABLE `release_permissions` (
  `permission_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `requester_identification` varchar(50) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_code` varchar(50) NOT NULL,
  `item_name` varchar(200) NOT NULL,
  `quantity_released` int(11) NOT NULL,
  `permission_number` varchar(100) NOT NULL,
  `issued_by` int(11) NOT NULL,
  `issued_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `release_permissions`
--

INSERT INTO `release_permissions` (`permission_id`, `request_id`, `requester_id`, `requester_identification`, `item_id`, `item_code`, `item_name`, `quantity_released`, `permission_number`, `issued_by`, `issued_at`, `notes`) VALUES
(1, 1, 1, 'REQ001', 1, 'COMP-001', 'ßè«ßêØßìÆßïìßë░ßê¡ ßï┤ßêÁßè¡ßëÂßìò', 1, 'RP-20260318-9214', 6, '2026-03-18 17:10:39', 'የመልቀቅ ፍቃድ ተሰጥቷል'),
(2, 2, 1, 'REQ001', 1, 'COMP-001', 'ßè«ßêØßìÆßïìßë░ßê¡ ßï┤ßêÁßè¡ßëÂßìò', 1, 'RP-20260319-3726', 6, '2026-03-19 18:34:18', 'የመልቀቅ ፍቃድ ተሰጥቷል');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `request_id` int(11) NOT NULL,
  `requester_id` int(11) NOT NULL,
  `form20_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`form20_data`)),
  `status` enum('pending_requester_main_dept','pending_property_mgmt_main_dept','pending_property_mgmt_dept','pending_registry_office','pending_treasury_release','item_retrieved','completed','rejected_by_requester_main_dept','rejected_by_property_mgmt_main_dept','rejected_by_property_mgmt_dept','rejected_by_registry_office','rejected_by_treasury') NOT NULL DEFAULT 'pending_requester_main_dept',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`request_id`, `requester_id`, `form20_data`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '{\"item_description\":\"swdsc\",\"quantity\":\"908\",\"reason\":\"uhkj\"}', 'completed', '2026-03-18 16:05:14', '2026-03-18 17:10:39'),
(2, 1, '{\"item_description\":\"hh\",\"quantity\":\"8\",\"reason\":\"tt\"}', 'completed', '2026-03-19 18:31:08', '2026-03-19 18:34:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(200) NOT NULL,
  `department` enum('requester','requester_main_dept','property_mgmt_main_dept','property_mgmt_dept','registry_office','treasury','it_admin') NOT NULL,
  `identification_number` varchar(50) NOT NULL,
  `failed_login_attempts` int(11) DEFAULT 0,
  `account_locked_until` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `full_name`, `department`, `identification_number`, `failed_login_attempts`, `account_locked_until`, `created_at`, `updated_at`) VALUES
(1, 'requester1', '$2y$10$NoCrwe2ocWUr3Y9vzcndGexauyD2/ln6mpj9XVpyCIYFxbXJEz.qu', 'ተጠቃሚ አንድ', 'requester', 'REQ001', 0, NULL, '2026-03-18 15:47:32', '2026-03-18 16:48:15'),
(2, 'requester_main1', '$2y$10$Nin4Hf3wYl0GfjQRpCkUguWHKEI3BfQvTt4ho8b.Y.0C59cVVYxeS', 'ጠያቂው ዋና ክፍል አንድ', 'requester_main_dept', 'RMD001', 0, NULL, '2026-03-18 15:47:32', '2026-03-18 16:12:06'),
(3, 'property_main1', '$2y$10$MW9IFgOmSKs/NrM5rEU5EeDaaldXphhEgmuegwR4jBvpr33tAbrcG', 'የንብረት አስተዳደር ዋና ክፍል አንድ', 'property_mgmt_main_dept', 'PMM001', 0, NULL, '2026-03-18 15:47:32', '2026-03-18 15:47:32'),
(4, 'property_dept1', '$2y$10$2Xz0A7lzpYaEpWsHcw2vNuOd1Euk2KVZYp.p7dkT0k92z6vWRUQHC', 'የንብረት አስተዳደር ክፍል አንድ', 'property_mgmt_dept', 'PMD001', 0, NULL, '2026-03-18 15:47:32', '2026-03-18 15:47:32'),
(5, 'registry1', '$2y$10$e2bIMQkPB4qI5kpGgSMcneTLGWKkJWw1Jy6dOHxeI7DMpxsGhM1.S', 'መዝገብ ቤት አንድ', 'registry_office', 'REG001', 0, NULL, '2026-03-18 15:47:32', '2026-03-18 15:47:32'),
(6, 'treasury1', '$2y$10$6O47XaD9Tas.WwAR8M/MZuUUdnuH45M/BHMtvG26tYIsIofhZMDLi', 'ግምጃ ቤት አንድ', 'treasury', 'TRS001', 0, NULL, '2026-03-18 15:47:32', '2026-03-18 15:47:32'),
(7, 'mengistu', '$2y$10$dXWM4v6zVA.n98.J2mbVu.s2SZJok3r5LCK/DkUewpiXMXXLAbHGi', 'mengistu nuru', 'it_admin', '82924', 0, NULL, '2026-03-19 20:23:17', '2026-03-19 20:51:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approvals`
--
ALTER TABLE `approvals`
  ADD PRIMARY KEY (`approval_id`),
  ADD KEY `idx_request` (`request_id`),
  ADD KEY `idx_approver` (`approver_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_request` (`request_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_action` (`action`);

--
-- Indexes for table `backup_records`
--
ALTER TABLE `backup_records`
  ADD PRIMARY KEY (`backup_id`),
  ADD UNIQUE KEY `request_id` (`request_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_request` (`request_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `ict_support_requests`
--
ALTER TABLE `ict_support_requests`
  ADD PRIMARY KEY (`support_id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `idx_requester` (`requester_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_priority` (`priority`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`item_id`),
  ADD UNIQUE KEY `item_code` (`item_code`),
  ADD KEY `idx_item_code` (`item_code`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_stock` (`quantity_in_stock`);

--
-- Indexes for table `item_assignments`
--
ALTER TABLE `item_assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `assigned_by` (`assigned_by`),
  ADD KEY `idx_request` (`request_id`),
  ADD KEY `idx_item` (`item_id`),
  ADD KEY `idx_requester` (`requester_id`);

--
-- Indexes for table `item_registrations`
--
ALTER TABLE `item_registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD UNIQUE KEY `request_id` (`request_id`),
  ADD KEY `registered_by` (`registered_by`),
  ADD KEY `idx_requester_id` (`requester_identification`),
  ADD KEY `idx_registered_at` (`registered_at`);

--
-- Indexes for table `item_returns`
--
ALTER TABLE `item_returns`
  ADD PRIMARY KEY (`return_id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `returned_by` (`returned_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `request_id` (`request_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `release_permissions`
--
ALTER TABLE `release_permissions`
  ADD PRIMARY KEY (`permission_id`),
  ADD UNIQUE KEY `request_id` (`request_id`),
  ADD UNIQUE KEY `permission_number` (`permission_number`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `issued_by` (`issued_by`),
  ADD KEY `idx_request` (`request_id`),
  ADD KEY `idx_requester` (`requester_id`),
  ADD KEY `idx_permission_number` (`permission_number`),
  ADD KEY `idx_issued_at` (`issued_at`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_requester` (`requester_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `identification_number` (`identification_number`),
  ADD KEY `idx_department` (`department`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_identification` (`identification_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approvals`
--
ALTER TABLE `approvals`
  MODIFY `approval_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `backup_records`
--
ALTER TABLE `backup_records`
  MODIFY `backup_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ict_support_requests`
--
ALTER TABLE `ict_support_requests`
  MODIFY `support_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `item_assignments`
--
ALTER TABLE `item_assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `item_registrations`
--
ALTER TABLE `item_registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `item_returns`
--
ALTER TABLE `item_returns`
  MODIFY `return_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `release_permissions`
--
ALTER TABLE `release_permissions`
  MODIFY `permission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approvals`
--
ALTER TABLE `approvals`
  ADD CONSTRAINT `approvals_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approvals_ibfk_2` FOREIGN KEY (`approver_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `audit_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `backup_records`
--
ALTER TABLE `backup_records`
  ADD CONSTRAINT `backup_records_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `backup_records_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `ict_support_requests`
--
ALTER TABLE `ict_support_requests`
  ADD CONSTRAINT `ict_support_requests_ibfk_1` FOREIGN KEY (`requester_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `ict_support_requests_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `item_assignments`
--
ALTER TABLE `item_assignments`
  ADD CONSTRAINT `item_assignments_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_assignments_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`item_id`),
  ADD CONSTRAINT `item_assignments_ibfk_3` FOREIGN KEY (`requester_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `item_assignments_ibfk_4` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `item_registrations`
--
ALTER TABLE `item_registrations`
  ADD CONSTRAINT `item_registrations_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_registrations_ibfk_2` FOREIGN KEY (`registered_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `item_returns`
--
ALTER TABLE `item_returns`
  ADD CONSTRAINT `item_returns_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `item_assignments` (`assignment_id`),
  ADD CONSTRAINT `item_returns_ibfk_2` FOREIGN KEY (`returned_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE;

--
-- Constraints for table `release_permissions`
--
ALTER TABLE `release_permissions`
  ADD CONSTRAINT `release_permissions_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `requests` (`request_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `release_permissions_ibfk_2` FOREIGN KEY (`requester_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `release_permissions_ibfk_3` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`item_id`),
  ADD CONSTRAINT `release_permissions_ibfk_4` FOREIGN KEY (`issued_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_ibfk_1` FOREIGN KEY (`requester_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
