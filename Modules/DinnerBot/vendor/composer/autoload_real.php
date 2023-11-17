<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitb9d2d2ee822d6d2704bcf49fd3d8c733
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitb9d2d2ee822d6d2704bcf49fd3d8c733', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitb9d2d2ee822d6d2704bcf49fd3d8c733', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitb9d2d2ee822d6d2704bcf49fd3d8c733::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
