-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2025 at 06:01 PM
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
-- Table structure for table `designs`
--

CREATE TABLE `designs` (
  `design_id` varchar(20) NOT NULL,
  `partner_id` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` int(11) NOT NULL,
  `status` enum('pending','approved','rejected','banned') NOT NULL DEFAULT 'pending',
  `file_url` varchar(255) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `designs`
--

INSERT INTO `designs` (`design_id`, `partner_id`, `title`, `description`, `price`, `status`, `file_url`, `thumbnail`, `category`, `created_at`) VALUES
('dsg0000001', 'user00000002', 'Desain Interior Rumah Modern', 'Desain interior rumah modern minimalis siap pakai', 2000000, 'pending', 'https://drive.google.com/file/d/1RFNto0_6dvplkjLtqmpuQf4v--Cg6Ro8/view?usp=drive_link', 'https://i.postimg.cc/FRmcM4Sj/modern-minimalist-home-interio.jpg', 'Desain Interior', '2025-04-13 11:32:26'),
('dsg0000002', 'user00000002', 'Desain Interior Kamar Mandi Elegan', 'Desain kamar mandi elegan siap bangun dengan file .PDF dan .DWG', 1050000, 'approved', 'https://drive.google.com/file/d/11OqA4iEsHgdpDu7pJZDg_9DpTzDF_YFE/view?usp=drive_link', 'https://i.postimg.cc/tR8cdt46/3-D-cartoon-Disney-character-po-2.jpg', 'Desain Interior', '2025-04-13 11:39:31');

-- --------------------------------------------------------

--
-- Table structure for table `design_previews`
--

CREATE TABLE `design_previews` (
  `preview_id` int(11) NOT NULL,
  `design_id` varchar(20) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `design_previews`
--

INSERT INTO `design_previews` (`preview_id`, `design_id`, `image_url`, `created_at`) VALUES
(12, 'dsg0000001', 'https://i.postimg.cc/9FfPXCnb/modern-minimalist-home-interio-1.jpg', '2025-04-13 11:32:26'),
(13, 'dsg0000001', 'https://i.postimg.cc/FHM0xcsC/modern-minimalist-home-interio-2.jpg', '2025-04-13 11:32:26'),
(14, 'dsg0000001', 'https://i.postimg.cc/Nj91TbYr/modern-minimalist-home-interio-3.jpg', '2025-04-13 11:32:26'),
(21, 'dsg0000002', 'https://i.postimg.cc/cLS52xY4/3-D-cartoon-Disney-character-po-3.jpg', '2025-04-13 12:21:09'),
(22, 'dsg0000002', 'https://i.postimg.cc/t4NmHKtd/3-D-cartoon-Disney-character-po-4.jpg', '2025-04-13 12:21:09'),
(23, 'dsg0000002', 'https://i.postimg.cc/8CkXkZt2/3-D-cartoon-Disney-character-po-5.jpg', '2025-04-13 12:21:09');

-- --------------------------------------------------------

--
-- Table structure for table `moderation_logs`
--

CREATE TABLE `moderation_logs` (
  `log_id` int(11) NOT NULL,
  `content_id` varchar(20) NOT NULL,
  `content_type` enum('service','design') NOT NULL,
  `moderator_id` varchar(20) NOT NULL,
  `action` enum('approved','rejected','banned','pending') NOT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `moderation_logs`
--

INSERT INTO `moderation_logs` (`log_id`, `content_id`, `content_type`, `moderator_id`, `action`, `reason`, `created_at`) VALUES
(8, 'srv0000002', 'service', 'user00000001', 'approved', NULL, '2025-04-13 11:40:51'),
(9, 'dsg0000002', 'design', 'user00000003', 'approved', NULL, '2025-04-13 11:41:15'),
(10, 'dsg0000002', 'design', 'user00000002', 'pending', 'Konten telah di-edit.', '2025-04-13 12:21:09'),
(11, 'dsg0000002', 'design', 'user00000003', 'approved', NULL, '2025-04-13 12:22:17');

-- --------------------------------------------------------

--
-- Table structure for table `portfolios`
--

CREATE TABLE `portfolios` (
  `portfolio_id` int(11) NOT NULL,
  `partner_id` varchar(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `document_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` enum('karya','sertifikat','penghargaan','lainnya') NOT NULL DEFAULT 'karya'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `harga`) VALUES
(1, 'Buku Tulis ', 15000),
(4, 'Bolpoin', 5000);

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
  `status` enum('pending','approved','rejected','banned') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category` varchar(50) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`service_id`, `partner_id`, `title`, `description`, `price`, `status`, `created_at`, `updated_at`, `category`, `thumbnail`) VALUES
