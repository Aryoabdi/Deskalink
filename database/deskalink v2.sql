-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2025 at 02:28 AM
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
-- Database: `deskalink`
--

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `reported_by` varchar(20) NOT NULL,
  `reported_user` varchar(20) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','in review','resolved') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `service_id` varchar(20) NOT NULL,
  `partner_id` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` varchar(20) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `partner_id` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `platform_fee` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `role` enum('client','partner','admin') NOT NULL DEFAULT 'client',
  `status` enum('active','suspended','banned') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `full_name`, `email`, `password`, `phone_number`, `profile_image`, `role`, `status`, `created_at`, `updated_at`) VALUES
('user00000001', 'AdminUmar', 'Umar Mukhtar', '23082010087@student.upnjatim.ac.id', '3e8ad33055772c70781edbd8ad1bfc5482fcd01f845010f292f59028ec6b0d18', '083146978084', NULL, 'admin', 'active', '2025-03-23 16:01:23', '2025-03-23 16:03:15'),
('user00000002', 'AdminAryo', 'Aryo Abdi', '23082010089@student.upnjatim.ac.id', '942717ea7c1ad4b23c6eb9fa62b7bc1b3623e42cdf2bee5ba9cbb48c78a72ddf', '0895341333529', NULL, 'admin', 'active', '2025-03-23 16:02:37', '2025-03-23 16:03:15'),
('user00000003', 'Partner1', 'Partner 1', 'partner1@mail.com', '2da2eae78ffe5642b65ceb42ebc4d011267cb9e7ab4a3ee4a21e9ce13722fba3', '083111111111', NULL, 'partner', 'active', '2025-03-23 17:26:22', '2025-03-23 18:50:05'),
('user00000004', 'Partner2', 'Partner 2', 'partner2@mail.com', '8401d9b46285c5fa02b3b897f2c849e6b89ed040163dec3707ce79dd84b0cc57', '083222222222', NULL, 'partner', 'active', '2025-03-23 17:27:12', '2025-03-23 17:27:12'),
('user00000005', 'Client1', 'Client 1', 'client1@mail.com', '4d3ce87b63e45948f4c54dc3dea665b21a866a729f69e2787517a46bae9c1425', '082111111111', NULL, 'client', 'active', '2025-03-23 17:28:08', '2025-03-23 17:28:08'),
('user00000006', 'Client2', 'Client 2', 'client2@mail.com', 'b1de7c9ae20cfb2ea6ab281cf1e1b1f73108c5e42e5e2d3d8ba041a096d702ae', '082222222222', NULL, 'client', 'active', '2025-03-23 17:28:41', '2025-03-23 17:28:41'),
('user00000007', 'Client3', 'Client 3', 'client3@mail.com', '9867161df113bbb00eeb8a21861e28b9e8d1bcb42890106dcd340f51578f59b5', '082333333333', NULL, 'client', 'active', '2025-03-23 17:35:57', '2025-03-23 17:35:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `reported_by` (`reported_by`),
  ADD KEY `reported_user` (`reported_user`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`service_id`),
  ADD KEY `partner_id` (`partner_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `partner_id` (`partner_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`reported_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`reported_user`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`partner_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
