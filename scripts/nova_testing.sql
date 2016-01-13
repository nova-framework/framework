-- phpMyAdmin SQL Dump
-- version 4.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 13, 2016 at 01:04 PM
-- Server version: 5.5.40-36.1
-- PHP Version: 5.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `nova_testing`
--

-- --------------------------------------------------------

--
-- Table structure for table `nova_categories`
--

CREATE TABLE IF NOT EXISTS `nova_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

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

CREATE TABLE IF NOT EXISTS `nova_courses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `description` text CHARACTER SET latin1
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

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

CREATE TABLE IF NOT EXISTS `nova_course_student` (
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nova_course_student`
--

INSERT INTO `nova_course_student` (`student_id`, `course_id`) VALUES
(1, 1),
(1, 2),
(2, 2),
(3, 1),
(3, 3),
(4, 1),
(4, 3);

-- --------------------------------------------------------

--
-- Table structure for table `nova_posts`
--

CREATE TABLE IF NOT EXISTS `nova_posts` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) CHARACTER SET latin1 NOT NULL,
  `content` text CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

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
-- Table structure for table `nova_students`
--

CREATE TABLE IF NOT EXISTS `nova_students` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `realname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

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

CREATE TABLE IF NOT EXISTS `nova_users` (
  `id` int(11) unsigned NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `nova_users`
--

INSERT INTO `nova_users` (`id`, `username`, `email`) VALUES
(1, 'admin', 'admin@novaframework.dev'),
(2, 'marcus', 'marcus@novaframework.dev'),
(3, 'michael', 'michael@novaframework.dev'),
(4, 'john', 'john@novaframework.dev'),
(5, 'mark', 'mark@novaframework.dev');

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
  ADD PRIMARY KEY (`student_id`,`course_id`);

--
-- Indexes for table `nova_posts`
--
ALTER TABLE `nova_posts`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `nova_courses`
--
ALTER TABLE `nova_courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `nova_posts`
--
ALTER TABLE `nova_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `nova_students`
--
ALTER TABLE `nova_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `nova_users`
--
ALTER TABLE `nova_users`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
