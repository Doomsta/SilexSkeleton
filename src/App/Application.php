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
use Assetic\Filter\CssMinFilter;
use Dflydev\Silex\Provider\DoctrineOrm\DoctrineOrmServiceProvider;
use Doctrine\ORM\EntityManager;
use Igorw\Silex\ConfigServiceProvider;
use Knp\Provider\ConsoleServiceProvider;
use RndStuff\Silex\Traits\DoctrineDbalTrait;
use RndStuff\Silex\Traits\KnpConsoleTrait;
use Silex\Application\MonologTrait;
use Silex\Application\TwigTrait;
use Silex\Application\UrlGeneratorTrait;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use SilexAssetic\AsseticServiceProvider;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Profiler\FileProfilerStorage;
use Tacker\Configurator;
use Tacker\LoaderBuilder;

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
        foreach($finder as $config) {
            $this->register(new ConfigServiceProvider(ROOT_PATH . '/config/base.yml'));
        }

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
        $this
            ->mount('/', new HomeController())
            ->mount('/hallo', new HomeController())
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
        $this->addCommand(new GreedCommand());
        $this->addCommand(new RouterCommand());
        $this->addCommand(new InstallCommand());
        $this->addCommand(new DumpCommand());
        $this->addCommand(new WatchCommand());
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
        $this->register(new DoctrineOrmServiceProvider, array(
            #"orm.proxies_dir" => "/path/to/proxies",
            "orm.em.options" => array(
                "mappings" => array(
                    // Using actual filesystem paths
                    array(
                        "type" => "annotation",
                        "namespace" => "App\\Model\\Entity",
                        "path" => __DIR__."/src/App/Entity",
                    ),
                ),
            ),
        ));
        $this->register(new SecurityServiceProvider(), array(
            'security.firewalls' => array(
                'unsecured' => array(
                    'pattern' => '^/hallo',
                    'form' => array('login_path' => '/user/login', 'check_path' => '/user/checkLogin'),
                    'users' => array(
                        'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
                    ),
                ),
                'admin' => array(
                    'pattern' => '^/admin/',
                    'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
                    'users' => array(
                        'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
                    ),
                ),
            )
        ));
        $this['user.manager'] = new UserManager($this, $this['orm.em']);
        $this->register(new UserServiceProvider());
        $this->mount('/user', new UserController());
    }


}
