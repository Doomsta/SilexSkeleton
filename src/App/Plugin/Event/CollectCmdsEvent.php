<?php

namespace App\Plugin\Event;


use Knp\Command\Command;
use Symfony\Component\EventDispatcher\Event;

class CollectCmdsEvent extends Event
{
    public const NAME = 'plugin.collect.cmds';
    /**
     * @var Command[]
     */
    private $cmds = [];

    /**
     * @param Command[] $cmds
     */
    public function addCmd(Command ...$cmds)
    {
        foreach ($cmds as $cmd) {
            $this->cmds[] = $cmd;
        }
    }

    /**
     * @return string[]
     */
    public function getCmds(): array
    {
        return $this->cmds;
    }

}