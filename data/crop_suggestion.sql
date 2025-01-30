-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 30, 2025 at 03:57 AM
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
-- Database: `crop_suggestion`
--

-- --------------------------------------------------------

--
-- Table structure for table `average_price_first_half`
--

CREATE TABLE `average_price_first_half` (
  `id` int(11) NOT NULL,
  `crop_id` int(11) NOT NULL,
  `price` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `average_price_first_half`
--

INSERT INTO `average_price_first_half` (`id`, `crop_id`, `price`) VALUES
(1, 1, 18.44),
(2, 2, 26.48),
(3, 3, 52.37),
(4, 4, 15.86),
(5, 5, 19.88),
(6, 6, 25.87),
(7, 7, 23.59),
(8, 8, 34.80),
(9, 9, 79.04),
(10, 10, 79.04),
(11, 11, 22.38),
(12, 12, 51.44),
(13, 13, 39.05),
(14, 14, 55.13),
(15, 15, 30.78);

-- --------------------------------------------------------

--
-- Table structure for table `average_price_second_half`
--

CREATE TABLE `average_price_second_half` (
  `id` int(11) NOT NULL,
  `crop_id` int(11) NOT NULL,
  `price` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `average_price_second_half`
--

INSERT INTO `average_price_second_half` (`id`, `crop_id`, `price`) VALUES
(1, 1, 53.79),
(2, 2, 34.55),
(3, 3, 53.15),
(4, 4, 15.94),
(5, 5, 22.59),
(6, 6, 34.74),
(7, 7, 30.33),
(8, 8, 39.25),
(9, 9, 112.37),
(10, 10, 112.37),
(11, 11, 22.59),
(12, 12, 44.48),
(13, 13, 34.81),
(14, 14, 50.07),
(15, 15, 25.95);

-- --------------------------------------------------------

--
-- Table structure for table `crop`
--

CREATE TABLE `crop` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crop`
--

INSERT INTO `crop` (`id`, `name`) VALUES
(1, 'Tomato'),
(2, 'Sweet Potato'),
(3, 'Sitaw'),
(4, 'Cassava'),
(5, 'Rice'),
(6, 'Cabbage'),
(7, 'Pechay'),
(8, 'Eggplant'),
(9, 'Red Pepper'),
(10, 'Green Pepper'),
(11, 'Squash'),
(12, 'Peanut'),
(13, 'Okra'),
(14, 'Ampalaya'),
(15, 'Corn');

-- --------------------------------------------------------

--
-- Table structure for table `crop_utilization`
--

CREATE TABLE `crop_utilization` (
  `id` int(11) NOT NULL,
  `crop_id` int(11) NOT NULL,
  `utilization_gm_per_day` decimal(6,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `crop_utilization`
--

INSERT INTO `crop_utilization` (`id`, `crop_id`, `utilization_gm_per_day`) VALUES
(1, 1, 4.15),
(2, 2, 13.00),
(3, 3, 0.13),
(4, 4, 6.00),
(5, 5, 373.00),
(6, 6, 2.74),
(7, 7, 1.06),
(8, 8, 5.60),
(9, 9, 0.40),
(10, 10, 0.40),
(11, 11, 4.41),
(12, 12, 2.92),
(13, 13, 0.68),
(14, 14, 2.00),
(15, 15, 68.00);

-- --------------------------------------------------------

--
-- Table structure for table `ph_level_requirements`
--

CREATE TABLE `ph_level_requirements` (
  `id` int(11) NOT NULL,
  `crop_id` int(11) NOT NULL,
  `min_pH` decimal(3,1) NOT NULL,
  `max_pH` decimal(3,1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ph_level_requirements`
--

INSERT INTO `ph_level_requirements` (`id`, `crop_id`, `min_pH`, `max_pH`) VALUES
(1, 1, 4.5, 6.5),
(2, 2, 5.0, 7.0),
(3, 3, 5.0, 6.0),
(4, 4, 5.8, 6.0),
(5, 5, 5.5, 6.5),
(6, 6, 5.5, 7.0),
(7, 7, 5.5, 7.0),
(8, 8, 5.5, 7.0),
(9, 9, 5.8, 6.8),
(10, 10, 5.8, 6.8),
(11, 11, 6.0, 7.0),
(12, 12, 6.0, 7.0),
(13, 13, 6.0, 6.7),
(14, 14, 5.0, 8.0),
(15, 15, 5.0, 8.0);

-- --------------------------------------------------------

--
-- Table structure for table `time_of_planting`
--

CREATE TABLE `time_of_planting` (
  `id` int(11) NOT NULL,
  `crop_id` int(11) NOT NULL,
  `start_month` varchar(20) NOT NULL,
  `end_month` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time_of_planting`
--

INSERT INTO `time_of_planting` (`id`, `crop_id`, `start_month`, `end_month`) VALUES
(1, 1, 'September', 'March'),
(2, 2, 'September', 'March'),
(3, 3, 'September', 'March'),
(4, 4, 'All Season', 'All Season'),
(5, 5, 'May', 'August'),
(6, 6, 'November', 'April'),
(7, 7, 'November', 'April'),
(8, 8, 'All Season', 'All Season'),
(9, 9, 'September', 'March'),
(10, 10, 'September', 'March'),
(11, 11, 'September', 'March'),
(12, 12, 'May', 'August'),
(13, 13, 'September', 'March'),
(14, 14, 'September', 'March'),
(15, 15, 'September', 'March');

-- --------------------------------------------------------

--
-- Table structure for table `time_of_planting_not_in_season`
--

CREATE TABLE `time_of_planting_not_in_season` (
  `id` int(11) NOT NULL,
  `crop_id` int(11) NOT NULL,
  `start_month` varchar(20) NOT NULL,
  `end_month` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time_of_planting_not_in_season`
--

INSERT INTO `time_of_planting_not_in_season` (`id`, `crop_id`, `start_month`, `end_month`) VALUES
(1, 1, 'April', 'August'),
(2, 2, 'April', 'August'),
(3, 3, 'April', 'August'),
(4, 4, 'All Season', 'All Season'),
(5, 5, 'September', 'April'),
(6, 6, 'May', 'October'),
(7, 7, 'May', 'October'),
(8, 8, 'All Season', 'All Season'),
(9, 9, 'April', 'August'),
(10, 10, 'April', 'August'),
(11, 11, 'April', 'August'),
(12, 12, 'September', 'April'),
(13, 13, 'April', 'August'),
(14, 14, 'April', 'August'),
(15, 15, 'April', 'August');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `average_price_first_half`
--
ALTER TABLE `average_price_first_half`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crop_id` (`crop_id`);

--
-- Indexes for table `average_price_second_half`
--
ALTER TABLE `average_price_second_half`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crop_id` (`crop_id`);

--
-- Indexes for table `crop`
--
ALTER TABLE `crop`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `crop_utilization`
--
ALTER TABLE `crop_utilization`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crop_id` (`crop_id`);

--
-- Indexes for table `ph_level_requirements`
--
ALTER TABLE `ph_level_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crop_id` (`crop_id`);

--
-- Indexes for table `time_of_planting`
--
ALTER TABLE `time_of_planting`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crop_id` (`crop_id`);

--
-- Indexes for table `time_of_planting_not_in_season`
--
ALTER TABLE `time_of_planting_not_in_season`
  ADD PRIMARY KEY (`id`),
  ADD KEY `crop_id` (`crop_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `average_price_first_half`
--
ALTER TABLE `average_price_first_half`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `average_price_second_half`
--
ALTER TABLE `average_price_second_half`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `crop`
--
ALTER TABLE `crop`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `crop_utilization`
--
ALTER TABLE `crop_utilization`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `ph_level_requirements`
--
ALTER TABLE `ph_level_requirements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `time_of_planting`
--
ALTER TABLE `time_of_planting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `time_of_planting_not_in_season`
--
ALTER TABLE `time_of_planting_not_in_season`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `average_price_first_half`
--
ALTER TABLE `average_price_first_half`
  ADD CONSTRAINT `average_price_first_half_ibfk_1` FOREIGN KEY (`crop_id`) REFERENCES `crop` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `average_price_second_half`
--
ALTER TABLE `average_price_second_half`
  ADD CONSTRAINT `average_price_second_half_ibfk_1` FOREIGN KEY (`crop_id`) REFERENCES `crop` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `crop_utilization`
--
ALTER TABLE `crop_utilization`
  ADD CONSTRAINT `crop_utilization_ibfk_1` FOREIGN KEY (`crop_id`) REFERENCES `crop` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ph_level_requirements`
--
ALTER TABLE `ph_level_requirements`
  ADD CONSTRAINT `ph_level_requirements_ibfk_1` FOREIGN KEY (`crop_id`) REFERENCES `crop` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `time_of_planting`
--
ALTER TABLE `time_of_planting`
  ADD CONSTRAINT `time_of_planting_ibfk_1` FOREIGN KEY (`crop_id`) REFERENCES `crop` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `time_of_planting_not_in_season`
--
ALTER TABLE `time_of_planting_not_in_season`
  ADD CONSTRAINT `time_of_planting_not_in_season_ibfk_1` FOREIGN KEY (`crop_id`) REFERENCES `crop` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
