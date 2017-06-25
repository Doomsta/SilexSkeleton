<?php

namespace App;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Pimple\Container;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


trait ContainerAwareTrait
{
    /**
     * @return Container
     */
    abstract protected function getContainer(): Container;

    /**
     * @return Logger
     */
    protected function getLogger(): Logger
    {
        return $this->getContainer()['monolog'];
    }

    protected function log($message, array $context = [], $level = Logger::INFO): bool
    {
        return $this->getLogger()->addRecord($level, $message, $context);
    }

    /**
     * @return UrlGeneratorInterface
     */
    protected function getUrlGenerator(): UrlGeneratorInterface
    {
        return $this->getContainer()['url_generator'];
    }

    protected function path($route, $parameters = [])
    {
        return $this->getUrlGenerator()->generate(
            $route,
            $parameters,
            UrlGeneratorInterface::ABSOLUTE_PATH
        );
    }

    protected function url($route, $parameters = [])
    {
        return $this->getUrlGenerator()->generate(
            $route,
            $parameters,
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @return EntityManagerInterface
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->getContainer()['orm.em'];
    }

    /**
     * @return \Twig_Environment
     */
    protected function getTwig(): \Twig_Environment
    {
        return $this->getContainer()['twig'];
    }

    protected function render($view, array $parameters = [], Response $response = null)
    {
        $twig = $this->getTwig();

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
    protected function renderView($view, array $parameters = array())
    {
        return $this->getTwig()->render($view, $parameters);
    }

    /**
     * @return \Knp\Console\Application
     */
    protected function getCli()
    {
        return $this->getContainer()['console'];
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function getEventDispatcher(): EventDispatcherInterface
    {
        return $this->getContainer()['dispatcher'];
    }
}