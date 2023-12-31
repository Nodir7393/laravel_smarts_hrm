<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit32ec4a9c3c1573f3beabb04b68304f86
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

        spl_autoload_register(array('ComposerAutoloaderInit32ec4a9c3c1573f3beabb04b68304f86', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit32ec4a9c3c1573f3beabb04b68304f86', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit32ec4a9c3c1573f3beabb04b68304f86::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
