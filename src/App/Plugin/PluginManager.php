<?php
/**
 * Created by PhpStorm.
 * User: arandel
 * Date: 25.06.17
 * Time: 16:28
 */

namespace App\Plugin;


use App\Application;
use App\ContainerAwareTrait;
use Pimple\Container;

class PluginManager
{
    use ContainerAwareTrait;
    /**
     * @var Plugin[]
     */
    private $plugins = [];
    /**
     * @var Application
     */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;

        $ed = $this->getEventDispatcher();
        $ed->addListener(
            'plugin.collect.jsFiles', function (Event\CollectJsFilesEvent $event) {
            $event->addJsFile(...$this->getJsFiles());
        });
        $ed->addListener(Event\CollectCssFilesEvent::NAME, function (Event\CollectCssFilesEvent $event) {
            $event->addCssFile(...$this->getCssFiles());
        });
        $ed->addListener(Event\CollectCmdsEvent::NAME, function (Event\CollectCmdsEvent $event) {
            $event->addCmd(...$this->getCmds());
        });

    }

    public function register(Plugin ...$plugins): self
    {
        foreach ($plugins as $plugin) {
            $this->plugins[$plugin->getIdentifier()] = $plugin;
        }
        return $this;
    }

    /**
     * @return Container
     */
    protected function getContainer(): Container
    {
        return $this->app;
    }

    private function getJsFiles()
    {
        $files = [];
        foreach ($this->plugins as $plugin) {

            $files += $plugin->getJsFiles();
        }
        return $files;
    }

    private function getCmds()
    {
        $cmds = [];
        foreach ($this->plugins as $plugin) {
            $cmds += $plugin->getCmds();
        }
        return $cmds;
    }

    private function getCssFiles()
    {
        $files = [];
        foreach ($this->plugins as $plugin) {
            $files += $plugin->getCssFiles();
        }
        return $files;
    }
}