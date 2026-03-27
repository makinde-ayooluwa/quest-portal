DELETE FROM admin_features;
DELETE FROM admin_features_accessors;
DELETE FROM admin_features_sublinks;
DELETE FROM admin_features_sublinks_accessors;
INSERT INTO
    admin_features(title, unique_id, collapseId)
VALUES
    (
        "Students Management",
        "studentsManagement",
        "studentManagement"
    );

INSERT INTO
    admin_features(title, unique_id, collapseId)
VALUES
    (
        "Users Management",
        "usersManagement",
        "staffManagement"
    );

INSERT INTO
    admin_features(title, unique_id, collapseId)
VALUES
    (
        "System Management",
        "systemManagement",
        "systemManagement"
    );

INSERT INTO
    admin_features_accessors(feature_unique_id, accessor)
VALUES
    ("studentsManagement", "head admin");

INSERT INTO
    admin_features_accessors(feature_unique_id, accessor)
VALUES
    ("usersManagement", "head admin");

INSERT INTO
    admin_features_accessors(feature_unique_id, accessor)
VALUES
    ("systemManagement", "head admin");

INSERT INTO
    admin_features_sublinks(unique_id, title, link, icon, parent_unique_id)
VALUES
    (
        "viewAllStudents",
        "View all students",
        "students.php",
        "bi bi-people",
        "studentsManagement"
    );

INSERT INTO
    admin_features_sublinks(unique_id, title, link, icon, parent_unique_id)
VALUES
    (
        "manageResults",
        "Manage Results",
        "manage_results.php",
        "bi bi-clipboard-check",
        "studentsManagement"
    );

INSERT INTO
    admin_features_sublinks(unique_id, title, link, icon, parent_unique_id)
VALUES
    (
        "supportRequests",
        "Support Requests",
        "support_requests.php",
        "bi bi-person-raised-hand",
        "studentsManagement"
    );

INSERT INTO
    admin_features_sublinks(unique_id, title, link, icon, parent_unique_id)
VALUES
    (
        "viewAllUsers",
        "View all users",
        "staff_management.php",
        "bi bi-people",
        "usersManagement"
    );

INSERT INTO
    admin_features_sublinks(unique_id, title, link, icon, parent_unique_id)
VALUES
    (
        "createUser",
        "CREATE user",
        "add_staff.php",
        "bi bi-plus",
        "usersManagement"
    );

    INSERT INTO
    admin_features_sublinks(unique_id, title, link, icon, parent_unique_id)
VALUES
    (
        "manageRoles",
        "Manage Roles",
        "role_management.php",
        "bi bi-person-workspace",
        "usersManagement"
    );

INSERT INTO
    admin_features_sublinks(unique_id, title, link, icon, parent_unique_id)
VALUES
    (
        "addNotification",
        "Add Notification",
        "add_notification.php",
        "bi bi-bell",
        "systemManagement"
    );

INSERT INTO
    admin_features_sublinks(unique_id, title, link, icon, parent_unique_id)
VALUES
    (
        "addEvent",
        "Add Event",
        "add_event.php",
        "bi bi-calender-event",
        "systemManagement"
    );

    

INSERT INTO
    admin_features_sublinks_accessors(feature_sublink_unique_id, accessor)
VALUES
    ("viewAllStudents", "head admin");

INSERT INTO
    admin_features_sublinks_accessors(feature_sublink_unique_id, accessor)
VALUES
    ("manageResults", "head admin");

INSERT INTO
    admin_features_sublinks_accessors(feature_sublink_unique_id, accessor)
VALUES
    ("supportRequests", "head admin");

INSERT INTO
    admin_features_sublinks_accessors(feature_sublink_unique_id, accessor)
VALUES
    ("viewAllUsers", "head admin");

INSERT INTO
    admin_features_sublinks_accessors(feature_sublink_unique_id, accessor)
VALUES
    ("createUser", "head admin");

    INSERT INTO
    admin_features_sublinks_accessors(feature_sublink_unique_id, accessor)
VALUES
    ("manageRoles", "head admin");

INSERT INTO
    admin_features_sublinks_accessors(feature_sublink_unique_id, accessor)
VALUES
    ("addNotification", "head admin");

INSERT INTO
    admin_features_sublinks_accessors(feature_sublink_unique_id, accessor)
VALUES
    ("addEvent", "head admin");

-- NEW ROLES TABLE
CREATE TABLE IF NOT EXISTS `roles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEED INITIAL ROLES
INSERT IGNORE INTO `roles` (`name`, `description`) VALUES
('super admin', 'Full system access - manages all roles and permissions'),
('head admin', 'Head administrator - full feature access'),
('teacher', 'Classroom teacher - limited student/results access'),
('support officer', 'Handles student support requests'),
('assessment officer', 'Manages results and assessments'),
('retention officer', 'Student retention and attendance tracking');

-- Grant role_management.php access to super admin and head admin
INSERT INTO admin_features_sublinks_accessors(feature_sublink_unique_id, accessor) VALUES
('manageRoles', 'super admin'),
('manageRoles', 'head admin');
