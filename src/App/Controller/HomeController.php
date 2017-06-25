<?php

namespace App\Controller;


use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{

    const TEMPLATE_PATH = 'Controller/Home';

    /**
     * @param ControllerCollection $controllers
     * @return ControllerCollection
     */
    protected function getRoutes(ControllerCollection $controllers)
    {
        $controllers->get('/', [$this, 'indexAction'])->bind('home.index');
        return $controllers;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        return 'awda';
        return $this->render(self::TEMPLATE_PATH.'/index.twig');
    }
}
