<?php

class Bootstrapper
{
    private const DS = DIRECTORY_SEPARATOR;
    private static $isBootstrapped = false;

    private function __construct()
    {
    }

    /**
     * @return bool
     */
    public function bootstrap(): bool
    {
        if (self::$isBootstrapped) {
            return false;
        }
        define('ROOT_PATH', realpath(__DIR__ . self::DS . '..'));
        self::$isBootstrapped = true;

        return true;
    }

    public function isBootstrapped(): bool
    {
        return self::$isBootstrapped;
    }

    /**
     * @var \App\Application|null
     */
    private static $app;

    /**
     * @return \App\Application
     */
    public static function getApp(): \App\Application
    {
        self::bootstrap();
        if (self::$app === null) {
            self::$app = new \App\Application();
        }
        return self::$app;
    }

}