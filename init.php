<?php

spl_autoload_register(function($class) {
    $classPath = $_SERVER['DOCUMENT_ROOT'] . '/' . str_replace('\\', '/', $class) . '.php';

    if (file_exists($classPath)) {  
        require $classPath;
    }
});