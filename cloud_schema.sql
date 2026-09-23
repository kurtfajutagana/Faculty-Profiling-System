-- ========================================================
-- PLP Faculty Profiling System - Cloud MySQL / TiDB Schema
-- Compatible with: MySQL 5.7+, MySQL 8.0+, MariaDB, TiDB Cloud Serverless, Aiven, Railway, Clever Cloud
-- ========================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Colleges Table
CREATE TABLE IF NOT EXISTS `colleges` (
  `college_id` int(11) NOT NULL AUTO_INCREMENT,
  `college_name` varchar(255) NOT NULL,
  PRIMARY KEY (`college_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `colleges` (`college_id`, `college_name`) VALUES
(1, 'College of Arts and Science'),
(2, 'College of Business and Accountancy'),
(3, 'College of Computer Studies'),
(4, 'College of Education'),
(5, 'College of Engineering'),
(6, 'College of Hospitality Management'),
(7, 'College of Nursing')
ON DUPLICATE KEY UPDATE `college_name` = VALUES(`college_name`);

-- 2. Faculty Table
CREATE TABLE IF NOT EXISTS `faculty` (
  `faculty_id` varchar(11) NOT NULL,
  `college_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `employment_type` enum('Full-Time','Part-Time') DEFAULT NULL,
  `specialization` varchar(10) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`faculty_id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_faculty_college` (`college_id`),
  CONSTRAINT `fk_faculty_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `college_id` int(11) DEFAULT NULL,
  `faculty_id` varchar(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('Admin','Faculty','Head') NOT NULL,
  `login_attempts` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  KEY `fk_users_college` (`college_id`),
  KEY `fk_users_faculty` (`faculty_id`),
  CONSTRAINT `fk_users_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_users_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Faculty Personal Info Table
CREATE TABLE IF NOT EXISTS `faculty_personal_info` (
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
  `citizenship` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`faculty_id`),
  CONSTRAINT `fk_personal_info_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Academic Background Table
CREATE TABLE IF NOT EXISTS `academic_background` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_id` varchar(11) NOT NULL,
  `level` varchar(50) NOT NULL,
  `institution_name` varchar(255) NOT NULL,
  `degree_course` varchar(150) DEFAULT NULL,
  `start_year` year(4) DEFAULT NULL,
  `end_year` year(4) DEFAULT NULL,
  `honors` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_academic_faculty` (`faculty_id`),
  CONSTRAINT `fk_academic_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. Civil Service Eligibility Table
CREATE TABLE IF NOT EXISTS `civil_service_eligibility` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_id` varchar(11) NOT NULL,
  `eligibility_type` varchar(150) DEFAULT NULL,
  `rating` varchar(20) DEFAULT NULL,
  `date_of_examination` date DEFAULT NULL,
  `place_of_examination` varchar(255) DEFAULT NULL,
  `license_number` varchar(100) DEFAULT NULL,
  `license_validity` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_civil_faculty` (`faculty_id`),
  CONSTRAINT `fk_civil_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7. Credentials Table
CREATE TABLE IF NOT EXISTS `credentials` (
  `credential_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `reason` text DEFAULT NULL,
  PRIMARY KEY (`credential_id`),
  KEY `faculty_id_type` (`faculty_id`,`credential_type`),
  CONSTRAINT `fk_credentials_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 8. Teaching Load Table
CREATE TABLE IF NOT EXISTS `teaching_load` (
  `load_id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_id` varchar(11) NOT NULL,
  `file_name` varchar(150) NOT NULL,
  `semester` enum('First Semester','Second Semester','Summer') NOT NULL,
  `start_year` year(4) NOT NULL,
  `end_year` year(4) NOT NULL,
  `regular_loads` int(11) NOT NULL,
  `overload_units` int(11) NOT NULL,
  `total_loads` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `status` enum('Pending','Verified','Rejected') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verified_at` timestamp NULL DEFAULT NULL,
  `reason` text DEFAULT NULL,
  PRIMARY KEY (`load_id`),
  KEY `fk_teaching_load_faculty` (`faculty_id`),
  CONSTRAINT `fk_teaching_load_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 9. Training Programs Table
CREATE TABLE IF NOT EXISTS `training_programs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_id` varchar(11) NOT NULL,
  `training_title` varchar(255) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `number_of_hours` int(11) DEFAULT NULL,
  `conducted_by` varchar(255) DEFAULT NULL,
  `learning_type` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_training_faculty` (`faculty_id`),
  CONSTRAINT `fk_training_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 10. User Logins Table
CREATE TABLE IF NOT EXISTS `user_logins` (
  `login_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `college_id` int(11) NOT NULL,
  `login_time` datetime NOT NULL DEFAULT current_timestamp(),
  `logout_time` datetime DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `session_status` enum('active','completed','timeout') DEFAULT 'active',
  PRIMARY KEY (`login_id`),
  KEY `fk_logins_user` (`user_id`),
  KEY `fk_logins_college` (`college_id`),
  CONSTRAINT `fk_logins_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_logins_college` FOREIGN KEY (`college_id`) REFERENCES `colleges` (`college_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 11. Work Experience Table
CREATE TABLE IF NOT EXISTS `work_experience` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_id` varchar(11) NOT NULL,
  `position_title` varchar(150) DEFAULT NULL,
  `department_or_agency` varchar(255) DEFAULT NULL,
  `monthly_salary` decimal(10,2) DEFAULT NULL,
  `salary_grade_step` varchar(50) DEFAULT NULL,
  `appointment_status` varchar(100) DEFAULT NULL,
  `is_government_service` enum('Yes','No') DEFAULT 'No',
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_work_faculty` (`faculty_id`),
  CONSTRAINT `fk_work_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- SEED DATA: Faculty Members
-- --------------------------------------------------------
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
('23-20000', 3, 'Riegie D. Tan', 'tanriegie@plpasig.edu.ph', 'Full-Time', 'DIT', '09123456789', 'Active', '2025-05-20 06:29:30'),
('23-20007', 3, 'Jayson Daluyon', 'daluyonjayson@plpasig.edu.ph', 'Part-Time', 'MSIT', '09382528344', 'Active', '2025-05-08 20:31:51'),
('23-20008', 3, 'Rebecca Fajardo', 'fajardorebecca@plpasig.edu.ph', 'Full-Time', 'MSEE', '09183456989', 'Active', '2025-05-08 20:31:51'),
('23-20009', 3, 'Catherine Sorbito', 'sorbitocatherine@plpasig.edu.ph', 'Full-Time', 'MSIT', '09171122334', 'Active', '2025-05-08 20:31:51'),
('23-20010', 3, 'Maricel D. Lopez', 'lopezmaricel@plpasig.edu.ph', 'Full-Time', 'DIT', '09123456789', 'Active', '2025-05-24 04:06:25'),
-- College 2: College of Business and Accountancy (CBA)
('23-10001', 2, 'Dr. Arthur V. Ramos', 'ramosarthur@plpasig.edu.ph', 'Full-Time', 'DBA', '09171234567', 'Active', '2025-05-24 05:00:00'),
('23-10002', 2, 'Patricia Anne Reyes', 'reyespatricia@plpasig.edu.ph', 'Full-Time', 'MBA', '09181234568', 'Active', '2025-05-24 05:00:00'),
('23-10003', 2, 'Ramon Bautista', 'bautistaramon@plpasig.edu.ph', 'Full-Time', 'CPA', '09191234569', 'Active', '2025-05-24 05:00:00'),
('23-10004', 2, 'Leticia M. Gomez', 'gomezleticia@plpasig.edu.ph', 'Part-Time', 'MM', '09201234570', 'Active', '2025-05-24 05:00:00'),
-- College 4: College of Education (COED)
('23-40001', 4, 'Dr. Gloria Mendoza', 'mendozagloria@plpasig.edu.ph', 'Full-Time', 'PhD', '09211234571', 'Active', '2025-05-24 05:00:00'),
('23-40002', 4, 'Maria Teresa Cruz', 'cruzmariateresa@plpasig.edu.ph', 'Full-Time', 'MAEd', '09221234572', 'Active', '2025-05-24 05:00:00'),
('23-40003', 4, 'Roberto Garcia', 'garciaroberto@plpasig.edu.ph', 'Part-Time', 'MAT', '09231234573', 'Active', '2025-05-24 05:00:00'),
('23-40004', 4, 'Ferdinand Torres', 'torresferdinand@plpasig.edu.ph', 'Full-Time', 'MAEd', '09241234574', 'Active', '2025-05-24 05:00:00'),
-- College 5: College of Engineering (COE)
('23-30000', 5, 'Godofredo S. Zapanta Jr.', 'zapantagodofredo@plpasig.edu.ph', 'Full-Time', 'ENG', '09382528344', 'Active', '2025-05-20 06:31:22'),
('23-30001', 5, 'Alberto A. Habrero', 'habreroalberto@plpasig.edu.ph', 'Full-Time', 'ENG', '09637298381', 'Active', '2025-05-20 06:31:22'),
('23-30002', 5, 'Karen V. Arguelles', 'arguelleskaren@plpasig.edu.ph', 'Full-Time', 'ENG', '09382528344', 'Active', '2025-05-20 06:36:44'),
('23-30003', 5, 'Jonathan V. Diosana', 'diosanajonathan@plpasig.edu.ph', 'Full-Time', 'DOC ENG', '09637298381', 'Active', '2025-05-20 06:36:44'),
-- College 6: College of Hospitality Management (CHM)
('23-60001', 6, 'Dr. Cynthia Fernandez', 'fernandezcynthia@plpasig.edu.ph', 'Full-Time', 'DPA', '09251234575', 'Active', '2025-05-24 05:00:00'),
('23-60002', 6, 'Marcus Villanueva', 'villanuevamarcus@plpasig.edu.ph', 'Full-Time', 'MSHM', '09261234576', 'Active', '2025-05-24 05:00:00'),
('23-60003', 6, 'Joanna Marie Lopez', 'lopezjoanna@plpasig.edu.ph', 'Part-Time', 'BSTM', '09271234577', 'Active', '2025-05-24 05:00:00'),
-- College 7: College of Nursing (CON)
('23-70001', 7, 'Dr. Kristine Joy Alcantara', 'alcantarakristine@plpasig.edu.ph', 'Full-Time', 'MAN', '09281234578', 'Active', '2025-05-24 05:00:00'),
('23-70002', 7, 'Benedict Dela Rosa', 'delarosabenedict@plpasig.edu.ph', 'Full-Time', 'MSN', '09291234579', 'Active', '2025-05-24 05:00:00'),
('23-70003', 7, 'Carmela Sophia Tan', 'tancarmela@plpasig.edu.ph', 'Part-Time', 'BSN', '09301234580', 'Active', '2025-05-24 05:00:00')
ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`);

-- --------------------------------------------------------
-- SEED DATA: User Accounts (Password: Password123 or default admin credentials)
-- --------------------------------------------------------
INSERT INTO `users` (`user_id`, `college_id`, `faculty_id`, `username`, `password_hash`, `role`, `login_attempts`, `created_at`) VALUES
(1, 1, NULL, 'cas_admin', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Admin', 0, '2025-05-08 19:31:22'),
(2, 2, NULL, 'cba_admin', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Admin', 0, '2025-05-08 19:31:22'),
(3, 3, NULL, 'ccs_admin', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Admin', 0, '2025-05-08 19:31:22'),
(4, 4, NULL, 'coed_admin', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Admin', 0, '2025-05-08 19:31:22'),
(5, 5, NULL, 'coe_admin', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Admin', 0, '2025-05-08 19:31:22'),
(6, 6, NULL, 'chm_admin', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Admin', 0, '2025-05-08 19:31:22'),
(7, 7, NULL, 'con_admin', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Admin', 0, '2025-05-08 19:31:22'),
(14, 3, '23-20007', '23-20007', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-08 20:49:47'),
(15, 3, '23-20008', '23-20008', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-08 20:49:47'),
(16, 3, '23-20009', '23-20009', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-08 20:49:47'),
(47, 1, '23-00001', '23-00001', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-20 06:17:39'),
(49, 1, '23-00002', '23-00002', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-20 06:50:49'),
(50, 1, '23-00004', '23-00004', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-20 06:57:30'),
(51, 1, '23-00003', '23-00003', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-20 06:57:30'),
(52, 1, '23-00005', '23-00005', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-20 07:08:18'),
(53, 3, '23-20000', '23-20000', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-20 07:08:18'),
(54, 5, '23-30000', '23-30000', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-20 07:13:50'),
(55, 5, '23-30001', '23-30001', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-20 07:13:50'),
(56, 5, '23-30002', '23-30002', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-20 07:13:50'),
(57, 5, '23-30003', '23-30003', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-20 07:13:50'),
(60, 1, '23-00005', 'cas_head', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Head', 0, '2025-05-23 20:12:33'),
(61, 3, '23-20000', 'ccs_head', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Head', 0, '2025-05-23 20:13:01'),
(62, 5, '23-30000', 'coe_head', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Head', 0, '2025-05-23 20:13:38'),
(63, 2, '23-10001', 'cba_head', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Head', 0, '2025-05-24 05:00:00'),
(64, 4, '23-40001', 'coed_head', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Head', 0, '2025-05-24 05:00:00'),
(65, 6, '23-60001', 'chm_head', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Head', 0, '2025-05-24 05:00:00'),
(66, 7, '23-70001', 'con_head', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Head', 0, '2025-05-24 05:00:00'),
(67, 2, '23-10001', '23-10001', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-24 05:00:00'),
(68, 2, '23-10002', '23-10002', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-24 05:00:00'),
(69, 4, '23-40001', '23-40001', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-24 05:00:00'),
(70, 6, '23-60001', '23-60001', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-24 05:00:00'),
(71, 7, '23-70001', '23-70001', '$2y$10$dwzTZYkGdNoZbTASPrQxseaBdPWOUJKLkVHGly3vu3TzdAaa6VrkS', 'Faculty', 0, '2025-05-24 05:00:00')
ON DUPLICATE KEY UPDATE `username` = VALUES(`username`);

-- --------------------------------------------------------
-- CLOUD-COMPATIBLE VIEWS (No DEFINER=root@localhost)
-- --------------------------------------------------------

-- View: vw_credentials_report
CREATE OR REPLACE VIEW `vw_credentials_report` AS
SELECT 
    `c`.`credential_id` AS `credential_id`,
    NULL AS `load_id`,
    `f`.`faculty_id` AS `faculty_id`,
    `f`.`full_name` AS `full_name`,
    `col`.`college_name` AS `college_name`,
    `c`.`credential_type` AS `credential_type`,
    `c`.`credential_name` AS `credential_name`,
    NULL AS `semester`,
    NULL AS `school_year`,
    NULL AS `total_loads`,
    `c`.`file_path` AS `file_path`,
    'Credential' AS `source_type`
FROM `credentials` `c`
JOIN `faculty` `f` ON `c`.`faculty_id` = `f`.`faculty_id`
JOIN `colleges` `col` ON `f`.`college_id` = `col`.`college_id`
WHERE `c`.`status` = 'Pending'

UNION ALL

SELECT 
    NULL AS `credential_id`,
    `t`.`load_id` AS `load_id`,
    `t`.`faculty_id` AS `faculty_id`,
    `f`.`full_name` AS `full_name`,
    `col`.`college_name` AS `college_name`,
    'Teaching Load' AS `credential_type`,
    `t`.`file_name` AS `credential_name`,
    `t`.`semester` AS `semester`,
    CONCAT(`t`.`start_year`, '-', `t`.`end_year`) AS `school_year`,
    `t`.`total_loads` AS `total_loads`,
    `t`.`file_path` AS `file_path`,
    'TeachingLoad' AS `source_type`
FROM `teaching_load` `t`
JOIN `faculty` `f` ON `t`.`faculty_id` = `f`.`faculty_id`
JOIN `colleges` `col` ON `f`.`college_id` = `col`.`college_id`
WHERE `t`.`status` = 'Pending';

-- View: vw_faculty_users
CREATE OR REPLACE VIEW `vw_faculty_users` AS
SELECT 
    `f`.`faculty_id` AS `faculty_id`,
    `c`.`college_name` AS `college_name`,
    `f`.`full_name` AS `full_name`,
    `f`.`email` AS `email`,
    `u`.`username` AS `username`,
    `f`.`status` AS `status`
FROM `faculty` `f`
JOIN `users` `u` ON `f`.`faculty_id` = `u`.`faculty_id`
JOIN `colleges` `c` ON `f`.`college_id` = `c`.`college_id`
WHERE `u`.`username` IS NOT NULL 
  AND `u`.`password_hash` IS NOT NULL 
  AND `u`.`username` <> '' 
  AND `u`.`password_hash` <> '';

SET FOREIGN_KEY_CHECKS = 1;

