-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 08, 2024 at 04:18 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dev_kalasan_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `username`, `email`, `password_hash`, `status`, `created_at`) VALUES
(3, 'andreejavier', 'Andree', 'andreejavier45@gmail.com', '$2y$10$xN/EPxoX/ypFw7kcgyhkXe3jd54zNHfPnkAb.NgAfWu2c6JGbzhm2', 'active', '2024-11-07 15:09:45');

-- --------------------------------------------------------

--
-- Table structure for table `analytics`
--

CREATE TABLE `analytics` (
  `id` int(11) NOT NULL,
  `tree_planted_id` int(11) DEFAULT NULL,
  `total_count` int(11) DEFAULT 0,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `tree_planted_id` int(11) DEFAULT NULL,
  `review_by` int(11) DEFAULT NULL,
  `review _date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','agree','disagree') DEFAULT 'pending',
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tree_images`
--

CREATE TABLE `tree_images` (
  `id` int(11) NOT NULL,
  `tree_planted_id` int(11) NOT NULL,
  `image_path` longblob NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tree_images`
--

INSERT INTO `tree_images` (`id`, `tree_planted_id`, `image_path`, `uploaded_at`) VALUES
(5, 5, 0x75706c6f6164732f74726565732f313733303939313435305f494d475f32303234313030325f3139313332335f3534322e6a7067, '2024-11-07 14:57:30');

-- --------------------------------------------------------

--
-- Table structure for table `tree_planted`
--

CREATE TABLE `tree_planted` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `exif_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`exif_data`)),
  `validated` tinyint(1) DEFAULT 0,
  `species_name` varchar(100) NOT NULL,
  `scientific_name` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` enum('Endemic','Indigenous') NOT NULL DEFAULT 'Endemic',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `admin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tree_planted`
--

INSERT INTO `tree_planted` (`id`, `user_id`, `latitude`, `longitude`, `date_time`, `address`, `image_path`, `exif_data`, `validated`, `species_name`, `scientific_name`, `description`, `category`, `created_at`, `admin_id`) VALUES
(5, 5, 8.36351681, 124.86948394, NULL, 'Kihare, Manolo Fortich, Bukidnon, Northern Mindanao, 8703, Philippines', 'uploads/672b6d2b23303-IMG_20241002_191323_542.jpg', '{\"latitude\":\"8.363516805555555\",\"longitude\":\"124.86948394444444\",\"date_time\":\"2024:10:02 19:13:23\",\"address\":\"Kihare, Manolo Fortich, Bukidnon, Northern Mindanao, 8703, Philippines\"}', 0, 'Balete', 'Ficus balete Merr', 'The balete tree (also known as balite or baliti) are several species of trees in the Philippines from the genus Ficus, which are broadly referred to as balete in the local language. A number of these are strangler figs, as they germinate upon other trees, before entrapping their host tree entirely and eventually killing it. Consequently the young plants are hemiepiphytes, i.e. epiphytes or air plants that grow several hanging roots which eventually touch the ground and take root. Some baletes produce natural rubber of an inferior quality. The Indian rubber tree, F. elastica, was formerly cultivated to some extent for rubber. Some of the species like tangisang-bayawak or Ficus variegata are large and could probably be utilized for match wood. The wood of Ficus species are soft, light, and of inferior quality, and the trees usually have ill-formed, short boles.[1', 'Endemic', '2024-11-06 13:20:43', 0),
(6, 5, 7.87980250, 125.00839090, '2024-06-08 14:37:33', 'Lake Apo Trail, Valencia, Bukidnon, Northern Mindanao, 8710, Philippines', 'uploads/672ce3b112edd-IMG_20240608_143733.jpg', '{\"latitude\":\"7.8798025\",\"longitude\":\"125.0083909\",\"date_time\":\"2024:06:08 14:37:33\",\"address\":\"Lake Apo Trail, Valencia, Bukidnon, Northern Mindanao, 8710, Philippines\"}', 0, '', NULL, NULL, 'Endemic', '2024-11-07 15:58:41', 0),
(7, 5, 7.63796720, 124.72541850, '2024-06-09 09:21:27', 'Magsaysay Avenue, Eastern Wao, Wao, Lanao del Sur, Bangsamoro, 9716, Philippines', 'uploads/672ceaed2c659-IMG_20240609_092127.jpg', '{\"latitude\":\"7.637967199999999\",\"longitude\":\"124.7254185\",\"date_time\":\"2024:06:09 09:21:27\",\"address\":\"Magsaysay Avenue, Eastern Wao, Wao, Lanao del Sur, Bangsamoro, 9716, Philippines\"}', 0, '', NULL, NULL, 'Endemic', '2024-11-07 16:29:33', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `email`, `role`, `profile_picture`, `created_at`) VALUES
(5, 'Andree', '$2y$10$4Ks2Ophr80QHweaLOKuS9u.LLYio7UgErzDOUmUsBchHImCjQ2cdG', 'Andree@gmail.com', 'user', NULL, '2024-11-06 13:18:18'),
(6, 'AndreeJavier', '$2y$10$iVyWc4XUrZy9DEH5XblOLeJFLu9it/88om1SSJGqWGl1h3gkndaVO', 'andreejavier45@gmail.com', 'user', NULL, '2024-11-08 00:08:39'),
(7, 'Andree P Javier', '$2y$10$PvA2rR0IGaRyYJ5RamlgFuyY7bhhSaj6Jv4zvtVWu4xpY.dpdCNUu', 'AndreePJavier@gmail.com', 'user', NULL, '2024-11-08 00:21:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `analytics`
--
ALTER TABLE `analytics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tree_planted_id` (`tree_planted_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tree_planted_id` (`tree_planted_id`),
  ADD KEY `review_by` (`review_by`);

--
-- Indexes for table `tree_images`
--
ALTER TABLE `tree_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tree_planted_id` (`tree_planted_id`);

--
-- Indexes for table `tree_planted`
--
ALTER TABLE `tree_planted`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tree_planted_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `analytics`
--
ALTER TABLE `analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tree_images`
--
ALTER TABLE `tree_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tree_planted`
--
ALTER TABLE `tree_planted`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `analytics`
--
ALTER TABLE `analytics`
  ADD CONSTRAINT `analytics_ibfk_1` FOREIGN KEY (`tree_planted_id`) REFERENCES `tree_planted` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_review_by_admin` FOREIGN KEY (`review_by`) REFERENCES `admin` (`admin_id`),
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`tree_planted_id`) REFERENCES `tree_planted` (`id`);

--
-- Constraints for table `tree_images`
--
ALTER TABLE `tree_images`
  ADD CONSTRAINT `tree_images_ibfk_1` FOREIGN KEY (`tree_planted_id`) REFERENCES `tree_planted` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tree_planted`
--
ALTER TABLE `tree_planted`
  ADD CONSTRAINT `fk_tree_planted_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
