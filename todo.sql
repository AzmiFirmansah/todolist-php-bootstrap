-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 18, 2025 at 09:55 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `todo`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `task` varchar(255) NOT NULL,
  `due_date` date NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `task`, `due_date`, `status`) VALUES
(62, 27, 'Morning Yoga & Meditation', '2025-02-16', 'Completed'),
(63, 27, 'Submit Monthly Work Report', '2025-02-15', 'Completed'),
(64, 27, 'Weekly Team Meeting (Virtual)', '2025-02-16', 'Pending'),
(65, 27, 'Grocery Shopping for the Week', '2025-02-16', 'Pending'),
(66, 27, 'Dermatologist Appointment', '2025-02-20', 'Pending'),
(67, 27, 'Dinner with College Friends', '2025-02-18', 'Pending'),
(68, 27, 'Renew Gym Membership', '2025-02-28', 'Pending'),
(69, 27, 'Book Club: Discuss \"Perahu Kertas\"', '2025-02-25', 'Pending'),
(70, 29, 'Weekly Futsal with Friends', '2025-02-15', 'Completed'),
(71, 29, 'Basic Car Maintenance Check', '2025-02-16', 'Completed'),
(72, 29, 'Complete Elden Ring Game', '2025-02-22', 'Pending'),
(73, 29, 'Weekend Hiking Trip Preparation', '2025-02-25', 'Pending'),
(74, 29, 'Organize Work Desk', '2025-03-02', 'Pending'),
(75, 29, 'Renew Gym Membership', '2025-03-05', 'Pending'),
(76, 29, 'Plan Birthday Surprise for Friend', '2025-03-10', 'Pending'),
(77, 29, 'Read a New Book', '2025-03-15', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `password`) VALUES
(27, 'Jane Doe', 'user5', '$2y$10$rgCQEwx/mYW9UFi6ipeCtuCqGHGJ3ElvH4PAIiUmHcx/3ZEeE9zo2'),
(29, 'John Doe', 'user2', '$2y$10$rYT4vMZKMJ63JOVcRQI5de8L4NeAS9iOudJfyrN9FRyG24kaTFg7.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `fk_user_tasks` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
