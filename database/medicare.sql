-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2025 at 04:34 PM
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
-- Database: `medicare`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_time` datetime NOT NULL,
  `reason` text DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `specialty` varchar(100) NOT NULL,
  `image` varchar(255) NOT NULL,
  `qualification` varchar(255) DEFAULT 'MBBS, MD (Colombo)',
  `experience` int(11) DEFAULT 10,
  `bio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `name`, `email`, `password`, `specialty`, `image`, `qualification`, `experience`, `bio`) VALUES
(1, 'Dr.Kavinga Perera', 'kavingaperera@medicare.puls.com', 'kavinga123', 'Cardiologist', 'images/doc_1765027603_6595.jpeg', 'MBBS, MD (Colombo)', 10, 'A highly skilled and compassionate specialist dedicated to providing the best medical care. With over a decade of clinical experience, they have treated thousands of patients and are actively involved in medical research and community health programs.'),
(2, 'Dr. Dilani Silva', '', '', 'Neurologist', 'https://randomuser.me/api/portraits/women/66.jpg', 'MBBS, MD (Colombo)', 10, 'A highly skilled and compassionate specialist dedicated to providing the best medical care. With over a decade of clinical experience, they have treated thousands of patients and are actively involved in medical research and community health programs.'),
(3, 'Dr. Ruwan Jayasinghe', '', '', 'Pediatrician', 'https://randomuser.me/api/portraits/men/55.jpg', 'MBBS, MD (Colombo)', 10, 'A highly skilled and compassionate specialist dedicated to providing the best medical care. With over a decade of clinical experience, they have treated thousands of patients and are actively involved in medical research and community health programs.'),
(4, 'Dr. Chamari Fernando', '', '', 'Orthopedic Surgeon', 'https://randomuser.me/api/portraits/women/24.jpg', 'MBBS, MD (Colombo)', 10, 'A highly skilled and compassionate specialist dedicated to providing the best medical care. With over a decade of clinical experience, they have treated thousands of patients and are actively involved in medical research and community health programs.'),
(5, 'Dr. Nimal Rajapakshe', '', '', 'Dermatologist', 'https://randomuser.me/api/portraits/men/82.jpg', 'MBBS, MD (Colombo)', 10, 'A highly skilled and compassionate specialist dedicated to providing the best medical care. With over a decade of clinical experience, they have treated thousands of patients and are actively involved in medical research and community health programs.'),
(6, 'Dr. Priyantha Bandara', '', '', 'Ophthalmologist', 'https://randomuser.me/api/portraits/men/33.jpg', 'MBBS, MD (Colombo)', 10, 'A highly skilled and compassionate specialist dedicated to providing the best medical care. With over a decade of clinical experience, they have treated thousands of patients and are actively involved in medical research and community health programs.'),
(7, 'Dr. Ashan De Alwis', '', '', 'Gynecologist', 'https://randomuser.me/api/portraits/men/41.jpg', 'MBBS, MD (Colombo)', 10, 'A highly skilled and compassionate specialist dedicated to providing the best medical care. With over a decade of clinical experience, they have treated thousands of patients and are actively involved in medical research and community health programs.'),
(8, 'Dr. Manjula Weerakkody', '', '', 'General Surgeon', 'https://randomuser.me/api/portraits/women/12.jpg', 'MBBS, MD (Colombo)', 10, 'A highly skilled and compassionate specialist dedicated to providing the best medical care. With over a decade of clinical experience, they have treated thousands of patients and are actively involved in medical research and community health programs.'),
(9, 'Dr. Kasun Dissanayake', '', '', 'Psychiatrist', 'https://randomuser.me/api/portraits/men/91.jpg', 'MBBS, MD (Colombo)', 10, 'A highly skilled and compassionate specialist dedicated to providing the best medical care. With over a decade of clinical experience, they have treated thousands of patients and are actively involved in medical research and community health programs.'),
(10, 'Dr. Thilini Gunawardena', '', '', 'ENT Specialist', 'https://randomuser.me/api/portraits/women/48.jpg', 'MBBS, MD (Colombo)', 10, 'A highly skilled and compassionate specialist dedicated to providing the best medical care. With over a decade of clinical experience, they have treated thousands of patients and are actively involved in medical research and community health programs.'),
(11, 'Dr. Sajith Mendis', '', '', 'Dental Surgeon', 'https://randomuser.me/api/portraits/men/60.jpg', 'MBBS, MD (Colombo)', 10, 'A highly skilled and compassionate specialist dedicated to providing the best medical care. With over a decade of clinical experience, they have treated thousands of patients and are actively involved in medical research and community health programs.');

-- --------------------------------------------------------

--
-- Table structure for table `hospital_reviews`
--

