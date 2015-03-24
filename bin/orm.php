#!/usr/bin/env php
<?php

use App\Application;

require __DIR__.'/../vendor/autoload.php';

$app = new Application();

return ConsoleRunner::createHelperSet($app['orm']);