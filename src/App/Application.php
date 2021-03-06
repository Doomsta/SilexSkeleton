<?php

namespace App;

use App\Console\Command\GreedCommand;
use App\Controller\HomeController;
use App\Plugin\Event\CollectCmdsEvent;
use App\Plugin\Event\CollectJsFilesEvent;
use App\Plugin\PluginManager;
use App\Plugin\TestPlugin;
use Knp\Provider\ConsoleServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Knp\Console\Application as ConsoleApplication;

class Application extends \Silex\Application
{
    use ContainerAwareTrait {getCli as public;}

    public function __construct($env = 'dev')
    {
        parent::__construct();
        $this['env'] = $env;
        $this['isCli'] = PHP_SAPI === 'cli';
        $this::initProviders($this);
        $this::initMountPoints($this);
        if ($this['isCli']) {
            $this->initCli($this);
        }
        $this::initPluginManager($this);
        $this->log('test');
    }#

    protected static function initPluginManager(self $app)
    {
        $pm = new PluginManager($app);
        $pm->register(new TestPlugin($app));
        $app['plugin.manager'] = $pm;
        $event = new CollectJsFilesEvent();
        $app->getEventDispatcher()->dispatch($event::NAME, $event);
        print_r($event->getJsFiles());
    }

    protected function getContainer()
    {
        return $this;
    }


    protected static function initProviders(self $app)
    {
        $app->register(new MonologServiceProvider(), [
            'monolog.name' => 'foo',
        ]);
        $app->register(new ServiceControllerServiceProvider());
        $app->register(new DoctrineServiceProvider());
        $app->register(new SessionServiceProvider());
        $app->register(new TwigServiceProvider(), [
            'twig.path' => __DIR__ . '/Views',
        ]);

        #$app['orm.em'] = function () {
        #    $paths = [__DIR__.'/Model/Entity'];
        #    $isDevMode = false;
        #    $dbParams = [
        #        'driver'   => 'pdo_mysql',
        #        'user'     => 'root',
        #        'password' => '',
        #        'dbname'   => 'foo',
        #    ];
        #    $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        #    return EntityManager::create($dbParams, $config);
        #};

    }

    protected static function initMountPoints(self $app)
    {
        $app->mount('/', new HomeController());
    }

    protected function initCli(self $app)
    {
        $app->register(new ConsoleServiceProvider(), [
            'console.name' => __METHOD__,
            'console.version' => '0.0',
            'console.project_directory' => ROOT_PATH
        ]);

        $this->extend('console', function (ConsoleApplication $console) use ($app){
            $event = new CollectCmdsEvent();
            $app->getEventDispatcher()->dispatch($event::NAME, $event);
            $console->addCommands($event->getCmds());
            return $console;
        });
    }
}
