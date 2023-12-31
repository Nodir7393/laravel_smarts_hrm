<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit3ed214e71a1e2f4cec2b0dbe3ee55c02
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

        spl_autoload_register(array('ComposerAutoloaderInit3ed214e71a1e2f4cec2b0dbe3ee55c02', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit3ed214e71a1e2f4cec2b0dbe3ee55c02', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit3ed214e71a1e2f4cec2b0dbe3ee55c02::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
