-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 24, 2024 at 09:06 AM
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
-- Database: `angat_sikat_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `balance_history`
--

CREATE TABLE `balance_history` (
  `history_id` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `balance` decimal(15,2) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budget_allocation`
--

CREATE TABLE `budget_allocation` (
  `allocation_id` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `category` enum('Activities','Purchases','Maintenance and Other Expenses') NOT NULL,
  `allocated_budget` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_spent` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budget_allocation`
--

INSERT INTO `budget_allocation` (`allocation_id`, `organization_id`, `category`, `allocated_budget`, `total_spent`, `created_at`, `updated_at`) VALUES
(1, 1, 'Activities', 50000.00, 27500.00, '2024-09-22 17:46:30', '2024-12-24 06:42:15'),
(2, 1, 'Purchases', 6500.00, 1200.00, '2024-09-22 17:46:30', '2024-10-08 17:26:50'),
(3, 1, 'Maintenance and Other Expenses', 28000.00, 800.00, '2024-09-22 17:46:30', '2024-10-08 17:26:33'),
(4, 3, 'Activities', 60000.00, 0.00, '2024-12-23 14:12:49', '2024-12-23 14:12:49'),
(6, 4, 'Activities', 25000.00, 0.00, '2024-12-23 14:27:01', '2024-12-23 14:27:01');

-- --------------------------------------------------------

--
-- Table structure for table `budget_approvals`
--

CREATE TABLE `budget_approvals` (
  `approval_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Disapproved') DEFAULT 'Pending',
  `organization_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budget_approvals`
--

INSERT INTO `budget_approvals` (`approval_id`, `title`, `category`, `attachment`, `status`, `organization_id`, `created_at`, `archived`) VALUES
(23, 'Test MOE', 'Maintenance', 'lesson-1.pdf', 'Approved', 1, '2024-12-03 14:20:20', 0),
(24, 'Test Purchase', 'Purchases', 'lesson-1.pdf', 'Approved', 1, '2024-12-03 14:20:50', 0),
(25, 'Test Event', 'Activities', 'lesson-1.pdf', 'Approved', 1, '2024-12-03 14:25:28', 0),
(26, 'Test Expense Event ', 'Activities', 'lesson-1.pdf', 'Approved', 1, '2024-12-03 14:25:43', 1),
(34, 'Test Event 3', 'Activities', 'lesson-1.pdf', 'Approved', 1, '2024-12-10 15:09:28', 0),
(35, 'Test Expense Event 2', 'Activities', 'lesson-1.pdf', 'Approved', 1, '2024-12-10 18:14:56', 0),
(36, 'Merchandise Sale', 'Activities', 'Lecture-2-ITEC-100.docx', 'Approved', 1, '2024-12-22 17:10:16', 0),
(37, 'Eduk Week', 'Activities', 'byte.png', 'Approved', 3, '2024-12-23 14:20:00', 0),
(38, 'TechFusion', 'Activities', 'lesson-1.pdf', 'Approved', 4, '2024-12-23 16:54:14', 0);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category`) VALUES
(1, 'Activities'),
(2, 'Purchases'),
(3, 'Maintenance and Other Expenses');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `event_venue` varchar(255) NOT NULL,
  `event_start_date` date NOT NULL,
  `event_end_date` date NOT NULL,
  `event_type` varchar(100) DEFAULT NULL,
  `event_status` enum('Pending','Approved','Disapproved') NOT NULL,
  `accomplishment_status` tinyint(1) DEFAULT 0,
  `total_amount` decimal(15,2) NOT NULL,
  `total_profit` decimal(15,2) NOT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `archived` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `plan_id`, `title`, `event_venue`, `event_start_date`, `event_end_date`, `event_type`, `event_status`, `accomplishment_status`, `total_amount`, `total_profit`, `organization_id`, `created_by`, `created_at`, `archived`) VALUES
