<?php

error_reporting(E_ALL);

$autoload = __DIR__ . '/../vendor/autoload.php';
if (! is_file($autoload)) {
    echo "Composer autoload file not found: {$autoload}", PHP_EOL;
    echo "Please issue 'composer install' and try again.", PHP_EOL;
    exit(1);
}

require $autoload;

