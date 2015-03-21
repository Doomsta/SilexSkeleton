<?php
namespace App;
use Pimple;

/**
 * Silex version of ContainerAwareInterface that is found in Symfony
 */
interface PimpleAwareInterface
{
    /**
     * @param Pimple $pimple
     * @return void
     * @internal param $ Pimple|null
     */
    public function setPimple(Pimple $pimple = null);
}
