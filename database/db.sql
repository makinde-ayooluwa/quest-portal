
CREATE TABLE IF NOT EXISTS `activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_activities_timestamp` (`timestamp`),
  KEY `idx_activities_user` (`user_type`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `assignment_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `submission_file` varchar(255) NOT NULL,
  `comments` text DEFAULT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('submitted','graded','late') DEFAULT 'submitted',
  `grade` varchar(10) DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `subject` varchar(100) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `due_date` date NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_assignments_class` (`class_name`),
  KEY `idx_assignments_due_date` (`due_date`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(255) NOT NULL,
  `mentor_name` varchar(255) NOT NULL,
  `mentor_email` varchar(255) NOT NULL,
  `mentor_id_as_staff` int(11) NOT NULL,
  `added_on` datetime NOT NULL DEFAULT curtime(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `classes_names_only` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `sent_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admission_number` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `mails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_for` varchar(255) NOT NULL,
  `mail_note` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `events_read` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_type` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `read_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)

) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `materialTitle` varchar(255) NOT NULL,
  `materialType` varchar(255) NOT NULL,
  `materialFile` varchar(255) NOT NULL,
  `uploaded_by` varchar(255) NOT NULL,
  `uploaded_on` datetime NOT NULL DEFAULT curtime(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `notification_reads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_id` int(11) NOT NULL,
  `user_type` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `read_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
  
) ENGINE=InnoDB AUTO_INCREMENT=172 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_type` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `read_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user` (`user_type`,`user_id`,`is_read`),
  KEY `idx_notifications_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `results` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `academic_term` varchar(255) NOT NULL,
  `student_admission_number` varchar(255) NOT NULL,
  `result_file` varchar(255) NOT NULL,
  `added_on` datetime NOT NULL DEFAULT curtime(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `staffs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `staff_role` varchar(255) NOT NULL,
  `employment_date` varchar(255) NOT NULL,
  `subject_given` varchar(255) NOT NULL,
  `staff_status` varchar(255) NOT NULL,
  `portal_code` varchar(255) DEFAULT NULL,
  `account_verification` varchar(255) NOT NULL DEFAULT 'Not verified',
  `added_on` datetime NOT NULL DEFAULT curtime(),
  `picture` varchar(255) NOT NULL DEFAULT 'assets/images/no-picture.jpg',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `dob` datetime NOT NULL,
  `home_address` varchar(255) NOT NULL,
  `gender` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `pwd` varchar(255) NOT NULL,
  `father_name` varchar(255) NOT NULL,
  `father_email` varchar(255) NOT NULL,
  `father_phone` varchar(255) NOT NULL,
  `mother_name` varchar(255) NOT NULL,
  `mother_email` varchar(255) NOT NULL,
  `mother_phone` varchar(255) NOT NULL,
  `admission_number` varchar(255) NOT NULL,
  `admission_date` datetime NOT NULL DEFAULT curtime(),
  `account_verification` varchar(255) NOT NULL DEFAULT 'Not verified',
  `added_on` datetime NOT NULL DEFAULT curtime(),
  `picture` varchar(255) NOT NULL DEFAULT 'assets/images/no-picture.jpg',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `supports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `support_topic` varchar(255) NOT NULL,
  `support_priority` varchar(255) NOT NULL,
  `support_subject` varchar(255) NOT NULL,
  `support_description` text NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `added_on` datetime NOT NULL DEFAULT curtime(),
  `status` enum('open','in_progress','closed') NOT NULL DEFAULT 'open',
  `admin_response` text DEFAULT NULL,
  `responded_by` int(11) DEFAULT NULL,
  `responded_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `system_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `context` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_system_logs_type` (`log_type`),
  KEY `idx_system_logs_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `password_resets` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `user_type` enum('student','teacher','admin') NOT NULL,
    `token` varchar(255) NOT NULL,
    `expires_at` datetime NOT NULL,
    `created_at` datetime NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE IF NOT EXISTS `admin_features` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `unique_id` varchar(255) not null,
  `collapseId` varchar(255) not null,
  `icon` varchar(255) not null DEFAULT ""
);



CREATE TABLE IF NOT EXISTS `admin_features_accessors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `feature_unique_id` varchar(255) not null,
  `accessor` varchar(255) not null
);



CREATE TABLE IF NOT EXISTS `admin_features_sublinks` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) NOT NULL,
  `unique_id` varchar(255) not null,
  `link` varchar(255) not null,
  `icon` varchar(255) not null,
  `parent_unique_id` varchar(255) not null
);



CREATE TABLE IF NOT EXISTS `admin_features_sublinks_accessors` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `feature_sublink_unique_id` varchar(255) not null,
  `accessor` varchar(255) not null
);
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;