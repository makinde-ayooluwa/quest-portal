<?php

    spl_autoload_register('myAutoLoader');
function myAutoLoader($className)
{
    $path = "admin_classes/";
    $extension = ".class.php";
    $fullPath = $path . $className . $extension;


    include_once $fullPath;
}
