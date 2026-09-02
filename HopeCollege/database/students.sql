-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2026 at 12:39 AM
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
  `student_id_no` varchar(60) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `registered_at` datetime DEFAULT current_timestamp(),
  `is_linked_copy` tinyint(1) NOT NULL DEFAULT 0,
  `source_student_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `parent_id`, `first_name`, `last_name`, `student_class`, `house`, `nhis_id`, `date_of_birth`, `gender`, `medical_condition`, `student_id_no`, `photo_path`, `registered_at`, `is_linked_copy`, `source_student_id`) VALUES
(1, 1, 'ama', 'hnxb', 'shs2', 'aggrey', NULL, '2026-04-29', 'Female', NULL, NULL, NULL, '2026-05-07 00:38:23', 0, NULL),
(2, 2, 'zeds', 'hills', 'shs2', NULL, NULL, '2026-04-29', 'Female', NULL, NULL, NULL, '2026-05-07 00:51:23', 0, NULL),
(3, 4, 'dsf', 'sfs', 'sf', 'fds', '043257837', '2026-04-28', 'Male', NULL, '5353553', 'uploads/student_514503d95abba493.png', '2026-05-11 13:36:43', 0, NULL),
(4, 5, 're', 'rw', 'ewr', 'wer', '651', '2026-04-29', 'Female', NULL, '6332', 'uploads/student_a0e3b56e9ee8947e.png', '2026-05-11 13:39:20', 0, NULL),
(5, 6, 'tfrde', 'gfd', 'er', 'gfd', NULL, '2026-04-29', NULL, NULL, NULL, NULL, '2026-05-11 13:56:46', 0, NULL),
(6, 7, 'jhgfd', 'jhgf', 'hgf', 'hgf', NULL, '2026-04-28', NULL, NULL, NULL, NULL, '2026-05-11 14:01:38', 0, NULL),
(7, 9, 'aaa', 'bbb', 'shs 3', 'hgh', '43234', '2026-04-29', 'Male', NULL, '5432345', 'uploads/student_d59db14426e936a3.png', '2026-05-11 21:32:10', 0, NULL),
(8, 10, 'ama', 'k', 'shs2', 'hjgfds', '65432', '2001-07-19', 'Female', NULL, '76543', 'uploads/student_ede80401a6d94855.png', '2026-05-11 21:34:32', 0, NULL),
(9, 12, 'dsv', 'sfs', 'sf', NULL, NULL, '2026-04-29', 'Male', NULL, NULL, NULL, '2026-05-11 21:35:46', 0, NULL),
(10, 13, 'ana', 'jjj', 'ssf 3', 'dfgr', '0987654333', '2021-06-16', 'Female', NULL, '333333334567', 'uploads/student_6206a1d4ee146e4a.png', '2026-05-12 18:35:54', 0, NULL),
(11, 14, 'nedh', 'shfgs', 'dfsdg', 'dfdg', '34252523', '2019-05-02', 'Male', 'n/a', '3254645', 'uploads/student_ad5e70699bfa58b0.png', '2026-05-12 21:11:48', 0, NULL),
(12, 16, 'ama', 'kay', 'shs 3', 'aggrey hse', '213121233', '2026-05-12', 'Female', 'asthma', '324324', 'uploads/student_f10aed3bca490d2c.png', '2026-05-12 21:37:57', 0, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_student_parent` (`parent_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_student_parent` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
