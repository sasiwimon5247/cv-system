
CREATE DATABASE IF NOT EXISTS `cv` ;
USE `cv`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8mb4_general_ci DEFAULT 'user',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DELETE FROM `users`;
INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
	(2, 'sasiwimon', 'sasiwimon5247@gmail.com', '$2y$10$EdtjuK2HZ0pfVfsp2E9unuMJiVhYXG3Lr7RhYvcpEmALEa81w43xe', 'user', '2025-07-25 17:14:39');



CREATE TABLE IF NOT EXISTS `activities_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `activity` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `project` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activities_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DELETE FROM `activities_info`;
INSERT INTO `activities_info` (`id`, `user_id`, `activity`, `project`, `created_at`, `updated_at`) VALUES
	(1, 2, '', 'วิทยานิพนเกี่ยวกับ ระบบพัฒนา cv', '2025-10-19 14:51:13', '2025-10-19 14:51:13');

CREATE TABLE IF NOT EXISTS `certificate_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `teacher_email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `reason` text COLLATE utf8mb4_general_ci,
  `request_token` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'PENDING' COMMENT 'สถานะ: PENDING, GRANTED, REJECTED',
  `certification_text` text COLLATE utf8mb4_general_ci,
  `certified_at` datetime DEFAULT NULL,
  `requested_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `request_token` (`request_token`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `certificate_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DELETE FROM `certificate_requests`;


CREATE TABLE IF NOT EXISTS `cv_profile` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `template_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DELETE FROM `cv_profile`;
INSERT INTO `cv_profile` (`id`, `user_id`, `profile_image`, `template_name`, `created_at`, `updated_at`) VALUES
	(1, 2, '2_1760433475.png', 'modern-sidebar', '2025-07-28 07:26:58', '2025-10-28 09:37:42');


CREATE TABLE IF NOT EXISTS `education_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `highschool_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `highschool_plan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `highschool_gpa` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `uni_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `stu_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `degree` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `faculty` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `major` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `grad_year` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `uni_gpa` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_education_user` (`user_id`),
  CONSTRAINT `fk_education_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DELETE FROM `education_info`;
INSERT INTO `education_info` (`id`, `user_id`, `highschool_name`, `highschool_plan`, `highschool_gpa`, `uni_name`, `stu_id`, `degree`, `faculty`, `major`, `grad_year`, `uni_gpa`, `created_at`, `updated_at`) VALUES
	(1, 2, 'โรงเรียนนครสวรรค์', 'วิทย์-คณิต', '3.66', 'มหาวิทยาลัยนเรศวร', '65314867', 'ปริญญาตรี', 'วิทยาศาสตร์', 'วิทยาการคอมพิวเตอร์', '2569', '2.66', '2025-10-19 14:47:13', '2025-10-29 12:09:30');


CREATE TABLE IF NOT EXISTS `experience_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `company` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `duration` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_experience_user` (`user_id`),
  CONSTRAINT `fk_experience_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DELETE FROM `experience_info`;
INSERT INTO `experience_info` (`id`, `user_id`, `position`, `company`, `duration`, `description`, `created_at`, `updated_at`) VALUES
	(1, 2, 'ผู้ช่วยโปรแกรมเมอร์', 'กองการถ่ายทอดเทคโนโลยีและทรัพย์สินทางปัญญา มหาวิทยาลัยนเรศวร', '3 ปี', 'ช่วยเขียนเว็บไซต์ของมหาวิทยาลัย', '2025-10-19 14:49:24', '2025-10-19 14:49:24');


CREATE TABLE IF NOT EXISTS `personal_info` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `profile_link` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DELETE FROM `personal_info`;
INSERT INTO `personal_info` (`user_id`, `full_name`, `position`, `email`, `phone`, `address`, `profile_link`, `updated_at`) VALUES
	(2, 'นางสาวศศิวิมล เทียนทอง', 'ux/ui', 'sasiwimon5247@gmail.com', '0635421406', 'มหาวิทยาลัย นเรศวร', 'https://github.com/sasiwimon5247', '2025-10-19 14:45:42');


CREATE TABLE IF NOT EXISTS `skills_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `technical_skills` text COLLATE utf8mb4_general_ci,
  `soft_skills` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_skills_user` (`user_id`),
  CONSTRAINT `fk_skills_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DELETE FROM `skills_info`;
INSERT INTO `skills_info` (`id`, `user_id`, `technical_skills`, `soft_skills`, `created_at`, `updated_at`) VALUES
	(1, 2, 'HTML, CSS, JavaScript, PHP, Python, C++', 'การทำงานเป็นทีม (Teamwork)\r\n\r\nการสื่อสารอย่างมีประสิทธิภาพ (Effective Communication)\r\n\r\nการแก้ไขปัญหา (Problem Solving)\r\n\r\nความคิดเชิงวิเคราะห์ (Analytical Thinking)\r\n\r\nความรับผิดชอบสูง (Responsibility)\r\n', '2025-10-19 14:50:40', '2025-10-19 18:54:18');

CREATE TABLE IF NOT EXISTS `summary_info` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `summary` text COLLATE utf8mb4_general_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `summary_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DELETE FROM `summary_info`;
INSERT INTO `summary_info` (`id`, `user_id`, `summary`, `updated_at`) VALUES
	(1, 2, 'เคยเขียนเว็บด้วยภาษา php', '2025-08-28 13:28:29');


