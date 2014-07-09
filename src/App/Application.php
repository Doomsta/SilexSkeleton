<?php

namespace App;

use App\Command\Assetic\DumpCommand;
use App\Command\Debug\RouterCommand;
use App\Command\GreedCommand;
use App\Command\InstallCommand;
use App\Controller\HomeController;
use Assetic\Asset\AssetCache;
use Assetic\Asset\GlobAsset;
use Assetic\AssetManager;
use Assetic\Cache\FilesystemCache;
use Assetic\Filter\CssMinFilter;
use Assetic\Filter\JSMinFilter;
use Assetic\Filter\LessphpFilter;
use Assetic\Filter\UglifyJsFilter;
use Assetic\Filter\Yui\JsCompressorFilter;
use Assetic\FilterManager;
use CssMin;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Statement;
use Igorw\Silex\ConfigServiceProvider;
use Knp\Provider\ConsoleServiceProvider;
use RndStuff\Silex\Traits\DoctrineDbalTrait;
use RndStuff\Silex\Traits\KnpConsoleTrait;
use Silex\Application\MonologTrait;
use Silex\Application\TwigTrait;
use Silex\Application\UrlGeneratorTrait;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Silex\Route\SecurityTrait;
use SilexAssetic\Assetic\Dumper;
use SilexAssetic\AsseticServiceProvider;
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
    use KnpConsoleTrait;
    use DoctrineDbalTrait;

    public function __construct($env = 'dev')
    {
        parent::__construct();
        $this['env'] = $env;
        $this['isCli'] = PHP_SAPI === 'cli';
        $this->initConfig($env);
        $this->initProviders();
        $this->initAssectic();
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
                'DATA_PATH' => ROOT_PATH . '/data',
                'LOG_PATH' => ROOT_PATH . '/log',
            )
        ));
        $this->register(new ConfigServiceProvider(
            ROOT_PATH . '/config/' . $env . '.yml',
            array(
                'ROOT_PATH' => ROOT_PATH,
                'APP_PATH' => __DIR__,
                'DATA_PATH' => ROOT_PATH . '/data',
                'LOG_PATH' => ROOT_PATH . '/log',
            )
        ));
    }

    protected function initAssectic()
    {

        $this->register(new AsseticServiceProvider(), array(
            'assetic.path_to_web' => ROOT_PATH.'/public',
            'assetic.options' => array(
                'debug' => $this['debug'],
                'auto_dump_assets' => $this['debug'],
            )
        ));
        $this['assetic.filter_manager'] = $this->share(
            $this->extend('assetic.filter_manager', function ($fm, $this) {
                $fm->set('css', new CssMinFilter());
                return $fm;
            })
        );
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
        $this->register(new DoctrineServiceProvider);
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
            'console.name' => $this['site']['name'],
            'console.version' => '0.0',
            'console.project_directory' => ROOT_PATH
        ));
        $this->addCommand(new GreedCommand());
        $this->addCommand(new RouterCommand());
        $this->addCommand(new InstallCommand());
        $this->addCommand(new DumpCommand());
    }

    public function run(Request $request = null)
    {
        if ($this['isCli']) {
            $this['console']->run();
        }
        parent::run($request);
    }
}
