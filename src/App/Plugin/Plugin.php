<?php

namespace App\Plugin;


use App\Application;
use App\ContainerAwareTrait;
use Pimple\Container;
use Silex\Api\EventListenerProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

// TODO: controller to mount in namespace
// TODO: register services
// TODO: dependencies
abstract class Plugin implements EventListenerProviderInterface
{

    use ContainerAwareTrait;

    private $app;

    /**
     * Plugin constructor.
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function isActive(): boolean
    {
        return true;
    }

    /**
     * @return string
     */
    abstract public function getName(): string;

    /**
     * must me url save and unique
     * @return string
     */
    public function getIdentifier(): string {
        return urlencode($this->getName());
    }

    /**
     * @return Application
     */
    protected function getApp(): Application
    {
        return $this->app;
    }

    public function subscribe(Container $app, EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addListener('plugin.collect.jsFiles', function () {

        });
        $dispatcher->addListener('plugin.collect.cssFiles', function () {

        });
        $dispatcher->addListener('plugin.collect.cmds', function () {

        });


    }

    private $cssFiles;

    protected final function addCssFile(string ...$filePath)
    {
        $this->cssFiles += $filePath;
    }

    private $jssFiles;

    protected final function addJsFile(string ...$filePath)
    {
        $this->jssFiles += $filePath;
    }
}