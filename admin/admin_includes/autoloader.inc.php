<?php

spl_autoload_register(function ($className) {
    // 1. Calculate the base directory
    $baseDir = dirname(__DIR__) . '/admin_classes/';

    // 2. Build the targeted file path
    $file = $baseDir . $className . '.class.php';
    $lowercaseFile = $baseDir . strtolower($className) . '.class.php';

    // 3. Check if the files exist. If found, require them.
    if (file_exists($file)) {
        require_once $file;
        return;
    } elseif (file_exists($lowercaseFile)) {
        require_once $lowercaseFile;
        return;
    }

    // 4. If neither exists, print a clear diagnostic report before the crash

    exit();
});