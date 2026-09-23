-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 30, 2025 at 07:26 AM
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
-- Database: `finalproj`
--
CREATE DATABASE IF NOT EXISTS `finalproj` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `finalproj`;

-- --------------------------------------------------------

--
-- Table structure for table `academic_background`
--

CREATE TABLE `academic_background` (
  `id` int(11) NOT NULL,
  `faculty_id` varchar(11) NOT NULL,
  `level` varchar(50) NOT NULL,
  `institution_name` varchar(255) NOT NULL,
  `degree_course` varchar(150) DEFAULT NULL,
  `start_year` year(4) DEFAULT NULL,
  `end_year` year(4) DEFAULT NULL,
  `honors` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_background`
--

INSERT INTO `academic_background` (`id`, `faculty_id`, `level`, `institution_name`, `degree_course`, `start_year`, `end_year`, `honors`, `created_at`) VALUES
(18, '23-20000', 'Elementary', 'Rosario Elementary School', 'N/A', '1999', '2005', 'N/A', '2025-05-20 07:49:53');

-- --------------------------------------------------------

--
-- Table structure for table `civil_service_eligibility`
--

CREATE TABLE `civil_service_eligibility` (
  `id` int(11) NOT NULL,
  `faculty_id` varchar(11) NOT NULL,
  `eligibility_type` varchar(150) DEFAULT NULL,
  `rating` varchar(20) DEFAULT NULL,
  `date_of_examination` date DEFAULT NULL,
  `place_of_examination` varchar(255) DEFAULT NULL,
  `license_number` varchar(100) DEFAULT NULL,
  `license_validity` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `civil_service_eligibility`
--

INSERT INTO `civil_service_eligibility` (`id`, `faculty_id`, `eligibility_type`, `rating`, `date_of_examination`, `place_of_examination`, `license_number`, `license_validity`, `created_at`) VALUES
(6, '23-20000', 'Professional', '10', '2025-05-12', 'Mandaluyong', 'N/A', NULL, '2025-05-20 07:51:15');

-- --------------------------------------------------------

--
-- Table structure for table `colleges`
--

CREATE TABLE `colleges` (
  `college_id` int(11) NOT NULL,
  `college_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colleges`
--

INSERT INTO `colleges` (`college_id`, `college_name`) VALUES
(1, 'College of Arts and Science'),
(2, 'College of Business and Accountancy'),
(3, 'College of Computer Studies'),
(4, 'College of Education'),
(5, 'College of Engineering'),
(6, 'College of Hospitality Management'),
(7, 'College of Nursing');

-- --------------------------------------------------------

--
-- Table structure for table `credentials`
--

CREATE TABLE `credentials` (
  `credential_id` int(11) NOT NULL,
  `faculty_id` varchar(11) NOT NULL,
  `credential_type` enum('PDS','SALN','TOR','Diploma','Certificates','Evaluation') NOT NULL,
  `credential_name` varchar(150) NOT NULL,
  `issued_by` varchar(150) NOT NULL,
  `issued_date` date NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `verified_at` timestamp NULL DEFAULT NULL,
  `status` enum('Pending','Verified','Rejected') NOT NULL DEFAULT 'Pending',
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credentials`
--

INSERT INTO `credentials` (`credential_id`, `faculty_id`, `credential_type`, `credential_name`, `issued_by`, `issued_date`, `expiry_date`, `file_path`, `uploaded_at`, `verified_at`, `status`, `reason`) VALUES
(25, '23-20000', 'PDS', 'PDS', 'Pamantasan ng Lungsod ng Pasig', '2025-05-19', NULL, 'uploads/credentials/682c32ee881187.84283777.pdf', '2025-05-20 07:45:06', '2025-05-20 07:45:06', 'Verified', NULL),
(26, '23-00001', 'PDS', 'PDS', 'Pamantasan ng lungsod ng pasig', '2025-05-22', NULL, 'uploads/credentials/682f7aaab77e32.91061570.pdf', '2025-05-22 19:27:54', '2025-05-22 19:27:54', 'Verified', NULL),
(27, '23-00001', 'SALN', 'SALN', 'Pamantasan ng lungsod ng pasig', '2025-05-22', NULL, 'uploads/credentials/682f7b7131e096.15577666.pdf', '2025-05-22 19:30:57', NULL, 'Pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `faculty_id` varchar(11) NOT NULL,
  `college_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `employment_type` enum('Full-Time','Part-Time') DEFAULT NULL,
  `specialization` varchar(10) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`faculty_id`, `college_id`, `full_name`, `email`, `employment_type`, `specialization`, `contact_number`, `status`, `created_at`) VALUES
('23-00001', 1, 'Benito Villareal', 'villarealbenito@plpasig.edu.ph', 'Part-Time', 'DR', '09123456789', 'Active', '2025-05-20 06:14:00'),
('23-00002', 1, 'Lea S. Velasco', 'velascolea@plpasig.edu.ph', 'Full-Time', 'DR', '09123456789', 'Active', '2025-05-20 06:18:15'),
('23-00003', 1, 'Raymundo Baui', 'bauiraymundo@plpasig.edu.ph', 'Full-Time', 'DR', '09123456789', 'Active', '2025-05-20 06:19:43'),
('23-00004', 1, 'Elena R. Javate', 'javateelena@plpasig.edu.ph', 'Part-Time', 'DR', '09382528344', 'Active', '2025-05-20 06:26:39'),
('23-00005', 1, 'Dr. Carolyn A. Alvero', 'alverocarolyn@plpasig.edu.ph', 'Full-Time', 'DR', '09123456789', 'Active', '2025-05-20 06:27:43'),
('23-00006', 1, 'Maria Cristina Reyes', 'reyesmariacristina@plpasig.edu.ph', 'Full-Time', 'MATH', '09123456789', 'Active', '2025-05-24 04:05:15'),
('23-00007', 1, 'Juan Dela Cruz', 'delacruzjuan@plpasig.edu.ph', 'Part-Time', 'SCI', '09234567890', 'Active', '2025-05-24 04:05:15'),
('23-00008', 1, 'Lourdes Santiago', 'santiagolourdes@plpasig.edu.ph', 'Full-Time', 'ENG', '09345678901', 'Active', '2025-05-24 04:05:15'),
('23-00009', 1, 'Ricardo Santos', 'santosricardo@plpasig.edu.ph', 'Full-Time', 'HIST', '09456789012', 'Active', '2025-05-24 04:05:15'),
('23-00010', 1, 'Amelia Fernandez', 'fernandezamelia@plpasig.edu.ph', 'Part-Time', 'FIL', '09567890123', 'Active', '2025-05-24 04:05:15'),
('23-00011', 1, 'Fernando Torres', 'torresfernando@plpasig.edu.ph', 'Full-Time', 'MATH', '09678901234', 'Active', '2025-05-24 04:05:15'),
('23-00012', 1, 'Carmen Navarro', 'navarrocarmen@plpasig.edu.ph', 'Part-Time', 'SCI', '09789012345', 'Active', '2025-05-24 04:05:15'),
('23-00013', 1, 'Alfredo Gomez', 'gomezalfredo@plpasig.edu.ph', 'Full-Time', 'ENG', '09890123456', 'Active', '2025-05-24 04:05:15'),
('23-00014', 1, 'Isabel Ortega', 'ortegaisabel@plpasig.edu.ph', 'Full-Time', 'HIST', '09901234567', 'Active', '2025-05-24 04:05:15'),
('23-00015', 1, 'Roberto Mendoza', 'mendozaroberto@plpasig.edu.ph', 'Part-Time', 'FIL', '09112345678', 'Active', '2025-05-24 04:05:15'),
('23-00016', 1, 'Teresa Ramos', 'ramosteresa@plpasig.edu.ph', 'Full-Time', 'MATH', '09223456789', 'Active', '2025-05-24 04:05:15'),
('23-00017', 1, 'Eduardo Castro', 'castroeduardo@plpasig.edu.ph', 'Part-Time', 'SCI', '09334567890', 'Active', '2025-05-24 04:05:15'),
('23-00018', 1, 'Lucia Herrera', 'herreralucia@plpasig.edu.ph', 'Full-Time', 'ENG', '09445678901', 'Active', '2025-05-24 04:05:15'),
('23-00019', 1, 'Arturo Vega', 'vegaarturo@plpasig.edu.ph', 'Full-Time', 'HIST', '09556789012', 'Active', '2025-05-24 04:05:15'),
('23-00020', 1, 'Patricia Del Rosario', 'delrosariopatricia@plpasig.edu.ph', 'Part-Time', 'FIL', '09667890123', 'Active', '2025-05-24 04:05:15'),
('23-00021', 1, 'Raul Jimenez', 'jimenezraul@plpasig.edu.ph', 'Full-Time', 'MATH', '09778901234', 'Active', '2025-05-24 04:05:15'),
('23-00022', 1, 'Sofia Aquino', 'aquinosofia@plpasig.edu.ph', 'Part-Time', 'SCI', '09889012345', 'Active', '2025-05-24 04:05:15'),
('23-00023', 1, 'Manuel Bautista', 'bautistamanuel@plpasig.edu.ph', 'Full-Time', 'ENG', '09990123456', 'Active', '2025-05-24 04:05:15'),
('23-00024', 1, 'Adriana Cortez', 'cortezadriana@plpasig.edu.ph', 'Full-Time', 'HIST', '09101234567', 'Active', '2025-05-24 04:05:15'),
('23-00025', 1, 'Francisco Rojas', 'rojaspfrancisco@plpasig.edu.ph', 'Part-Time', 'FIL', '09212345678', 'Active', '2025-05-24 04:05:15'),
('23-00026', 1, 'Gloria Miranda', 'mirandagloria@plpasig.edu.ph', 'Full-Time', 'MATH', '09323456789', 'Active', '2025-05-24 04:05:15'),
('23-00027', 1, 'Ramon Galang', 'galangramon@plpasig.edu.ph', 'Part-Time', 'SCI', '09434567890', 'Active', '2025-05-24 04:05:15'),
('23-00028', 1, 'Beatriz Soriano', 'sorianobeatriz@plpasig.edu.ph', 'Full-Time', 'ENG', '09545678901', 'Active', '2025-05-24 04:05:15'),
('23-00029', 1, 'Sergio Marquez', 'marquezsergio@plpasig.edu.ph', 'Full-Time', 'HIST', '09656789012', 'Active', '2025-05-24 04:05:15'),
('23-00030', 1, 'Victoria Lim', 'limvictoria@plpasig.edu.ph', 'Part-Time', 'FIL', '09767890123', 'Active', '2025-05-24 04:05:15'),
('23-00031', 1, 'Hector Dominguez', 'dominguezhector@plpasig.edu.ph', 'Full-Time', 'MATH', '09878901234', 'Active', '2025-05-24 04:05:15'),
('23-00032', 1, 'Rosa Gutierrez', 'gutierrezrosa@plpasig.edu.ph', 'Part-Time', 'SCI', '09989012345', 'Active', '2025-05-24 04:05:15'),
('23-00033', 1, 'Felipe Navarro', 'navarrofelipe@plpasig.edu.ph', 'Full-Time', 'ENG', '09190123456', 'Active', '2025-05-24 04:05:15'),
('23-00034', 1, 'Consuelo Reyes', 'reyesconsuelo@plpasig.edu.ph', 'Full-Time', 'HIST', '09201234567', 'Active', '2025-05-24 04:05:15'),
('23-00035', 1, 'Enrique Salazar', 'salazarenrique@plpasig.edu.ph', 'Part-Time', 'FIL', '09312345678', 'Active', '2025-05-24 04:05:15'),
('23-00036', 1, 'Aurora Dela Peña', 'delapenaaurora@plpasig.edu.ph', 'Full-Time', 'MATH', '09423456789', 'Active', '2025-05-24 04:05:15'),
('23-00037', 1, 'Gregorio Cordero', 'corderogregorio@plpasig.edu.ph', 'Part-Time', 'SCI', '09534567890', 'Active', '2025-05-24 04:05:15'),
('23-00038', 1, 'Leticia Espinoza', 'espinozaleticia@plpasig.edu.ph', 'Full-Time', 'ENG', '09645678901', 'Active', '2025-05-24 04:05:15'),
('23-00039', 1, 'Oscar Valdez', 'valdezoscar@plpasig.edu.ph', 'Full-Time', 'HIST', '09756789012', 'Active', '2025-05-24 04:05:15'),
('23-00040', 1, 'Esperanza Molina', 'molinaesperanza@plpasig.edu.ph', 'Part-Time', 'FIL', '09867890123', 'Active', '2025-05-24 04:05:15'),
('23-00041', 1, 'Rogelio Cabrera', 'cabrerarogelio@plpasig.edu.ph', 'Full-Time', 'MATH', '09978901234', 'Active', '2025-05-24 04:05:15'),
('23-00042', 1, 'Nora Agustin', 'agustinnora@plpasig.edu.ph', 'Part-Time', 'SCI', '09189012345', 'Active', '2025-05-24 04:05:15'),
('23-00043', 1, 'Armando Pineda', 'pinedaarmando@plpasig.edu.ph', 'Full-Time', 'ENG', '09290123456', 'Active', '2025-05-24 04:05:15'),
('23-00044', 1, 'Lydia Castillo', 'castillolydia@plpasig.edu.ph', 'Full-Time', 'HIST', '09301234567', 'Active', '2025-05-24 04:05:15'),
('23-00045', 1, 'Reynaldo Mercado', 'mercadoreynaldo@plpasig.edu.ph', 'Part-Time', 'FIL', '09412345678', 'Active', '2025-05-24 04:05:15'),
('23-20000', 3, 'Riegie D. Tan', 'tanriegie@plpasig.edu.ph', 'Full-Time', 'DIT', '09123456789', 'Active', '2025-05-20 06:29:30'),
('23-20007', 3, 'Jayson Daluyon', 'daluyonjayson@plpasig.edu.ph', 'Part-Time', 'MSIT', '09382528344', 'Active', '2025-05-08 20:31:51'),
('23-20008', 3, 'Rebecca Fajardo', 'fajardorebecca@plpasig.edu.ph', 'Full-Time', 'MSEE', '09183456989', 'Active', '2025-05-08 20:31:51'),
('23-20009', 3, 'Catherine Sorbito', 'sorbitocatherine@plpasig.edu.ph', 'Full-Time', 'MSIT', '09171122334', 'Active', '2025-05-08 20:31:51'),
('23-20010', 3, 'Maricel D. Lopez', 'lopezmaricel@plpasig.edu.ph', 'Full-Time', 'DIT', '09123456789', 'Active', '2025-05-24 04:06:25'),
('23-20011', 3, 'Allan S. Garcia', 'garciaallan@plpasig.edu.ph', 'Part-Time', 'MSIT', '09234567890', 'Active', '2025-05-24 04:06:25'),
('23-20012', 3, 'Jennifer T. Cruz', 'cruzjennifer@plpasig.edu.ph', 'Full-Time', 'MSEE', '09345678901', 'Active', '2025-05-24 04:06:25'),
('23-20013', 3, 'Mark Anthony Reyes', 'reyesmarkanthony@plpasig.edu.ph', 'Full-Time', 'MSIT', '09456789012', 'Active', '2025-05-24 04:06:25'),
('23-20014', 3, 'Cynthia P. Mendoza', 'mendozacynthia@plpasig.edu.ph', 'Part-Time', 'DIT', '09567890123', 'Active', '2025-05-24 04:06:25'),
('23-20015', 3, 'Rodolfo V. Santos', 'santosrodolfo@plpasig.edu.ph', 'Full-Time', 'MSIT', '09678901234', 'Active', '2025-05-24 04:06:25'),
('23-20016', 3, 'Lorna F. Torres', 'torreslorna@plpasig.edu.ph', 'Part-Time', 'MSEE', '09789012345', 'Active', '2025-05-24 04:06:25'),
('23-20017', 3, 'Arnold Q. Dela Cruz', 'delacruzarnold@plpasig.edu.ph', 'Full-Time', 'MSIT', '09890123456', 'Active', '2025-05-24 04:06:25'),
('23-20018', 3, 'Marissa G. Fernandez', 'fernandezmarissa@plpasig.edu.ph', 'Full-Time', 'DIT', '09901234567', 'Active', '2025-05-24 04:06:25'),
('23-20019', 3, 'Romeo H. Lim', 'limromeo@plpasig.edu.ph', 'Part-Time', 'MSIT', '09112345678', 'Active', '2025-05-24 04:06:25'),
('23-20020', 3, 'Evelyn J. Ramos', 'ramosevelyn@plpasig.edu.ph', 'Full-Time', 'MSEE', '09223456789', 'Active', '2025-05-24 04:06:25'),
('23-20021', 3, 'Dennis K. Ortega', 'ortegadennis@plpasig.edu.ph', 'Part-Time', 'MSIT', '09334567890', 'Active', '2025-05-24 04:06:25'),
('23-20022', 3, 'Gina L. Castro', 'castrogina@plpasig.edu.ph', 'Full-Time', 'DIT', '09445678901', 'Active', '2025-05-24 04:06:25'),
('23-20023', 3, 'Ferdinand M. Herrera', 'herreraferdinand@plpasig.edu.ph', 'Full-Time', 'MSIT', '09556789012', 'Active', '2025-05-24 04:06:25'),
('23-20024', 3, 'Shirley N. Vega', 'vegashirley@plpasig.edu.ph', 'Part-Time', 'MSEE', '09667890123', 'Active', '2025-05-24 04:06:25'),
('23-20025', 3, 'Ricardo O. Del Rosario', 'delrosarioricardo@plpasig.edu.ph', 'Full-Time', 'MSIT', '09778901234', 'Active', '2025-05-24 04:06:25'),
('23-20026', 3, 'Angelita P. Jimenez', 'jimenezangelita@plpasig.edu.ph', 'Part-Time', 'DIT', '09889012345', 'Active', '2025-05-24 04:06:25'),
('23-20027', 3, 'Benjamin Q. Aquino', 'aquinobenjamin@plpasig.edu.ph', 'Full-Time', 'MSIT', '09990123456', 'Active', '2025-05-24 04:06:25'),
('23-20028', 3, 'Corazon R. Bautista', 'bautistacorazon@plpasig.edu.ph', 'Full-Time', 'MSEE', '09101234567', 'Active', '2025-05-24 04:06:25'),
('23-20029', 3, 'Dante S. Cortez', 'cortezdante@plpasig.edu.ph', 'Part-Time', 'MSIT', '09212345678', 'Active', '2025-05-24 04:06:25'),
('23-20030', 3, 'Elvira T. Rojas', 'rojasevira@plpasig.edu.ph', 'Full-Time', 'DIT', '09323456789', 'Active', '2025-05-24 04:06:25'),
('23-20031', 3, 'Felix U. Miranda', 'mirandafelix@plpasig.edu.ph', 'Part-Time', 'MSIT', '09434567890', 'Active', '2025-05-24 04:06:25'),
('23-20032', 3, 'Grace V. Galang', 'galanggrace@plpasig.edu.ph', 'Full-Time', 'MSEE', '09545678901', 'Active', '2025-05-24 04:06:25'),
('23-20033', 3, 'Hector W. Soriano', 'sorianohector@plpasig.edu.ph', 'Full-Time', 'MSIT', '09656789012', 'Active', '2025-05-24 04:06:25'),
('23-20034', 3, 'Irene X. Marquez', 'marquezirene@plpasig.edu.ph', 'Part-Time', 'DIT', '09767890123', 'Active', '2025-05-24 04:06:25'),
('23-20035', 3, 'Jerry Y. Lim', 'limjerry@plpasig.edu.ph', 'Full-Time', 'MSIT', '09878901234', 'Active', '2025-05-24 04:06:25'),
('23-20036', 3, 'Karen Z. Dominguez', 'dominguezkaren@plpasig.edu.ph', 'Part-Time', 'MSEE', '09989012345', 'Active', '2025-05-24 04:06:25'),
('23-20037', 3, 'Leonardo A. Gutierrez', 'gutierrezleonardo@plpasig.edu.ph', 'Full-Time', 'MSIT', '09190123456', 'Active', '2025-05-24 04:06:25'),
('23-20038', 3, 'Marilyn B. Navarro', 'navarromarilyn@plpasig.edu.ph', 'Full-Time', 'DIT', '09201234567', 'Active', '2025-05-24 04:06:25'),
('23-20039', 3, 'Nestor C. Reyes', 'reyesnestor@plpasig.edu.ph', 'Part-Time', 'MSIT', '09312345678', 'Active', '2025-05-24 04:06:25'),
('23-20040', 3, 'Olivia D. Salazar', 'salazarolivia@plpasig.edu.ph', 'Full-Time', 'MSEE', '09423456789', 'Active', '2025-05-24 04:06:25'),
('23-20041', 3, 'Patrick E. Dela Peña', 'delapenapatrick@plpasig.edu.ph', 'Part-Time', 'MSIT', '09534567890', 'Active', '2025-05-24 04:06:25'),
('23-20042', 3, 'Queenie F. Cordero', 'corderoqueenie@plpasig.edu.ph', 'Full-Time', 'DIT', '09645678901', 'Active', '2025-05-24 04:06:25'),
('23-20043', 3, 'Ramon G. Espinoza', 'espinoramon@plpasig.edu.ph', 'Full-Time', 'MSIT', '09756789012', 'Active', '2025-05-24 04:06:25'),
('23-20044', 3, 'Susan H. Valdez', 'valdezsusan@plpasig.edu.ph', 'Part-Time', 'MSEE', '09867890123', 'Active', '2025-05-24 04:06:25'),
('23-20045', 3, 'Tomas I. Molina', 'molinatomas@plpasig.edu.ph', 'Full-Time', 'MSIT', '09978901234', 'Active', '2025-05-24 04:06:25'),
('23-20046', 3, 'Ursula J. Cabrera', 'cabreraursula@plpasig.edu.ph', 'Part-Time', 'DIT', '09189012345', 'Active', '2025-05-24 04:06:25'),
('23-20047', 3, 'Vicente K. Agustin', 'agustinvicente@plpasig.edu.ph', 'Full-Time', 'MSIT', '09290123456', 'Active', '2025-05-24 04:06:25'),
('23-20048', 3, 'Wilma L. Pineda', 'pinedawilma@plpasig.edu.ph', 'Full-Time', 'MSEE', '09301234567', 'Active', '2025-05-24 04:06:25'),
('23-20049', 3, 'Xavier M. Castillo', 'castilloxavier@plpasig.edu.ph', 'Part-Time', 'MSIT', '09412345678', 'Active', '2025-05-24 04:06:25'),
('23-30000', 5, 'Godofredo S. Zapanta Jr.', 'zapantagodofredo@plpasig.edu.ph', 'Full-Time', 'ENG', '09382528344', 'Active', '2025-05-20 06:31:22'),
('23-30001', 5, 'Alberto A. Habrero', 'habreroalberto@plpasig.edu.ph', 'Full-Time', 'ENG', '09637298381', 'Active', '2025-05-20 06:31:22'),
('23-30002', 5, 'Karen V. Arguelles', 'arguelleskaren@plpasig.edu.ph', 'Full-Time', 'ENG', '09382528344', 'Active', '2025-05-20 06:36:44'),
('23-30003', 5, 'Jonathan V. Diosana', 'diosanajonathan@plpasig.edu.ph', 'Full-Time', 'DOC ENG', '09637298381', 'Active', '2025-05-20 06:36:44'),
('23-30004', 5, 'Antonio L. Suinan', 'suinanantonio@plpasig.edu.ph', 'Part-Time', 'ENG', '09126538471', 'Active', '2025-05-20 06:36:44'),
('23-30005', 5, 'Romeo V. Bawa', 'bawaromeo@plpasig.edu.ph', 'Part-Time', 'ENG', '0913583628492', 'Active', '2025-05-20 06:36:44'),
('23-30006', 5, 'Yvonne N. Mercado', 'mercadoyvonne@plpasig.edu.ph', 'Full-Time', 'ENG', '09123456789', 'Active', '2025-05-24 04:07:51'),
('23-30007', 5, 'Zaldy O. Lopez', 'lopezaldy@plpasig.edu.ph', 'Part-Time', 'DOC ENG', '09234567890', 'Active', '2025-05-24 04:07:51'),
('23-30008', 5, 'Andrea P. Garcia', 'garciaandrea@plpasig.edu.ph', 'Full-Time', 'ENG', '09345678901', 'Active', '2025-05-24 04:07:51'),
('23-30009', 5, 'Bobby Q. Cruz', 'cruzbobby@plpasig.edu.ph', 'Full-Time', 'DOC ENG', '09456789012', 'Active', '2025-05-24 04:07:51'),
('23-30010', 5, 'Cecilia R. Reyes', 'reyescecilia@plpasig.edu.ph', 'Part-Time', 'ENG', '09567890123', 'Active', '2025-05-24 04:07:51'),
('23-30011', 5, 'Danny S. Mendoza', 'mendozadanny@plpasig.edu.ph', 'Full-Time', 'DOC ENG', '09678901234', 'Active', '2025-05-24 04:07:51'),
('23-30012', 5, 'Elena T. Santos', 'santoselena@plpasig.edu.ph', 'Part-Time', 'ENG', '09789012345', 'Active', '2025-05-24 04:07:51'),
('23-30013', 5, 'Freddie U. Torres', 'torresfreddie@plpasig.edu.ph', 'Full-Time', 'DOC ENG', '09890123456', 'Active', '2025-05-24 04:07:51'),
('23-30014', 5, 'Gloria V. Dela Cruz', 'delacruzgloria@plpasig.edu.ph', 'Full-Time', 'ENG', '09901234567', 'Active', '2025-05-24 04:07:51'),
('23-30015', 5, 'Henry W. Fernandez', 'fernandezhenry@plpasig.edu.ph', 'Part-Time', 'DOC ENG', '09112345678', 'Active', '2025-05-24 04:07:51'),
('23-30016', 5, 'Iris X. Lim', 'limiris@plpasig.edu.ph', 'Full-Time', 'ENG', '09223456789', 'Active', '2025-05-24 04:07:51'),
('23-30017', 5, 'Joel Y. Ramos', 'ramosjoel@plpasig.edu.ph', 'Part-Time', 'DOC ENG', '09334567890', 'Active', '2025-05-24 04:07:51'),
('23-30018', 5, 'Kaye Z. Ortega', 'ortegakaye@plpasig.edu.ph', 'Full-Time', 'ENG', '09445678901', 'Active', '2025-05-24 04:07:51'),
('23-30019', 5, 'Lester A. Castro', 'castrolester@plpasig.edu.ph', 'Full-Time', 'DOC ENG', '09556789012', 'Active', '2025-05-24 04:07:51'),
('23-30020', 5, 'Monica B. Herrera', 'herreramonica@plpasig.edu.ph', 'Part-Time', 'ENG', '09667890123', 'Active', '2025-05-24 04:07:51'),
('23-30021', 5, 'Norberto C. Vega', 'veganorberto@plpasig.edu.ph', 'Full-Time', 'DOC ENG', '09778901234', 'Active', '2025-05-24 04:07:51'),
('23-30022', 5, 'Ophelia D. Del Rosario', 'delrosarioophelia@plpasig.edu.ph', 'Part-Time', 'ENG', '09889012345', 'Active', '2025-05-24 04:07:51'),
('23-30023', 5, 'Pedro E. Jimenez', 'jimenezpedro@plpasig.edu.ph', 'Full-Time', 'DOC ENG', '09990123456', 'Active', '2025-05-24 04:07:51'),
('23-30024', 5, 'Quincy F. Aquino', 'aquinoquincy@plpasig.edu.ph', 'Full-Time', 'ENG', '09101234567', 'Active', '2025-05-24 04:07:51'),
('23-30025', 5, 'Rosalinda G. Bautista', 'bautistarosalinda@plpasig.edu.ph', 'Part-Time', 'DOC ENG', '09212345678', 'Active', '2025-05-24 04:07:51'),
('23-30026', 5, 'Samuel H. Cortez', 'cortezsamuel@plpasig.edu.ph', 'Full-Time', 'ENG', '09323456789', 'Active', '2025-05-24 04:07:51'),
('23-30027', 5, 'Teresita I. Rojas', 'rojasteresita@plpasig.edu.ph', 'Part-Time', 'DOC ENG', '09434567890', 'Active', '2025-05-24 04:07:51'),
('23-30028', 5, 'Umberto J. Miranda', 'mirandaumberto@plpasig.edu.ph', 'Full-Time', 'ENG', '09545678901', 'Active', '2025-05-24 04:07:51'),
('23-30029', 5, 'Violeta K. Galang', 'galangvioleta@plpasig.edu.ph', 'Full-Time', 'DOC ENG', '09656789012', 'Active', '2025-05-24 04:07:51'),
('23-30030', 5, 'Winston L. Soriano', 'sorianowinston@plpasig.edu.ph', 'Part-Time', 'ENG', '09767890123', 'Active', '2025-05-24 04:07:51'),
('23-30031', 5, 'Xenia M. Marquez', 'marquezxenia@plpasig.edu.ph', 'Full-Time', 'DOC ENG', '09878901234', 'Active', '2025-05-24 04:07:51'),
('23-30032', 5, 'Yancy N. Lim', 'limyancy@plpasig.edu.ph', 'Part-Time', 'ENG', '09989012345', 'Active', '2025-05-24 04:07:51'),
('23-30033', 5, 'Zenaida O. Dominguez', 'dominguezzenaida@plpasig.edu.ph', 'Full-Time', 'DOC ENG', '09190123456', 'Active', '2025-05-24 04:07:51'),
('23-30034', 5, 'Alvin P. Gutierrez', 'gutierrezalvin@plpasig.edu.ph', 'Full-Time', 'ENG', '09201234567', 'Active', '2025-05-24 04:07:51'),
('23-30035', 5, 'Bernadette Q. Navarro', 'navarrobernadette@plpasig.edu.ph', 'Part-Time', 'DOC ENG', '09312345678', 'Active', '2025-05-24 04:07:51'),
('23-30036', 5, 'Carlos R. Reyes', 'reyescarlos@plpasig.edu.ph', 'Full-Time', 'ENG', '09423456789', 'Active', '2025-05-24 04:07:51'),
('23-30037', 5, 'Daisy S. Salazar', 'salazardaisy@plpasig.edu.ph', 'Part-Time', 'DOC ENG', '09534567890', 'Active', '2025-05-24 04:07:51'),
('23-30038', 5, 'Efren T. Dela Peña', 'delapenaefren@plpasig.edu.ph', 'Full-Time', 'ENG', '09645678901', 'Active', '2025-05-24 04:07:51'),
('23-30039', 5, 'Fe U. Cordero', 'corderoafe@plpasig.edu.ph', 'Full-Time', 'DOC ENG', '09756789012', 'Active', '2025-05-24 04:07:51'),
('23-30040', 5, 'Greg V. Espinoza', 'espinozagreg@plpasig.edu.ph', 'Part-Time', 'ENG', '09867890123', 'Active', '2025-05-24 04:07:51'),
('23-30041', 5, 'Helen W. Valdez', 'valdezhelen@plpasig.edu.ph', 'Full-Time', 'DOC ENG', '09978901234', 'Active', '2025-05-24 04:07:51'),
('23-30042', 5, 'Ivan X. Molina', 'molinaivan@plpasig.edu.ph', 'Part-Time', 'ENG', '09189012345', 'Active', '2025-05-24 04:07:51'),
('23-30043', 5, 'Janet Y. Cabrera', 'cabrerajanet@plpasig.edu.ph', 'Full-Time', 'DOC ENG', '09290123456', 'Active', '2025-05-24 04:07:51'),
('23-30044', 5, 'Kevin Z. Agustin', 'agustinkevin@plpasig.edu.ph', 'Full-Time', 'ENG', '09301234567', 'Active', '2025-05-24 04:07:51'),
('23-30045', 5, 'Laura A. Pineda', 'pinedalaura@plpasig.edu.ph', 'Part-Time', 'DOC ENG', '09412345678', 'Active', '2025-05-24 04:07:51');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_personal_info`
--

CREATE TABLE `faculty_personal_info` (
  `faculty_id` varchar(11) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `birthplace` varchar(150) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `civil_status` enum('Single','Married','Widowed','Separated') DEFAULT NULL,
  `address` text DEFAULT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `gsis_id_no` varchar(11) DEFAULT NULL,
  `pagibig_id_no` varchar(12) DEFAULT NULL,
  `philhealth_no` varchar(12) DEFAULT NULL,
  `sss_no` varchar(10) DEFAULT NULL,
  `tin_no` varchar(12) DEFAULT NULL,
  `citizenship` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_personal_info`
--

INSERT INTO `faculty_personal_info` (`faculty_id`, `birthdate`, `birthplace`, `gender`, `civil_status`, `address`, `height_cm`, `weight_kg`, `blood_type`, `gsis_id_no`, `pagibig_id_no`, `philhealth_no`, `sss_no`, `tin_no`, `citizenship`) VALUES
('23-00001', '0000-00-00', '', 'Male', '', '', 170.00, 70.00, '', '', '', '', '', '', ''),
('23-00002', '0000-00-00', '', 'Female', '', '', 165.00, 45.00, '', '', '', '', '', '', ''),
('23-00003', '0000-00-00', '', 'Male', '', '', 169.00, 55.00, '', '', '', '', '', '', ''),
('23-00004', '0000-00-00', '', 'Female', '', '', 154.00, 51.00, '', '', '', '', '', '', ''),
('23-00005', '0000-00-00', '', 'Female', '', '', 156.00, 49.00, '', '', '', '', '', '', ''),
('23-00006', '0000-00-00', '', 'Female', '', '', 0.00, 0.00, '', '', '', '', '', '', ''),
('23-00007', '0000-00-00', '', 'Male', '', '', 165.00, 60.00, '', '', '', '', '', '', ''),
('23-00008', '0000-00-00', '', 'Female', '', '', 160.00, 55.00, '', '', '', '', '', '', ''),
('23-00009', '0000-00-00', '', 'Male', '', '', 170.00, 65.00, '', '', '', '', '', '', ''),
('23-00010', '0000-00-00', '', 'Female', '', '', 158.00, 52.00, '', '', '', '', '', '', ''),
('23-00011', '0000-00-00', '', 'Male', '', '', 172.00, 70.00, '', '', '', '', '', '', ''),
('23-00012', '0000-00-00', '', 'Female', '', '', 162.00, 58.00, '', '', '', '', '', '', ''),
('23-00013', '0000-00-00', '', 'Male', '', '', 168.00, 62.00, '', '', '', '', '', '', ''),
('23-00014', '0000-00-00', '', 'Female', '', '', 155.00, 50.00, '', '', '', '', '', '', ''),
('23-00015', '0000-00-00', '', 'Male', '', '', 175.00, 75.00, '', '', '', '', '', '', ''),
('23-00016', '0000-00-00', '', 'Female', '', '', 163.00, 57.00, '', '', '', '', '', '', ''),
('23-00017', '0000-00-00', '', 'Male', '', '', 169.00, 68.00, '', '', '', '', '', '', ''),
('23-00018', '0000-00-00', '', 'Female', '', '', 161.00, 54.00, '', '', '', '', '', '', ''),
('23-00019', '0000-00-00', '', 'Male', '', '', 171.00, 72.00, '', '', '', '', '', '', ''),
('23-00020', '0000-00-00', '', 'Female', '', '', 159.00, 53.00, '', '', '', '', '', '', ''),
('23-00021', '0000-00-00', '', 'Male', '', '', 173.00, 74.00, '', '', '', '', '', '', ''),
('23-00022', '0000-00-00', '', 'Female', '', '', 164.00, 56.00, '', '', '', '', '', '', ''),
('23-00023', '0000-00-00', '', 'Male', '', '', 167.00, 63.00, '', '', '', '', '', '', ''),
('23-00024', '0000-00-00', '', 'Female', '', '', 156.00, 51.00, '', '', '', '', '', '', ''),
('23-00025', '0000-00-00', '', 'Male', '', '', 174.00, 73.00, '', '', '', '', '', '', ''),
('23-00026', '0000-00-00', '', 'Female', '', '', 157.00, 52.00, '', '', '', '', '', '', ''),
('23-00027', '0000-00-00', '', 'Male', '', '', 176.00, 76.00, '', '', '', '', '', '', ''),
('23-00028', '0000-00-00', '', 'Female', '', '', 162.00, 55.00, '', '', '', '', '', '', ''),
('23-00029', '0000-00-00', '', 'Male', '', '', 168.00, 64.00, '', '', '', '', '', '', ''),
('23-00030', '0000-00-00', '', 'Female', '', '', 160.00, 54.00, '', '', '', '', '', '', ''),
('23-00031', '0000-00-00', '', 'Male', '', '', 170.00, 69.00, '', '', '', '', '', '', ''),
('23-00032', '0000-00-00', '', 'Female', '', '', 163.00, 56.00, '', '', '', '', '', '', ''),
('23-00033', '0000-00-00', '', 'Male', '', '', 169.00, 67.00, '', '', '', '', '', '', ''),
('23-00034', '0000-00-00', '', 'Female', '', '', 161.00, 55.00, '', '', '', '', '', '', ''),
('23-00035', '0000-00-00', '', 'Male', '', '', 172.00, 71.00, '', '', '', '', '', '', ''),
('23-00036', '0000-00-00', '', 'Female', '', '', 159.00, 53.00, '', '', '', '', '', '', ''),
('23-00037', '0000-00-00', '', 'Male', '', '', 171.00, 70.00, '', '', '', '', '', '', ''),
('23-00038', '0000-00-00', '', 'Female', '', '', 158.00, 52.00, '', '', '', '', '', '', ''),
('23-00039', '0000-00-00', '', 'Male', '', '', 175.00, 74.00, '', '', '', '', '', '', ''),
('23-00040', '0000-00-00', '', 'Female', '', '', 162.00, 57.00, '', '', '', '', '', '', ''),
('23-00041', '0000-00-00', '', 'Male', '', '', 167.00, 65.00, '', '', '', '', '', '', ''),
('23-00042', '0000-00-00', '', 'Female', '', '', 164.00, 58.00, '', '', '', '', '', '', ''),
('23-00043', '0000-00-00', '', 'Male', '', '', 168.00, 66.00, '', '', '', '', '', '', ''),
('23-00044', '0000-00-00', '', 'Female', '', '', 160.00, 54.00, '', '', '', '', '', '', ''),
('23-00045', '0000-00-00', '', 'Male', '', '', 173.00, 72.00, '', '', '', '', '', '', ''),
('23-20000', '0000-00-00', '', 'Male', 'Single', '', 170.00, 70.00, '', '', '', '', '', '', 'Chinese'),
('23-20007', '0000-00-00', '', 'Male', '', '', 165.00, 55.00, '', '', '', '', '', '', ''),
('23-20008', '0000-00-00', '', 'Female', '', '', 164.00, 54.00, '', '', '', '', '', '', ''),
('23-20009', '0000-00-00', '', 'Female', '', '', 164.00, 54.00, '', '', '', '', '', '', ''),
('23-20010', '0000-00-00', '', 'Male', 'Single', '', 170.00, 70.00, '', '', '', '', '', '', ''),
('23-20011', '0000-00-00', '', 'Female', 'Married', '', 165.00, 55.00, '', '', '', '', '', '', ''),
('23-20012', '0000-00-00', '', 'Male', 'Single', '', 168.00, 65.00, '', '', '', '', '', '', ''),
('23-20013', '0000-00-00', '', 'Female', 'Married', '', 160.00, 52.00, '', '', '', '', '', '', ''),
('23-20014', '0000-00-00', '', 'Male', 'Single', '', 172.00, 68.00, '', '', '', '', '', '', ''),
('23-20015', '0000-00-00', '', 'Female', 'Married', '', 163.00, 57.00, '', '', '', '', '', '', ''),
('23-20016', '0000-00-00', '', 'Male', 'Single', '', 169.00, 70.00, '', '', '', '', '', '', ''),
('23-20017', '0000-00-00', '', 'Female', 'Married', '', 162.00, 56.00, '', '', '', '', '', '', ''),
('23-20018', '0000-00-00', '', 'Male', 'Single', '', 171.00, 72.00, '', '', '', '', '', '', ''),
('23-20019', '0000-00-00', '', 'Female', 'Married', '', 164.00, 58.00, '', '', '', '', '', '', ''),
('23-20020', '0000-00-00', '', 'Male', 'Single', '', 167.00, 65.00, '', '', '', '', '', '', ''),
('23-20021', '0000-00-00', '', 'Female', 'Married', '', 159.00, 53.00, '', '', '', '', '', '', ''),
('23-20022', '0000-00-00', '', 'Male', 'Single', '', 173.00, 74.00, '', '', '', '', '', '', ''),
('23-20023', '0000-00-00', '', 'Female', 'Married', '', 161.00, 55.00, '', '', '', '', '', '', ''),
('23-20024', '0000-00-00', '', 'Male', 'Single', '', 170.00, 69.00, '', '', '', '', '', '', ''),
('23-20025', '0000-00-00', '', 'Female', 'Married', '', 166.00, 60.00, '', '', '', '', '', '', ''),
('23-20026', '0000-00-00', '', 'Male', 'Single', '', 168.00, 67.00, '', '', '', '', '', '', ''),
('23-20027', '0000-00-00', '', 'Female', 'Married', '', 162.00, 56.00, '', '', '', '', '', '', ''),
('23-20028', '0000-00-00', '', 'Male', 'Single', '', 174.00, 75.00, '', '', '', '', '', '', ''),
('23-20029', '0000-00-00', '', 'Female', 'Married', '', 160.00, 54.00, '', '', '', '', '', '', ''),
('23-20030', '0000-00-00', '', 'Male', 'Single', '', 169.00, 68.00, '', '', '', '', '', '', ''),
('23-20031', '0000-00-00', '', 'Female', 'Married', '', 163.00, 57.00, '', '', '', '', '', '', ''),
('23-20032', '0000-00-00', '', 'Male', 'Single', '', 171.00, 71.00, '', '', '', '', '', '', ''),
('23-20033', '0000-00-00', '', 'Female', 'Married', '', 165.00, 59.00, '', '', '', '', '', '', ''),
('23-20034', '0000-00-00', '', 'Male', 'Single', '', 167.00, 64.00, '', '', '', '', '', '', ''),
('23-20035', '0000-00-00', '', 'Female', 'Married', '', 158.00, 52.00, '', '', '', '', '', '', ''),
('23-20036', '0000-00-00', '', 'Male', 'Single', '', 172.00, 73.00, '', '', '', '', '', '', ''),
('23-20037', '0000-00-00', '', 'Female', 'Married', '', 164.00, 58.00, '', '', '', '', '', '', ''),
('23-20038', '0000-00-00', '', 'Male', 'Single', '', 170.00, 70.00, '', '', '', '', '', '', ''),
('23-20039', '0000-00-00', '', 'Female', 'Married', '', 161.00, 55.00, '', '', '', '', '', '', ''),
('23-20040', '0000-00-00', '', 'Male', 'Single', '', 168.00, 66.00, '', '', '', '', '', '', ''),
('23-20041', '0000-00-00', '', 'Female', 'Married', '', 163.00, 56.00, '', '', '', '', '', '', ''),
('23-20042', '0000-00-00', '', 'Male', 'Single', '', 173.00, 74.00, '', '', '', '', '', '', ''),
('23-20043', '0000-00-00', '', 'Female', 'Married', '', 159.00, 53.00, '', '', '', '', '', '', ''),
('23-20044', '0000-00-00', '', 'Male', 'Single', '', 169.00, 67.00, '', '', '', '', '', '', ''),
('23-20045', '0000-00-00', '', 'Female', 'Married', '', 162.00, 56.00, '', '', '', '', '', '', ''),
('23-20046', '0000-00-00', '', 'Male', 'Single', '', 171.00, 72.00, '', '', '', '', '', '', ''),
('23-20047', '0000-00-00', '', 'Female', 'Married', '', 164.00, 58.00, '', '', '', '', '', '', ''),
('23-20048', '0000-00-00', '', 'Male', 'Single', '', 167.00, 65.00, '', '', '', '', '', '', ''),
('23-20049', '0000-00-00', '', 'Female', 'Married', '', 160.00, 54.00, '', '', '', '', '', '', ''),
('23-30000', '0000-00-00', '', 'Male', '', '', 164.00, 54.00, '', '', '', '', '', '', ''),
('23-30001', '0000-00-00', '', 'Male', '', '', 167.00, 56.00, '', '', '', '', '', '', ''),
('23-30002', '0000-00-00', '', 'Female', '', '', 157.00, 58.00, '', '', '', '', '', '', ''),
('23-30003', '0000-00-00', '', 'Male', '', '', 162.00, 52.00, '', '', '', '', '', '', ''),
('23-30004', '0000-00-00', '', 'Male', '', '', 167.00, 49.00, '', '', '', '', '', '', ''),
('23-30005', '0000-00-00', '', 'Male', '', '', 160.00, 54.00, '', '', '', '', '', '', ''),
('23-30006', '0000-00-00', '', 'Male', '', '', 170.00, 70.00, '', '', '', '', '', '', ''),
('23-30007', '0000-00-00', '', 'Female', '', '', 165.00, 55.00, '', '', '', '', '', '', ''),
('23-30008', '0000-00-00', '', 'Male', '', '', 168.00, 65.00, '', '', '', '', '', '', ''),
('23-30009', '0000-00-00', '', 'Female', '', '', 160.00, 52.00, '', '', '', '', '', '', ''),
('23-30010', '0000-00-00', '', 'Male', '', '', 172.00, 68.00, '', '', '', '', '', '', ''),
('23-30011', '0000-00-00', '', 'Female', '', '', 163.00, 57.00, '', '', '', '', '', '', ''),
('23-30012', '0000-00-00', '', 'Male', '', '', 169.00, 70.00, '', '', '', '', '', '', ''),
('23-30013', '0000-00-00', '', 'Female', '', '', 162.00, 56.00, '', '', '', '', '', '', ''),
('23-30014', '0000-00-00', '', 'Male', '', '', 171.00, 72.00, '', '', '', '', '', '', ''),
('23-30015', '0000-00-00', '', 'Female', '', '', 164.00, 58.00, '', '', '', '', '', '', ''),
('23-30016', '0000-00-00', '', 'Male', '', '', 167.00, 65.00, '', '', '', '', '', '', ''),
('23-30017', '0000-00-00', '', 'Female', '', '', 159.00, 53.00, '', '', '', '', '', '', ''),
('23-30018', '0000-00-00', '', 'Male', '', '', 173.00, 74.00, '', '', '', '', '', '', ''),
('23-30019', '0000-00-00', '', 'Female', '', '', 161.00, 55.00, '', '', '', '', '', '', ''),
('23-30020', '0000-00-00', '', 'Male', '', '', 170.00, 69.00, '', '', '', '', '', '', ''),
('23-30021', '0000-00-00', '', 'Female', '', '', 166.00, 60.00, '', '', '', '', '', '', ''),
('23-30022', '0000-00-00', '', 'Male', '', '', 168.00, 67.00, '', '', '', '', '', '', ''),
('23-30023', '0000-00-00', '', 'Female', '', '', 162.00, 56.00, '', '', '', '', '', '', ''),
('23-30024', '0000-00-00', '', 'Male', '', '', 174.00, 75.00, '', '', '', '', '', '', ''),
('23-30025', '0000-00-00', '', 'Female', '', '', 160.00, 54.00, '', '', '', '', '', '', ''),
('23-30026', '0000-00-00', '', 'Male', '', '', 169.00, 68.00, '', '', '', '', '', '', ''),
('23-30027', '0000-00-00', '', 'Female', '', '', 163.00, 57.00, '', '', '', '', '', '', ''),
('23-30028', '0000-00-00', '', 'Male', '', '', 171.00, 71.00, '', '', '', '', '', '', ''),
('23-30029', '0000-00-00', '', 'Female', '', '', 165.00, 59.00, '', '', '', '', '', '', ''),
('23-30030', '0000-00-00', '', 'Male', '', '', 167.00, 64.00, '', '', '', '', '', '', ''),
('23-30031', '0000-00-00', '', 'Female', '', '', 158.00, 52.00, '', '', '', '', '', '', ''),
('23-30032', '0000-00-00', '', 'Male', '', '', 172.00, 73.00, '', '', '', '', '', '', ''),
('23-30033', '0000-00-00', '', 'Female', '', '', 164.00, 58.00, '', '', '', '', '', '', ''),
('23-30034', '0000-00-00', '', 'Male', '', '', 170.00, 70.00, '', '', '', '', '', '', ''),
('23-30035', '0000-00-00', '', 'Female', '', '', 161.00, 55.00, '', '', '', '', '', '', ''),
('23-30036', '0000-00-00', '', 'Male', '', '', 168.00, 66.00, '', '', '', '', '', '', ''),
('23-30037', '0000-00-00', '', 'Female', '', '', 163.00, 56.00, '', '', '', '', '', '', ''),
('23-30038', '0000-00-00', '', 'Male', '', '', 173.00, 74.00, '', '', '', '', '', '', ''),
('23-30039', '0000-00-00', '', 'Female', '', '', 159.00, 53.00, '', '', '', '', '', '', ''),
('23-30040', '0000-00-00', '', 'Male', '', '', 169.00, 67.00, '', '', '', '', '', '', ''),
('23-30041', '0000-00-00', '', 'Female', '', '', 162.00, 56.00, '', '', '', '', '', '', ''),
('23-30042', '0000-00-00', '', 'Male', '', '', 171.00, 72.00, '', '', '', '', '', '', ''),
('23-30043', '0000-00-00', '', 'Female', '', '', 164.00, 58.00, '', '', '', '', '', '', ''),
('23-30044', '0000-00-00', '', 'Male', '', '', 167.00, 65.00, '', '', '', '', '', '', ''),
('23-30045', '0000-00-00', '', 'Female', '', '', 160.00, 54.00, '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `teaching_load`
--

CREATE TABLE `teaching_load` (
  `load_id` int(11) NOT NULL,
  `faculty_id` varchar(11) NOT NULL,
  `file_name` varchar(150) NOT NULL,
  `semester` enum('First Semester','Second Semester','Summer') NOT NULL,
  `start_year` year(4) NOT NULL,
  `end_year` year(4) NOT NULL,
  `regular_loads` int(11) NOT NULL,
  `overload_units` int(11) NOT NULL,
  `total_loads` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` enum('Pending','Verified','Rejected') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verified_at` timestamp NULL DEFAULT NULL,
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teaching_load`
--

INSERT INTO `teaching_load` (`load_id`, `faculty_id`, `file_name`, `semester`, `start_year`, `end_year`, `regular_loads`, `overload_units`, `total_loads`, `file_path`, `status`, `created_at`, `verified_at`, `reason`) VALUES
(36, '23-20000', 'Teaching Load SY 2024-2025', 'First Semester', '2024', '2025', 0, 0, 15, 'uploads/teaching_loads/682c3143a99628.19558208.pdf', 'Rejected', '2025-05-20 07:37:39', '2025-05-20 07:41:36', 'wrong pdf file'),
(37, '23-00001', 'Teaching Load SY 2022-2023', 'First Semester', '2022', '2023', 7, 2, 9, 'uploads/teaching_loads/682f661c46f959.11967084.pdf', 'Verified', '2025-05-21 19:37:36', '2025-05-22 19:14:48', NULL),
(38, '23-00001', 'Teaching Load SY 2023-2024', 'Summer', '2023', '2024', 14, 5, 19, 'uploads/teaching_loads/68306cb7338e17.24516362.pdf', 'Rejected', '2025-05-23 12:40:00', '2025-05-23 12:42:05', 'wrong pdf'),
(39, '23-00001', 'Teaching Load SY 2022-2023', 'First Semester', '2022', '2023', 14, 5, 19, 'uploads/teaching_loads/68314c1d6bdc80.74667747.pdf', 'Verified', '2025-05-24 04:33:33', '2025-05-24 04:34:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `training_programs`
--

CREATE TABLE `training_programs` (
  `id` int(11) NOT NULL,
  `faculty_id` varchar(11) NOT NULL,
  `training_title` varchar(255) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `number_of_hours` int(11) DEFAULT NULL,
  `conducted_by` varchar(255) DEFAULT NULL,
  `learning_type` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_programs`
--

INSERT INTO `training_programs` (`id`, `faculty_id`, `training_title`, `date_from`, `date_to`, `number_of_hours`, `conducted_by`, `learning_type`, `created_at`) VALUES
(5, '23-20000', 'MS Office', '2025-05-11', '2025-05-12', 8, 'PLP-CCS', 'Technical', '2025-05-20 07:53:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `college_id` int(11) DEFAULT NULL,
  `faculty_id` varchar(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('Admin','Faculty','Head') NOT NULL,
  `login_attempts` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `college_id`, `faculty_id`, `username`, `password_hash`, `role`, `login_attempts`, `created_at`) VALUES
(1, 1, NULL, 'cas_admin', '$2y$10$oTKZFwOgnpIZH/Ty7CNS7eR5fM6wbwKje6b8q1IHj6bC8t1lZYe8i', 'Admin', 0, '2025-05-08 19:31:22'),
(2, 2, NULL, 'cba_admin', '$2y$10$woWTotNZbXuEu9AxZ.sYeOc4AIbsZYX7YZmKQ4o7bfHkesLAr/PwS', 'Admin', 0, '2025-05-08 19:31:22'),
(3, 3, NULL, 'ccs_admin', '$2y$10$gsXiGNt/.A8k71efwfWVneBKbDzk6YRYCeShhanHh1J6oS4Kzz3nC', 'Admin', 0, '2025-05-08 19:31:22'),
(4, 4, NULL, 'coed_admin', '$2y$10$ioZ4Gs.poic3F0Osuc92HOLWtbpC5HWpTCPto5o80V/SUf1..Yjnq', 'Admin', 0, '2025-05-08 19:31:22'),
(5, 5, NULL, 'coe_admin', '$2y$10$b4jhoCPOwCMc1f.sQIAu8OqiJ/RjbSXRa1TNMO4pcpO.4fGVdWKgW', 'Admin', 0, '2025-05-08 19:31:22'),
(6, 6, NULL, 'chm_admin', '$2y$10$CQr/BPeP4IbeOHz9jtBdX.XF3P58ZopDCa4qf9q.vslQNAoWnV9NO', 'Admin', 0, '2025-05-08 19:31:22'),
(7, 7, NULL, 'con_admin', '$2y$10$tROc5J6xksJ3JJXvD8igaOYUhGCDTnAVvdnb7xpFmLWDdlAV64fKm', 'Admin', 0, '2025-05-08 19:31:22'),
(14, 3, '23-20007', '23-20007', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-08 20:49:47'),
(15, 3, '23-20008', '23-20008', '$2y$10$cl5jGsfWIjjXdcJOLEtwh.jt.xYYsUfF8UWTAgU1YnMZiecNVU5Ru', 'Faculty', 0, '2025-05-08 20:49:47'),
(16, 3, '23-20009', '23-20009', '$2y$10$cMwhUbVEGLnfiqOXin4dWOkVlqoOt68TEOkQe0PkkJEizsTb6SLf.', 'Faculty', 0, '2025-05-08 20:49:47'),
(47, 1, '23-00001', '23-00001', '$2y$10$UQSxLYYqppmRKNGYsyVv4O2YVaxx9l0oSW.XH60ax3TEUR2voItN2', 'Faculty', 0, '2025-05-20 06:17:39'),
(49, 1, '23-00002', '23-00002', '$2y$10$TXdkd0fFghRYpIBMhTcOnO0wwQdvZ739bWGrvpmTjXGYEEfspS1Re', 'Faculty', 0, '2025-05-20 06:50:49'),
(50, 1, '23-00004', '23-00004', '$2y$10$qTgZR28i/2nJGNiBirujEOvG2GoEBopUTAxpV0ghekod86Cy2.SWS', 'Faculty', 0, '2025-05-20 06:57:30'),
(51, 1, '23-00003', '23-00003', '$2y$10$dQyTs2JjtJkisC.NXV5Qq.wwt15D24elr1RG5VdJyaxF9tAEP6N/u', 'Faculty', 0, '2025-05-20 06:57:30'),
(52, 1, '23-00005', '23-00005', '$2y$10$nHyfTThdRnxkUkIO.KxLC.NJ8V.st0OZN6HW7/F6FyZ2wekm/Lq1K', 'Faculty', 0, '2025-05-20 07:08:18'),
(53, 3, '23-20000', '23-20000', '$2y$10$uhp/NtciKbDEIAALZ3xQ5uJTyTaaIEER8xkIP0JciajKxNsCw8PQi', 'Faculty', 0, '2025-05-20 07:08:18'),
(54, 5, '23-30000', '23-30000', '$2y$10$pa8b0TDhl5aIqbiwp/8TOubkyUJMjeqBNbTf0a1SJMEr4MCPTw0Gi', 'Faculty', 0, '2025-05-20 07:13:50'),
(55, 5, '23-30001', '23-30001', '$2y$10$pahJVsO/xp8aJMPPNcCygeuGk6PGaQlgswhmP9HvD.D54iVkFfH82', 'Faculty', 0, '2025-05-20 07:13:50'),
(56, 5, '23-30002', '23-30002', '$2y$10$QpN3UfzrsVyPCJyZSozlJescKsyM/QmFjSdVGb7s.oWVML/wCgMFS', 'Faculty', 0, '2025-05-20 07:13:50'),
(57, 5, '23-30003', '23-30003', '$2y$10$B5vYJvXsvCWy4uEH3B3lhOTPPiJa32zS/1mqgDYe9PyScCPpltCNS', 'Faculty', 0, '2025-05-20 07:13:50'),
(58, 5, '23-30004', '23-30004', '$2y$10$EKr9E48AmwV0l6KK2LlsiuTf03mhpjANyoDSe5I3HdXKOGNZP1oaW', 'Faculty', 0, '2025-05-20 07:13:50'),
(59, 5, '23-30005', '23-30005', '$2y$10$AKmHJWh0pU7Ag7tS0aM6lu29sezaYp032vWE6BFYMU4uk4WGGrov.', 'Faculty', 0, '2025-05-20 07:13:50'),
(60, 1, '23-00005', 'cas_head', '$2y$10$dZv/GFTugnUYUK5LkTG1EOSAbJYxzU0f4sVLV7WasJVY2OfMCyg2G', 'Head', 0, '2025-05-23 20:12:33'),
(61, 3, '23-20000', 'ccs_head', '$2y$10$koRS9JxQJ8ZkjmT4Zhtt8e6jUYDupPaRDNkVOiERj1W/9xJAMgbty', 'Head', 0, '2025-05-23 20:13:01'),
(62, 5, '23-30000', 'coe_head', '$2y$10$1Fb0Rfkuq3Q4WjpXXPX/D.ioDc3yZrFXs35.geYryaO.6xC1rtU4e', 'Head', 0, '2025-05-23 20:13:38'),
(63, 1, '23-00006', '23-00006', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(64, 1, '23-00007', '23-00007', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(65, 1, '23-00008', '23-00008', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(66, 1, '23-00009', '23-00009', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(67, 1, '23-00010', '23-00010', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(68, 1, '23-00011', '23-00011', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(69, 1, '23-00012', '23-00012', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(70, 1, '23-00013', '23-00013', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(71, 1, '23-00014', '23-00014', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(72, 1, '23-00015', '23-00015', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(73, 1, '23-00016', '23-00016', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(74, 1, '23-00017', '23-00017', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(75, 1, '23-00018', '23-00018', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(76, 1, '23-00019', '23-00019', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(77, 1, '23-00020', '23-00020', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(78, 1, '23-00021', '23-00021', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(79, 1, '23-00022', '23-00022', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(80, 1, '23-00023', '23-00023', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(81, 1, '23-00024', '23-00024', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(82, 1, '23-00025', '23-00025', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(83, 1, '23-00026', '23-00026', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(84, 1, '23-00027', '23-00027', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(85, 1, '23-00028', '23-00028', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(86, 1, '23-00029', '23-00029', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(87, 1, '23-00030', '23-00030', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(88, 1, '23-00031', '23-00031', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(89, 1, '23-00032', '23-00032', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(90, 1, '23-00033', '23-00033', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(91, 1, '23-00034', '23-00034', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(92, 1, '23-00035', '23-00035', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(93, 1, '23-00036', '23-00036', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(94, 1, '23-00037', '23-00037', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(95, 1, '23-00038', '23-00038', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(96, 1, '23-00039', '23-00039', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(97, 1, '23-00040', '23-00040', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(98, 1, '23-00041', '23-00041', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(99, 1, '23-00042', '23-00042', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(100, 1, '23-00043', '23-00043', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(101, 1, '23-00044', '23-00044', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(102, 1, '23-00045', '23-00045', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(103, 3, '23-20010', '23-20010', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(104, 3, '23-20011', '23-20011', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(105, 3, '23-20012', '23-20012', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(106, 3, '23-20013', '23-20013', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(107, 3, '23-20014', '23-20014', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(108, 3, '23-20015', '23-20015', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(109, 3, '23-20016', '23-20016', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(110, 3, '23-20017', '23-20017', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(111, 3, '23-20018', '23-20018', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(112, 3, '23-20019', '23-20019', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(113, 3, '23-20020', '23-20020', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(114, 3, '23-20021', '23-20021', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(115, 3, '23-20022', '23-20022', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(116, 3, '23-20023', '23-20023', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(117, 3, '23-20024', '23-20024', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(118, 3, '23-20025', '23-20025', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(119, 3, '23-20026', '23-20026', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(120, 3, '23-20027', '23-20027', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(121, 3, '23-20028', '23-20028', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(122, 3, '23-20029', '23-20029', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(123, 3, '23-20030', '23-20030', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(124, 3, '23-20031', '23-20031', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(125, 3, '23-20032', '23-20032', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(126, 3, '23-20033', '23-20033', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(127, 3, '23-20034', '23-20034', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(128, 3, '23-20035', '23-20035', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(129, 3, '23-20036', '23-20036', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(130, 3, '23-20037', '23-20037', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(131, 3, '23-20038', '23-20038', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(132, 3, '23-20039', '23-20039', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(133, 3, '23-20040', '23-20040', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(134, 3, '23-20041', '23-20041', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(135, 3, '23-20042', '23-20042', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(136, 3, '23-20043', '23-20043', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(137, 3, '23-20044', '23-20044', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(138, 3, '23-20045', '23-20045', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(139, 3, '23-20046', '23-20046', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(140, 3, '23-20047', '23-20047', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(141, 3, '23-20048', '23-20048', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(142, 3, '23-20049', '23-20049', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(143, 5, '23-30006', '23-30006', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(144, 5, '23-30007', '23-30007', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(145, 5, '23-30008', '23-30008', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(146, 5, '23-30009', '23-30009', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(147, 5, '23-30010', '23-30010', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(148, 5, '23-30011', '23-30011', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(149, 5, '23-30012', '23-30012', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(150, 5, '23-30013', '23-30013', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(151, 5, '23-30014', '23-30014', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(152, 5, '23-30015', '23-30015', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(153, 5, '23-30016', '23-30016', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(154, 5, '23-30017', '23-30017', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(155, 5, '23-30018', '23-30018', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(156, 5, '23-30019', '23-30019', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(157, 5, '23-30020', '23-30020', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(158, 5, '23-30021', '23-30021', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(159, 5, '23-30022', '23-30022', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(160, 5, '23-30023', '23-30023', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(161, 5, '23-30024', '23-30024', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(162, 5, '23-30025', '23-30025', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(163, 5, '23-30026', '23-30026', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(164, 5, '23-30027', '23-30027', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(165, 5, '23-30028', '23-30028', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(166, 5, '23-30029', '23-30029', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(167, 5, '23-30030', '23-30030', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(168, 5, '23-30031', '23-30031', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(169, 5, '23-30032', '23-30032', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(170, 5, '23-30033', '23-30033', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(171, 5, '23-30034', '23-30034', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(172, 5, '23-30035', '23-30035', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(173, 5, '23-30036', '23-30036', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(174, 5, '23-30037', '23-30037', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(175, 5, '23-30038', '23-30038', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(176, 5, '23-30039', '23-30039', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(177, 5, '23-30040', '23-30040', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(178, 5, '23-30041', '23-30041', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(179, 5, '23-30042', '23-30042', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(180, 5, '23-30043', '23-30043', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(181, 5, '23-30044', '23-30044', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42'),
(182, 5, '23-30045', '23-30045', '$2y$10$nB/wtNg/4dNI5WRoyaxJk.KGFVJGloOQInm378YhojGtfcS7jm/a6', 'Faculty', 0, '2025-05-24 04:08:42');

-- --------------------------------------------------------

--
-- Table structure for table `user_logins`
--

CREATE TABLE `user_logins` (
  `login_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `college_id` int(11) NOT NULL,
  `login_time` datetime NOT NULL DEFAULT current_timestamp(),
  `logout_time` datetime DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `session_status` enum('active','completed','timeout') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_logins`
--

INSERT INTO `user_logins` (`login_id`, `user_id`, `college_id`, `login_time`, `logout_time`, `ip_address`, `user_agent`, `session_status`) VALUES
(93, 47, 1, '2025-05-24 03:56:58', '2025-05-24 03:57:26', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'completed'),
(94, 47, 1, '2025-05-24 04:03:04', '2025-05-24 04:03:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'completed'),
(95, 51, 1, '2025-05-24 04:06:18', '2025-05-24 04:09:03', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'completed'),
(96, 51, 1, '2025-05-24 04:19:23', '2025-05-24 04:21:39', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'completed'),
(97, 47, 1, '2025-05-24 11:32:14', '2025-05-24 11:47:03', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'completed'),
(98, 63, 1, '2025-05-24 12:09:26', '2025-05-24 12:15:59', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'completed'),
(99, 14, 3, '2025-05-24 12:16:14', '2025-05-24 12:16:40', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'completed'),
(100, 64, 1, '2025-05-24 12:16:50', '2025-05-24 12:18:12', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'completed'),
(101, 47, 1, '2025-05-24 12:19:07', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36', 'active');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_credentials_report`
-- (See below for the actual view)
--
CREATE TABLE `vw_credentials_report` (
`credential_id` int(11)
,`load_id` int(11)
,`faculty_id` varchar(11)
,`full_name` varchar(100)
,`college_name` varchar(255)
,`credential_type` varchar(13)
,`credential_name` varchar(150)
,`semester` enum('First Semester','Second Semester','Summer')
,`school_year` varchar(9)
,`total_loads` int(11)
,`file_path` varchar(255)
,`source_type` varchar(12)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_faculty_users`
-- (See below for the actual view)
--
CREATE TABLE `vw_faculty_users` (
`faculty_id` varchar(11)
,`college_name` varchar(255)
,`full_name` varchar(100)
,`email` varchar(100)
,`username` varchar(50)
,`status` enum('Active','Inactive')
);

-- --------------------------------------------------------

--
-- Table structure for table `work_experience`
--

CREATE TABLE `work_experience` (
  `id` int(11) NOT NULL,
  `faculty_id` varchar(11) NOT NULL,
  `position_title` varchar(150) DEFAULT NULL,
  `department_or_agency` varchar(255) DEFAULT NULL,
  `monthly_salary` decimal(10,2) DEFAULT NULL,
  `salary_grade_step` varchar(50) DEFAULT NULL,
  `appointment_status` varchar(100) DEFAULT NULL,
  `is_government_service` enum('Yes','No') DEFAULT 'No',
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `work_experience`
--

INSERT INTO `work_experience` (`id`, `faculty_id`, `position_title`, `department_or_agency`, `monthly_salary`, `salary_grade_step`, `appointment_status`, `is_government_service`, `date_from`, `date_to`, `created_at`) VALUES
(5, '23-20000', 'Dean', 'College of Computer Studies - Pamantasan ng Lungsod ng Pasig', 123456.00, '1234', 'Full Time', 'Yes', '2025-05-04', '2029-10-24', '2025-05-20 07:52:28');

-- --------------------------------------------------------

--
-- Structure for view `vw_credentials_report`
--
DROP TABLE IF EXISTS `vw_credentials_report`;

CREATE VIEW `vw_credentials_report` AS SELECT `c`.`credential_id` AS `credential_id`, NULL AS `load_id`, `f`.`faculty_id` AS `faculty_id`, `f`.`full_name` AS `full_name`, `col`.`college_name` AS `college_name`, `c`.`credential_type` AS `credential_type`, `c`.`credential_name` AS `credential_name`, NULL AS `semester`, NULL AS `school_year`, NULL AS `total_loads`, `c`.`file_path` AS `file_path`, 'Credential' AS `source_type` FROM ((`credentials` `c` join `faculty` `f` on(`c`.`faculty_id` = `f`.`faculty_id`)) join `colleges` `col` on(`f`.`college_id` = `col`.`college_id`)) WHERE `c`.`status` = 'Pending'union all select NULL AS `credential_id`,`t`.`load_id` AS `load_id`,`t`.`faculty_id` AS `faculty_id`,`f`.`full_name` AS `full_name`,`col`.`college_name` AS `college_name`,'Teaching Load' AS `credential_type`,`t`.`file_name` AS `credential_name`,`t`.`semester` AS `semester`,concat(`t`.`start_year`,'-',`t`.`end_year`) AS `school_year`,`t`.`total_loads` AS `total_loads`,`t`.`file_path` AS `file_path`,'TeachingLoad' AS `source_type` from ((`teaching_load` `t` join `faculty` `f` on(`t`.`faculty_id` = `f`.`faculty_id`)) join `colleges` `col` on(`f`.`college_id` = `col`.`college_id`)) where `t`.`status` = 'Pending'  ;

-- --------------------------------------------------------

--
-- Structure for view `vw_faculty_users`
--
DROP TABLE IF EXISTS `vw_faculty_users`;

CREATE VIEW `vw_faculty_users` AS SELECT `f`.`faculty_id` AS `faculty_id`, `c`.`college_name` AS `college_name`, `f`.`full_name` AS `full_name`, `f`.`email` AS `email`, `u`.`username` AS `username`, `f`.`status` AS `status` FROM ((`faculty` `f` join `users` `u` on(`f`.`faculty_id` = `u`.`faculty_id`)) join `colleges` `c` on(`f`.`college_id` = `c`.`college_id`)) WHERE `u`.`username` is not null AND `u`.`password_hash` is not null AND `u`.`username` <> '' AND `u`.`password_hash` <> '' ORDER BY cast(substr(`f`.`faculty_id`,4) as unsigned) ASC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_background`
--
ALTER TABLE `academic_background`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_academic_faculty` (`faculty_id`);

--
-- Indexes for table `civil_service_eligibility`
--
ALTER TABLE `civil_service_eligibility`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `colleges`
--
ALTER TABLE `colleges`
  ADD PRIMARY KEY (`college_id`);

--
-- Indexes for table `credentials`
--
ALTER TABLE `credentials`
  ADD PRIMARY KEY (`credential_id`),
  ADD UNIQUE KEY `faculty_id` (`faculty_id`,`credential_type`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`faculty_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_faculty_college` (`college_id`);

--
-- Indexes for table `faculty_personal_info`
--
ALTER TABLE `faculty_personal_info`
  ADD PRIMARY KEY (`faculty_id`);

--
-- Indexes for table `teaching_load`
--
ALTER TABLE `teaching_load`
  ADD PRIMARY KEY (`load_id`),
  ADD KEY `fk_teaching_load_faculty` (`faculty_id`);

--
-- Indexes for table `training_programs`
--
ALTER TABLE `training_programs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_users_faculty` (`faculty_id`),
  ADD KEY `fk_users_college` (`college_id`);

--
-- Indexes for table `user_logins`
--
ALTER TABLE `user_logins`
  ADD PRIMARY KEY (`login_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `college_id` (`college_id`),
  ADD KEY `idx_active_sessions` (`user_id`,`session_status`),
  ADD KEY `idx_unlogged_sessions` (`logout_time`,`session_status`);

--
-- Indexes for table `work_experience`
--
ALTER TABLE `work_experience`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_background`
--
ALTER TABLE `academic_background`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `civil_service_eligibility`
--
ALTER TABLE `civil_service_eligibility`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `colleges`
--
ALTER TABLE `colleges`
  MODIFY `college_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `credentials`
--
ALTER TABLE `credentials`
  MODIFY `credential_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `teaching_load`
--
ALTER TABLE `teaching_load`
  MODIFY `load_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `training_programs`
--
ALTER TABLE `training_programs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `user_logins`
--
ALTER TABLE `user_logins`
  MODIFY `login_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `work_experience`
--
ALTER TABLE `work_experience`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `academic_background`
--
ALTER TABLE `academic_background`
  ADD CONSTRAINT `academic_background_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_academic_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE;

--
-- Constraints for table `civil_service_eligibility`
--
ALTER TABLE `civil_service_eligibility`
  ADD CONSTRAINT `civil_service_eligibility_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE;

--
-- Constraints for table `credentials`
--
ALTER TABLE `credentials`
  ADD CONSTRAINT `fk_credentials_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_faculty_cred` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `fk_faculty_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `faculty_personal_info`
--
ALTER TABLE `faculty_personal_info`
  ADD CONSTRAINT `faculty_personal_info_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE;

--
-- Constraints for table `teaching_load`
--
ALTER TABLE `teaching_load`
  ADD CONSTRAINT `fk_faculty_teaching` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_teaching_load_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE;

--
-- Constraints for table `training_programs`
--
ALTER TABLE `training_programs`
  ADD CONSTRAINT `training_programs_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_college_users` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_faculty_users` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_users_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_users_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE SET NULL;

--
-- Constraints for table `user_logins`
--
ALTER TABLE `user_logins`
  ADD CONSTRAINT `fk_user_logins_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`),
  ADD CONSTRAINT `user_logins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `work_experience`
--
ALTER TABLE `work_experience`
  ADD CONSTRAINT `work_experience_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
