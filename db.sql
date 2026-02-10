-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2026 at 10:41 AM
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
-- Database: `lost_found`
--

-- --------------------------------------------------------

--
-- Table structure for table `claimss`
--

CREATE TABLE `claimss` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_description` text NOT NULL,
  `item_type` varchar(50) NOT NULL,
  `item_image` varchar(255) DEFAULT NULL,
  `claimer_name` varchar(150) NOT NULL,
  `claimer_number` varchar(50) NOT NULL,
  `claimer_classroom` varchar(50) NOT NULL,
  `claimer_image` varchar(255) NOT NULL,
  `claimed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `itemss`
--

CREATE TABLE `itemss` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL,
  `status` enum('pending','approved') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `itemss`
--

INSERT INTO `itemss` (`id`, `description`, `type`, `image`, `created_at`, `user_id`, `status`) VALUES
(6, 'asdadsad', 'electronic', 'uploads/item_1770527601.png', '2026-02-08 05:13:21', 1, 'approved'),
(10, 'White T-Shirt', 'clothing', 'uploads/item_1770527793.png', '2026-02-08 05:16:33', 1, 'approved'),
(11, 'Brown Wallet', 'money', 'uploads/item_1770527825.png', '2026-02-08 05:17:05', 1, 'approved'),
(12, 'Pink earrings', 'jewelry', 'uploads/item_1770527856.png', '2026-02-08 05:17:36', 1, 'approved'),
(13, 'Black Bag With lego keychain', 'other', 'uploads/item_1770527878.png', '2026-02-08 05:17:58', 1, 'approved'),
(14, 'Description', 'electronic', '', '2026-02-08 05:18:00', 1, 'approved'),
(15, 'Description', 'electronic', 'uploads/item_1770527938.png', '2026-02-08 05:18:58', 1, 'approved'),
(16, 'Description', 'electronic', '', '2026-02-08 05:19:01', 1, 'approved'),
(17, 'Description', 'electronic', '', '2026-02-08 05:19:06', 1, 'approved'),
(18, 'Description', 'electronic', '', '2026-02-08 05:19:08', 1, 'approved'),
(19, 'Description', 'electronic', '', '2026-02-08 05:19:09', 1, 'approved'),
(20, 'Description', 'electronic', '', '2026-02-08 05:19:10', 1, 'approved'),
(21, 'Description', 'electronic', '', '2026-02-08 05:19:11', 1, 'approved'),
(22, 'Description', 'electronic', '', '2026-02-08 05:19:12', 1, 'approved'),
(23, 'Description', 'electronic', '', '2026-02-08 05:19:13', 1, 'approved'),
(24, 'Description', 'electronic', '', '2026-02-08 05:19:14', 1, 'approved'),
(25, 'Description', 'electronic', '', '2026-02-08 05:19:15', 1, 'approved'),
(26, 'Description', 'electronic', '', '2026-02-08 05:19:17', 1, 'approved'),
(27, 'Description', 'electronic', '', '2026-02-08 05:19:18', 1, 'approved'),
(28, 'Description', 'electronic', '', '2026-02-08 05:19:19', 1, 'approved'),
(29, 'Description', 'electronic', '', '2026-02-08 05:19:20', 1, 'approved'),
(30, 'Description', 'electronic', '', '2026-02-08 05:19:21', 1, 'approved'),
(31, 'Description', 'electronic', '', '2026-02-08 05:19:21', 1, 'approved'),
(32, 'Hello', 'other', 'uploads/item_1770528130.png', '2026-02-08 06:22:12', 4, 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `userss`
--

CREATE TABLE `userss` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userss`
--

INSERT INTO `userss` (`id`, `username`, `password`, `role`, `created_at`) VALUES
(1, 'james', '03ac674216f3e15c761ee1a5e255f067953623c8b388b4459e13f978d7c846f4', 'admin', '2026-02-02 12:37:59'),
(2, 'admin', '1234', 'admin', '2026-02-08 02:26:23'),
(3, 'jake', '1234', 'user', '2026-02-08 02:26:43'),
(4, 'testuser', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'user', '2026-02-08 02:41:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `claimss`
--
ALTER TABLE `claimss`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `itemss`
--
ALTER TABLE `itemss`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `userss`
--
ALTER TABLE `userss`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `claimss`
--
ALTER TABLE `claimss`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `itemss`
--
ALTER TABLE `itemss`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `userss`
--
ALTER TABLE `userss`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `claimss`
--
ALTER TABLE `claimss`
  ADD CONSTRAINT `claimss_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `itemss` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
