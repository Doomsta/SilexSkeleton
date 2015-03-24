<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use App\Application;
// replace with file to your own project bootstrap


$app = new Application();

return ConsoleRunner::createHelperSet($app['orm.em']);