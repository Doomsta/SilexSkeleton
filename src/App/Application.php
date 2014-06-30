<?php

namespace App;

use App\Command\Greed;
use App\Controller\HomeController;
use Igorw\Silex\ConfigServiceProvider;
use LExpress\Silex\ConsoleServiceProvider;
use Silex\Application\MonologTrait;
use Silex\Application\TwigTrait;
use Silex\Application\UrlGeneratorTrait;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Profiler\FileProfilerStorage;

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', __DIR__ . '/../..');
}

class Application extends \Silex\Application
{
    use MonologTrait;
    use UrlGeneratorTrait;
    use TwigTrait;

    public function __construct($env = 'dev')
    {
        parent::__construct();
        $this['env'] = $env;
        $this['isCli'] = PHP_SAPI === 'cli';
        $this->initConfig($env);
        $this->initProviders();
        $this->initMountPoints();
        if ($this['isCli']) {
            $this->initCli();
        }
    }

    protected function initConfig($env)
    {
        $this->register(new ConfigServiceProvider(
            ROOT_PATH . '/config/base.yml',
            array(
                'ROOT_PATH' => ROOT_PATH,
                'APP_PATH' => __DIR__,
                'LOG_PATH' => ROOT_PATH . '/log',
            )
        ));
        $this->register(new ConfigServiceProvider(
            ROOT_PATH . '/config/' . $env . '.yml',
            array(
                'ROOT_PATH' => ROOT_PATH,
                'APP_PATH' => __DIR__,
                'LOG_PATH' => ROOT_PATH . '/log',
            )
        ));
    }

    protected function initProviders()
    {
        $this->register(
            new MonologServiceProvider(),
            array(
                'monolog.name' => $this['site']['name'],
            )
        );
        $this->register(new UrlGeneratorServiceProvider());
        $this->register(new ServiceControllerServiceProvider());
        $this->register(new SessionServiceProvider());
        $this->register(new TwigServiceProvider(),
            array(
                'twig.path' => __DIR__ . '/Views',
            )
        );

        if ($this['debug']) {
            $this->offsetGet('twig.loader.filesystem')->addPath(
                ROOT_PATH . '/vendor/symfony/web-profiler-bundle/Symfony/Bundle/WebProfilerBundle/Resources/views',
                'WebProfiler'
            );
            $this->register(new WebProfilerServiceProvider(), array(
                'profiler.storage' => new FileProfilerStorage('file:' . ROOT_PATH . '/tmp/profiler'),
                'profiler.mount_prefix' => '/_p',
            ));
        }
    }

    protected function initMountPoints()
    {
        $this->mount('/', new HomeController());
    }

    protected function initCli()
    {
        $this->register(new ConsoleServiceProvider(), array(
            'console.name'    => $this['site']['name'],
            'console.version' => '0.0',
        ));
        $this['command.greed'] = $this->share(function ($app) {
            return new Greed();
        });
    }

    public function run(Request $request = null)
    {
        if ($this['isCli']) {
            echo 'test';
            $this['console']->run();
        }
        parent::run($request);
    }
}
