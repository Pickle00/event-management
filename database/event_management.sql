-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 05, 2025 at 03:29 PM
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
-- Database: `event_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', 'admin123', '2025-11-24 00:27:21');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `map_iframe` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `start_date`, `end_date`, `location`, `image`, `map_iframe`, `status`, `created_at`) VALUES
(1, 'Annual Tech Conference 2024', 'A premier technology conference', '2024-10-26 09:00:00', '2024-10-26 18:00:00', 'Convention Center', 'uploads/event_6923f224a3a9b3.81755462.jpg', '', 'Upcoming', '2025-11-09 07:28:16'),
(2, 'Summer Music Festival', 'Three days of amazing music', '2024-08-15 12:00:00', '2024-08-17 23:00:00', 'Central Park', 'uploads/event_6923f22ed0ad62.09072691.jpg', '', 'Upcoming', '2025-11-09 07:28:16'),
(3, 'Jazz Nights Live', 'Experience a soulful evening of jazz with international and local artists.', '2024-11-05 10:00:00', '2024-11-05 16:00:00', 'Blue Note Lounge, Pokhara', 'uploads/event_693299792ceb07.46123119.jpg', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3560.987654321!2d83.982123!3d28.209123!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39979a123456789%3A0xabcdef1234567890!2sBlue%20Note%20Lounge%2C%20Pokhara!5e0!3m2!1sen!2snp!4v1700000000001\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>', 'Upcoming', '2025-11-09 07:28:16'),
(4, 'Charity Gala 2024', 'An elegant evening', '2024-09-20 19:00:00', '2024-09-20 23:00:00', 'Grand Ballroom', 'uploads/event_6923f239c9f628.43910844.jpg', '', 'Upcoming', '2025-11-09 07:28:16'),
(5, 'Twenty One Pilots Live in Kathmandu', 'Join us for an electrifying night as the Grammy-winning duo Twenty One Pilots take the stage in Kathmandu. Expect a high-energy performance, hits from their catalogue, immersive visuals, and an unforgettable experience for fans of alternative rock and indie pop. Located in the heart of the city, this concert will bring their signature blend of rap, rock, and theatrical flair to Nepal for the first time. Whether you’re a longtime fan or new to their music, get ready for an immersive show.', '2025-11-12 20:30:00', '2025-11-12 21:30:00', 'Thamel, Kathmandu, Nepal', 'uploads/event_69234d85da7601.05080627.jpg', '<iframe src=\"https://www.google.com/maps/embed?pb=...\" width=\"100%\" height=\"350\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>\r\n', 'Upcoming', '2025-11-11 14:55:37'),
(7, 'Rock Fiesta 2025', 'Join the ultimate rock music celebration with top bands from around the world.', '2025-11-25 23:49:00', '2025-11-27 13:51:00', 'Grand Arena, Kathmandu', 'uploads/event_69234cc2b8b0d1.74420745.jpg', '<iframe src=\"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3532.123456789!2d85.324123!3d27.701234!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39eb190f1234567%3A0xabcdef1234567890!2sGrand%20Arena%2C%20Kathmandu!5e0!3m2!1sen!2snp!4v1700000000000\" width=\"600\" height=\"450\" style=\"border:0;\" allowfullscreen=\"\" loading=\"lazy\"></iframe>', 'Upcoming', '2025-11-23 18:04:50');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `ticket_type_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `service_fee` decimal(10,2) DEFAULT NULL,
  `processing_fee` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'card',
  `payment_reference` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `event_id`, `ticket_type_id`, `quantity`, `total_price`, `subtotal`, `service_fee`, `processing_fee`, `total`, `payment_status`, `payment_method`, `payment_reference`, `status`, `created_at`) VALUES
(1, 1, 5, NULL, NULL, NULL, 100.00, 5.00, 2.50, 107.50, 'pending', 'esewa', NULL, 'pending', '2025-11-23 16:59:35'),
(2, 1, 5, NULL, NULL, NULL, 100.00, 5.00, 2.50, 107.50, 'pending', 'esewa', NULL, 'pending', '2025-11-23 17:03:32'),
(3, 1, 5, NULL, NULL, NULL, 100.00, 5.00, 2.50, 107.50, 'pending', 'esewa', NULL, 'pending', '2025-11-23 17:03:32'),
(4, 1, 5, NULL, NULL, NULL, 100.00, 5.00, 2.50, 107.50, 'paid', 'esewa', '000D3PD', 'completed', '2025-11-22 18:15:00'),
(5, 1, 5, NULL, NULL, NULL, 100.00, 10.00, NULL, 110.00, 'paid', 'esewa', '000D3UE', 'completed', '2025-11-22 18:15:00'),
(6, 1, 5, NULL, NULL, NULL, 200.00, 10.00, NULL, 210.00, 'paid', 'esewa', '000D3UG', 'completed', '2025-11-23 17:09:15'),
(7, 1, 5, NULL, NULL, NULL, 50.00, 10.00, NULL, 60.00, 'pending', 'esewa', NULL, 'pending', '2025-11-25 16:41:28'),
(8, 1, 5, NULL, NULL, NULL, 50.00, 10.00, NULL, 60.00, 'pending', 'esewa', NULL, 'pending', '2025-12-05 04:09:30'),
(9, 1, 5, NULL, NULL, NULL, 100.00, 10.00, NULL, 110.00, 'paid', 'esewa', '000D87E', 'completed', '2025-12-05 07:55:36'),
(10, 2, 5, NULL, NULL, NULL, 50.00, 10.00, NULL, 60.00, 'paid', 'esewa', '000D87V', 'completed', '2025-12-05 08:43:00');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `ticket_type_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `ticket_type_id`, `quantity`, `price`, `created_at`) VALUES
(1, 1, 5, 2, 50.00, '2025-11-23 09:24:33'),
(2, 2, 5, 2, 50.00, '2025-11-23 09:32:37'),
(3, 3, 5, 2, 50.00, '2025-11-23 09:36:44'),
(4, 4, 5, 2, 50.00, '2025-11-23 09:39:51'),
(5, 5, 5, 2, 50.00, '2025-11-23 16:57:03'),
(6, 6, 5, 4, 50.00, '2025-11-23 17:09:15'),
(7, 7, 5, 1, 50.00, '2025-11-25 16:41:28'),
(8, 8, 5, 1, 50.00, '2025-12-05 04:09:30'),
(9, 9, 5, 2, 50.00, '2025-12-05 07:55:36'),
(10, 10, 5, 1, 50.00, '2025-12-05 08:43:00');

-- --------------------------------------------------------

--
-- Table structure for table `ticket_types`
--

CREATE TABLE `ticket_types` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `ticket_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `sold` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket_types`
--

INSERT INTO `ticket_types` (`id`, `event_id`, `ticket_name`, `price`, `quantity`, `sold`) VALUES
(1, 1, 'General Admission', 50.00, 1000, 540),
(2, 2, 'General Admission', 75.00, 2500, 2500),
(3, 3, 'General Admission', 30.00, 50, 15),
(4, 4, 'General Admission', 100.00, 200, 0),
(5, 5, 'General Admission', 50.00, 500, 11),
(7, 7, 'General Admission', 50.00, 500, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Pickle', 'pickle00@gmail.com', '123', '2025-11-11 14:28:49'),
(2, 'Noah', 'nikita@gmail.com', '123', '2025-12-05 08:42:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `ticket_type_id` (`ticket_type_id`);

--
-- Indexes for table `ticket_types`
--
ALTER TABLE `ticket_types`
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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ticket_types`
--
ALTER TABLE `ticket_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
