<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitfeb918efdfb8d47f8e2eb7c91cfe0869
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

        spl_autoload_register(array('ComposerAutoloaderInitfeb918efdfb8d47f8e2eb7c91cfe0869', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitfeb918efdfb8d47f8e2eb7c91cfe0869', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitfeb918efdfb8d47f8e2eb7c91cfe0869::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
