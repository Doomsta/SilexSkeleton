<?php


namespace App\Console\Command;


use Knp\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('app:install')
            ->setDescription('installs the app file perm etc..')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Start');
        $output->writeln('assets:');
        $command = $this->getApplication()->find('app:assetic:dump');
        $command->run($input, $output);

    }
}
 