('srv0000001', 'user00000002', 'Desain Rumah 5x5', 'Future house 5x5 m', 1000000.00, 'pending', '2025-04-12 04:23:32', '2025-04-13 09:38:05', NULL, 'https://i.postimg.cc/hj4GJf4v/rumah-tampak-depan-desain-modern-minimalis.jpg'),
('srv0000002', 'user00000002', 'Gambar Teknik AutoCAD Rumah & Gedung', 'Gambar kerja lengkap arsitektur dan struktur bangunan.', 950000.00, 'approved', '2025-04-13 09:33:25', '2025-04-13 11:40:51', 'Teknik', 'https://i.postimg.cc/c48KKZvW/technical-drawing-3324368-1280.jpg'),
('srv0000003', 'user00000002', 'Interior Kamar Mandi Elegan', 'Layout furniture, pemilihan warna, dan pencahayaan ruangan.', 750000.00, 'pending', '2025-04-13 09:37:14', '2025-04-13 09:37:14', 'Interior', 'https://i.postimg.cc/hvYp2gng/3-D-cartoon-Disney-character-po-1.jpg');

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
  `google_id` varchar(50) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `role` enum('client','partner','admin') NOT NULL DEFAULT 'client',
  `status` enum('active','suspended','banned') NOT NULL DEFAULT 'active',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `profile_image` varchar(255) NOT NULL DEFAULT 'https://i.postimg.cc/qqChrG8y/profile.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` text,
  `is_profile_completed` tinyint(1) DEFAULT 0,
  `bio` text NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `google_id`, `username`, `password`, `full_name`, `email`, `phone_number`, `role`, `status`, `updated_at`, `profile_image`, `created_at`, `description`, `is_profile_completed`, `bio`) VALUES
('user00000001', NULL, 'AdminUmar', '3e8ad33055772c70781edbd8ad1bfc5482fcd01f845010f292f59028ec6b0d18', 'Umar Mukhtar', 'adminumar@mail.com', '083146978084', 'admin', 'active', '2025-04-13 09:02:33', 'https://i.postimg.cc/qqChrG8y/profile.png', '2025-04-09 04:09:11', NULL, 1, 'Admin'),
('user00000002', NULL, 'PartnerDimas', '4fb5a25f8b8b4ca86bc2f56e53c089b53f9a8664cedf6363955f86cb40e85e42', 'Dimas Rhoyhan Budi Satrio', 'partnerdimas@mail.com', '083123456789', 'partner', 'active', '2025-04-13 09:02:33', 'https://i.postimg.cc/qqChrG8y/profile.png', '2025-04-10 04:02:14', NULL, 1, 'Partner'),
('user00000003', NULL, 'AdminAryo', '942717ea7c1ad4b23c6eb9fa62b7bc1b3623e42cdf2bee5ba9cbb48c78a72ddf', 'MOKHAMMAD AFRYLIANTO ARYO ABDI', 'adminaryo@mail.com', '082333333333', 'admin', 'active', '2025-04-13 10:17:47', 'https://i.postimg.cc/qqChrG8y/profile.png', '2025-04-13 10:17:24', NULL, 1, 'Admin'),
('user00000004', '101298454911749023479', 'PartnerUmar', NULL, '23082010087 UMAR MUKHTAR', '23082010087@student.upnjatim.ac.id', '083111111111', 'partner', 'active', '2025-04-13 14:13:25', 'https://lh3.googleusercontent.com/a/ACg8ocISEAYgDKGPZbBsVlXkKGGETiQ6DzIjZolGu9BoZ4ymtvrv=s96-c', '2025-04-13 14:12:45', NULL, 1, 'Partner');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `designs`
--
ALTER TABLE `designs`
  ADD PRIMARY KEY (`design_id`),
  ADD KEY `fk_design_partner` (`partner_id`);

--
-- Indexes for table `design_previews`
--
ALTER TABLE `design_previews`
  ADD PRIMARY KEY (`preview_id`),
  ADD KEY `design_id` (`design_id`);

--
-- Indexes for table `moderation_logs`
--
ALTER TABLE `moderation_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `fk_log_admin` (`moderator_id`);

--
-- Indexes for table `portfolios`
--
ALTER TABLE `portfolios`
  ADD PRIMARY KEY (`portfolio_id`),
  ADD KEY `partner_id` (`partner_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `design_previews`
--
ALTER TABLE `design_previews`
  MODIFY `preview_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `moderation_logs`
--
ALTER TABLE `moderation_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `portfolios`
--
ALTER TABLE `portfolios`
  MODIFY `portfolio_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `designs`
--
ALTER TABLE `designs`
  ADD CONSTRAINT `fk_design_partner` FOREIGN KEY (`partner_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `design_previews`
--
ALTER TABLE `design_previews`
  ADD CONSTRAINT `design_previews_ibfk_1` FOREIGN KEY (`design_id`) REFERENCES `designs` (`design_id`) ON DELETE CASCADE;

--
-- Constraints for table `moderation_logs`
--
ALTER TABLE `moderation_logs`
  ADD CONSTRAINT `fk_log_admin` FOREIGN KEY (`moderator_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `portfolios`
--
ALTER TABLE `portfolios`
  ADD CONSTRAINT `portfolios_ibfk_1` FOREIGN KEY (`partner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `fk_service_partner` FOREIGN KEY (`partner_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
    `product_id` varchar(50) NOT NULL,
    `partner_id` varchar(50) NOT NULL,
    `name` varchar(255) NOT NULL,
    `description` text,
    `price` decimal(10,2) NOT NULL,
    `image_url` varchar(255),
    `status` varchar(20) DEFAULT 'active',
    `category` enum('product','service') NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`product_id`),
    FOREIGN KEY (`partner_id`) REFERENCES `users`(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
    `order_id` varchar(50) NOT NULL,
    `user_id` varchar(50) NOT NULL,
    `total_amount` decimal(10,2) NOT NULL,
    `status` varchar(20) DEFAULT 'pending',
    `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`order_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `order_id` varchar(50) NOT NULL,
    `product_id` varchar(50) NOT NULL,
    `quantity` int(11) NOT NULL,
    `price` decimal(10,2) NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`order_id`),
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
