<?php

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');

setlocale(LC_ALL, isset($_SERVER['LOCALE']) ? $_SERVER['LOCALE'] : getenv('LOCALE'));

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
spl_autoload_register(function ($classname) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $classname . '.php';
});