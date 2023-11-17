<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitcd2421ad0a17315076a4241db57f9f2e
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

        spl_autoload_register(array('ComposerAutoloaderInitcd2421ad0a17315076a4241db57f9f2e', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitcd2421ad0a17315076a4241db57f9f2e', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitcd2421ad0a17315076a4241db57f9f2e::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
