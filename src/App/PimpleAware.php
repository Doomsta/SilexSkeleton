<?php
namespace App;

abstract class PimpleAware implements PimpleAwareInterface
{
    protected $pimple;

    /**
     * {@inheritDoc}
     */
    public function setPimple(\Pimple $pimple = null)
    {
        $this->pimple = $pimple;
    }
}