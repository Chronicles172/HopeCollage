-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2026 at 12:43 PM
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
-- Database: `schoolconnect`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(80) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(120) NOT NULL,
  `role` enum('admin','domestic_affairs','houseparent_male','houseparent_female','houseparent') NOT NULL DEFAULT 'admin',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$12$f4EOQLX6nYpIEZcxC.aBiO9XK0ZiAmHaFq4SGFOPGiKCqOC6UGCLm', 'School Administrator', 'admin@schoolconnect.local', 'admin', '2026-05-07 00:32:18'),
(2, 'domestic_affairs', '$2y$10$mXn8VSQ5P9UwZQkLDcVrhO1esImopeITtnPRrO80d7SAmT7.VCygi', 'Head of Domestic Affairs', 'domestic@schoolconnect.local', 'domestic_affairs', '2026-05-07 00:32:18'),
(7, 'houseparent', '$2y$12$1Mc7sby8Jj96WvdiyVK.cuXxRsjX1dBiB2c03NX0dfWw87DMgdYyS', 'House Parent', 'hp@schoolconnect.local', 'houseparent', '2026-05-15 00:57:51');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `body` text NOT NULL,
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `body`, `is_pinned`, `created_by`, `created_at`) VALUES
(1, 'Welcome to the SchoolConnect Parent Portal', 'Dear Parents and Guardians,\n\nWelcome to the SchoolConnect Parent Portal! You can now log in using your registered phone number to:\n\n• View upcoming school events\n• Track your exeat requests (pending, approved, or declined)\n• Read important announcements from the school\n\nIf you have any questions, please contact the school administration.', 0, 1, '2026-05-14 12:18:37'),
(2, 'Ending of Term', 'Good luck', 0, 1, '2026-05-15 00:12:43');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `visit_type` enum('Event Attendance','Visitation','Walk-in') NOT NULL DEFAULT 'Event Attendance',
  `signed_at` datetime DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `name` varchar(160) NOT NULL,
  `event_type` enum('PTA Meeting','Visitation Day','Sports Day','Open Day','Other') NOT NULL DEFAULT 'PTA Meeting',
  `event_date` date NOT NULL,
  `event_time` time DEFAULT NULL,
  `venue` varchar(160) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `event_type`, `event_date`, `event_time`, `venue`, `description`, `created_by`, `created_at`) VALUES
(6, 'gg', 'PTA Meeting', '2026-05-15', '10:00:00', 'yeah', 'fract', 1, '2026-05-15 01:29:43');

-- --------------------------------------------------------

--
-- Table structure for table `exeat_requests`
--

CREATE TABLE `exeat_requests` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `departure_date` date NOT NULL,
  `departure_time` time NOT NULL,
  `expected_return` date NOT NULL,
  `actual_return` date DEFAULT NULL,
  `status` enum('pending','approved','declined') NOT NULL DEFAULT 'pending',
  `review_note` text DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `exeat_requests`
--

INSERT INTO `exeat_requests` (`id`, `student_id`, `parent_id`, `reason`, `departure_date`, `departure_time`, `expected_return`, `actual_return`, `status`, `review_note`, `reviewed_by`, `reviewed_at`, `created_at`) VALUES
(3, 4, 3, 'hospital checkup', '2026-09-02', '11:00:00', '2026-09-06', NULL, 'pending', NULL, NULL, NULL, '2026-09-02 14:21:58');

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `id` int(11) NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `relationship` enum('Father','Mother','Guardian','Other') NOT NULL DEFAULT 'Guardian',
  `national_id_type` enum('Ghana Card','Passport','Driver''s License') DEFAULT NULL,
  `national_id_no` varchar(80) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `registered_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`id`, `first_name`, `last_name`, `phone`, `email`, `address`, `relationship`, `national_id_type`, `national_id_no`, `photo_path`, `registered_at`) VALUES
(1, 'Ernest', 'Boamah', '0257716145', 'boamah.meek@gmail.com', 'JSK', 'Father', 'Ghana Card', '2454676', 'uploads/parent_11c441bec711a919.jpg', '2026-05-14 12:26:57'),
(2, 'Alfred', 'Boamah', '0531246679', 'kwame@email.com', 'JSK', 'Guardian', 'Ghana Card', '2345678', 'uploads/parent_e95f003def3e078a.jpg', '2026-05-15 16:00:13'),
(3, 'Charles', 'Kumi', '0553270043', 'kumi@gmail.com', 'Hsee 2w2', 'Father', 'Ghana Card', '002211445588', 'uploads/parent_4b52801b09df5a77.jpg', '2026-09-02 14:16:29');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `student_class` varchar(60) NOT NULL,
  `house` varchar(80) DEFAULT NULL,
  `nhis_id` varchar(60) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `medical_condition` text DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `registered_at` datetime DEFAULT current_timestamp(),
  `is_linked_copy` tinyint(1) NOT NULL DEFAULT 0,
  `source_student_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `parent_id`, `first_name`, `last_name`, `student_class`, `house`, `nhis_id`, `date_of_birth`, `gender`, `medical_condition`, `photo_path`, `registered_at`, `is_linked_copy`, `source_student_id`) VALUES
(2, 2, 'Ama', 'Boamah', '2', '1', '23456789', '2013-01-08', 'Female', 'N/A', 'uploads/student_45f3ba2ce2239ae5.jpg', '2026-05-15 16:00:13', 0, NULL),
(3, 2, 'kojo', 'Boamah', '3', '2', '23456789', '2013-12-14', 'Male', 'N/A', 'uploads/student_13128bc350f0bd69.jpg', '2026-05-15 16:00:13', 0, NULL),
(4, 3, 'Pricsca', 'Kumi', 'shs 2', 'hse 2', '1115544222', '2000-12-11', 'Female', 'asthma', 'uploads/student_7a0311e207bb63b4.jpg', '2026-09-02 14:16:29', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_parents`
--

CREATE TABLE `student_parents` (
  `student_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_parents`
--

INSERT INTO `student_parents` (`student_id`, `parent_id`) VALUES
(2, 2),
(3, 2),
(4, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ann_admin` (`created_by`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_event_parent` (`event_id`,`parent_id`),
  ADD KEY `fk_att_parent` (`parent_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_event_admin` (`created_by`);

--
-- Indexes for table `exeat_requests`
--
ALTER TABLE `exeat_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_exeat_student` (`student_id`),
  ADD KEY `fk_exeat_parent` (`parent_id`),
  ADD KEY `fk_exeat_reviewer` (`reviewed_by`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_student_parent` (`parent_id`);

--
-- Indexes for table `student_parents`
--
ALTER TABLE `student_parents`
  ADD PRIMARY KEY (`student_id`,`parent_id`),
  ADD KEY `fk_sp_parent` (`parent_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `exeat_requests`
--
ALTER TABLE `exeat_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `fk_ann_admin` FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_att_event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_att_parent` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_event_admin` FOREIGN KEY (`created_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `exeat_requests`
--
ALTER TABLE `exeat_requests`
  ADD CONSTRAINT `fk_exeat_parent` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_exeat_reviewer` FOREIGN KEY (`reviewed_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_exeat_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_student_parent` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_parents`
--
ALTER TABLE `student_parents`
  ADD CONSTRAINT `fk_sp_parent` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sp_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
