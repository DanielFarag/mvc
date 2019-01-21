<?php

define("APP_PATH",dirname(dirname(__FILE__)));

require APP_PATH . '/vendor/autoload.php';
$configuration = new Framework\Configuration(['type'=>'ini']);
$driver = $configuration->initialize();
echo '<pre>';
print_r($driver->parse(Framework\Path::Config('database.ini')));
echo '</pre>';
