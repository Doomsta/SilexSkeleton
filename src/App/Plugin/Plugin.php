<?php

namespace App\Plugin;


use App\Application;
use App\ContainerAwareTrait;
use Knp\Command\Command;

// TODO: controller to mount in namespace
// TODO: register services
// TODO: dependencies
abstract class Plugin
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
    public function getIdentifier(): string
    {
        return urlencode($this->getName());
    }

    /**
     * @return Application
     */
    protected function getApp(): Application
    {
        return $this->app;
    }

    public function getCssFiles()
    {
        return [];
    }

    public function getJsFiles()
    {
        return [];
    }

    /**
     * @return Command[]
     */
    public function getCmds() :array
    {
        return [];
    }
}