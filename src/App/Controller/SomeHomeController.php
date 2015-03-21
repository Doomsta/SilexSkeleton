<?php

namespace App\Controller;


use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

class SomeHomeController extends PageController
{

    const TEMPLATE_PATH = 'Controller/Home';

    /**
     * @param ControllerCollection $controllers
     * @return ControllerCollection
     */
    protected function getRoutes(ControllerCollection $controllers)
    {
        $controllers->get('/', array($this, 'indexAction'));
        $controllers->get('/moep', array($this, 'indexAction'));
        $controllers->post('/moep', array($this, 'indexAction'));
        return $controllers;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return $this->render(self::TEMPLATE_PATH.'/index.twig');
    }
}
