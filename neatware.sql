-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2025 at 08:07 PM
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
-- Database: `neatware`
--

-- --------------------------------------------------------

--
-- Table structure for table `area_status`
--

CREATE TABLE `area_status` (
  `room_Id` int(11) NOT NULL,
  `Area` varchar(100) DEFAULT NULL,
  `inspection_id` int(11) DEFAULT NULL,
  `status` enum('Clean','Needs cleaning','Needs attention') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `area_status`
--

INSERT INTO `area_status` (`room_Id`, `Area`, `inspection_id`, `status`) VALUES
(171, 'Hallway', 46, 'Clean'),
(172, 'Canteen', 46, 'Clean'),
(173, 'Function Room', 46, 'Clean'),
(174, 'Comfort Room', 46, 'Needs cleaning'),
(175, 'Ceiling', 46, 'Clean'),
(176, 'Hallway', 47, 'Clean'),
(177, 'Canteen', 47, 'Clean'),
(178, 'Function Room', 47, 'Clean'),
(179, 'Comfort Room', 47, 'Clean'),
(180, 'Ceiling', 47, 'Needs attention'),
(181, 'Hallway', 51, 'Clean'),
(182, 'Canteen', 51, 'Needs cleaning'),
(183, 'Function Room', 51, 'Clean'),
(184, 'Comfort Room', 51, 'Clean'),
(185, 'Ceiling', 51, 'Clean'),
(186, 'Hallway', 52, 'Clean'),
(187, 'Canteen', 52, 'Clean'),
(188, 'Function Room', 52, 'Needs cleaning'),
(189, 'Comfort Room', 52, 'Needs attention'),
(190, 'Ceiling', 52, 'Clean'),
(191, 'Hallway', 53, 'Clean'),
(192, 'Canteen', 53, 'Needs cleaning'),
(193, 'Function Room', 53, 'Clean'),
(194, 'Comfort Room', 53, 'Needs attention'),
(195, 'Ceiling', 53, 'Clean'),
(196, 'Hallway', 54, 'Clean'),
(197, 'Canteen', 54, 'Needs cleaning'),
(198, 'Function Room', 54, 'Clean'),
(199, 'Comfort Room', 54, 'Clean'),
(200, 'Ceiling', 54, 'Needs attention'),
(201, 'Hallway', 55, 'Clean'),
(202, 'Canteen', 55, 'Needs cleaning'),
(203, 'Function Room', 55, 'Needs cleaning'),
(204, 'Comfort Room', 55, 'Clean'),
(205, 'Ceiling', 55, 'Clean'),
(206, 'Hallway', 56, 'Clean'),
(207, 'Canteen', 56, 'Needs cleaning'),
(208, 'Function Room', 56, 'Clean'),
(209, 'Comfort Room', 56, 'Clean'),
(210, 'Ceiling', 56, 'Needs attention');

-- --------------------------------------------------------

--
-- Table structure for table `assignment`
--

CREATE TABLE `assignment` (
  `Personel_Id` int(11) NOT NULL,
  `Personel` varchar(100) DEFAULT NULL,
  `Area_Assignment` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignment`
--

INSERT INTO `assignment` (`Personel_Id`, `Personel`, `Area_Assignment`) VALUES
(3333, 'Lorona', '1st Floor'),
(4444, 'Melanie', '2nd Floor');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `Comments` varchar(255) DEFAULT NULL,
  `inspection_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `Comments`, `inspection_id`) VALUES
(32, ' OK; ', 46),
(33, ' OK; Ceiling: broken light; ', 47),
(34, ' OK; ', 51),
(35, ' OK; ', 52),
(36, ' OK; ', 53),
(37, ' OK; Ceiling: fed; Ceiling: fd; Ceiling: ; ', 54),
(38, ' OK; ', 55),
(39, ' OK; Ceiling: day; Ceiling: ; ', 56);

-- --------------------------------------------------------

--
-- Table structure for table `floor_status`
--

CREATE TABLE `floor_status` (
  `id` int(11) NOT NULL,
  `floor` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `issues_reported` varchar(255) DEFAULT 'None',
  `last_cleaned` varchar(50) DEFAULT '-'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `floor_status`
--

INSERT INTO `floor_status` (`id`, `floor`, `status`, `issues_reported`, `last_cleaned`) VALUES
(6, 1, 'Clean', 'None', '2025-05-01'),
(7, 2, 'Needs Cleaning', 'Spill in hallway', '2025-05-01'),
(8, 3, 'Needs Attention', 'Broken window in Room 301', '2025-05-01'),
(9, 4, 'Clean', 'None', '2025-05-01'),
(10, 5, 'Needs Cleaning', 'Trash in pantry', '2025-05-01'),
(11, 6, 'Needs Attention', 'Water leakage in restroom', '2025-05-01'),
(12, 7, 'Clean', 'None', '2025-05-01'),
(13, 8, 'Needs Cleaning', 'Dust accumulation in gym', '2025-05-01');

-- --------------------------------------------------------

--
-- Table structure for table `inspection`
--

CREATE TABLE `inspection` (
  `Inspection_Id` int(11) NOT NULL,
  `Date` date DEFAULT NULL,
  `inspector_id` int(11) DEFAULT NULL,
  `day` varchar(15) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `ass_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inspection`
--

INSERT INTO `inspection` (`Inspection_Id`, `Date`, `inspector_id`, `day`, `time`, `ass_id`) VALUES
(46, '2025-05-02', 19, 'Friday', '09:00:00', 3333),
(47, '2025-05-02', 19, 'Friday', '14:00:00', 3333),
(48, '2025-05-03', 21, 'Saturday', '09:00:00', 3333),
(49, '2025-05-03', 22, 'Saturday', '09:00:00', 3333),
(50, '2025-05-03', 22, 'Saturday', '09:00:00', 3333),
(51, '2025-05-07', 21, 'Wednesday', '14:00:00', 3333),
(52, '2025-04-28', 21, 'Monday', '14:00:00', 3333),
(53, '2025-04-28', 21, 'Monday', '14:00:00', 3333),
(54, '2025-05-08', 21, 'Thursday', '14:00:00', 3333),
(55, '2025-04-28', 21, 'Monday', '14:00:00', 3333),
(56, '2025-05-08', 21, 'Thursday', '14:00:00', 3333);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `image`) VALUES
(19, 'bumacod_najil@plpasig.edu.ph', '$2y$10$1LDycU56n.2RDkwfUi4ui.afjEk3wZI8OBCsOzSFbDz3MtQGTDHWa', 'NAJIL', 'BUMACOD', 'uploads/6809c83f1cc50.jpg'),
(20, 'nadzkiller68@gmail.com', '$2y$10$3d3bJjAhp4RnAJDEwQWph.WRvSZ7zak0OTsN0wJqKnFs/uXX4bcta', 'JUSTINE', 'FOLLOSO', 'uploads/68142d052b9fe.jpg'),
(21, 'bert@123.com', '$2y$10$wi4wIW7kGa.TCPschZGFUOw9HtW7d3lMsZB.dgAMVxlu0miNjVOy6', 'bert', 'napay', 'uploads/6815c0ff634c6.jpg'),
(22, 'justineFaustino13@gmail.com', '$2y$10$UHYc1ITNCql2Z8qFX0/O0O/LPVwnk04CXoTG/E.uciDRObe5SG8S2', 'Justine', 'Faustino', 'uploads/6815c3581af97.jpg'),
(23, 'user@example.com', '$2y$10$oIREt8sk0YdmljQdli2MxuJJrncMta72Zi0V5oGNLJxOi2ErKiGNe', 'sample', 'user', 'uploads/6815c8676d131.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `area_status`
--
ALTER TABLE `area_status`
  ADD PRIMARY KEY (`room_Id`),
  ADD KEY `inspec` (`inspection_id`);

--
-- Indexes for table `assignment`
--
ALTER TABLE `assignment`
  ADD PRIMARY KEY (`Personel_Id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `inspec_com` (`inspection_id`);

--
-- Indexes for table `floor_status`
--
ALTER TABLE `floor_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inspection`
--
ALTER TABLE `inspection`
  ADD PRIMARY KEY (`Inspection_Id`),
  ADD KEY `inspector_id` (`inspector_id`),
  ADD KEY `ass_d` (`ass_id`);

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
-- AUTO_INCREMENT for table `area_status`
--
ALTER TABLE `area_status`
  MODIFY `room_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=211;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `floor_status`
--
ALTER TABLE `floor_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `inspection`
--
ALTER TABLE `inspection`
  MODIFY `Inspection_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `area_status`
--
ALTER TABLE `area_status`
  ADD CONSTRAINT `inspec` FOREIGN KEY (`inspection_id`) REFERENCES `inspection` (`Inspection_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `inspec_com` FOREIGN KEY (`inspection_id`) REFERENCES `inspection` (`Inspection_Id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inspection`
--
ALTER TABLE `inspection`
  ADD CONSTRAINT `ass_d` FOREIGN KEY (`ass_id`) REFERENCES `assignment` (`Personel_Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inspector_id` FOREIGN KEY (`inspector_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
