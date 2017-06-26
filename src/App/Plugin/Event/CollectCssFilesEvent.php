<?php
/**
 * Created by PhpStorm.
 * User: arandel
 * Date: 26.06.17
 * Time: 08:31
 */

namespace App\Plugin\Event;


use Symfony\Component\EventDispatcher\Event;

class CollectCssFilesEvent extends Event
{

    public const NAME = 'plugin.collect.cssFiles';

    private $files = [];

    /**
     * @param \string[] ...$files
     */
    public function addCssFile(string ...$files)
    {
        $this->files += $files;
    }

    /**
     * @return string[]
     */
    public function getCssFiles(): array
    {
        return $this->files = array_unique($this->files);
    }

}