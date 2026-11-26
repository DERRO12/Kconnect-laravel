-- Satisfaction surveys (K-Connect). Run on the K-Connect database (kiboko_intranet_portal) in phpMyAdmin.
-- Only adds new tables; safe to run more than once. K-Connect also creates them on the first visit to Admin > Satisfaction Surveys.

CREATE TABLE IF NOT EXISTS `surveys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `slug` varchar(40) NOT NULL,
  `intro` text DEFAULT NULL,
  `thank_you` text DEFAULT NULL,
  `status` enum('draft','open','closed') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_survey_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `survey_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `section` varchar(150) DEFAULT NULL,
  `question` text NOT NULL,
  `help_text` varchar(255) DEFAULT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'likert',
  `options` text DEFAULT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_sq_survey` (`survey_id`, `is_active`, `sort_order`),
  CONSTRAINT `survey_questions_survey_fk` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `survey_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `submitted_on` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sr_survey` (`survey_id`, `submitted_on`),
  CONSTRAINT `survey_responses_survey_fk` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `survey_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `response_id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `section` varchar(150) DEFAULT NULL,
  `question_text` text NOT NULL,
  `answer` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sa_response` (`response_id`),
  KEY `idx_sa_question` (`question_id`),
  CONSTRAINT `survey_answers_response_fk` FOREIGN KEY (`response_id`) REFERENCES `survey_responses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

