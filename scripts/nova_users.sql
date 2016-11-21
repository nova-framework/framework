-- phpMyAdmin SQL Dump
-- version 4.5.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 21, 2016 at 09:41 PM
-- Server version: 10.0.28-MariaDB
-- PHP Version: 5.6.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nova`
--

-- --------------------------------------------------------

--
-- Table structure for table `nova_users`
--

CREATE TABLE `nova_users` (
  `id` int(11) UNSIGNED NOT NULL,
  `role_id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `realname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `active` tinyint(4) UNSIGNED NOT NULL DEFAULT '0',
  `image` varchar(255) DEFAULT NULL,
  `activation_code` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nova_users`
--

INSERT INTO `nova_users` (`id`, `role_id`, `username`, `password`, `realname`, `email`, `active`, `image`, `activation_code`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', '$2y$10$MZpxcVZpwTCCotIkkfPP5O1sDC7GiKzD9klh4MoM/aE44YaVm4Xga', 'Administrator', 'admin@novaframework.dev', 1, NULL, NULL, NULL, '2016-06-03 10:15:00', '2016-09-05 22:38:39'),
(2, 2, 'marcus', '$2y$10$B1Q7LNu2xuIcFJ1lAotb5O93kkvUfFdOzUZhTmSdkQZ.6woLmgu3S', 'Marcus Spears', 'marcus@novaframework.dev', 1, NULL, NULL, NULL, '2016-06-03 10:19:00', '2016-06-03 10:19:00'),
(3, 3, 'michael', '$2y$10$klop7YxFoZOVqDq3hA7efeKEz4csFhAelfwP8M4s1ROlgpkBx9qVW', 'Michael White', 'michael@novaframework.dev', 1, NULL, NULL, NULL, '2016-06-03 10:20:00', '2016-06-05 14:22:19'),
(4, 5, 'john', '$2y$10$WzBPFMiFeJ2XK9eW34zEgelSJI3R1TVrOWbjVDxFXDeMQxoh8asYK', 'John Kennedy', 'john@novaframework.dev', 1, NULL, NULL, NULL, '2016-06-03 10:21:00', '2016-06-05 14:22:32'),
(5, 5, 'mark', '$2y$10$z4bRYEcnoHOR.GuObWTATuH/x1lto.2wUJ1RxCYWOmfjay2LnTd8W', 'Mark Black', 'mark@novaframework.dev', 1, NULL, NULL, NULL, '2016-06-03 10:22:00', '2016-06-05 13:16:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `nova_users`
--
ALTER TABLE `nova_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nova_users`
--
ALTER TABLE `nova_users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