CREATE TABLE `hospital_reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL,
  `review_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospital_reviews`
--

INSERT INTO `hospital_reviews` (`id`, `user_id`, `user_name`, `rating`, `review_text`, `created_at`) VALUES
(1, 0, 'Ishara Jayasekara', 5, 'Excellent service! The hospital environment is very clean and the staff is highly professional.', '2025-12-03 17:57:16'),
(2, 0, 'Dinuka Madushan', 4, 'Quick emergency response. The facilities are top-notch, though the waiting time was a bit long.', '2025-12-03 17:57:16'),
(3, 0, 'Sanduni Perera', 5, 'The online appointment system is a game changer. Very convenient and easy to use!', '2025-12-03 17:57:16');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `service_description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `sender` enum('patient','doctor') NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'paid',
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `diagnosis` varchar(255) NOT NULL,
  `medication` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `medicine_list` text DEFAULT NULL,
  `dosage_instructions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `rating` int(11) NOT NULL,
  `review_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','doctor','patient') DEFAULT NULL,
  `specialty` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `dob`, `gender`, `address`, `password`, `role`, `specialty`, `created_at`) VALUES
(5, 'System Admin', 'admin@medicare.com', NULL, NULL, NULL, NULL, '$2y$10$RthqM7FNT69FXPFiOrY/XOrUTyHsVNxNEa6APi6bS3nK6Hr64pbRG', 'admin', NULL, '2025-12-03 23:40:12'),
(6, 'Jehan Fernando', 'jehanfernando@gmail.com', '+94753356246', '2003-12-01', 'Male', '8/11 Sri Mahindarama Road Ratmalana', '$2y$10$Z.c7/fheExnPqRBV1joTlelcLVr13QOVGfbYS/9JOs45pkNrYQ0aa', 'patient', NULL, '2025-12-04 12:32:30'),
(7, 'Dr. Kavinga Perera', 'Dr.KavingaPerera@medicare.com', NULL, NULL, NULL, NULL, '$2y$10$YourDefaultHashHere...', 'doctor', 'Cardiologist', '2025-12-04 12:42:01'),
(8, 'Dr. Dilani Silva', 'Dr.DilaniSilva@medicare.com', NULL, NULL, NULL, NULL, '$2y$10$YourDefaultHashHere...', 'doctor', 'Neurologist', '2025-12-04 12:42:01'),
(9, 'Dr. Ruwan Jayasinghe', 'Dr.RuwanJayasinghe@medicare.com', NULL, NULL, NULL, NULL, '$2y$10$YourDefaultHashHere...', 'doctor', 'Pediatrician', '2025-12-04 12:42:01'),
(10, 'Dr. Chamari Fernando', 'Dr.ChamariFernando@medicare.com', NULL, NULL, NULL, NULL, '$2y$10$YourDefaultHashHere...', 'doctor', 'Orthopedic Surgeon', '2025-12-04 12:42:01'),
(11, 'Dr. Nimal Rajapakshe', 'Dr.NimalRajapakshe@medicare.com', NULL, NULL, NULL, NULL, '$2y$10$YourDefaultHashHere...', 'doctor', 'Dermatologist', '2025-12-04 12:42:01'),
(12, 'Dr. Priyantha Bandara', 'Dr.PriyanthaBandara@medicare.com', NULL, NULL, NULL, NULL, '$2y$10$YourDefaultHashHere...', 'doctor', 'Ophthalmologist', '2025-12-04 12:42:01'),
(13, 'Dr. Ashan De Alwis', 'Dr.AshanDeAlwis@medicare.com', NULL, NULL, NULL, NULL, '$2y$10$YourDefaultHashHere...', 'doctor', 'Gynecologist', '2025-12-04 12:42:01'),
(14, 'Dr. Manjula Weerakkody', 'Dr.ManjulaWeerakkody@medicare.com', NULL, NULL, NULL, NULL, '$2y$10$YourDefaultHashHere...', 'doctor', 'General Surgeon', '2025-12-04 12:42:01'),
(15, 'Dr. Kasun Dissanayake', 'Dr.KasunDissanayake@medicare.com', NULL, NULL, NULL, NULL, '$2y$10$YourDefaultHashHere...', 'doctor', 'Psychiatrist', '2025-12-04 12:42:01'),
(16, 'Dr. Thilini Gunawardena', 'Dr.ThiliniGunawardena@medicare.com', NULL, NULL, NULL, NULL, '$2y$10$YourDefaultHashHere...', 'doctor', 'ENT Specialist', '2025-12-04 12:42:01'),
(17, 'Dr. Sajith Mendis', 'Dr.SajithMendis@medicare.com', NULL, NULL, NULL, NULL, '$2y$10$YourDefaultHashHere...', 'doctor', 'Dental Surgeon', '2025-12-04 12:42:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_reviews`
--
ALTER TABLE `hospital_reviews`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `hospital_reviews`
--
ALTER TABLE `hospital_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
