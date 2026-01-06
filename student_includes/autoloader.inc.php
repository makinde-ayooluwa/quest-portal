<?php

spl_autoload_register('myAutoLoader');

function myAutoLoader($className){
    $path = "student_classes/";
    $extension = ".class.php";
    $fullPath = $path . $className . $extension;

    if(!file_exists($fullPath)){
        return false;
    }

    include_once $fullPath;
}