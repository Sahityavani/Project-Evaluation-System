-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 24, 2025 at 06:18 PM
-- Server version: 10.6.20-MariaDB
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `muflixpw_pesv2`
--

-- --------------------------------------------------------

--
-- Table structure for table `batches`
--

CREATE TABLE `batches` (
  `batch_id` int(11) NOT NULL,
  `batch_name` varchar(255) DEFAULT NULL,
  `batch_title` varchar(255) DEFAULT NULL,
  `batch_members` int(11) DEFAULT NULL,
  `supervisor` varchar(255) DEFAULT NULL,
  `mrg_id` int(11) DEFAULT NULL,
  `dpeg_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dept_evaluators`
--

CREATE TABLE `dept_evaluators` (
  `evaluator_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile` bigint(20) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `dpeg_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dpeg`
--

CREATE TABLE `dpeg` (
  `dpeg_id` int(11) NOT NULL,
  `dpeg_name` varchar(255) DEFAULT NULL,
  `dpeg_title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dpeg_batches`
--

CREATE TABLE `dpeg_batches` (
  `id` int(10) DEFAULT NULL,
  `dpeg_id` int(10) DEFAULT NULL,
  `batch_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dpeg_evaluations`
--

CREATE TABLE `dpeg_evaluations` (
  `dpeg_evaluation_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `evaluator_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `marks` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `evaluation_date` date NOT NULL,
  `student_rollno` varchar(255) NOT NULL,
  `evaluation_type` varchar(255) DEFAULT NULL,
  `sub_parts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sub_parts`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dpeg_evaluators`
--

CREATE TABLE `dpeg_evaluators` (
  `dpeg_id` int(10) DEFAULT NULL,
  `evaluator_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_type`
--

CREATE TABLE `evaluation_type` (
  `id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `max_marks_mrg` int(11) DEFAULT NULL,
  `max_marks_supervisor` int(11) DEFAULT NULL,
  `sub_parts` text DEFAULT NULL,
  `sub_parts_supervisor` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sub_parts_supervisor`)),
  `type` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaluators`
--

CREATE TABLE `evaluators` (
  `evaluator_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `mobile` bigint(20) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL,
  `mrg_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaluator_marks`
--

CREATE TABLE `evaluator_marks` (
  `mark_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `evaluator_id` int(11) NOT NULL,
  `marks` decimal(5,2) NOT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mrg`
--

CREATE TABLE `mrg` (
  `mrg_id` int(11) NOT NULL,
  `mrg_name` varchar(255) DEFAULT NULL,
  `mrg_title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mrg_batches`
--

CREATE TABLE `mrg_batches` (
  `id` int(11) NOT NULL,
  `mrg_id` int(11) DEFAULT NULL,
  `batch_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mrg_evaluations`
--

CREATE TABLE `mrg_evaluations` (
  `mrg_evaluation_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `evaluator_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `marks` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `evaluation_date` date NOT NULL,
  `student_rollno` varchar(255) NOT NULL,
  `evaluation_type` varchar(255) DEFAULT NULL,
  `sub_parts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sub_parts`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mrg_evaluators`
--

CREATE TABLE `mrg_evaluators` (
  `mrg_id` int(11) DEFAULT NULL,
  `evaluator_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `rollno` varchar(15) NOT NULL,
  `name` varchar(100) NOT NULL,
  `section` varchar(30) DEFAULT NULL,
  `year` int(11) NOT NULL,
  `batch_id` int(11) DEFAULT NULL,
  `supervisor` varchar(50) DEFAULT NULL,
  `mrg_id` int(11) DEFAULT NULL,
  `mobile` bigint(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `id` int(11) DEFAULT NULL,
  `dpeg_id` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supervisors`
--

CREATE TABLE `supervisors` (
  `id` int(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `batch_id` int(5) DEFAULT NULL,
  `password` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supervisor_evaluations`
--

CREATE TABLE `supervisor_evaluations` (
  `supervisor_evaluation_id` int(11) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `supervisor_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `marks` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `evaluation_date` date NOT NULL,
  `student_rollno` varchar(255) NOT NULL,
  `evaluation_type` int(10) DEFAULT NULL,
  `sub_parts` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sub_parts`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` varchar(250) NOT NULL,
  `mobile` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `batches`
--
ALTER TABLE `batches`
  ADD PRIMARY KEY (`batch_id`),
  ADD KEY `mrg_id` (`mrg_id`),
  ADD KEY `dpeg_id` (`dpeg_id`);

--
-- Indexes for table `dept_evaluators`
--
ALTER TABLE `dept_evaluators`
  ADD PRIMARY KEY (`evaluator_id`);

--
-- Indexes for table `dpeg`
--
ALTER TABLE `dpeg`
  ADD PRIMARY KEY (`dpeg_id`);

--
-- Indexes for table `dpeg_batches`
--
ALTER TABLE `dpeg_batches`
  ADD KEY `dpeg_id` (`dpeg_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `dpeg_evaluations`
--
ALTER TABLE `dpeg_evaluations`
  ADD PRIMARY KEY (`dpeg_evaluation_id`,`student_rollno`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `evaluator_id` (`evaluator_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `dpeg_evaluations_ibfk_3` (`student_rollno`);

--
-- Indexes for table `dpeg_evaluators`
--
ALTER TABLE `dpeg_evaluators`
  ADD KEY `dpeg_id` (`dpeg_id`),
  ADD KEY `evaluator_id` (`evaluator_id`);

--
-- Indexes for table `evaluators`
--
ALTER TABLE `evaluators`
  ADD PRIMARY KEY (`evaluator_id`),
  ADD KEY `mrg_id` (`mrg_id`);

--
-- Indexes for table `evaluator_marks`
--
ALTER TABLE `evaluator_marks`
  ADD PRIMARY KEY (`mark_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `evaluator_id` (`evaluator_id`);

--
-- Indexes for table `mrg`
--
ALTER TABLE `mrg`
  ADD PRIMARY KEY (`mrg_id`);

--
-- Indexes for table `mrg_batches`
--
ALTER TABLE `mrg_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mrg_id` (`mrg_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `mrg_evaluations`
--
ALTER TABLE `mrg_evaluations`
  ADD PRIMARY KEY (`mrg_evaluation_id`,`student_rollno`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `evaluator_id` (`evaluator_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `mrg_evaluators`
--
ALTER TABLE `mrg_evaluators`
  ADD KEY `mrg_id` (`mrg_id`),
  ADD KEY `evaluator_id` (`evaluator_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`rollno`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `mrg_id` (`mrg_id`),
  ADD KEY `dpeg_id` (`dpeg_id`);

--
-- Indexes for table `supervisors`
--
ALTER TABLE `supervisors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `supervisor_evaluations`
--
ALTER TABLE `supervisor_evaluations`
  ADD PRIMARY KEY (`supervisor_evaluation_id`,`student_rollno`),
  ADD KEY `batch_id` (`batch_id`),
  ADD KEY `supervisor_id` (`supervisor_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `batches`
--
ALTER TABLE `batches`
  ADD CONSTRAINT `batches_ibfk_1` FOREIGN KEY (`mrg_id`) REFERENCES `mrg` (`mrg_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `batches_ibfk_2` FOREIGN KEY (`dpeg_id`) REFERENCES `dpeg` (`dpeg_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dpeg_batches`
--
ALTER TABLE `dpeg_batches`
  ADD CONSTRAINT `dpeg_batches_ibfk_1` FOREIGN KEY (`dpeg_id`) REFERENCES `dpeg` (`dpeg_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dpeg_batches_ibfk_2` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`batch_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dpeg_evaluations`
--
ALTER TABLE `dpeg_evaluations`
  ADD CONSTRAINT `dpeg_evaluations_ibfk_1` FOREIGN KEY (`batch_id`) REFERENCES `batches` (`batch_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dpeg_evaluations_ibfk_2` FOREIGN KEY (`evaluator_id`) REFERENCES `dept_evaluators` (`evaluator_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dpeg_evaluations_ibfk_3` FOREIGN KEY (`student_rollno`) REFERENCES `students` (`rollno`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
