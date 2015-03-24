<?php

namespace App;

use App\Form\UserType;
use Silex\Application as Silex;
use Silex\ServiceProviderInterface;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

class UserServiceProvider implements ServiceProviderInterface
{


    /**
     * Registers services on the given app.
     * This method should only be used to configure services and parameters.
     * It should not get services.
     * @param \App\Application|\Silex\Application $app An Application instance
     */
    public function register(Silex $app)
    {
        $app['user.manager'] = $app::share(
            function ($app) {
                return new UserManager($app, $app['orm.em']);
            }
        );

        $app['user'] = $app::share(function ($app) {
            return ($app['user.manager']->getCurrentUser());
        });

        $app['user.em'] = $app::share(function () use ($app) {
            return $app['orm.em'];
        });

        $app['user.form.registration'] = $app->protect(function () use ($app) {
            $encoder = $app['security.encoder_factory']->getEncoder(Entity::$user); //TODO use the generic getter
            #$encoder = $app['security.encoder.digest'];
            $type = new UserType($encoder);
            return $app['form.factory']->create($type);
        });

        $app['user.default_role'] = $app::share(function () use ($app) {
            return $app['user.em']->getRepository(Entity::$role)->findOneByRole('ROLE_USER');
        });
        $app['user.login.redirect'] = 'home';
    }

    /**
     * Bootstraps the application.
     * This method is called after all services are registers
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Silex $app)
    {
    }
}
