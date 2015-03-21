<?php

namespace App\Console\Command\Debug;

use Knp\Command\Command;
use Silex\Route;
use Symfony\Component\Console\Helper\Table;
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
        $table = new Table($output);
        $table->setHeaders(array('PATH', 'METHODS'));
        foreach($routes->all() as $route) {
            $table->addRow(array($route->getPath(),implode($route->getMethods(), ', ')));
        }
        $table->render();
    }
}
