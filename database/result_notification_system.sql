-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Feb 06, 2021 at 12:49 PM
-- Server version: 8.0.21
-- PHP Version: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ilaro_result_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
CREATE TABLE IF NOT EXISTS `course` (
  `code` varchar(6) NOT NULL,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`code`, `title`) VALUES
('COM111', 'OPERATING SYSTEM 1'),
('COM312', 'DATABASE DESIGN 1'),
('COM314', 'COMPUTER ARCHITECTURE'),
('COM315', 'COMPUTER PROGRAMMING USING C#'),
('ICT128', 'CISCO ESSENTIALS');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
CREATE TABLE IF NOT EXISTS `department` (
  `sn` tinyint NOT NULL AUTO_INCREMENT,
  `department` varchar(1500) NOT NULL,
  `session` varchar(9) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`sn`, `department`, `session`) VALUES
(1, 'Accountancy##Agricultural And Bio-Environmental Engineering / Technology##Architectural Technology##Banking And Finance##Building Technology##Business Administration And Management##Chemical Engineering Technology##Computer Engineering##Computer Science##Electrical / Electronic Engineering##Estate Management And Valuation##Food Technology##Hospitality Management##Insurance##Library And Information Science##Marketing##Nutrition And Dietetics##Office Technology And Management##Public Administration##Quantity Surveying##Science Laboratory Technology##Statistics##Surveying And Geo-Informatics##Taxation##Urban And Regional Planning', '2019/2020');

-- --------------------------------------------------------

--
-- Table structure for table `lecturer`
--

DROP TABLE IF EXISTS `lecturer`;
CREATE TABLE IF NOT EXISTS `lecturer` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sname` varchar(30) NOT NULL,
  `fname` varchar(30) NOT NULL,
  `mname` varchar(30) NOT NULL,
  `gender` varchar(1) NOT NULL,
  `address` varchar(110) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `regdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lecturer`
--

INSERT INTO `lecturer` (`id`, `sname`, `fname`, `mname`, `gender`, `address`, `phone`, `regdate`) VALUES
(1, 'Uthman', 'Adekunle', 'Adewale', 'F', 'Room 004, Computer Science', '09088781211', '2020-12-04 07:36:09'),
(2, 'Ibrahim', 'Onifade', 'Yakubu', 'M', 'Room002,ComputerScience', '08165346948', '2020-12-04 08:14:00'),
(3, 'Bolatito', 'Rahmon', 'Bolu', 'M', 'Room011,ComputerScience', '08111212132', '2020-12-04 08:14:00'),
(4, 'Wale', 'Olorungbebe', 'Kunle', 'M', 'Room008,Statistics', '09098776765', '2020-12-04 08:15:17');

-- --------------------------------------------------------

--
-- Table structure for table `master`
--

DROP TABLE IF EXISTS `master`;
CREATE TABLE IF NOT EXISTS `master` (
  `email` varchar(70) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fname` varchar(30) NOT NULL,
  `lname` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `status` varchar(1) NOT NULL,
  `regdate` datetime NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `master`
--

INSERT INTO `master` (`email`, `password`, `fname`, `lname`, `status`, `regdate`) VALUES
('admin@gmail.com', '$2y$10$DJmMjM9Ll.MSyN8iEQqmLex4fgrTNsJvC2SU2pwVk7zV3q1H3SPSq', 'Rasheeda', 'Idris', '1', '2020-12-03 12:09:00');

-- --------------------------------------------------------

--
-- Table structure for table `result`
--

DROP TABLE IF EXISTS `result`;
CREATE TABLE IF NOT EXISTS `result` (
  `sn` int NOT NULL AUTO_INCREMENT,
  `matric` varchar(15) NOT NULL,
  `code` varchar(6) NOT NULL,
  `score` varchar(2) NOT NULL,
  `session` varchar(9) NOT NULL,
  `sms` varchar(1) NOT NULL,
  PRIMARY KEY (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `result`
--

INSERT INTO `result` (`sn`, `matric`, `code`, `score`, `session`, `sms`) VALUES
(1, 'H/CS/20/0999', 'COM312', '40', '2019/2020', ''),
(2, 'H/CS/20/0983', 'COM312', '95', '2019/2020', ''),
(3, 'H/CS/20/0922', 'COM312', '51', '2019/2020', ''),
(4, 'H/CS/20/0989', 'COM312', '47', '2019/2020', ''),
(5, 'H/CS/20/0982', 'COM312', '22', '2019/2020', ''),
(6, 'H/CS/20/0910', 'COM312', '85', '2019/2020', ''),
(24, 'H/CS/20/0910', 'COM315', '85', '2019/2020', ''),
(23, 'H/CS/20/0982', 'COM315', '22', '2019/2020', ''),
(22, 'H/CS/20/0989', 'COM315', '47', '2019/2020', ''),
(21, 'H/CS/20/0922', 'COM315', '51', '2019/2020', ''),
(20, 'H/CS/20/0983', 'COM315', '95', '2019/2020', ''),
(19, 'H/CS/20/0999', 'COM315', '40', '2019/2020', '');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `matric` varchar(15) NOT NULL,
  `sname` varchar(30) NOT NULL,
  `fname` varchar(30) NOT NULL,
  `mname` varchar(30) NOT NULL,
  `gender` varchar(1) NOT NULL,
  `department` varchar(100) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `regdate` datetime NOT NULL,
  PRIMARY KEY (`matric`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`matric`, `sname`, `fname`, `mname`, `gender`, `department`, `phone`, `regdate`) VALUES
('H/CS/20/0987', 'Tunde', 'Fatai', '', 'M', 'Computer Science', '09088776611', '2020-12-03 14:09:32'),
('H/CS/20/0912', 'Abass', 'Idris', 'Kunle', 'M', 'Computer Science', '08133218753', '2020-12-03 14:16:48'),
('H/CS/20/0982', 'Yakubu', 'Ibrahim', 'Enezi', 'M', 'Computer Science', '08178665455', '2020-12-04 07:10:52'),
('H/CS/20/0989', 'Kunle', 'Omolara', 'Rasheeda', 'F', 'Computer Science', '09178665455', '2020-12-04 07:10:52'),
('H/CS/20/0922', 'Tijani', 'Hassan', 'Bolu', 'M', 'Computer Science', '08178115455', '2020-12-04 07:10:52'),
('H/CS/20/0983', 'Ibrahim', 'Olawale', 'Yakubu', 'M', 'Computer Science', '08178665451', '2020-12-04 07:10:52'),
('H/CS/20/0999', 'Olawale', 'Fatai', 'Kunle', 'M', 'Computer Science', '08178665452', '2020-12-04 07:10:52'),
('H/CS/20/0910', 'Yakubu', 'Ebunlomo', 'Grace', 'M', 'Computer Science', '08178665411', '2020-12-04 07:10:52');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
