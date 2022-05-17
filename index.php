<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require('init.php');


use Mysql;

$ms = new Mysql([21, 'Papaya', 'Love', '2000-07-20', 0, 'Minsk']);