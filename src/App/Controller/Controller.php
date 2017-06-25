<?php

namespace App\Controller;

use App\Application;
use App\ContainerAwareTrait;
use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Silex\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Security\Core\SecurityContext;

abstract class Controller implements \Silex\Api\ControllerProviderInterface
{
    use ContainerAwareTrait;

    /** @var  Application */
    private $app;

    /**
     * @param \Silex\Application $app
     * @return mixed
     */
    public function connect(\Silex\Application $app)
    {
        $this->app = $app;
        return $this->getRoutes($app['controllers_factory']);
    }

    /**
     * @param ControllerCollection $controllers
     * @return ControllerCollection
     */
    abstract protected function getRoutes(ControllerCollection $controllers);

    protected function getContainer()
    {
        return $this->app;
    }
}
