<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The K-Connect schema, exactly as it is in kiboko_intranet_portal.
 *
 * On the existing database every table already exists, so this migration does nothing
 * (no table is changed, no row is touched). On an empty database it creates the same 61 tables.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($this->tables() as $table => $sql) {
            if (! Schema::hasTable($table)) {
                DB::unprepared($sql);
            }
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Never drop K-Connect tables from a migration: that would lose data.
    }

    private function tables(): array
    {
        return [
        'admin_users' => <<<'SQL'
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','editor') DEFAULT 'editor',
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'announcements' => <<<'SQL'
CREATE TABLE `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `badge_text` varchar(100) DEFAULT NULL,
  `badge_color` varchar(20) DEFAULT NULL,
  `action_text` varchar(100) DEFAULT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'assets' => <<<'SQL'
CREATE TABLE `assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_code` varchar(50) NOT NULL COMMENT 'Unique Asset Number/Tag',
  `asset_name` varchar(255) NOT NULL,
  `category` enum('Laptop','Desktop','Monitor','Printer','Phone','Tablet','Networking','Server','Other IT','Furniture','Vehicle','Other') NOT NULL DEFAULT 'Other IT',
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `specifications` text DEFAULT NULL COMMENT 'RAM, ROM, Processor, etc.',
  `ram` varchar(50) DEFAULT NULL,
  `storage` varchar(50) DEFAULT NULL,
  `processor` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_cost` decimal(12,2) DEFAULT NULL,
  `warranty_expiry` date DEFAULT NULL,
  `condition_status` enum('New','Excellent','Good','Fair','Poor','Damaged','Retired') NOT NULL DEFAULT 'Good',
  `notes` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `asset_code` (`asset_code`),
  KEY `idx_category` (`category`),
  KEY `idx_status` (`status`),
  KEY `idx_condition` (`condition_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'asset_assignments' => <<<'SQL'
CREATE TABLE `asset_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `assigned_date` datetime NOT NULL DEFAULT current_timestamp(),
  `expected_return_date` date DEFAULT NULL,
  `actual_return_date` datetime DEFAULT NULL,
  `condition_on_assignment` enum('New','Excellent','Good','Fair','Poor','Damaged') DEFAULT 'Good',
  `condition_on_return` enum('New','Excellent','Good','Fair','Poor','Damaged') DEFAULT NULL,
  `assignment_notes` text DEFAULT NULL,
  `return_notes` text DEFAULT NULL,
  `assigned_by` int(11) DEFAULT NULL COMMENT 'Admin who assigned',
  `received_by` int(11) DEFAULT NULL COMMENT 'Admin who received return',
  `status` enum('Active','Returned','Lost','Damaged','Transferred') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  KEY `employee_id` (`employee_id`),
  KEY `idx_status` (`status`),
  KEY `idx_assigned_date` (`assigned_date`),
  KEY `idx_asset_status` (`asset_id`,`status`),
  KEY `fk_assignment_assigned_by` (`assigned_by`),
  KEY `fk_assignment_received_by` (`received_by`),
  CONSTRAINT `fk_assignment_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_assignment_assigned_by` FOREIGN KEY (`assigned_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_assignment_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_assignment_received_by` FOREIGN KEY (`received_by`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'asset_maintenance' => <<<'SQL'
CREATE TABLE `asset_maintenance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `maintenance_date` date NOT NULL,
  `maintenance_type` enum('Repair','Upgrade','Cleaning','Inspection','Software Update','Other') NOT NULL DEFAULT 'Repair',
  `description` text NOT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `performed_by` varchar(100) DEFAULT NULL,
  `next_maintenance_date` date DEFAULT NULL,
  `status` enum('Scheduled','In Progress','Completed','Cancelled') DEFAULT 'Scheduled',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `asset_id` (`asset_id`),
  KEY `idx_date` (`maintenance_date`),
  CONSTRAINT `fk_maintenance_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'backup_logs' => <<<'SQL'
CREATE TABLE `backup_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `backup_date` date NOT NULL,
  `employee_id` int(11) NOT NULL,
  `companies` text DEFAULT NULL,
  `status` enum('Completed','Partial','Failed') NOT NULL DEFAULT 'Completed',
  `notes` text DEFAULT NULL,
  `screenshot` varchar(255) DEFAULT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_backup_date` (`backup_date`),
  KEY `idx_employee_id` (`employee_id`),
  KEY `idx_status` (`status`),
  KEY `idx_ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'birthdays' => <<<'SQL'
CREATE TABLE `birthdays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `birth_date` date NOT NULL,
  `message` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_birth_date_status` (`birth_date`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'brand_assets' => <<<'SQL'
CREATE TABLE `brand_assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('Logos','Documents','Templates','Presentations','Images','Videos','Other') NOT NULL DEFAULT 'Logos',
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_path` varchar(500) NOT NULL,
  `thumbnail_path` varchar(500) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `status` (`status`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'brand_categories' => <<<'SQL'
CREATE TABLE `brand_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-folder',
  `color` varchar(20) DEFAULT '#009ada',
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'ceo_messages' => <<<'SQL'
CREATE TABLE `ceo_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `author` varchar(100) DEFAULT 'CEO',
  `read_more_url` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'companies' => <<<'SQL'
CREATE TABLE `companies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'departments' => <<<'SQL'
CREATE TABLE `departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `organization_type` enum('Leadership','Sales','Support') NOT NULL DEFAULT 'Support',
  `sales_division` enum('P&G','Spark','Star','CD','None') DEFAULT 'None',
  `support_department` enum('IT','HR','Internal Audit','Legal','Warehouse','Procurement','Logistics','Administration','Finance','Other') DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT 1,
  `head_employee_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `company_id` (`company_id`),
  KEY `parent_id` (`parent_id`),
  KEY `head_employee_id` (`head_employee_id`),
  KEY `organization_type` (`organization_type`),
  CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `departments_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `departments_ibfk_3` FOREIGN KEY (`head_employee_id`) REFERENCES `organization_chart` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'employees' => <<<'SQL'
CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_code` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `extension` varchar(20) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `join_date` date DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `emergency_phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `role` enum('employee','admin','manager','hr','director') DEFAULT 'employee',
  `status` tinyint(1) DEFAULT 1 COMMENT '1=active, 0=inactive',
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `token_expires_at` datetime DEFAULT NULL,
  `registered_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `employee_code` (`employee_code`),
  KEY `idx_status` (`status`),
  KEY `idx_department` (`department`),
  KEY `company_id` (`company_id`),
  KEY `idx_employee_code_status` (`employee_code`,`status`),
  KEY `idx_full_name` (`full_name`(100)),
  KEY `idx_email` (`email`(100)),
  KEY `idx_department_status` (`department`,`status`),
  KEY `idx_company_status` (`company_id`,`status`),
  KEY `idx_role_status` (`role`,`status`),
  KEY `idx_join_date` (`join_date`),
  KEY `idx_birth_date` (`birth_date`),
  KEY `idx_verification_token` (`verification_token`),
  KEY `idx_is_verified` (`is_verified`),
  CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'employee_spotlights' => <<<'SQL'
CREATE TABLE `employee_spotlights` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_name` varchar(255) NOT NULL,
  `employee_photo` varchar(255) DEFAULT NULL,
  `spotlight_type` enum('employee','team') NOT NULL DEFAULT 'employee',
  `achievement_title` varchar(255) NOT NULL,
  `achievement_description` text DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL COMMENT 'Rating out of 5.0',
  `department` varchar(100) DEFAULT NULL,
  `featured_quote` text DEFAULT NULL,
  `badge_text` varchar(100) DEFAULT NULL,
  `badge_color` varchar(20) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `spotlight_type` (`spotlight_type`),
  KEY `status` (`status`),
  KEY `idx_status_order` (`status`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'events' => <<<'SQL'
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'event_items' => <<<'SQL'
CREATE TABLE `event_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `event_time` time DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `category` enum('Get together','Orientation','Training','Meeting','Social','Other') DEFAULT 'Meeting',
  `rsvp_required` tinyint(1) DEFAULT 0,
  `rsvp_url` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `event_date` (`event_date`),
  KEY `status` (`status`),
  KEY `idx_status_date` (`status`,`event_date`),
  KEY `idx_id_status` (`id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'gallery_categories' => <<<'SQL'
CREATE TABLE `gallery_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'gallery_items' => <<<'SQL'
CREATE TABLE `gallery_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `thumbnail` varchar(500) NOT NULL,
  `external_url` varchar(500) NOT NULL,
  `source_type` enum('onedrive','googledrive','dropbox','youtube','vimeo','other') DEFAULT 'other',
  `category` varchar(100) DEFAULT 'General',
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `status` (`status`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'homepage_carousel' => <<<'SQL'
CREATE TABLE `homepage_carousel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `media_type` enum('image','youtube','vimeo','social') NOT NULL,
  `media_url` varchar(500) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'it_answers' => <<<'SQL'
CREATE TABLE `it_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `it_answers_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `it_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `it_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `it_questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'it_questions' => <<<'SQL'
CREATE TABLE `it_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_text` text NOT NULL,
  `question_type` enum('text','textarea','radio','checkbox','number','file') NOT NULL DEFAULT 'text',
  `options` text DEFAULT NULL COMMENT 'Comma-separated options for radio/checkbox',
  `is_required` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'it_settings' => <<<'SQL'
CREATE TABLE `it_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'it_tickets' => <<<'SQL'
CREATE TABLE `it_tickets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_number` varchar(50) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `category` enum('Hardware','Software','Network','ERP','Printer','SFA','Email','Other') NOT NULL DEFAULT 'Other',
  `priority` enum('Low','Medium','High','Urgent') NOT NULL DEFAULT 'Medium',
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `status` enum('New','In Progress','Resolved','Closed','Rejected') NOT NULL DEFAULT 'New',
  `admin_notes` text DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `resolved_at` datetime DEFAULT NULL,
  `closed_at` datetime DEFAULT NULL,
  `created_by_admin` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_number` (`ticket_number`),
  KEY `employee_id` (`employee_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `status` (`status`),
  KEY `priority` (`priority`),
  CONSTRAINT `it_tickets_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `it_tickets_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'it_ticket_assignments' => <<<'SQL'
CREATE TABLE `it_ticket_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `assigned_to` int(11) NOT NULL,
  `assigned_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `assigned_by` (`assigned_by`),
  CONSTRAINT `it_ticket_assignments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `it_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `it_ticket_assignments_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `it_ticket_assignments_ibfk_3` FOREIGN KEY (`assigned_by`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'it_ticket_attachments' => <<<'SQL'
CREATE TABLE `it_ticket_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `comment_id` (`comment_id`),
  CONSTRAINT `it_ticket_attachments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `it_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `it_ticket_attachments_ibfk_2` FOREIGN KEY (`comment_id`) REFERENCES `it_ticket_comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'it_ticket_comments' => <<<'SQL'
CREATE TABLE `it_ticket_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `has_images` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `it_ticket_comments_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `it_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `it_ticket_comments_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'it_ticket_status_history' => <<<'SQL'
CREATE TABLE `it_ticket_status_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `from_status` varchar(50) DEFAULT NULL,
  `to_status` varchar(50) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `changed_by` (`changed_by`),
  CONSTRAINT `it_ticket_status_history_ibfk_1` FOREIGN KEY (`ticket_id`) REFERENCES `it_tickets` (`id`) ON DELETE CASCADE,
  CONSTRAINT `it_ticket_status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'key_contacts' => <<<'SQL'
CREATE TABLE `key_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `extension` varchar(20) DEFAULT NULL,
  `mobile` varchar(50) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `office_location` varchar(255) DEFAULT NULL,
  `office_hours` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT 'Management',
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `status` (`status`),
  KEY `sort_order` (`sort_order`),
  KEY `idx_emergency` (`status`,`category`,`sort_order`),
  KEY `idx_status_order` (`status`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'navigation_links' => <<<'SQL'
CREATE TABLE `navigation_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(500) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `is_label` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `target` varchar(10) DEFAULT '_self',
  `icon` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `css_class` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `rel` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_status` (`status`),
  KEY `idx_sort_order` (`sort_order`),
  CONSTRAINT `navigation_links_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `navigation_links` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'news_comments' => <<<'SQL'
CREATE TABLE `news_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL,
  `commenter_name` varchar(100) DEFAULT NULL,
  `comment_text` text NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `news_id` (`news_id`),
  CONSTRAINT `news_comments_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'news_events' => <<<'SQL'
CREATE TABLE `news_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `category` enum('Announcements','Events','People','Milestones') DEFAULT 'Announcements',
  `author` varchar(100) DEFAULT NULL,
  `author_role` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `comments_count` int(11) DEFAULT 0,
  `likes_count` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'news_images' => <<<'SQL'
CREATE TABLE `news_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `caption` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `news_id` (`news_id`),
  CONSTRAINT `news_images_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'news_likes' => <<<'SQL'
CREATE TABLE `news_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`news_id`,`ip_address`),
  CONSTRAINT `news_likes_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'news_posts' => <<<'SQL'
CREATE TABLE `news_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `category` enum('Announcements','Events','People','Milestones') NOT NULL DEFAULT 'Announcements',
  `author_name` varchar(100) NOT NULL,
  `author_role` varchar(100) DEFAULT 'Admin',
  `featured_image` varchar(255) DEFAULT NULL,
  `comments_count` int(11) DEFAULT 0,
  `likes_count` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  KEY `idx_status_created` (`status`,`created_at`),
  KEY `idx_category` (`category`),
  KEY `idx_id_status` (`id`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'nominations' => <<<'SQL'
CREATE TABLE `nominations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `nominator_id` int(11) NOT NULL,
  `nominee_id` int(11) NOT NULL,
  `status` enum('pending','reviewed','approved','rejected') DEFAULT 'pending',
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `read_by` int(11) DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `level1_reviewed_by` int(11) DEFAULT NULL,
  `level1_reviewed_at` datetime DEFAULT NULL,
  `level1_notes` text DEFAULT NULL,
  `level1_decision` enum('pending','shortlisted','rejected') DEFAULT 'pending',
  `level2_reviewed_by` int(11) DEFAULT NULL,
  `level2_reviewed_at` datetime DEFAULT NULL,
  `level2_notes` text DEFAULT NULL,
  `level2_decision` enum('pending','shortlisted','rejected') DEFAULT 'pending',
  `level3_reviewed_by` int(11) DEFAULT NULL,
  `level3_reviewed_at` datetime DEFAULT NULL,
  `level3_notes` text DEFAULT NULL,
  `level3_decision` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `quarter` tinyint(1) DEFAULT NULL COMMENT '1=Q1, 2=Q2, 3=Q3, 4=Q4',
  `year` int(11) DEFAULT NULL,
  `is_winner` tinyint(1) DEFAULT 0,
  `winner_announced_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `nominator_id` (`nominator_id`),
  KEY `nominee_id` (`nominee_id`),
  KEY `idx_category_status` (`category_id`,`status`),
  KEY `idx_nominator_status` (`nominator_id`,`status`),
  KEY `idx_nominee_status` (`nominee_id`,`status`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_is_read` (`is_read`),
  KEY `idx_status_created` (`status`,`created_at`),
  KEY `idx_read_status` (`is_read`,`status`),
  KEY `idx_quarter_year` (`quarter`,`year`),
  KEY `idx_year` (`year`),
  KEY `idx_winner` (`is_winner`),
  KEY `idx_category_nominator` (`category_id`,`nominator_id`),
  KEY `idx_nominator_quarter` (`nominator_id`,`quarter`,`year`),
  CONSTRAINT `nominations_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `nomination_categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nominations_ibfk_2` FOREIGN KEY (`nominator_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nominations_ibfk_3` FOREIGN KEY (`nominee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'nomination_answers' => <<<'SQL'
CREATE TABLE `nomination_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomination_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `nomination_id` (`nomination_id`),
  KEY `question_id` (`question_id`),
  KEY `idx_nomination_question` (`nomination_id`,`question_id`),
  KEY `idx_question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'nomination_categories' => <<<'SQL'
CREATE TABLE `nomination_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'award',
  `color` varchar(20) DEFAULT '#009ada',
  `is_self_nomination` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_status_order` (`status`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'nomination_committee' => <<<'SQL'
CREATE TABLE `nomination_committee` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `committee_level` tinyint(1) NOT NULL COMMENT '1=Level 1, 2=Level 2, 3=Level 3',
  `role` varchar(100) DEFAULT NULL COMMENT 'Committee role (Chair, Member, Secretary, etc.)',
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_employee_level` (`employee_id`,`committee_level`),
  KEY `idx_level_status` (`committee_level`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'nomination_questions' => <<<'SQL'
CREATE TABLE `nomination_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('text','long_text','number','radio','checkbox','rating','date','attachment') DEFAULT 'text',
  `options` text DEFAULT NULL COMMENT 'Comma-separated options for radio/checkbox',
  `is_required` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `idx_category_status_order` (`category_id`,`status`,`sort_order`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'nomination_settings' => <<<'SQL'
CREATE TABLE `nomination_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `current_quarter` tinyint(1) NOT NULL DEFAULT 1,
  `current_year` int(11) NOT NULL,
  `quarter_start_date` date DEFAULT NULL,
  `quarter_end_date` date DEFAULT NULL,
  `cooldown_days` int(11) DEFAULT 2,
  `max_nominations_per_quarter` int(11) DEFAULT NULL COMMENT 'NULL = unlimited',
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `unique_votes_per_quarter` tinyint(1) DEFAULT 0 COMMENT '0=multiple votes allowed, 1=only one vote per category per quarter',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'nomination_user_tracking' => <<<'SQL'
CREATE TABLE `nomination_user_tracking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `quarter` tinyint(1) NOT NULL,
  `year` int(11) NOT NULL,
  `nomination_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_category_quarter` (`user_id`,`category_id`,`quarter`,`year`),
  KEY `idx_user_quarter` (`user_id`,`quarter`,`year`),
  KEY `fk_tracking_category` (`category_id`),
  KEY `fk_tracking_nomination` (`nomination_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'organization_chart' => <<<'SQL'
CREATE TABLE `organization_chart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `sub_department` varchar(255) DEFAULT NULL,
  `sub_department_id` int(11) DEFAULT NULL,
  `sales_division` enum('P&G','Spark','Star','CD','None') DEFAULT 'None',
  `support_department` enum('IT','HR','Internal Audit','Legal','Warehouse','Procurement','Logistics','Administration','Finance','Other') DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `office_location` varchar(255) DEFAULT NULL,
  `company` enum('KEL Uganda','KEL Tanzania','KFS','Group') DEFAULT 'Group',
  `company_id` int(11) DEFAULT NULL,
  `organization_type` enum('Sales','Support','Leadership') DEFAULT 'Support',
  `photo` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Reports to (employee id)',
  `level` int(11) DEFAULT 1 COMMENT 'Hierarchy level',
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `department` (`department`),
  KEY `idx_company` (`company`),
  KEY `idx_org_type` (`organization_type`),
  KEY `idx_sales_div` (`sales_division`),
  KEY `idx_support_dept` (`support_department`),
  KEY `fk_org_company` (`company_id`),
  KEY `fk_org_department` (`department_id`),
  KEY `fk_org_sub_department` (`sub_department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'orgchart_structure' => <<<'SQL'
CREATE TABLE `orgchart_structure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Reports to employee_id',
  `level` tinyint(2) NOT NULL DEFAULT 1 COMMENT '1=CEO,2=Directors,3=Heads,4=Managers,5=Staff',
  `level_label` varchar(50) DEFAULT NULL COMMENT 'CEO, Director, Head, Manager, Staff',
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_employee` (`employee_id`),
  KEY `parent_id` (`parent_id`),
  KEY `level` (`level`),
  KEY `sort_order` (`sort_order`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Organizational chart structure'
SQL,
        'password_reset_attempts' => <<<'SQL'
CREATE TABLE `password_reset_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `employee_code` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `attempt_time` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_employee_code` (`employee_code`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_attempt_time` (`attempt_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'projects' => <<<'SQL'
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_code` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('Planning','Active','On Hold','Completed','Cancelled') DEFAULT 'Planning',
  `priority` enum('Low','Medium','High','Urgent') DEFAULT 'Medium',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `project_code` (`project_code`),
  KEY `created_by` (`created_by`),
  KEY `status` (`status`),
  KEY `priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'project_departments' => <<<'SQL'
CREATE TABLE `project_departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_dept` (`project_id`,`department`),
  KEY `project_id` (`project_id`),
  KEY `department` (`department`),
  CONSTRAINT `fk_project_dept_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'project_members' => <<<'SQL'
CREATE TABLE `project_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `role` enum('Admin','Manager','Member','Viewer') DEFAULT 'Member',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_project_member` (`project_id`,`employee_id`),
  KEY `project_id` (`project_id`),
  KEY `employee_id` (`employee_id`),
  KEY `role` (`role`),
  CONSTRAINT `fk_project_member_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_project_member_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'project_tasks' => <<<'SQL'
CREATE TABLE `project_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `task_code` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `assigned_by` int(11) NOT NULL,
  `priority` enum('Low','Medium','High','Urgent') DEFAULT 'Medium',
  `status` enum('To Do','In Progress','Review','Done','Blocked') DEFAULT 'To Do',
  `due_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `completed_date` datetime DEFAULT NULL,
  `completion_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_code` (`task_code`),
  KEY `project_id` (`project_id`),
  KEY `assigned_to` (`assigned_to`),
  KEY `assigned_by` (`assigned_by`),
  KEY `department` (`department`),
  KEY `status` (`status`),
  KEY `priority` (`priority`),
  CONSTRAINT `fk_project_task_assigned_by` FOREIGN KEY (`assigned_by`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_project_task_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_project_task_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'project_task_attachments' => <<<'SQL'
CREATE TABLE `project_task_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `uploaded_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `uploaded_by` (`uploaded_by`),
  CONSTRAINT `fk_task_attachment_employee` FOREIGN KEY (`uploaded_by`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_task_attachment_task` FOREIGN KEY (`task_id`) REFERENCES `project_tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'project_task_comments' => <<<'SQL'
CREATE TABLE `project_task_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `employee_id` (`employee_id`),
  CONSTRAINT `fk_task_comment_employee` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_task_comment_task` FOREIGN KEY (`task_id`) REFERENCES `project_tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'project_task_status_history' => <<<'SQL'
CREATE TABLE `project_task_status_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `from_status` varchar(50) DEFAULT NULL,
  `to_status` varchar(50) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `task_id` (`task_id`),
  KEY `changed_by` (`changed_by`),
  CONSTRAINT `fk_task_status_employee` FOREIGN KEY (`changed_by`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_task_status_task` FOREIGN KEY (`task_id`) REFERENCES `project_tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'quarter_winners' => <<<'SQL'
CREATE TABLE `quarter_winners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `nomination_id` int(11) NOT NULL,
  `nominee_id` int(11) NOT NULL,
  `quarter` tinyint(1) NOT NULL,
  `year` int(11) NOT NULL,
  `winner_rank` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Winner, 2=Runner Up',
  `announced_by` int(11) DEFAULT NULL,
  `announced_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `nomination_id` (`nomination_id`),
  KEY `nominee_id` (`nominee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'quick_links' => <<<'SQL'
CREATE TABLE `quick_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'rewards_attachments' => <<<'SQL'
CREATE TABLE `rewards_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rewards_item_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `rewards_item_id` (`rewards_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'rewards_comments' => <<<'SQL'
CREATE TABLE `rewards_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rewards_item_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `employee_name` varchar(255) NOT NULL,
  `employee_photo` varchar(255) DEFAULT NULL,
  `comment` text NOT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `rewards_item_id` (`rewards_item_id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'rewards_items' => <<<'SQL'
CREATE TABLE `rewards_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `video_type` enum('youtube','vimeo','other') DEFAULT 'youtube',
  `video_url` varchar(500) NOT NULL,
  `video_id` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `views_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'rewards_reactions' => <<<'SQL'
CREATE TABLE `rewards_reactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rewards_item_id` int(11) NOT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `reaction_type` enum('like','celebrate','support','insightful') DEFAULT 'like',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_reaction` (`rewards_item_id`,`employee_id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'social_links' => <<<'SQL'
CREATE TABLE `social_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platform` varchar(50) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `url` varchar(500) NOT NULL,
  `color` varchar(50) DEFAULT '#009ada',
  `bg_color` varchar(50) DEFAULT '#f0f9ff',
  `username` varchar(255) DEFAULT NULL,
  `followers_count` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `platform` (`platform`),
  KEY `status` (`status`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'system_logs' => <<<'SQL'
CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` enum('admin','employee') NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `action` varchar(100) NOT NULL COMMENT 'login, logout, page_view, create, update, delete, registration',
  `page_accessed` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_type` (`user_type`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`),
  KEY `idx_user_type_action` (`user_type`,`action`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='System activity and login logs'
SQL,
        'top_resources' => <<<'SQL'
CREATE TABLE `top_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `icon` varchar(100) NOT NULL DEFAULT 'fa-link',
  `url` varchar(500) DEFAULT NULL,
  `color` varchar(50) DEFAULT '#009ada',
  `bg_color` varchar(50) DEFAULT '#f0f9ff',
  `category` varchar(100) DEFAULT 'Forms & Templates',
  `description` text DEFAULT NULL,
  `target` enum('_self','_blank') DEFAULT '_self',
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category` (`category`),
  KEY `status` (`status`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        'whistleblowing_reports' => <<<'SQL'
CREATE TABLE `whistleblowing_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reference_number` varchar(50) NOT NULL,
  `submission_type` enum('anonymous','confidential','open') NOT NULL DEFAULT 'anonymous',
  `full_name` varchar(255) DEFAULT NULL,
  `entity` varchar(100) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `contact_method` varchar(255) DEFAULT NULL,
  `relationship` enum('current_employee','former_employee','contractor','supplier','customer','other') NOT NULL DEFAULT 'current_employee',
  `relationship_other` varchar(255) DEFAULT NULL,
  `nature_concerns` text DEFAULT NULL,
  `concern_other` varchar(255) DEFAULT NULL,
  `what_happened` text NOT NULL,
  `who_involved` text DEFAULT NULL,
  `incident_type` enum('single','recurring') DEFAULT 'single',
  `incident_date` date DEFAULT NULL,
  `incident_from_date` date DEFAULT NULL,
  `incident_to_date` date DEFAULT NULL,
  `incident_location` text DEFAULT NULL,
  `awareness_method` varchar(255) DEFAULT NULL,
  `awareness_other` varchar(255) DEFAULT NULL,
  `estimated_impact` varchar(255) DEFAULT NULL,
  `evidence_description` text DEFAULT NULL,
  `witnesses` text DEFAULT NULL,
  `prior_reported` tinyint(1) DEFAULT 0,
  `prior_report_details` text DEFAULT NULL,
  `urgent` tinyint(1) DEFAULT 0,
  `involves_handler` tinyint(1) DEFAULT 0,
  `declaration_signature` varchar(255) DEFAULT NULL,
  `declaration_date` date DEFAULT NULL,
  `status` enum('new','under_review','investigating','resolved','closed','dismissed') NOT NULL DEFAULT 'new',
  `admin_notes` text DEFAULT NULL,
  `resolution_notes` text DEFAULT NULL,
  `received_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference_number` (`reference_number`),
  KEY `submission_type` (`submission_type`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
SQL,
        ];
    }
};