(21, 1, 'Test Event', 'Court I', '2024-12-06', '2024-12-07', 'Income', 'Approved', 1, 2.00, 1.00, 1, NULL, '2024-12-03 10:01:13', 1),
(22, 4, 'Test Expense Event ', 'Court I', '2024-12-03', '2024-12-04', 'Expense', 'Approved', 0, 0.00, 0.00, 1, NULL, '2024-12-03 12:51:09', 0),
(23, 5, 'Test Expense Event 2', 'Court I', '2024-12-07', '2024-12-07', 'Expense', 'Approved', 0, 0.00, 0.00, 1, NULL, '2024-12-03 16:54:48', 0),
(24, 6, 'TechFusion', 'Court I', '2024-12-09', '2024-12-10', 'Expense', 'Approved', 1, 25000.00, 0.00, 1, NULL, '2024-12-09 08:40:53', 0),
(25, 1, 'Merchandise Sale', 'DCS', '2024-12-06', '2024-12-25', 'Income', 'Approved', 1, 0.00, 0.00, 1, NULL, '2024-12-22 16:53:24', 0),
(26, 7, 'Eduk Week', 'DTE', '2024-12-23', '2024-12-27', 'Expense', 'Approved', 0, 0.00, 0.00, 3, NULL, '2024-12-23 14:19:41', 0),
(27, 8, 'TechFusion', 'DCS', '2024-12-31', '2025-01-04', 'Income', 'Approved', 0, 0.00, 0.00, 4, NULL, '2024-12-23 16:36:09', 0);

-- --------------------------------------------------------

--
-- Table structure for table `events_summary`
--

