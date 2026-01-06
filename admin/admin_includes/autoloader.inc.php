<?php

/**
 * Universal autoloader
 * - Supports your existing .class.php files
 * - Supports PHPMailer namespace
 * - Does NOT affect other classes
 */

spl_autoload_register(function ($className) {

    $baseDir = __DIR__ . '/../admin_classes/';

    /*
     =========================
     PHPMailer (namespaced)
     =========================
     Class example:
     PHPMailer\PHPMailer\PHPMailer
     File:
     admin_classes/PHPMailer/src/PHPMailer.php
    */
    if (strpos($className, 'PHPMailer\\PHPMailer\\') === 0) {
        $relativeClass = str_replace('PHPMailer\\PHPMailer\\', '', $className);
        $file = $baseDir . 'PHPMailer/src/' . $relativeClass . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
        return;
    }

    /*
     =========================
     Your normal classes
     =========================
     Example:
     Student → admin_classes/Student.class.php
    */
    $file = $baseDir . $className . '.class.php';

    if (file_exists($file)) {
        require_once $file;
    }
});
