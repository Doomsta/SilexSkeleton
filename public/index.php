<?php

use App\Application;

define('ROOT_PATH', __DIR__.'/..');

require_once(ROOT_PATH.'/vendor/autoload.php');

$app = new Application('dev');


echo "\n1";
$app['test'] = $app->share(function () {
    echo 'new'."\n";
    return 'new';
});
echo "\n2";
$app->extend('test', function ($test) {
   echo $test.' extended'."\n";
   return $test.' extended';
});
echo "\n3";
$app['test'] = $app->share($app->extend('test', function ($test) {
    echo $test.' 1'."\n";
    return $test.' 1';
}));
echo "\n4";
$app['test'] = $app::share($app->extend('test', function ($test) {
    echo $test.'.2'."\n";
    return $test.'.2';
}));
echo "\n5\n";
echo $app['test'];
echo "\n6\n";
echo $app['test'];
echo "\n";


$app->run();
