<?php

namespace App\Controller;

use App\Application;
use Monolog\Logger;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\SecurityContext;

abstract class PageController implements ControllerProviderInterface, LoggerAwareInterface
{
    /** @var  Application */
    private $app;
    /** @var LoggerInterface */
    private $logger;

    /**
     *
     */
    public function __construct()
    {
        $this->logger = new NullLogger();
    }

    /**
     * @param \Silex\Application $app
     * @return mixed
     */
    public function connect(\Silex\Application $app)
    {
        $this->app = $app;
        return $this->getRoutes($this->getControllerFactory());
    }

    /**
     * @param ControllerCollection $controllers
     * @return ControllerCollection
     */
    abstract protected function getRoutes(ControllerCollection $controllers);

    /**
     * @return ControllerCollection
     */
    private function getControllerFactory()
    {
        return $this->getApp()['controllers_factory'];
    }

    /**
     * @return Application
     */
    private function getApp()
    {
        return $this->app;
    }

    protected function render($view, array $parameters = array(), Response $response = null)
    {
        return $this->getApp()->render($view, $parameters, $response);
    }

    /**
     * @param $level
     * @param $message
     * @param array $context
     */
    protected function log($level, $message, array $context = array())
    {
        $this->logger->log($level, $message, $context);
    }

    /**
     * @param LoggerInterface $logger
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
