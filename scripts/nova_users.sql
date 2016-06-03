-- phpMyAdmin SQL Dump
-- version 4.5.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 03, 2016 at 10:32 AM
-- Server version: 10.0.25-MariaDB
-- PHP Version: 5.6.22

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
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `realname` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `remember_token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nova_users`
--

INSERT INTO `nova_users` (`id`, `username`, `password`, `realname`, `email`, `created_at`, `updated_at`, `remember_token`) VALUES
(1, 'admin', '$2y$10$MZpxcVZpwTCCotIkkfPP5O1sDC7GiKzD9klh4MoM/aE44YaVm4Xga', 'Administrator', 'admin@novaframework.dev', '2016-06-03 10:15:00', '2016-06-03 10:15:00', NULL),
(2, 'marcus', '$2y$10$B1Q7LNu2xuIcFJ1lAotb5O93kkvUfFdOzUZhTmSdkQZ.6woLmgu3S', 'Marcus Spears', 'marcus@novaframework.dev', '2016-06-03 10:19:00', '2016-06-03 10:19:00', NULL),
(3, 'michael', '$2y$10$klop7YxFoZOVqDq3hA7efeKEz4csFhAelfwP8M4s1ROlgpkBx9qVW', 'Michael White', 'michael@novaframework.dev', '2016-06-03 10:20:00', '2016-06-03 10:20:00', NULL),
(4, 'john', '$2y$10$WzBPFMiFeJ2XK9eW34zEgelSJI3R1TVrOWbjVDxFXDeMQxoh8asYK', 'John Kennedy', 'john@novaframework.dev', '2016-06-03 10:21:00', '2016-06-03 10:21:00', NULL),
(5, 'mark', '$2y$10$z4bRYEcnoHOR.GuObWTATuH/x1lto.2wUJ1RxCYWOmfjay2LnTd8W', 'Mark Black', 'mark@novaframework.dev', '2016-06-03 10:22:00', '2016-06-03 10:22:00', NULL);

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
