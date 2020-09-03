<?php

spl_autoload_register(function (string $className){

    $classesPath = str_replace('\\','/', $className);
    $fullPath = __DIR__ . '/' . $classesPath . '.php';

    if (file_exists($fullPath)) require_once $fullPath;
});