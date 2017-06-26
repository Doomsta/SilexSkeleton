<?php
/**
 * Created by PhpStorm.
 * User: arandel
 * Date: 26.06.17
 * Time: 08:31
 */

namespace App\Plugin\Event;


use Symfony\Component\EventDispatcher\Event;

class CollectJsFilesEvent extends Event
{

    public const NAME = 'plugin.collect.jsFiles';

    private $files = [];

    /**
     * @param \string[] ...$files
     */
    public function addJsFile(string ...$files)
    {
        $this->files += $files;
    }

    /**
     * @return string[]
     */
    public function getJsFiles(): array
    {
        return $this->files = array_unique($this->files);
    }

}