<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf6720aea471842780eb3a3dec4810783
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Modules\\Database\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Modules\\Database\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Modules\\Database\\Console\\EventHandlerCommand' => __DIR__ . '/../..' . '/Console/EventHandlerCommand.php',
        'Modules\\Database\\Database\\Seeders\\DatabaseDatabaseSeeder' => __DIR__ . '/../..' . '/Database/Seeders/DatabaseDatabaseSeeder.php',
        'Modules\\Database\\Http\\Controllers\\DatabaseController' => __DIR__ . '/../..' . '/Http/Controllers/DatabaseController.php',
        'Modules\\Database\\Providers\\DatabaseServiceProvider' => __DIR__ . '/../..' . '/Providers/DatabaseServiceProvider.php',
        'Modules\\Database\\Providers\\RouteServiceProvider' => __DIR__ . '/../..' . '/Providers/RouteServiceProvider.php',
        'Modules\\Database\\Services\\MessageHandler' => __DIR__ . '/../..' . '/Services/MessageHandler.php',
        'Modules\\Database\\Services\\MessageService' => __DIR__ . '/../..' . '/Services/MessageService.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf6720aea471842780eb3a3dec4810783::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf6720aea471842780eb3a3dec4810783::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf6720aea471842780eb3a3dec4810783::$classMap;

        }, null, ClassLoader::class);
    }
}
