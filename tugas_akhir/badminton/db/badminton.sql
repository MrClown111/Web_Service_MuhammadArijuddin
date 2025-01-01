-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 01, 2025 at 04:13 PM
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
-- Database: `badminton`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `court_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `court_id`, `booking_date`, `start_time`, `end_time`, `total_price`, `status`, `created_at`) VALUES
(21, 12, 1, '2024-12-26', '19:00:00', '00:00:21', 100000.00, 'pending', '2024-12-25 15:13:26'),
(22, 12, 1, '2024-12-26', '19:00:00', '21:00:00', 100000.00, 'pending', '2024-12-25 15:14:49'),
(23, 12, 1, '2025-01-02', '22:00:00', '23:00:00', 50000.00, 'pending', '2024-12-25 15:44:54'),
(24, 12, 1, '2024-12-26', '19:00:00', '21:00:00', 100000.00, 'pending', '2024-12-25 15:58:09'),
(25, 12, 1, '2024-12-26', '19:00:00', '21:00:00', 100000.00, 'pending', '2024-12-25 16:11:37'),
(26, 13, 2, '2024-12-27', '19:00:00', '21:00:00', 100000.00, 'pending', '2024-12-26 07:30:48'),
(27, 13, 2, '2024-12-27', '19:00:00', '21:00:00', 100000.00, 'pending', '2024-12-26 07:34:52'),
(28, 14, 1, '2025-01-04', '19:00:00', '21:00:00', 100000.00, 'pending', '2024-12-31 12:10:04'),
(29, 14, 2, '2025-01-05', '18:00:00', '21:00:00', 150000.00, 'pending', '2024-12-31 14:05:36'),
(30, 15, 5, '2025-01-11', '19:00:00', '21:00:00', 100000.00, 'pending', '2024-12-31 14:07:28');

-- --------------------------------------------------------

--
-- Table structure for table `courts`
--

CREATE TABLE `courts` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `price_per_hour` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courts`
--

INSERT INTO `courts` (`id`, `name`, `description`, `price_per_hour`, `created_at`) VALUES
(1, 'A1', 'karpet', 50000.00, '2024-12-24 10:50:02'),
(2, 'A2', 'karpet', 50000.00, '2024-12-24 12:11:13'),
(5, 'A3', 'Karpet', 50000.00, '2024-12-31 14:06:33'),
(6, 'A3', 'Karpet', 50000.00, '2024-12-31 14:07:35');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `payment_method` enum('pending','completed','failed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `user_id`, `amount_paid`, `payment_date`, `payment_status`, `payment_method`) VALUES
(18, 23, 12, 50000.00, '2024-12-25 09:44:57', 'completed', 'pending'),
(19, 23, 12, 50000.00, '2024-12-25 09:53:42', 'completed', ''),
(20, 23, 12, 50000.00, '2024-12-25 09:57:40', 'completed', ''),
(21, 24, 12, 100000.00, '2024-12-25 09:58:11', 'completed', ''),
(22, 24, 12, 100000.00, '2024-12-25 10:02:55', 'completed', ''),
(23, 25, 12, 100000.00, '2024-12-25 10:12:17', 'completed', ''),
(24, 26, 13, 100000.00, '2024-12-26 01:30:52', 'completed', ''),
(25, 26, 13, 100000.00, '2024-12-26 01:34:08', 'completed', ''),
(26, 27, 13, 100000.00, '2024-12-26 01:36:15', 'completed', ''),
(27, 28, 14, 100000.00, '2024-12-31 07:46:15', 'completed', ''),
(28, 29, 14, 150000.00, '2024-12-31 08:05:37', 'completed', ''),
(29, 30, 15, 100000.00, '2024-12-31 08:07:32', 'completed', '');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `court` varchar(20) NOT NULL,
  `rating` tinyint(4) NOT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `court`, `rating`, `review`, `created_at`) VALUES
(1, 12, 'A1', 5, '0', '2024-12-25 16:20:31'),
(2, 12, 'A1', 5, '0', '2024-12-25 16:22:31'),
(3, 12, 'A2', 5, '0', '2024-12-25 16:33:56'),
(4, 13, 'A1', 4, '0', '2024-12-26 07:29:42');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `created_at`) VALUES
(12, 'asep', 'asep@gmail.com', '$2y$10$1W0gGN51WWpVWtesA/aoQOwms6YY.hM3D7dGytOplQ1z3A88mWWTW', '009988765', '2024-12-25 14:20:22'),
(13, 'bintang', 'bintang@gmail.com', '$2y$10$FCO5z29LHCVflH8VvcV5e.7PLvp9wtir3GUq9oo7c7oFF92NM4hqW', '0123456789', '2024-12-26 07:29:20'),
(14, 'ari', 'ari@gmail.com', '$2y$10$dRKYMsvWQbuceuJlURkUXOAobVj9WhaWlk8SFXpNdW5o0m4uVizi.', '0112223345', '2024-12-31 12:09:43'),
(15, 'ilham', 'ilham@gmail.com', '$2y$10$sSLYj89n9OKIfp8uJ1O1/OeAJLKTbjtB2T8Wuz2VyK44fDTiqZMAm', '01234567890', '2024-12-31 14:06:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `court_id` (`court_id`);

--
-- Indexes for table `courts`
--
ALTER TABLE `courts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `payments_ibfk_2` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `court_id` (`court`);

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `courts`
--
ALTER TABLE `courts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`court_id`) REFERENCES `courts` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
