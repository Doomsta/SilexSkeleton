<?php

namespace Doomsta\Silex\Traits;

use Knp\Command\Command;

trait ConsoleTrait
{
    public function addCommand(Command $command)
    {
        $this->extend('console', function ($console) use ($command) {
            $console->add($command);
            return $console;
        });
    }
} 