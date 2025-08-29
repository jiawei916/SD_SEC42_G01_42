-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 29, 2025 at 04:28 AM
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
-- Database: `vetgroomlist`
--

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
(1, 'tan', 'fattylao2000@gmail.com', 'Can I see', '2025-08-26 14:17:20', '2025-08-26 22:24:39'),
(2, 'Chenchen', 'rhodesisland@gmail.com', 'Maria is here', '2025-08-26 14:19:05', '2025-08-26 22:24:39'),
(3, 'Chenchen', 'fattylao2000@gmail.com', 'Feedback that works', '2025-08-27 04:08:04', '2025-08-27 12:08:04'),
(4, 'Chenchen', 'fattylao2000@gmail.com', 'Testing again', '2025-08-27 04:35:45', '2025-08-27 12:35:45'),
(5, 'Chenchen', 'fattylao2000@gmail.com', 'Feedback that works good', '2025-08-27 05:22:45', '2025-08-27 13:22:45'),
(6, 'Chenchen', 'nilof59552@amcret.com', 'This is bad service', '2025-08-27 06:34:28', '2025-08-27 14:34:28'),
(7, 'another feedback', 'nilof59552@amcret.com', 'service is so good', '2025-08-27 06:39:48', '2025-08-27 14:39:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
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

INSERT INTO `users` (`id`, `name`, `role`, `email`, `password`, `token`, `verified`, `reset_token`, `reset_expires`, `otp`, `otp_expires`) VALUES
(2, 'chen', 'customer', 'tan@gmail.com', '$2y$10$7ghitnqPogQCW0/Dp/wxnOhTvC0TlZnXK9wIf8Z7xVstvHGf6VRfO', '', 0, NULL, NULL, '', NULL),
(3, 'Maria Nearl', 'customer', 'rhodesisland@gmail.com', '$2y$10$WOlR/k.Uo/ObadfzDCto0.g6Pau8z1mUtefB/M5EWw93mZE4RQ0Fu', '2d4dabee9779d86d43afe51e48ea8258', 1, NULL, '2025-08-26 18:48:14', '', NULL),
(36, 'Chenchen', 'admin', 'fattylao2000@gmail.com', '$2y$10$zaQWDbogvdJhuWa1XwVlr.Zv/DxCA7MzygoXKZCw3RCyX5tLVAWoe', '', 1, '1f9f2df3bc6433e0aea0468aa318b004', '2025-08-27 07:53:50', '839359', '2025-08-27 07:53:50'),
(37, 'Vincent', 'staff', 'vincent@gmail.com', '$2y$10$AGDhfn6tcVZWXiqtG7EkPOwEsPaRmT8pNWH6AdI015FZXh6RvldUW', '', 1, NULL, NULL, '', NULL),
(38, 'Admin', 'admin', 'admin@gmail.com', '$2y$10$wUrKN1hvxXPfED9Zf9sPIeXiR7tcWSFiOWNxIFXCxfDrIVQyU4Z9S', '', 1, '5d894137a60a92d171c52af6b051defc', '2025-08-27 08:29:00', '', NULL),
(39, 'Haru Urara', 'customer', 'bestestprize@gmail.com', '$2y$10$fzTfHZsZ28yP6lQ4pVniSu4WFcTtB.fEo3q0vxgtQq/JBH3T1uP6i', '', 1, NULL, NULL, '', NULL),
(40, 'tantan', 'customer', 'newemail@gmail.com', '$2y$10$X0EBzEAQUu3O5V5Q6WqqquKuGmBVeAKk8MsicjzesI6rEgnch60YC', '', 1, NULL, NULL, '', NULL),
(54, 'Tan Qi Heng', 'customer', 'tanqiheng68@gmail.com', '$2y$10$ESFkXsu1dSr2wQP6rmd.8.7.r9qnjxBXIjg9b0m3otEiNRbNaAyUC', '', 0, NULL, NULL, '289407', '2025-08-26 17:22:33'),
(69, 'new account', 'customer', 'nilof59552@amcret.com', '$2y$10$QO91981UZ1Hisb6tDyr23eeQon5r0Rt.3nXHv/Use9aLCPW22ZjNy', '', 1, NULL, NULL, '', NULL),
(70, 'Asyikin', 'customer', 'nurasyikinmuhamad24@gmail.com', '$2y$10$n/bBTlhkHC907y.6UNJo0.39B3pCyvQcQn7nFhhIp7YVE0Z.vv1HS', '', 1, NULL, NULL, '', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
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
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
