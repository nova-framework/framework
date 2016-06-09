-- phpMyAdmin SQL Dump
-- version 4.5.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 09, 2016 at 10:15 PM
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
-- Table structure for table `nova_roles`
--

CREATE TABLE `nova_roles` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(40) CHARACTER SET utf8 NOT NULL,
  `slug` varchar(40) CHARACTER SET utf8 NOT NULL,
  `description` varchar(255) CHARACTER SET utf8 NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `nova_roles`
--

INSERT INTO `nova_roles` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Root', 'root', 'Use this account with extreme caution. When using this account it is possible to cause irreversible damage to the system.', '2016-06-05 01:48:00', '2016-06-05 01:48:00'),
(2, 'Administrator', 'administrator', 'Full access to create, edit, and update companies, and orders.', '2016-06-05 01:48:00', '2016-06-05 01:48:00'),
(3, 'Manager', 'manager', 'Ability to create new companies and orders, or edit and update any existing ones.', '2016-06-05 01:48:00', '2016-06-05 01:48:00'),
(4, 'Company Manager', 'company-manager', 'Able to manage the company that the user belongs to, including adding sites, creating new users and assigning licences.', '2016-06-05 01:48:00', '2016-06-05 01:48:00'),
(5, 'User', 'user', 'A standard user that can have a licence assigned to them. No administrative features.', '2016-06-05 01:48:00', '2016-06-05 01:48:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `nova_roles`
--
ALTER TABLE `nova_roles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nova_roles`
--
ALTER TABLE `nova_roles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
