<?php
/**
 * Created by PhpStorm.
 * User: arandel
 * Date: 25.06.17
 * Time: 16:28
 */

namespace App\Plugin;


use App\Application;

class PluginManager
{

    private $plugins = [];

    public function __construct(Application $app)
    {
    }

    public function register(Plugin ...$plugins): self
    {
        foreach ($plugins as $plugin) {
            $this->plugins[$plugin->getIdentifier()] = $plugin;
        }
        return $this;
    }


}