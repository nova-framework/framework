-- phpMyAdmin SQL Dump
-- version 4.5.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 19, 2016 at 08:20 AM
-- Server version: 10.0.25-MariaDB
-- PHP Version: 5.6.21

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
  `email` varchar(100) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nova_users`
--

INSERT INTO `nova_users` (`id`, `username`, `password`, `email`, `remember_token`) VALUES
(1, 'admin', '$2y$10$r4pnT4d0jRJRjs9ICpoRVe00Jz1IJFxE.pZTA553R7ThsZJGoGrcq', 'admin@novaframework.dev', NULL),
(2, 'marcus', '$2y$10$yfffkOK3sqZy81eQituydeZE1bPuSkcZpLGT0aJFfFk7dmi5KpCFq', 'marcus@novaframework.dev', NULL),
(3, 'michael', '$2y$10$klop7YxFoZOVqDq3hA7efeKEz4csFhAelfwP8M4s1ROlgpkBx9qVW', 'michael@novaframework.dev', NULL),
(4, 'john', '$2y$10$WzBPFMiFeJ2XK9eW34zEgelSJI3R1TVrOWbjVDxFXDeMQxoh8asYK', 'john@novaframework.dev', NULL),
(5, 'mark', '$2y$10$z4bRYEcnoHOR.GuObWTATuH/x1lto.2wUJ1RxCYWOmfjay2LnTd8W', 'mark@novaframework.dev', NULL);

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
