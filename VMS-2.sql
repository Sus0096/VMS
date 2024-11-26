-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 26, 2024 at 02:49 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `VMS`
--

-- --------------------------------------------------------

--
-- Table structure for table `add_document`
--

CREATE TABLE `add_document` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `document_image` varchar(255) DEFAULT NULL,
  `id_type` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `add_image`
--

CREATE TABLE `add_image` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `visitor_image` varchar(255) NOT NULL,
  `id_type` enum('visitor','staff') NOT NULL DEFAULT 'visitor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `add_image`
--

INSERT INTO `add_image` (`id`, `full_name`, `visitor_image`, `id_type`) VALUES
(19, 'a', 'uploads/img_6734500c61bcf8.29972969-1094142-free-rias-gremory-wallpapers-1920x1080-mobile.jpg', 'staff');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(5, 'root', 'kc.aadarsha.ak@gmail.com', '$2y$10$52mZCmVPAKt6BoeNqXl5d.k0NMSpiu3P3WELPQ58UjwZ2rDMfQlNa', '2024-10-26 08:32:45'),
(6, 'root', 'example@gmail.com', '$2y$10$ke1wCTZtGDdUnTbLeimgwOfbnlERL.nJ1pmeLT5XGY/rjy0dvxiSK', '2024-11-22 03:21:50');

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `id` int(11) NOT NULL,
  `id_number` varchar(50) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `approved_by` varchar(100) DEFAULT NULL,
  `requested_by` varchar(100) DEFAULT NULL,
  `request_source` varchar(50) DEFAULT NULL,
  `check_in_time` datetime DEFAULT NULL,
  `check_out_time` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`id`, `id_number`, `full_name`, `contact`, `email`, `reason`, `approved_by`, `requested_by`, `request_source`, `check_in_time`, `check_out_time`, `created_at`) VALUES
(7, '1', 'a', 'a', 'kc.aadarsha.ak@gmail.com', 'a', 'a', 'a', 'a', '2024-11-05 07:40:05', '2024-11-06 21:17:20', '2024-11-05 06:40:05'),
(9, 'bb', 'a', 'bb', 'info@timesglobal.com.np', 'bb', 'bb', 'bb', 'bb', '2024-11-07 07:15:11', '2024-11-07 08:12:49', '2024-11-07 06:15:11'),
(11, '1', 'aka', 'a', 'kc.aadarsha.ak@gmail.com', 'a', 'a', 'a', 'a', '2024-11-07 09:28:06', '2024-11-07 16:07:31', '2024-11-07 08:28:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `add_document`
--
ALTER TABLE `add_document`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `add_image`
--
ALTER TABLE `add_image`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `add_document`
--
ALTER TABLE `add_document`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `add_image`
--
ALTER TABLE `add_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
