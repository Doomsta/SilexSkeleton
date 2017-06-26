<?php
/**
 * Created by PhpStorm.
 * User: arandel
 * Date: 26.06.17
 * Time: 08:39
 */

namespace App\Plugin;


use App\Console\Command\GreedCommand;
use Pimple\Container;

class TestPlugin extends Plugin
{


    /**
     * @return string
     */
    public function getName(): string
    {
        return 'test';
    }

    public function getJsFiles()
    {
        return  ['test/test.js'];
    }

    public function getCmds() :array
    {
        return [
            new GreedCommand()
        ];
    }

    /**
     * @return Container
     */
    protected function getContainer(): Container
    {
        return $this->getApp();
    }
}