CREATE TABLE `events_summary` (
  `summary_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `venue` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `type` varchar(255) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `total_profit` decimal(15,2) NOT NULL,
  `status` enum('Pending','Approved','Disapproved') NOT NULL,
  `accomplishment_status` tinyint(4) DEFAULT NULL,
  `archived` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events_summary`
--

INSERT INTO `events_summary` (`summary_id`, `event_id`, `title`, `venue`, `start_date`, `end_date`, `type`, `organization_id`, `total_amount`, `total_profit`, `status`, `accomplishment_status`, `archived`, `created_at`, `updated_at`) VALUES
(1, 21, 'Test Event', 'Court I', '2024-12-06', '2024-12-07', 'Income', 1, 2.00, 1.00, 'Approved', NULL, 0, '2024-12-03 16:44:19', '2024-12-09 08:42:45'),
(2, 21, 'Test Event', 'Court I', '2024-12-06', '2024-12-07', 'Income', 1, 2.00, 1.00, 'Approved', NULL, 0, '2024-12-09 08:25:18', '2024-12-09 08:42:45'),
(3, 21, 'Test Event', 'Court I', '2024-12-06', '2024-12-07', 'Income', 1, 2.00, 1.00, 'Approved', NULL, 0, '2024-12-09 08:25:39', '2024-12-09 08:42:45'),
(4, 21, 'Test Event', 'Court I', '2024-12-06', '2024-12-07', 'Income', 1, 2.00, 1.00, 'Approved', NULL, 0, '2024-12-09 08:26:06', '2024-12-09 08:42:45'),
(5, 21, 'Test Event', 'Court I', '2024-12-06', '2024-12-07', 'Income', 1, 0.00, 0.00, 'Approved', NULL, 0, '2024-12-22 16:23:14', '2024-12-22 16:23:14'),
(6, 25, 'Merchandise Sale', 'DCS', '2024-12-06', '2024-12-25', 'Income', 1, 73500.00, 25500.00, 'Approved', NULL, 0, '2024-12-23 18:49:30', '2024-12-23 19:34:32'),
(7, 24, 'TechFusion', 'Court I', '2024-12-09', '2024-12-10', 'Expense', 1, 25000.00, 0.00, 'Approved', NULL, 0, '2024-12-23 19:45:15', '2024-12-24 06:11:23');

-- --------------------------------------------------------

--
-- Table structure for table `event_items`
--

CREATE TABLE `event_items` (
  `item_id` int(11) NOT NULL,
  `event_id` int(11) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `profit` decimal(15,2) NOT NULL,
  `total_profit` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_items`
--

INSERT INTO `event_items` (`item_id`, `event_id`, `description`, `quantity`, `unit`, `amount`, `total_amount`, `profit`, `total_profit`) VALUES
(1, 5, 'Test I', 5, '1', 30.00, 150.00, 0.00, 0.00),
(2, NULL, 'Test I', 5, '1', 30.00, 150.00, 0.00, 0.00),
(5, 6, 'Food Allowance', 20, '0', 45.00, 900.00, 0.00, 0.00),
(8, 7, 'Test', 5, '1', 46.00, 230.00, 0.00, 0.00),
(9, 10, 'Speakers', 2, '1', 40.00, 80.00, 0.00, 0.00),
(10, 10, 'Laptop', 1, '1', 25.00, 25.00, 0.00, 0.00),
(11, 10, 'Chairs', 50, '1', 30.00, 1500.00, 0.00, 0.00),
(13, 13, 'Food Allowance', 40, '1', 75.00, 3000.00, 0.00, 0.00),
(14, 19, 'Food Allowance', 40, '1', 75.00, 3000.00, 0.00, 0.00),
(15, 8, 'BYTE Lanyard', 300, '1', 80.00, 24000.00, 0.00, 0.00),
(16, 8, 'BYTE Shirt', 300, '1', 400.00, 120000.00, 0.00, 0.00),
(17, 20, 'Multimedia Fees', 1, '1', 1000.00, 1000.00, 0.00, 0.00),
(23, 21, 'Test', 1, '1', 1.00, 2.00, 1.00, 1.00),
(24, 24, 'Test', 1, '1', 25000.00, 25000.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `event_summary_items`
--

CREATE TABLE `event_summary_items` (
  `summary_item_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `profit` decimal(15,2) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `total_profit` decimal(15,2) NOT NULL,
  `reference` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_summary_items`
--

INSERT INTO `event_summary_items` (`summary_item_id`, `event_id`, `description`, `quantity`, `unit`, `amount`, `profit`, `total_amount`, `total_profit`, `reference`) VALUES
(1, 21, 'Test', 1, 1, 1.00, 1.00, 2.00, 1.00, 'lesson-1.pdf'),
(2, 25, 'BYTE Shirt', 100, 1, 400.00, 225.00, 62500.00, 22500.00, 'lesson-1.pdf'),
(3, 25, 'BYTE Lanyard', 100, 1, 80.00, 30.00, 11000.00, 3000.00, 'lesson-1.pdf'),
(4, 24, 'Test', 1, 1, 25000.00, 0.00, 25000.00, 0.00, 'lesson-1.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `category` enum('Activities','Purchases','Maintenance') NOT NULL,
  `title` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`expense_id`, `organization_id`, `category`, `title`, `amount`, `reference`, `created_at`) VALUES
(12, 1, 'Activities', 'TechFusion', 25000.00, 'lesson-1.pdf', '2024-12-24 06:42:15');

-- --------------------------------------------------------

--
-- Table structure for table `financial_plan`
--

CREATE TABLE `financial_plan` (
  `plan_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` enum('Activities','Purchases','Maintenance and Other Expenses') NOT NULL,
  `organization_id` int(11) NOT NULL,
  `type` enum('Income','Expense') NOT NULL,
  `date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `financial_plan`
--

INSERT INTO `financial_plan` (`plan_id`, `title`, `category`, `organization_id`, `type`, `date`, `amount`) VALUES
(1, 'Merchandise Sale', '', 1, 'Income', '2024-12-06', 100000.00),
(2, 'Test MOE', 'Maintenance and Other Expenses', 1, 'Expense', '0000-00-00', 5000.00),
(3, 'Test Purchase', 'Purchases', 1, 'Expense', '0000-00-00', 5000.00),
(4, 'Test Expense Event ', 'Activities', 1, 'Expense', '2024-12-03', 15000.00),
(5, 'Test Expense Event 2', 'Activities', 1, 'Expense', '2024-12-07', 10000.00),
(6, 'TechFusion', 'Activities', 1, 'Expense', '2024-12-09', 25000.00),
(7, 'Eduk Week', 'Activities', 3, 'Expense', '2024-12-23', 50000.00),
(8, 'TechFusion', '', 4, 'Income', '2024-12-31', 100000.00);

-- --------------------------------------------------------

--
-- Table structure for table `income`
--

CREATE TABLE `income` (
  `income_id` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `income`
--

INSERT INTO `income` (`income_id`, `organization_id`, `category`, `title`, `amount`, `reference`, `created_at`) VALUES
(1, 1, '', 'Merchandise Sale', 25500.00, 'lesson-1.pdf', '2024-12-24 07:23:42');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

CREATE TABLE `maintenance` (
  `maintenance_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `maintenance_status` enum('Pending','Approved','Disapproved') DEFAULT 'Pending',
  `completion_status` tinyint(1) DEFAULT 0,
  `organization_id` int(11) NOT NULL,
  `archived` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance`
--

INSERT INTO `maintenance` (`maintenance_id`, `plan_id`, `title`, `total_amount`, `maintenance_status`, `completion_status`, `organization_id`, `archived`, `created_at`, `updated_at`) VALUES
(3, 2, 'Test MOE', 500.00, 'Approved', 0, 1, 0, '2024-12-03 12:44:50', '2024-12-03 14:28:43');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_items`
--

CREATE TABLE `maintenance_items` (
  `item_id` int(11) NOT NULL,
  `maintenance_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance_items`
--

INSERT INTO `maintenance_items` (`item_id`, `maintenance_id`, `description`, `quantity`, `unit`, `amount`, `total_amount`) VALUES
(1, 3, 'Test MOE Item', 10, 1, 50.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_summary`
--

CREATE TABLE `maintenance_summary` (
  `summary_id` int(11) NOT NULL,
  `maintenance_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `status` enum('Pending','Approved','Disapproved') NOT NULL,
  `completion_status` tinyint(4) DEFAULT 0,
  `archived` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_summary_items`
--

CREATE TABLE `maintenance_summary_items` (
  `summary_item_id` int(11) NOT NULL,
  `maintenance_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `reference` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `recipient_id`, `organization_id`, `message`, `is_read`, `created_at`) VALUES
(1, 3, 0, 'A new budget approval request titled \'Test Expense Event 2\' has been submitted.', 1, '2024-12-09 00:48:27'),
(2, 3, 0, 'A new budget approval request for \'Test Expense Event 2\' has been submitted.', 1, '2024-12-09 01:36:28'),
(3, 1, 1, 'Your budget request for \'Test Expense Event 2\' has been approved.', 0, '2024-12-09 04:00:10'),
(4, 2, 1, 'Your budget request for \'Test Expense Event 2\' has been approved.', 0, '2024-12-09 04:00:10'),
(5, 3, 1, 'Your budget request for \'Test Expense Event 2\' has been approved.', 0, '2024-12-09 04:00:10'),
(6, 1, 1, 'Your budget request for \'Test Expense Event 2\' has been approved.', 0, '2024-12-09 16:34:24'),
(7, 2, 1, 'Your budget request for \'Test Expense Event 2\' has been approved.', 0, '2024-12-09 16:34:24'),
(8, 3, 1, 'Your budget request for \'Test Expense Event 2\' has been approved.', 0, '2024-12-09 16:34:24'),
(9, 3, 0, 'A new budget approval request for \'Test Expense Event 2\' has been submitted.', 0, '2024-12-10 23:06:22'),
(10, 1, 1, 'Your budget request for \'Test Expense Event 2\' has been approved.', 0, '2024-12-10 23:06:51'),
(11, 2, 1, 'Your budget request for \'Test Expense Event 2\' has been approved.', 0, '2024-12-10 23:06:51'),
(12, 3, 1, 'Your budget request for \'Test Expense Event 2\' has been approved.', 0, '2024-12-10 23:06:51'),
(13, 3, 0, 'A new budget approval request for \'Test Event 3\' has been submitted.', 0, '2024-12-10 23:09:28'),
(14, 1, 1, 'Your budget request for \'Test Event 3\' has been approved.', 0, '2024-12-10 23:10:21'),
(15, 2, 1, 'Your budget request for \'Test Event 3\' has been approved.', 0, '2024-12-10 23:10:21'),
(16, 3, 1, 'Your budget request for \'Test Event 3\' has been approved.', 0, '2024-12-10 23:10:21'),
(17, 3, 0, 'A new budget approval request for \'Test Expense Event 2\' has been submitted.', 0, '2024-12-11 02:14:56'),
(18, 1, 1, 'Your budget request for \'Test Expense Event 2\' has been approved.', 0, '2024-12-11 02:15:36'),
(19, 2, 1, 'Your budget request for \'Test Expense Event 2\' has been approved.', 0, '2024-12-11 02:15:36'),
(20, 3, 1, 'Your budget request for \'Test Expense Event 2\' has been approved.', 0, '2024-12-11 02:15:36'),
(21, 3, 0, 'A new budget approval request for \'Merchandise Sale\' has been submitted.', 0, '2024-12-23 01:10:16'),
(22, 3, 0, 'A new budget approval request for \'Eduk Week\' has been submitted.', 0, '2024-12-23 22:20:00'),
(23, 5, 3, 'Your budget request for \'Eduk Week\' has been approved.', 0, '2024-12-23 22:21:25'),
(24, 3, 0, 'A new budget approval request for \'TechFusion\' has been submitted.', 0, '2024-12-24 00:54:14'),
(25, 6, 4, 'Your budget request for \'TechFusion\' has been approved.', 0, '2024-12-24 01:00:31'),
(26, 1, 1, 'Your budget request for \'Merchandise Sale\' has been approved.', 0, '2024-12-24 02:48:34'),
(27, 2, 1, 'Your budget request for \'Merchandise Sale\' has been approved.', 0, '2024-12-24 02:48:34'),
(28, 3, 1, 'Your budget request for \'Merchandise Sale\' has been approved.', 0, '2024-12-24 02:48:34');

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `organization_id` int(11) NOT NULL,
  `organization_name` varchar(255) NOT NULL,
  `organization_logo` varchar(255) DEFAULT NULL,
  `organization_members` int(11) NOT NULL DEFAULT 0,
  `organization_status` enum('Probationary','Level I','Level II') NOT NULL DEFAULT 'Probationary',
  `organization_color` varchar(7) DEFAULT NULL,
  `archived` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `balance` decimal(15,2) DEFAULT 0.00,
  `beginning_balance` decimal(15,2) DEFAULT 0.00,
  `income` decimal(15,2) DEFAULT 0.00,
  `expense` decimal(15,2) DEFAULT 0.00,
  `cash_on_bank` decimal(15,2) DEFAULT 0.00,
  `cash_on_hand` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`organization_id`, `organization_name`, `organization_logo`, `organization_members`, `organization_status`, `organization_color`, `archived`, `created_at`, `balance`, `beginning_balance`, `income`, `expense`, `cash_on_bank`, `cash_on_hand`) VALUES
(1, 'Beacon of Youth Technology Enthusiasts', 'byte.png', 500, 'Level I', '#1c7d60', 0, '2024-09-22 12:16:55', 72500.00, 100000.00, 0.00, 0.00, 22000.00, 500.00),
(3, 'Future Educators Organization', 'feo.png', 400, 'Level I', '#3193b4', 0, '2024-12-22 17:13:27', 100000.00, 100000.00, 0.00, 0.00, 0.00, 0.00),
(4, 'Computer Scientists and Developers Society', 'code.png', 400, 'Level I', '#3f99ee', 0, '2024-12-22 17:50:51', 125000.00, 125000.00, 0.00, 0.00, 125000.00, 0.00),
(5, 'Artrads Dance Crew', 'logo_67695ef3d92895.89449814.png', 35, 'Level I', '#f9db1a', 0, '2024-12-23 13:00:35', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `purchase_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `purchase_status` enum('Pending','Approved','Disapproved') NOT NULL DEFAULT 'Pending',
  `completion_status` tinyint(1) DEFAULT 0,
  `archived` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `organization_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`purchase_id`, `plan_id`, `title`, `total_amount`, `purchase_status`, `completion_status`, `archived`, `created_at`, `organization_id`) VALUES
(3, 3, 'Test Purchase', 1001.00, 'Approved', 0, 0, '2024-12-03 11:40:19', 1);

-- --------------------------------------------------------

--
-- Table structure for table `purchases_summary`
--

CREATE TABLE `purchases_summary` (
  `summary_id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `organization_id` int(11) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `status` enum('Pending','Approved','Disapproved') NOT NULL,
  `completion_status` tinyint(4) DEFAULT 0,
  `archived` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `item_id` int(11) NOT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) GENERATED ALWAYS AS (`quantity` * `amount`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`item_id`, `purchase_id`, `description`, `quantity`, `unit`, `amount`) VALUES
(2, 3, 'Test', 1.00, '1', 1001.00);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_summary_items`
--

CREATE TABLE `purchase_summary_items` (
  `summary_item_id` int(11) NOT NULL,
  `purchase_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `reference` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','officer','member') NOT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `archived` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `first_name`, `last_name`, `email`, `role`, `organization_id`, `archived`, `created_at`) VALUES
(1, 'sayilisayily', '1234', 'Zylei', 'Sugue', 'zylei.sugue@cvsu.edu.ph', 'officer', 1, 1, '2024-12-23 16:21:49'),
(2, 'JerichoPao', '$2y$10$ppHUUKHxwQYNGTLdedFzg.0XpCbrIwcw7ShYBQ.E5yIDnzlyqQhrO', 'Jericho', 'Pao', 'jerichopao@gmail.com', 'officer', 1, 0, '2024-12-23 16:21:49'),
(3, 'admin', '$2y$10$ZhjHxFaq77LMDZK1WSfss.w6QvlSROnTpjIE9Gov/wb7soNaNY/f6', 'Admin', '', 'admin@mail.com', 'admin', 1, 0, '2024-12-23 16:21:49'),
(5, 'irhyll', '$2y$10$.g2up32AHDND7JWmDtrDpuqa4srZUD.3OoEj8sSTsoSsbWqaY9jJC', 'James Irhyll', 'Dela Cruz', 'irhyll@cvsu.edu.ph', 'officer', 3, 0, '2024-12-23 16:21:49'),
(6, 'Marielle', '$2y$10$HL2yGbXp1gekNXSixvR0Ae4uh/l6AvDS/G4S/CEEKozFQ1Si0x6la', 'Marielle', 'Martires', 'marielle@cvsu.edu.ph', 'officer', 4, 0, '2024-12-23 16:21:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `balance_history`
--
ALTER TABLE `balance_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `budget_allocation`
--
ALTER TABLE `budget_allocation`
  ADD PRIMARY KEY (`allocation_id`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `budget_approvals`
--
ALTER TABLE `budget_approvals`
  ADD PRIMARY KEY (`approval_id`),
  ADD KEY `fk_organization` (`organization_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `organization_id` (`organization_id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `fk_events_plan_id` (`plan_id`);

--
-- Indexes for table `events_summary`
--
ALTER TABLE `events_summary`
  ADD PRIMARY KEY (`summary_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `event_items`
--
ALTER TABLE `event_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `event_summary_items`
--
ALTER TABLE `event_summary_items`
  ADD PRIMARY KEY (`summary_item_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `financial_plan`
--
ALTER TABLE `financial_plan`
  ADD PRIMARY KEY (`plan_id`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `income`
--
ALTER TABLE `income`
  ADD PRIMARY KEY (`income_id`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD PRIMARY KEY (`maintenance_id`),
  ADD KEY `fk_organization_maintenance` (`organization_id`),
  ADD KEY `fk_maintenance_plan_id` (`plan_id`);

--
-- Indexes for table `maintenance_items`
--
ALTER TABLE `maintenance_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `maintenance_id` (`maintenance_id`);

--
-- Indexes for table `maintenance_summary`
--
ALTER TABLE `maintenance_summary`
  ADD PRIMARY KEY (`summary_id`),
  ADD KEY `maintenance_id` (`maintenance_id`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `maintenance_summary_items`
--
ALTER TABLE `maintenance_summary_items`
  ADD PRIMARY KEY (`summary_item_id`),
  ADD KEY `maintenance_id` (`maintenance_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipient_id` (`recipient_id`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`organization_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`purchase_id`),
  ADD KEY `fk_organization_id` (`organization_id`),
  ADD KEY `fk_purchases_plan_id` (`plan_id`);

--
-- Indexes for table `purchases_summary`
--
ALTER TABLE `purchases_summary`
  ADD PRIMARY KEY (`summary_id`),
  ADD KEY `purchase_id` (`purchase_id`),
  ADD KEY `organization_id` (`organization_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `purchase_id` (`purchase_id`);

--
-- Indexes for table `purchase_summary_items`
--
ALTER TABLE `purchase_summary_items`
  ADD PRIMARY KEY (`summary_item_id`),
  ADD KEY `purchase_id` (`purchase_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `organization_id` (`organization_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `balance_history`
--
ALTER TABLE `balance_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `budget_allocation`
--
ALTER TABLE `budget_allocation`
  MODIFY `allocation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `budget_approvals`
--
ALTER TABLE `budget_approvals`
  MODIFY `approval_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `events_summary`
--
ALTER TABLE `events_summary`
  MODIFY `summary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `event_items`
--
ALTER TABLE `event_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `event_summary_items`
--
ALTER TABLE `event_summary_items`
  MODIFY `summary_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `financial_plan`
--
ALTER TABLE `financial_plan`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `income`
--
ALTER TABLE `income`
  MODIFY `income_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `maintenance`
--
ALTER TABLE `maintenance`
  MODIFY `maintenance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `maintenance_items`
--
ALTER TABLE `maintenance_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `maintenance_summary`
--
ALTER TABLE `maintenance_summary`
  MODIFY `summary_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_summary_items`
--
ALTER TABLE `maintenance_summary_items`
  MODIFY `summary_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `organization_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `purchase_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purchases_summary`
--
ALTER TABLE `purchases_summary`
  MODIFY `summary_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `purchase_summary_items`
--
ALTER TABLE `purchase_summary_items`
  MODIFY `summary_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `balance_history`
--
ALTER TABLE `balance_history`
  ADD CONSTRAINT `balance_history_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`organization_id`) ON DELETE CASCADE;

--
-- Constraints for table `budget_allocation`
--
ALTER TABLE `budget_allocation`
  ADD CONSTRAINT `budget_allocation_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`organization_id`) ON DELETE CASCADE;

--
-- Constraints for table `budget_approvals`
--
ALTER TABLE `budget_approvals`
  ADD CONSTRAINT `fk_organization` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`organization_id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`organization_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_events_plan_id` FOREIGN KEY (`plan_id`) REFERENCES `financial_plan` (`plan_id`);

--
-- Constraints for table `events_summary`
--
ALTER TABLE `events_summary`
  ADD CONSTRAINT `events_summary_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `events_summary_ibfk_2` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`organization_id`) ON DELETE CASCADE;

--
-- Constraints for table `event_items`
--
ALTER TABLE `event_items`
  ADD CONSTRAINT `event_items_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`);

--
-- Constraints for table `event_summary_items`
--
ALTER TABLE `event_summary_items`
  ADD CONSTRAINT `event_summary_items_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`organization_id`) ON DELETE CASCADE;

--
-- Constraints for table `financial_plan`
--
ALTER TABLE `financial_plan`
  ADD CONSTRAINT `financial_plan_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`organization_id`) ON DELETE CASCADE;

--
-- Constraints for table `income`
--
ALTER TABLE `income`
  ADD CONSTRAINT `income_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`organization_id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance`
--
ALTER TABLE `maintenance`
  ADD CONSTRAINT `fk_maintenance_plan_id` FOREIGN KEY (`plan_id`) REFERENCES `financial_plan` (`plan_id`),
  ADD CONSTRAINT `fk_organization_maintenance` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`organization_id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_items`
--
ALTER TABLE `maintenance_items`
  ADD CONSTRAINT `maintenance_items_ibfk_1` FOREIGN KEY (`maintenance_id`) REFERENCES `maintenance` (`maintenance_id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_summary`
--
ALTER TABLE `maintenance_summary`
  ADD CONSTRAINT `maintenance_summary_ibfk_1` FOREIGN KEY (`maintenance_id`) REFERENCES `maintenance` (`maintenance_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_summary_ibfk_2` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`organization_id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_summary_items`
--
ALTER TABLE `maintenance_summary_items`
  ADD CONSTRAINT `maintenance_summary_items_ibfk_1` FOREIGN KEY (`maintenance_id`) REFERENCES `maintenance` (`maintenance_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `fk_organization_id` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`organization_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_purchases_plan_id` FOREIGN KEY (`plan_id`) REFERENCES `financial_plan` (`plan_id`);

--
-- Constraints for table `purchases_summary`
--
ALTER TABLE `purchases_summary`
  ADD CONSTRAINT `purchases_summary_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`purchase_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchases_summary_ibfk_2` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`organization_id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`purchase_id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_summary_items`
--
ALTER TABLE `purchase_summary_items`
  ADD CONSTRAINT `purchase_summary_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`purchase_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`organization_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
