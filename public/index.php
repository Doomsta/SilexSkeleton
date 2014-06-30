<?php

use App\Application;

define('ROOT_PATH', __DIR__.'/..');

require_once(ROOT_PATH.'/vendor/autoload.php');

$app = new Application('dev');
$app->run();
