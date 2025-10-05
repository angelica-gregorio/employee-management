-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 05, 2025 at 11:57 AM
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
-- Database: `employeedetails`
--

-- --------------------------------------------------------

--
-- Table structure for table `employeedetails`
--

CREATE TABLE `employeedetails` (
  `DataEntryID` int(11) NOT NULL,
  `LastName` varchar(28) NOT NULL,
  `FirstName` varchar(28) NOT NULL,
  `ShiftDate` date NOT NULL,
  `ShiftNo` int(11) NOT NULL,
  `Hours` int(11) NOT NULL,
  `DutyType` enum('On Duty','Overtime','Late','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employeedetails`
--

INSERT INTO `employeedetails` (`DataEntryID`, `LastName`, `FirstName`, `ShiftDate`, `ShiftNo`, `Hours`, `DutyType`) VALUES
(1, 'Gregorio', 'Angelica', '2025-09-08', 2, 9, 'Overtime'),
(2, 'Tumlos', 'Edouard', '2025-09-08', 2, 6, 'Late'),
(3, 'Sanchez', 'Brendan Lou', '2025-09-08', 3, 5, 'Late'),
(4, 'Alado', 'Christian', '2025-09-08', 2, 8, 'On Duty'),
(5, 'Tumlos', 'Edouard', '2025-09-08', 2, 9, 'Overtime'),
(7, 'Padojinog', 'Julia', '2025-09-08', 3, 8, 'On Duty'),
(8, 'Chua', 'Khyler', '2025-09-08', 3, 6, 'Late'),
(9, 'Gregorio', 'Angelica', '2025-09-09', 1, 6, 'Late'),
(10, 'Santos', 'Ysabella', '2025-09-09', 1, 8, 'On Duty'),
(11, 'Millares', 'Brendan', '2025-09-09', 1, 8, 'On Duty'),
(12, 'Alado', 'Christian', '2025-09-09', 2, 9, 'Overtime'),
(13, 'Tumlos', 'Edouard', '2025-09-09', 2, 9, 'Overtime'),
(14, 'Senario', 'Kassie', '2025-09-09', 2, 8, 'On Duty'),
(15, 'Padojinog', 'Julia', '2025-09-09', 3, 8, 'On Duty'),
(16, 'Chua', 'Khyler', '2025-09-09', 3, 6, 'Late'),
(17, 'Gregorio', 'Angelica', '2025-09-10', 1, 8, 'On Duty'),
(18, 'Santos', 'Ysabella', '2025-09-10', 1, 8, 'On Duty'),
(19, 'Millares', 'Brendan', '2025-09-10', 1, 8, 'On Duty'),
(20, 'Alado', 'Christian', '2025-09-10', 2, 6, 'Late'),
(21, 'Tumlos', 'Edouard', '2025-09-10', 2, 9, 'Overtime'),
(22, 'Senario', 'Kassie', '2025-09-10', 2, 8, 'On Duty'),
(23, 'Padojinog', 'Julia', '2025-09-10', 3, 8, 'On Duty'),
(24, 'Chua', 'Khyler', '2025-09-10', 3, 8, 'On Duty'),
(25, 'Gregorio', 'Angelica', '2025-09-11', 1, 8, 'On Duty'),
(26, 'Santos', 'Ysabella', '2025-09-11', 1, 8, 'On Duty'),
(27, 'Millares', 'Brendan', '2025-09-11', 1, 5, 'Late'),
(28, 'Alado', 'Christian', '2025-09-11', 2, 8, 'On Duty'),
(29, 'Tumlos', 'Edouard', '2025-09-11', 2, 9, 'Overtime'),
(30, 'Senario', 'Kassie', '2025-09-11', 2, 8, 'On Duty'),
(31, 'Padojinog', 'Julia', '2025-09-11', 3, 8, 'On Duty'),
(32, 'Chua', 'Khyler', '2025-09-11', 3, 9, 'Overtime'),
(33, 'Gregorio', 'Angelica', '2025-09-12', 1, 8, 'On Duty'),
(34, 'Santos', 'Ysabella', '2025-09-12', 1, 8, 'On Duty'),
(35, 'Millares', 'Brendan', '2025-09-12', 1, 8, 'On Duty'),
(36, 'Alado', 'Christian', '2025-09-12', 2, 9, 'Overtime'),
(37, 'Tumlos', 'Edouard', '2025-09-12', 2, 6, 'Late'),
(38, 'Senario', 'Kassie', '2025-09-12', 2, 8, 'On Duty'),
(39, 'Padojinog', 'Julia', '2025-09-12', 3, 8, 'On Duty'),
(40, 'Chua', 'Khyler', '2025-09-12', 3, 8, 'On Duty'),
(41, 'Gregorio', 'Angelica', '2025-09-13', 1, 7, 'Late'),
(42, 'Santos', 'Ysabella', '2025-09-13', 1, 8, 'On Duty'),
(43, 'Millares', 'Brendan', '2025-09-13', 1, 9, 'Overtime'),
(44, 'Alado', 'Christian', '2025-09-13', 2, 8, 'On Duty'),
(45, 'Tumlos', 'Edouard', '2025-09-13', 2, 8, 'On Duty'),
(46, 'Senario', 'Kassie', '2025-09-13', 2, 8, 'On Duty'),
(47, 'Padojinog', 'Julia', '2025-09-13', 3, 9, 'Overtime'),
(48, 'Chua', 'Khyler', '2025-09-13', 3, 8, 'On Duty'),
(49, 'Gregorio', 'Angelica', '2025-09-14', 1, 8, 'On Duty'),
(50, 'Santos', 'Ysabella', '2025-09-14', 1, 6, 'Late'),
(51, 'Millares', 'Brendan', '2025-09-14', 1, 8, 'On Duty'),
(52, 'Alado', 'Christian', '2025-09-14', 2, 8, 'On Duty'),
(53, 'Tumlos', 'Edouard', '2025-09-14', 2, 9, 'Overtime'),
(54, 'Senario', 'Kassie', '2025-09-14', 2, 8, 'On Duty'),
(55, 'Padojinog', 'Julia', '2025-09-14', 3, 8, 'On Duty'),
(56, 'Chua', 'Khyler', '2025-09-14', 3, 6, 'Late'),
(57, 'Gregorio', 'Angelica', '2025-10-05', 2, 10, 'Overtime');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employeedetails`
--
ALTER TABLE `employeedetails`
  ADD PRIMARY KEY (`DataEntryID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employeedetails`
--
ALTER TABLE `employeedetails`
  MODIFY `DataEntryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
