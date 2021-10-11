<?php

$filename = __DIR__ .'/../../vendor/autoload.php';

if (!file_exists($filename)) {
    echo "You need to execute `composer install` before running the tests.\n";
    exit;
}

$loader = require $filename;
$loader->add('Garfix\\JsMinify\\Test', __DIR__);
