SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `testdb1` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

USE `testdb1`;

DROP TABLE IF EXISTS `nova_car`;

CREATE TABLE IF NOT EXISTS `nova_car` (
  `carid` int(11) NOT NULL,
  `make` varchar(45) NOT NULL,
  `model` varchar(90) NOT NULL,
  `costs` double NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

INSERT INTO `nova_car` (`carid`, `make`, `model`, `costs`) VALUES
(1, 'Tesla', 'Model S', 97000),
(2, 'BMW', 'i8', 138000);


ALTER TABLE `nova_car`
  ADD PRIMARY KEY (`carid`);


ALTER TABLE `nova_car`
  MODIFY `carid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
