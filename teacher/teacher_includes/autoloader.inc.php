<?php

spl_autoload_register('myAutoLoader');
function myAutoLoader($className)
{
    $path = "teacher_classes/";
    $extension = ".class.php";
    $fullPath = $path . $className . $extension;


    include_once $fullPath;
}
