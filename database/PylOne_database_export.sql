-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 04, 2020 at 03:52 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `PylOne`
--

-- --------------------------------------------------------

--
-- Table structure for table `activation_tokens`
--

CREATE TABLE `activation_tokens` (
  `ID` int(11) NOT NULL,
  `student_ID` int(11) NOT NULL,
  `hash` varchar(50) NOT NULL,
  `expiry_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `activation_used_addresses`
--

CREATE TABLE `activation_used_addresses` (
  `utbm_address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `ID` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`ID`, `code`, `name`) VALUES
(1, 'TC', 'Tronc Commun'),
(3, 'INFO', 'Informatique'),
(4, 'ENERGIE', 'Énergie'),
(5, 'GMC', 'Génie Mécanique et Conception'),
(6, 'IMSI', 'Ingénierie et Management des Systèmes Industriels'),
(7, 'EDIM', 'Ergonomie, Design et Ingénierie Mécanique');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `ID` int(11) NOT NULL,
  `name` int(11) DEFAULT NULL COMMENT 'can be null in case of a final/mid-term exam, which totally describes the exam regarding a certain UV',
  `creator_ID` int(11) NOT NULL,
  `exam_type` set('Médian','Final','CC','Projet') NOT NULL,
  `UV_ID` int(11) NOT NULL,
  `coefficient` float NOT NULL,
  `eliminatory_mark` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `ID` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `owner_ID` int(11) NOT NULL,
  `exam_ID` int(11) NOT NULL,
  `type_ID` int(11) NOT NULL,
  `upload_date` int(11) NOT NULL DEFAULT current_timestamp(),
  `downloads` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `file_types`
--

