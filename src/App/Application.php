<?php

namespace App;

use App\Console\Command\GreedCommand;
use App\Console\Command\InstallCommand;
use App\Console\Command\Assetic\DumpCommand;
use App\Console\Command\Assetic\WatchCommand;
use App\Console\Command\Debug\RouterCommand;
use App\Controller\HomeController;
use App\Controller\SomeHomeController;
use App\Controller\UserController;
use App\Model\TablePrefix;
use Assetic\Filter\CssMinFilter;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Igorw\Silex\ConfigServiceProvider;
use Knp\Provider\ConsoleServiceProvider;
use RndStuff\Silex\Traits\DoctrineDbalTrait;
use RndStuff\Silex\Traits\KnpConsoleTrait;
use Silex\Application\MonologTrait;
use Silex\Application\TwigTrait;
use Silex\Application\UrlGeneratorTrait;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use SilexAssetic\AsseticServiceProvider;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Application as ConsoleApplication;


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
        $this->initPathConfig();
        $this->initConfig($env);
        $this->initProviders();
        $this->initUser();
        $this->initAssectic();
        $this->initMountPoints();
        if ($this['isCli']) {
            $this->initCli();
        }
    }

    protected function initPathConfig()
    {
        $this->register(new ConfigServiceProvider(ROOT_PATH . '/config/paths.yml'));
        $this['path.config'] = ROOT_PATH . '/config';
    }

    protected function initConfig($env)
    {
        $finder = new Finder();
        $finder->in($this['path.config'])->name('/^'.$env.'.yml$/');
        #foreach($finder as $config) {
            $this->register(new ConfigServiceProvider(ROOT_PATH . '/config/base.yml'));
        #}

        $this->register(new ConfigServiceProvider(
            ROOT_PATH . '/config/' . $env . '.yml',
            array(
                'ROOT_PATH' => ROOT_PATH,
                'APP_PATH' => __DIR__,
                'DATA_PATH' => ROOT_PATH . '/storage',
                'LOG_PATH' => ROOT_PATH . '/storage/log',
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
        $this->register(new DoctrineServiceProvider());
        $this->register(new SessionServiceProvider());
        $this->register(new TwigServiceProvider(),
            array(
                'twig.path' => __DIR__ . '/Views',
            )
        );
    }

    protected function initMountPoints()
    {
        $this
            ->mount('/', new HomeController())
            ->mount('/home', new HomeController())
            ->mount('/test', new SomeHomeController())
            ->mount('/test/test', new SomeHomeController())

        ;
    }

    protected function initCli()
    {
        $this->register(new ConsoleServiceProvider(), array(
            'console.name' => $this['site']['name'],
            'console.version' => '0.0',
            'console.project_directory' => ROOT_PATH
        ));

        #return ConsoleRunner::createHelperSet($app['orm']);
        $this->extend('console', function ($console) {
            /** @var ConsoleApplication $console */
            $console->setHelperSet(ConsoleRunner::createHelperSet($this['orm.em']));
            $console->add(new GreedCommand());
            $console->add(new RouterCommand());
            $console->add(new InstallCommand());
            $console->add(new DumpCommand());
            $console->add(new WatchCommand());
            $console->add(new \Doctrine\ORM\Tools\Console\Command\ClearCache\QueryCommand());
            $console->add(new \Doctrine\ORM\Tools\Console\Command\SchemaTool\CreateCommand());
            $console->add(new \Doctrine\ORM\Tools\Console\Command\SchemaTool\UpdateCommand());
            $console->add(new \Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand());
            return $console;
        });
    }

    public function run(Request $request = null)
    {
        if ($this['isCli']) {
            $this['console']->run();
        }
        parent::run($request);
    }

    private function initUser()
    {
        $this->register(new FormServiceProvider());
        $this->register(new TranslationServiceProvider());
        $this->register(new DoctrineOrmServiceProvider, array(
            #"orm.proxies_dir" => "/path/to/proxies",
            "orm.em.options" => array(
                "mappings" => array(
                    // Using actual filesystem paths
                    array(
                        "type" => "annotation",
                        "namespace" => "App\\Model\\Entity",
                        "path" => __DIR__."/Model/Entity",
                    ),
                ),
            ),
        ));
        $this['dbs.event_manager']['default']->addEventListener(\Doctrine\ORM\Events::loadClassMetadata, new TablePrefix('app_'));
        $this->register(new SecurityServiceProvider(), array(
            'security.firewalls' => array(
                'user.login' => array(
                    'pattern' => '^/user/login$',
                ),
                'secured' => array(
                    'pattern' => '^.*$',
                    'anonymous' => true,
                    'form' => array('login_path' => '/user/login', 'check_path' => '/user/checkLogin'),
                    'logout' => array('logout_path' => '/user/logout'),
                    'users' => $this::share(
                        function ($app) {
                            return new UserManager($app, $app['orm.em']);
                        }
                    ),
                ),
            )
        ));
        $this['user.manager'] = new UserManager($this, $this['orm.em']);
        $this->register(new UserServiceProvider());
        $this->mount('/user', new UserController());
    }


}
