<?php

namespace App\Command\Debug;

use Knp\Command\Command;
use Silex\Route;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouteCollection;

class RouterCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        if (!$this->getSilexApplication()) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('app:debug:router');
        $this->setDescription('Displays current routes for an application');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getSilexApplication();
        /** @var RouteCollection $routes */
        $routes = $app['routes'];
        foreach($routes->all() as $route) {
            $output->writeln($route->getPath().'  '.implode($route->getMethods(), '/'));
        }
    }
}
