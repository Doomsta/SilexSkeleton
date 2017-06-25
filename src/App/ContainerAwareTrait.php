<?php
/**
 * Created by PhpStorm.
 * User: arandel
 * Date: 25.06.17
 * Time: 15:00
 */

namespace App;


use Monolog\Logger;
use Silex\Application\TwigTrait;
use Silex\Application\UrlGeneratorTrait;

trait ContainerTrait
{

    public function log($message, array $context = [], $level = Logger::INFO)
    {
        return $this->getContainer()['monolog']->addRecord($level, $message, $context);
    }

    /**
     * Generates a path from the given parameters.
     *
     * @param string $route The name of the route
     * @param mixed $parameters An array of parameters
     *
     * @return string The generated path
     */
    public function path($route, $parameters = array())
    {
        return $this->getContainer()['url_generator']->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * Generates an absolute URL from the given parameters.
     *
     * @param string $route The name of the route
     * @param mixed $parameters An array of parameters
     *
     * @return string The generated URL
     */
    public function url($route, $parameters = array())
    {
        return $this->getContainer()['url_generator']->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * Renders a view and returns a Response.
     *
     * To stream a view, pass an instance of StreamedResponse as a third argument.
     *
     * @param string $view The view name
     * @param array $parameters An array of parameters to pass to the view
     * @param Response $response A Response instance
     *
     * @return Response A Response instance
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $twig = $this->getContainer()['twig'];

        if ($response instanceof StreamedResponse) {
            $response->setCallback(function () use ($twig, $view, $parameters) {
                $twig->display($view, $parameters);
            });
        } else {
            if (null === $response) {
                $response = new Response();
            }
            $response->setContent($twig->render($view, $parameters));
        }

        return $response;
    }

    /**
     * Renders a view.
     *
     * @param string $view The view name
     * @param array $parameters An array of parameters to pass to the view
     *
     * @return string The rendered view
     */
    public function renderView($view, array $parameters = array())
    {
        return $this->getContainer()['twig']->render($view, $parameters);
    }
}