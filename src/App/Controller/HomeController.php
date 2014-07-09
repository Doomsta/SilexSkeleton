<?php

namespace App\Controller;


use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends PageController
{

    protected function getRoutes(ControllerCollection $controllers)
    {
        $controllers->get('/', array($this, 'indexAction'))->bind('home.index');
        return $controllers;
    }


    public function indexAction(Request $request)
    {
        return $this->getApp()->render('Controller/Home/index.twig');
    }
}
