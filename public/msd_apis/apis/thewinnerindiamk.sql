-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2026 at 04:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `thewinnerindiamk`
--

-- --------------------------------------------------------

--
-- Table structure for table `commission_rates`
--

CREATE TABLE `commission_rates` (
  `level` int(11) NOT NULL,
  `percentage` decimal(5,4) NOT NULL COMMENT 'As decimal (0.25 for 25%)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commission_rates`
--

INSERT INTO `commission_rates` (`level`, `percentage`) VALUES
(1, 0.2500),
(2, 0.0500),
(3, 0.0400),
(4, 0.0200),
(5, 0.0100),
(6, 0.0100),
(7, 0.0100),
(8, 0.0100),
(9, 0.0100),
(10, 0.0100),
(11, 0.0050),
(12, 0.0050),
(13, 0.0050),
(14, 0.0050),
(15, 0.0050),
(16, 0.0050),
(17, 0.0100),
(18, 0.0100),
(19, 0.0100),
(20, 0.0200);

-- --------------------------------------------------------

--
-- Table structure for table `daily_payout_log`
--

CREATE TABLE `daily_payout_log` (
  `id` int(11) NOT NULL,
  `investment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payout_date` date NOT NULL,
  `calculated_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_payout_log`
--

INSERT INTO `daily_payout_log` (`id`, `investment_id`, `user_id`, `payout_date`, `calculated_amount`, `created_at`) VALUES
(1, 3, 2, '2025-12-09', 250.00, '2025-12-08 18:43:55'),
(10, 3, 2, '2025-12-10', 250.00, '2025-12-10 08:10:18'),
(11, 4, 2, '2025-12-10', 1250.00, '2025-12-10 08:10:18'),
(14, 3, 2, '2025-12-11', 250.00, '2025-12-12 09:20:25'),
(15, 3, 2, '2025-12-12', 250.00, '2025-12-12 09:20:25'),
(17, 4, 2, '2025-12-11', 1250.00, '2025-12-12 09:20:26'),
(18, 4, 2, '2025-12-12', 1250.00, '2025-12-12 09:20:26');

-- --------------------------------------------------------

--
-- Table structure for table `investments`
--

CREATE TABLE `investments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `daily_rate` decimal(10,5) NOT NULL DEFAULT 0.00250,
  `initial_daily_payout` decimal(12,2) NOT NULL,
  `last_payout_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `investments`
--

INSERT INTO `investments` (`id`, `user_id`, `amount`, `daily_rate`, `initial_daily_payout`, `last_payout_date`, `created_at`) VALUES
(3, 2, 100000.00, 0.00250, 250.00, NULL, '2025-12-07 18:33:59'),
(4, 2, 500000.00, 0.00250, 1250.00, NULL, '2025-12-08 19:18:17');

-- --------------------------------------------------------

--
-- Table structure for table `payout_summary`
--

CREATE TABLE `payout_summary` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_investment` decimal(12,2) NOT NULL DEFAULT 0.00,
  `days_completed` int(11) NOT NULL DEFAULT 0,
  `total_paid` decimal(12,2) NOT NULL DEFAULT 0.00,
  `remaining_payout` decimal(12,2) NOT NULL DEFAULT 0.00,
  `daily_payout` decimal(12,2) NOT NULL DEFAULT 0.00,
  `remaining_days` decimal(10,2) NOT NULL DEFAULT 0.00,
  `final_end_day` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  `total_payout_days` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payout_summary`
--

INSERT INTO `payout_summary` (`id`, `user_id`, `total_investment`, `days_completed`, `total_paid`, `remaining_payout`, `daily_payout`, `remaining_days`, `final_end_day`, `created_at`, `updated_at`, `completed_at`, `total_payout_days`) VALUES
(1, 2, 600000.00, 5, 4750.00, 595250.00, 1500.00, 396.00, 401.00, '2025-12-08 18:43:55', '2025-12-13 21:28:26', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `rank_thresholds`
--

CREATE TABLE `rank_thresholds` (
  `id` int(11) NOT NULL,
  `rank_name` varchar(50) NOT NULL,
  `required_direct_investment` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rank_thresholds`
--

INSERT INTO `rank_thresholds` (`id`, `rank_name`, `required_direct_investment`) VALUES
(1, 'Promotor', 100000.00),
(2, 'Field Officer', 500000.00),
(3, 'Sub Organizer', 1000000.00),
(4, 'Organizer', 2500000.00),
(5, 'Deputy Manager', 5000000.00),
(6, 'Manager', 10000000.00),
(7, 'Sr. Manager', 20000000.00),
(8, 'Dpt. General Manager', 35000000.00),
(9, 'Regional Manager', 50000000.00),
(10, 'Divisional Manager', 75000000.00),
(11, 'CEO', 100000000.00);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `companylogo` varchar(255) DEFAULT NULL,
  `companyName` varchar(255) NOT NULL,
  `companyPhone` varchar(20) NOT NULL,
  `companyPhone2` varchar(20) DEFAULT NULL,
  `companyEmail` varchar(255) NOT NULL,
  `companyWebsite` text DEFAULT NULL,
  `companyAddress` text NOT NULL,
  `GSTnumber` varchar(50) DEFAULT NULL,
  `Tax` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_inclusive` enum('yes','no') NOT NULL DEFAULT 'yes',
  `invoice_number` varchar(50) DEFAULT NULL,
  `upi_id` varchar(255) NOT NULL,
  `googlemaps_review_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `companylogo`, `companyName`, `companyPhone`, `companyPhone2`, `companyEmail`, `companyWebsite`, `companyAddress`, `GSTnumber`, `Tax`, `tax_inclusive`, `invoice_number`, `upi_id`, `googlemaps_review_url`) VALUES
(1, 'https://invoice.cyberspacedigital.in/apis/uploads/CompanyLogo.webp', 'MSD VENTURES', '8143407758', '', 'mail@cyberspacedigital.in', 'www.cyberspacedigital.in', 'Shivajipamen, Vizag', 'VPNV10026C', 0.00, 'yes', 'CSD', 'cyberspacedigital@ybl', 'https://g.page/r/Cci0AD_-b2q3EBM/review');

-- --------------------------------------------------------

--
-- Table structure for table `theme_settings`
--

CREATE TABLE `theme_settings` (
  `id` int(11) NOT NULL,
  `roletype` enum('superadmin','admin','manager','employee','accounts','customer','sales_executive','project_manager','hr','support','inventory_manager','vendor') DEFAULT 'admin',
  `showDashboard` enum('on','off') DEFAULT 'on',
  `showAddInvestment` enum('on','off') NOT NULL DEFAULT 'off',
  `showCustomers` enum('on','off') DEFAULT 'on',
  `showAnalysis` enum('on','off') DEFAULT 'on',
  `showSettings` enum('on','off') DEFAULT 'on',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `showMyInvestment` enum('on','off') DEFAULT 'off',
  `showMyTeam` enum('on','off') DEFAULT 'off',
  `showGenealogy` enum('on','off') DEFAULT 'off',
  `showPayoutManagement` enum('on','off') DEFAULT 'off',
  `showWithdrawalApprovals` enum('on','off') DEFAULT 'off'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `theme_settings`
--

INSERT INTO `theme_settings` (`id`, `roletype`, `showDashboard`, `showAddInvestment`, `showCustomers`, `showAnalysis`, `showSettings`, `created_at`, `updated_at`, `showMyInvestment`, `showMyTeam`, `showGenealogy`, `showPayoutManagement`, `showWithdrawalApprovals`) VALUES
(1, 'superadmin', 'on', 'on', 'on', 'on', 'on', '2025-10-30 09:26:31', '2025-12-08 05:41:07', 'on', 'off', 'off', 'off', 'off'),
(2, 'admin', 'on', 'on', 'on', 'on', 'on', '2025-10-30 09:26:31', '2025-12-08 05:41:29', 'on', 'on', 'on', 'on', 'on'),
(5, 'customer', 'on', 'off', 'off', 'off', 'off', '2025-11-01 18:33:55', '2025-12-08 05:43:07', 'on', 'off', 'off', 'off', 'off');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(255) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `emailaddress` varchar(255) NOT NULL,
  `phonenumber` varchar(20) DEFAULT NULL,
  `role` enum('admin','manager','employee','customer') NOT NULL DEFAULT 'customer',
  `current_rank` varchar(50) DEFAULT 'Customer',
  `password` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `wallet_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `refered_investment_volume` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_investment` decimal(12,2) NOT NULL DEFAULT 0.00,
  `date_of_birth` date DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reference_code` varchar(255) DEFAULT NULL,
  `reference_by` varchar(255) DEFAULT NULL,
  `pannel_access` tinyint(1) NOT NULL DEFAULT 0,
  `access_code` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `emailaddress`, `phonenumber`, `role`, `current_rank`, `password`, `address`, `wallet_balance`, `refered_investment_volume`, `total_investment`, `date_of_birth`, `last_login`, `created_at`, `updated_at`, `reference_code`, `reference_by`, `pannel_access`, `access_code`) VALUES
(1, 'Company', 'company@msdventures.com', '8143407758', 'admin', 'Field Officer', 'ilovemotherA1!', 'Company Address', 0.00, 600000.00, 0.00, NULL, NULL, '2025-12-06 05:08:11', '2025-12-13 21:28:35', '8143407758', NULL, 1, NULL),
(2, 'A1', 'A1@thewinnerindia.com', '9848582644', 'customer', 'Customer', 'ilovemotherA1!', 'Company Address', 0.00, 0.00, 600000.00, NULL, NULL, '2025-12-06 05:08:11', '2025-12-13 21:28:35', '9848582644', '8143407758', 1, NULL),
(3, 'B1', 'B1@thewinnerindia.com', '6303541355', 'customer', 'Customer', 'ilovemotherA1!', 'Company Address', 0.00, 0.00, 0.00, NULL, NULL, '2025-12-06 05:08:11', '2025-12-09 05:20:14', '6303541355', '9848582644', 1, NULL),
(13, 'test', '8564478952@thewinnerindia.com', '8564478952', 'customer', 'Customer', 'ilovemotherA1!', NULL, 0.00, 0.00, 0.00, NULL, NULL, '2025-12-08 11:51:43', '2025-12-09 03:41:22', '8564478952', '8143407758', 0, '2501'),
(14, 'B1dsafdf', '4567892546@msdventures.com', '4567892546', 'customer', 'Customer', '4567892546', NULL, 0.00, 0.00, 0.00, NULL, NULL, '2025-12-09 03:43:19', '2025-12-09 03:43:59', '4567892546', '8564478952', 1, NULL);

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `after_user_insert` AFTER INSERT ON `users` FOR EACH ROW BEGIN
  INSERT INTO user_bank_account (user_id, bank_name, upi_id, bank_account_number, ifsc_code)
  VALUES (NEW.user_id, '', '', '', '');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_bank_account`
--

CREATE TABLE `user_bank_account` (
  `id` int(11) NOT NULL,
  `user_id` int(255) UNSIGNED NOT NULL,
  `bank_name` varchar(255) NOT NULL,
  `upi_id` varchar(255) NOT NULL,
  `bank_account_number` varchar(255) NOT NULL,
  `ifsc_code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `allow_edit` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_bank_account`
--

INSERT INTO `user_bank_account` (`id`, `user_id`, `bank_name`, `upi_id`, `bank_account_number`, `ifsc_code`, `created_at`, `updated_at`, `allow_edit`) VALUES
(1, 1, 'Kotak Mahindra Bank', 'cyberspacedigital@ybl', '1312429244', 'KKBK007704', '2025-12-05 21:04:38', '2025-12-09 04:47:07', 1),
(14, 11, '', '', '', '', '2025-12-06 05:34:49', '2025-12-06 05:34:49', 1),
(15, 12, '', '', '', '', '2025-12-06 06:25:06', '2025-12-06 06:25:06', 1),
(16, 13, '', '', '', '', '2025-12-08 11:51:43', '2025-12-08 11:51:43', 1),
(17, 14, '', '', '', '', '2025-12-09 03:43:19', '2025-12-09 03:43:19', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `last_activity` timestamp NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `session_token`, `expires_at`, `ip_address`, `user_agent`, `last_activity`, `created_at`) VALUES
(8, 1, '38ed4969b0fe6bbc2b6b442ed53cbfb1f660ce6c7c6a6a10c07cec72f360350c', '2025-12-14 22:28:02', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-13 21:28:02', '2025-12-13 21:28:02');

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `transaction_type` enum('CREDIT','DEBIT') NOT NULL,
  `source_table` varchar(50) NOT NULL,
  `source_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `current_balance_after` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallet_transactions`
--

INSERT INTO `wallet_transactions` (`id`, `user_id`, `transaction_type`, `source_table`, `source_id`, `amount`, `current_balance_after`, `status`, `created_at`) VALUES
(1, 1, 'CREDIT', 'referral_commission', 3, 62.50, 62.50, NULL, '2025-12-08 18:33:59'),
(2, 2, 'CREDIT', 'daily_payout_log', 1, 250.00, 250.00, NULL, '2025-12-08 18:30:00'),
(3, 1, 'CREDIT', 'referral_commission', 1, 62.50, 125.00, NULL, '2025-12-08 18:30:00'),
(4, 1, 'CREDIT', 'referral_commission', 4, 312.50, 437.50, NULL, '2025-12-08 19:18:17'),
(5, 3, 'DEBIT', 'withdrawals', 1, 1000.00, 0.00, 'completed', '2025-12-06 05:20:14'),
(6, 2, 'CREDIT', 'daily_payout_log', 10, 250.00, 500.00, NULL, '2025-12-09 18:30:00'),
(7, 1, 'CREDIT', 'referral_commission', 10, 62.50, 500.00, NULL, '2025-12-09 18:30:00'),
(8, 2, 'CREDIT', 'daily_payout_log', 11, 1250.00, 1750.00, NULL, '2025-12-09 18:30:00'),
(9, 1, 'CREDIT', 'referral_commission', 11, 312.50, 812.50, NULL, '2025-12-09 18:30:00'),
(10, 2, 'CREDIT', 'daily_payout_log', 14, 250.00, 2000.00, NULL, '2025-12-10 18:30:00'),
(11, 1, 'CREDIT', 'referral_commission', 14, 62.50, 875.00, NULL, '2025-12-10 18:30:00'),
(12, 2, 'CREDIT', 'daily_payout_log', 15, 250.00, 2250.00, NULL, '2025-12-11 18:30:00'),
(13, 1, 'CREDIT', 'referral_commission', 15, 62.50, 937.50, NULL, '2025-12-11 18:30:00'),
(14, 2, 'CREDIT', 'daily_payout_log', 17, 1250.00, 3500.00, NULL, '2025-12-10 18:30:00'),
(15, 1, 'CREDIT', 'referral_commission', 17, 312.50, 1250.00, NULL, '2025-12-10 18:30:00'),
(16, 2, 'CREDIT', 'daily_payout_log', 18, 1250.00, 4750.00, NULL, '2025-12-11 18:30:00'),
(17, 1, 'CREDIT', 'referral_commission', 18, 312.50, 1562.50, NULL, '2025-12-11 18:30:00'),
(18, 1, 'DEBIT', 'withdrawals', 2, 1562.50, 0.00, 'completed', '2025-12-13 21:28:35'),
(19, 2, 'DEBIT', 'withdrawals', 3, 4750.00, 0.00, 'completed', '2025-12-13 21:28:35');

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `withdrawals`
--

INSERT INTO `withdrawals` (`id`, `user_id`, `amount`, `status`, `notes`, `admin_notes`, `created_at`, `processed_at`, `updated_at`) VALUES
(1, 1, 1000.00, 'completed', '', '\n2025-12-09 10:23:55 - Status changed to: completed - Status changed to completed on 12/9/2025, 2:53:55 PM\n2025-12-12 10:26:04 - Status changed to: completed - Status changed to completed on 12/12/2025, 2:56:04 PM\n2025-12-12 10:26:09 - Status changed to: pending - Status changed to pending on 12/12/2025, 2:56:09 PM\n2025-12-13 22:46:36 - Status changed to: completed - Status changed to completed on 12/14/2025, 3:16:36 AM', '2025-12-09 09:22:21', NULL, '2025-12-13 21:46:36'),
(2, 1, 1562.50, 'completed', '', '\n2025-12-13 22:46:29 - Status changed to: completed - Status changed to completed on 12/14/2025, 3:16:29 AM', '2025-12-13 21:28:35', NULL, '2025-12-13 21:46:29'),
(3, 2, 4750.00, 'completed', '', '\n2025-12-13 22:46:33 - Status changed to: completed - Status changed to completed on 12/14/2025, 3:16:33 AM', '2025-12-13 21:28:35', NULL, '2025-12-13 21:46:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `commission_rates`
--
ALTER TABLE `commission_rates`
  ADD PRIMARY KEY (`level`);

--
-- Indexes for table `daily_payout_log`
--
ALTER TABLE `daily_payout_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `log_unique` (`investment_id`,`payout_date`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `investment_id` (`investment_id`),
  ADD KEY `idx_user_payout_date` (`user_id`,`payout_date`),
  ADD KEY `idx_daily_log_investment_paid` (`investment_id`,`payout_date`);

--
-- Indexes for table `investments`
--
ALTER TABLE `investments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payout_summary`
--
ALTER TABLE `payout_summary`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `user_id_2` (`user_id`),
  ADD KEY `idx_payout_summary_user_status` (`user_id`);

--
-- Indexes for table `rank_thresholds`
--
ALTER TABLE `rank_thresholds`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rank_name` (`rank_name`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `theme_settings`
--
ALTER TABLE `theme_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roletype` (`roletype`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `emailaddress` (`emailaddress`),
  ADD UNIQUE KEY `reference_code` (`reference_code`),
  ADD KEY `idx_access_code` (`access_code`);

--
-- Indexes for table `user_bank_account`
--
ALTER TABLE `user_bank_account`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_bank_account_user` (`user_id`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_session_token` (`session_token`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `daily_payout_log`
--
ALTER TABLE `daily_payout_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `investments`
--
ALTER TABLE `investments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payout_summary`
--
ALTER TABLE `payout_summary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `rank_thresholds`
--
ALTER TABLE `rank_thresholds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `theme_settings`
--
ALTER TABLE `theme_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(255) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_bank_account`
--
ALTER TABLE `user_bank_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD CONSTRAINT `withdrawals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
