-- phpMyAdmin SQL Dump
-- version 4.5.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 05, 2016 at 12:22 PM
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
-- Table structure for table `nova_categories`
--

CREATE TABLE `nova_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nova_categories`
--

INSERT INTO `nova_categories` (`id`, `name`, `description`) VALUES
(1, 'Nova Framework', 'This is the Description of category: Nova Framework'),
(2, 'PHP Programming', 'This is the Description of category: PHP Programming'),
(3, 'Fun', 'This is the Description for category: Fun');

-- --------------------------------------------------------

--
-- Table structure for table `nova_courses`
--

CREATE TABLE `nova_courses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nova_courses`
--

INSERT INTO `nova_courses` (`id`, `name`, `description`) VALUES
(1, 'PHP Programming', 'This is the Description of Course: PHP Programming'),
(2, 'WebSockets and Web-Media Services', 'This is the Description of Course: WebSockets and Web-Media Services'),
(3, 'Nova Framework for Noobs', 'This is the Description of Course: Nova Framework for Noobs');

-- --------------------------------------------------------

--
-- Table structure for table `nova_course_student`
--

CREATE TABLE `nova_course_student` (
  `id` int(11) UNSIGNED NOT NULL,
  `student_id` int(11) UNSIGNED NOT NULL,
  `course_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nova_course_student`
--

INSERT INTO `nova_course_student` (`id`, `student_id`, `course_id`) VALUES
(1, 1, 1),
(4, 3, 1),
(5, 3, 3),
(6, 4, 1),
(7, 4, 3),
(8, 1, 2),
(9, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `nova_password_reminders`
--

CREATE TABLE `nova_password_reminders` (
  `email` varchar(100) NOT NULL,
  `token` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `nova_posts`
--

CREATE TABLE `nova_posts` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET latin1 NOT NULL,
  `content` text CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nova_posts`
--

INSERT INTO `nova_posts` (`id`, `author_id`, `category_id`, `title`, `content`) VALUES
(1, 2, 1, 'Short introduction into Nova ORM', 'This is a short introduction into Nova ORM'),
(2, 2, 2, 'IF THEN ELSE or DO WHILE? A second approach', 'When to use IF THEN ELSE and when to use DO WHILE, to optimize your code.'),
(3, 1, 1, 'How to install Nova Framework in five minutes', 'There we describe the recommended method to install Nova Framework.'),
(4, 3, 2, 'A new lightweight ORM called SweetORM', 'Tom from Netherlands written a shiny little ORM, colloquially called Daddy Snail, err... SweetORM. '),
(5, 1, 1, 'Nova Framework downloads? over 1000000!', 'This month, Nova Framework downloads reached a tool about 1000000.'),
(6, 1, 3, 'Funny Dogs', 'Nope! Nothing there, but if you ask Jim, maybe he can show you some photos...'),
(7, 2, 2, 'Develop a Video-Chat for your website!', 'There are described methods to build a Video-Chat using WebSockets and other Browser native Services.');

-- --------------------------------------------------------

--
-- Table structure for table `nova_profiles`
--

CREATE TABLE `nova_profiles` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `country` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `nova_profiles`
--

INSERT INTO `nova_profiles` (`id`, `user_id`, `country`) VALUES
(1, 1, 'Italy'),
(2, 2, 'UK'),
(3, 3, 'USA'),
(4, 4, 'UK'),
(5, 5, 'Australia');

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
(5, 'User', 'user', 'A standard user that can have a licence assigned to them. No administrative features.', '2016-06-05 01:48:00', '2016-06-05 01:48:00'),
(6, 'Guest', 'guest', 'The Role of an unauthenticated Visitor of the site.', '2016-06-05 10:47:13', '2016-06-05 10:54:12');

-- --------------------------------------------------------

--
-- Table structure for table `nova_students`
--

CREATE TABLE `nova_students` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `realname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nova_students`
--

INSERT INTO `nova_students` (`id`, `username`, `realname`, `email`) VALUES
(1, 'john', 'John Doe', 'john.doe@novaframwork.dev'),
(2, 'jane', 'Jane Doe', 'jane.doe@novaframwork.dev'),
(3, 'tom', 'Tom Wayne', 'tom.mcdonald@novaframwork.dev'),
(4, 'maria', 'Maria Carey', 'maria.carey@novaframework.dev');

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
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nova_users`
--

INSERT INTO `nova_users` (`id`, `role_id`, `username`, `password`, `realname`, `email`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 1, 'admin', '$2y$10$MZpxcVZpwTCCotIkkfPP5O1sDC7GiKzD9klh4MoM/aE44YaVm4Xga', 'Administrator', 'admin@novaframework.dev', NULL, '2016-06-03 10:15:00', '2016-06-03 11:11:34'),
(2, 2, 'marcus', '$2y$10$B1Q7LNu2xuIcFJ1lAotb5O93kkvUfFdOzUZhTmSdkQZ.6woLmgu3S', 'Marcus Spears', 'marcus@novaframework.dev', NULL, '2016-06-03 10:19:00', '2016-06-03 10:19:00'),
(3, 2, 'michael', '$2y$10$klop7YxFoZOVqDq3hA7efeKEz4csFhAelfwP8M4s1ROlgpkBx9qVW', 'Michael White', 'michael@novaframework.dev', NULL, '2016-06-03 10:20:00', '2016-06-03 10:20:00'),
(4, 3, 'john', '$2y$10$WzBPFMiFeJ2XK9eW34zEgelSJI3R1TVrOWbjVDxFXDeMQxoh8asYK', 'John Kennedy', 'john@novaframework.dev', NULL, '2016-06-03 10:21:00', '2016-06-03 10:21:00'),
(5, 3, 'mark', '$2y$10$z4bRYEcnoHOR.GuObWTATuH/x1lto.2wUJ1RxCYWOmfjay2LnTd8W', 'Mark Black', 'mark@novaframework.dev', NULL, '2016-06-03 10:22:00', '2016-06-03 10:22:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `nova_categories`
--
ALTER TABLE `nova_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nova_courses`
--
ALTER TABLE `nova_courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nova_course_student`
--
ALTER TABLE `nova_course_student`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nova_password_reminders`
--
ALTER TABLE `nova_password_reminders`
  ADD KEY `email` (`email`),
  ADD KEY `token` (`token`);

--
-- Indexes for table `nova_posts`
--
ALTER TABLE `nova_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nova_profiles`
--
ALTER TABLE `nova_profiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nova_roles`
--
ALTER TABLE `nova_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nova_students`
--
ALTER TABLE `nova_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nova_users`
--
ALTER TABLE `nova_users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `nova_categories`
--
ALTER TABLE `nova_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `nova_courses`
--
ALTER TABLE `nova_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `nova_course_student`
--
ALTER TABLE `nova_course_student`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `nova_posts`
--
ALTER TABLE `nova_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `nova_profiles`
--
ALTER TABLE `nova_profiles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `nova_roles`
--
ALTER TABLE `nova_roles`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `nova_students`
--
ALTER TABLE `nova_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `nova_users`
--
ALTER TABLE `nova_users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
