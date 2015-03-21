<?php

namespace App\Console\Command\Assetic;

use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Dumps assets when their source files are modified.
 */
class WatchCommand extends Command
{

    protected function configure()
    {
        $this->setName('assetic:watch');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
       $output->writeln('<error>TODO implement this</error>');
    }

}