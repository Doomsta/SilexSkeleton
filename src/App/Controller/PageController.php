<?php

namespace App\Controller;

use App\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Security\Core\SecurityContext;

abstract class PageController implements ControllerProviderInterface
{
    /** @var  Application */
    private $app;

    public function connect(\Silex\Application $app)
    {
        $this->app = $app;
        return $this->getRoutes($this->getControllerFactory());
    }

    abstract protected function getRoutes(ControllerCollection $controllers);

    /**
     * @return ControllerCollection
     */
    protected function getControllerFactory() {
        return $this->app['controllers_factory'];
    }

    /**
     * @return HttpKernel
     */
    protected function getKernel() {
        return $this->app['kernel'];
    }

    /**
     * @return Request
     */
    protected function getRequest() {
        return $this->app['request'];
    }

    /**
     * @return SecurityContext
     */
    protected function getSecurity() {
        return $this->app['security'];
    }

    /**
     * @return Application
     */
    protected function getApp()
    {
        return $this->app;
    }
}