CREATE TABLE `file_types` (
  `ID` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `file_types`
--

INSERT INTO `file_types` (`ID`, `name`) VALUES
(1, 'Sujet d\'annale'),
(2, 'Corrigé d\'annale - personnel'),
(3, 'Corrigé d\'annale - officiel'),
(4, 'Fiche de révision personnelle');

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `ID` int(11) NOT NULL,
  `semester_ID` int(11) NOT NULL,
  `creator_ID` int(11) DEFAULT NULL,
  `group_type` set('CM','TD','TP') NOT NULL,
  `exam_ID` int(11) NOT NULL,
  `teacher_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `majors`
--

CREATE TABLE `majors` (
  `ID` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `department_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `majors`
--

INSERT INTO `majors` (`ID`, `code`, `name`, `department_ID`) VALUES
(1, 'PEE', 'Production de l\'énergie électrique', 4),
(2, 'ESE', 'Électronique et systèmes embarqués', 4),
(3, 'RCEE', 'Réseaux et conversion d\'énergie électrique', 4),
(4, 'BIEE', 'Bâtiment intelligent et efficacité énergétique', 4),
(5, 'LEIM', 'Logiciels embarqués et informatique mobile', 3),
(6, 'RT', 'Réseaux et télécommunications', 3),
(7, 'I2RV', 'Image, interactions et réalité virtuelle', 3),
(8, 'ILC', 'Ingénierie des logiciels et de la connaissance', 3),
(9, 'SMART', 'Science des matériaux appliquées aux projets technologiques', 5),
(10, 'CSM', 'Conception des systèmes mécatroniques', 5),
(11, 'MOST', 'Modélisation et optimisation des systèmes thermodynamiques', 5),
(12, 'CDP', 'Conception, développement de produits', 5),
(13, 'QPI', 'Qualité - performance industrielle', 6),
(14, 'ICP', 'Innovation et conception de procédés', 6),
(15, 'LOI', 'Logistique et organisation industrielle', 6),
(16, 'INP', 'Ingénierie numérique de process', 6),
(17, 'DIC', 'Design industriel et conception', 7),
(18, 'IneCO', 'Innovation et éco-conception', 7),
(19, 'EIC', 'Ergonomie, innovation et conception', 7);

-- --------------------------------------------------------

--
-- Table structure for table `major_uv_associations`
--

CREATE TABLE `major_uv_associations` (
  `association_type` tinyint(4) NOT NULL COMMENT '0 = Recommended, 1 = Mandatory',
  `UV_ID` int(11) NOT NULL,
  `major_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `ID` int(11) NOT NULL,
  `season` set('A','S') NOT NULL COMMENT 'Autumn/Spring',
  `year` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'If active, the semester will be the current semester. Otherwise, it will appear in the user''s history'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `ID` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `upload_pts` float(10,0) DEFAULT NULL,
  `pseudo` varchar(50) NOT NULL,
  `notif_email_address` varchar(255) DEFAULT NULL,
  `passwd_hash` varchar(255) NOT NULL,
  `department_ID` int(11) NOT NULL,
  `major_ID` int(11) DEFAULT NULL,
  `promo` int(11) NOT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `deletion_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sub_exams`
--

CREATE TABLE `sub_exams` (
  `ID` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `group_ID` int(11) NOT NULL,
  `sub_coefficient` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `UVs`
--

CREATE TABLE `UVs` (
  `code` varchar(4) NOT NULL,
  `title` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `season` set('A','S','AS') NOT NULL,
  `department_ID` int(11) DEFAULT NULL,
  `major_ID` int(11) DEFAULT NULL,
  `nb_ECTS` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `UV_types`
--

CREATE TABLE `UV_types` (
  `ID` int(11) NOT NULL,
  `shortname` varchar(2) NOT NULL,
  `fullname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `UV_types`
--

INSERT INTO `UV_types` (`ID`, `shortname`, `fullname`) VALUES
(1, 'EC', 'Expression et communication'),
(2, 'QC', 'Questionner et créer'),
(3, 'OM', 'Organiser et manager'),
(4, 'TM', 'Techniques et méthodes'),
(5, 'CS', 'Connaissances scientifiques'),
(6, 'ST', 'Stages, projets, périodes de travail à l\'extérieur');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activation_tokens`
--
ALTER TABLE `activation_tokens`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `student_ID` (`student_ID`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `owner_ID` (`owner_ID`);

--
-- Indexes for table `file_types`
--
ALTER TABLE `file_types`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `creator_ID` (`creator_ID`);

--
-- Indexes for table `majors`
--
ALTER TABLE `majors`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `department_ID` (`department_ID`);

--
-- Indexes for table `major_uv_associations`
--
ALTER TABLE `major_uv_associations`
  ADD PRIMARY KEY (`major_ID`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `active` (`active`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `sub_exams`
--
ALTER TABLE `sub_exams`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `sub_exams_ibfk_1` (`group_ID`);

--
-- Indexes for table `UVs`
--
ALTER TABLE `UVs`
  ADD KEY `type` (`type`);

--
-- Indexes for table `UV_types`
--
ALTER TABLE `UV_types`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activation_tokens`
--
ALTER TABLE `activation_tokens`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `file_types`
--
ALTER TABLE `file_types`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `majors`
--
ALTER TABLE `majors`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `sub_exams`
--
ALTER TABLE `sub_exams`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `UV_types`
--
ALTER TABLE `UV_types`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activation_tokens`
--
ALTER TABLE `activation_tokens`
  ADD CONSTRAINT `activation_tokens_ibfk_1` FOREIGN KEY (`student_ID`) REFERENCES `students` (`ID`);

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`owner_ID`) REFERENCES `students` (`ID`);

--
-- Constraints for table `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`creator_ID`) REFERENCES `students` (`ID`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `majors`
--
ALTER TABLE `majors`
  ADD CONSTRAINT `majors_ibfk_1` FOREIGN KEY (`department_ID`) REFERENCES `departments` (`ID`);

--
-- Constraints for table `sub_exams`
--
ALTER TABLE `sub_exams`
  ADD CONSTRAINT `sub_exams_ibfk_1` FOREIGN KEY (`group_ID`) REFERENCES `groups` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `UVs`
--
ALTER TABLE `UVs`
  ADD CONSTRAINT `UVs_ibfk_1` FOREIGN KEY (`type`) REFERENCES `UV_types` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
