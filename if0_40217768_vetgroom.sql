-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql303.infinityfree.com
-- Generation Time: Oct 21, 2025 at 11:48 PM
-- Server version: 11.4.7-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_40217768_vetgroom`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_type` varchar(255) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `address` text NOT NULL,
  `special_instructions` text DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `service_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `service_type`, `appointment_date`, `appointment_time`, `address`, `special_instructions`, `status`, `created_at`, `updated_at`, `service_id`) VALUES
(1, 38, 'Basic Grooming', '2025-09-22', '12:00:00', 'Here', 'The Lizard likes to be kept warm', 'completed', '2025-09-21 04:25:40', '2025-10-12 09:16:42', 1),
(5, 109, 'Basic Grooming', '2025-09-22', '12:00:00', 'Somewhere', 'Lizard licks.', 'completed', '2025-09-22 07:14:42', '2025-10-12 09:16:49', 1),
(6, 109, 'Basic Grooming', '2025-09-24', '08:00:00', 'here', 'no', 'completed', '2025-09-24 03:27:57', '2025-10-12 09:16:57', 1),
(7, 109, 'Basic Grooming', '2025-12-31', '16:00:00', 'Here', 'Lizard', 'pending', '2025-09-24 04:22:26', '2025-10-12 08:40:04', 1),
(8, 109, 'Basic Grooming', '2025-09-30', '08:00:00', 'Address', 'Cat', 'pending', '2025-09-26 11:09:49', '2025-10-12 08:40:04', 1),
(17, 109, 'Vaccinations', '2025-10-26', '18:00:00', 'Somewhere in Malaysia', 'its the dragon again.', 'pending', '2025-10-12 13:34:46', '2025-10-12 13:34:46', 4),
(18, 109, 'Dental Cleaning', '2025-10-31', '06:00:00', 'UTM', 'Wary of bad breath', 'completed', '2025-10-13 00:24:07', '2025-10-13 00:31:32', 5),
(31, 109, 'Basic Grooming', '2025-10-22', '16:00:00', 'Malaysia Johor Bahru branch', 'This is payment testing, ignore', 'pending', '2025-10-22 03:27:07', '2025-10-22 03:27:07', 1),
(32, 109, 'Basic Grooming', '2025-10-22', '12:00:00', 'Here', 'Testing ', 'pending', '2025-10-22 03:31:55', '2025-10-22 03:31:55', 1);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `feedback` text NOT NULL,
  `date_submitted` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `username`, `email`, `feedback`, `date_submitted`, `created_at`) VALUES
(5, 'Chenchen', 'fattylao2000@gmail.com', 'Feedback that works good', '2025-08-27 05:22:45', '2025-08-27 13:22:45'),
(6, 'Chenchen', 'nilof59552@amcret.com', 'This is bad service', '2025-08-27 06:34:28', '2025-08-27 14:34:28'),
(7, 'another feedback', 'nilof59552@amcret.com', 'service is so good', '2025-08-27 06:39:48', '2025-08-27 14:39:48'),
(14, 'Tan', 'fattylao2000@gmail.com', 'The website needs some work to fill in empty images', '2025-10-22 00:13:20', '2025-10-21 17:13:20'),
(15, 'Rui', 'quincie@gmail.com', 'Website laggy', '2025-10-22 00:16:30', '2025-10-21 17:16:30');

-- --------------------------------------------------------

--
-- Table structure for table `pets`
--

CREATE TABLE `pets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `species` varchar(50) NOT NULL,
  `breed` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `medical_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration` int(11) NOT NULL COMMENT 'Duration in minutes',
  `is_active` tinyint(1) DEFAULT 1,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `name`, `description`, `price`, `duration`, `is_active`, `image`) VALUES
(1, 'Basic Grooming', 'Bath, brush, nail trim, and ear cleaning', '45.00', 60, 1, 'assets/img/service/grooming.jpg'),
(2, 'Full Grooming', 'Basic grooming plus haircut and styling', '65.00', 90, 1, 'assets/img/service/full-grooming.jpg'),
(3, 'Veterinary Checkup', 'General health examination and consultation', '55.00', 30, 1, 'assets/img/service/checkup.jpg'),
(4, 'Vaccinations', 'Vaccination and Check up for your pets.', '50.00', 40, 1, 'assets/img/service/vaccine.jpg'),
(5, 'Dental Cleaning', 'Teeth cleaning and oral health check', '75.00', 45, 1, 'assets/img/service/dental.jpg'),
(6, 'Flea & Tick Treatment', 'Comprehensive parasite treatment', '40.00', 30, 1, 'assets/img/service/nail-trimming.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'customer',
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL,
  `otp` varchar(6) NOT NULL,
  `otp_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `role`, `email`, `password`, `token`, `verified`, `reset_token`, `reset_expires`, `otp`, `otp_expires`) VALUES
(1, 'Administrator', 'admin', 'admin@gmail.com', '$2y$10$7ECALjt/UX5vrjgQyMM3c.ZXm5S6Db3OaFgt8yAtMVx9LfJ7OmPsK', '', 1, NULL, NULL, '', NULL),
(36, 'Chenchen', 'staff', 'fattylao2000@gmail.com', '$2y$10$tPtRMacQoJ5Jac1b9rPBtu8mTiAHepOfRekt/wetUbtLSrzVH.EhW', '', 1, NULL, NULL, '', NULL),
(39, 'Haru Urara', 'customer', 'bestestprize@gmail.com', '$2y$10$fzTfHZsZ28yP6lQ4pVniSu4WFcTtB.fEo3q0vxgtQq/JBH3T1uP6i', '', 1, NULL, NULL, '', NULL),
(109, 'Username', 'customer', 'femic77057@artvara.com', '$2y$10$UcYTPcoDsryI2c7ODm1.POn9jSeHRtobpNYCEYdx4olXnILMNAVsG', '', 1, NULL, NULL, '600469', '2025-09-22 09:25:31'),
(112, 'name', 'staff', 'newstaff@gmail.com', '$2y$10$MvxTL9kYi4C5EX7poItCpex6kEW6FK7LhqtqXUDTHhO60bC4gTOea', '', 1, NULL, NULL, '', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pets`
--
ALTER TABLE `pets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `pets`
--
ALTER TABLE `pets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pets`
--
ALTER TABLE `pets`
  ADD CONSTRAINT `pets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
