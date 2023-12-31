<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit02fe12f1ad2c3319775c09667e36e7f3
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Modules\\ManagerBot\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Modules\\ManagerBot\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Modules\\ManagerBot\\Console\\ManagerBotCommand' => __DIR__ . '/../..' . '/Console/ManagerBotCommand.php',
        'Modules\\ManagerBot\\Database\\Seeders\\ManagerBotDatabaseSeeder' => __DIR__ . '/../..' . '/Database/Seeders/ManagerBotDatabaseSeeder.php',
        'Modules\\ManagerBot\\Http\\Controllers\\ManagerBotController' => __DIR__ . '/../..' . '/Http/Controllers/ManagerBotController.php',
        'Modules\\ManagerBot\\Providers\\ManagerBotServiceProvider' => __DIR__ . '/../..' . '/Providers/ManagerBotServiceProvider.php',
        'Modules\\ManagerBot\\Providers\\RouteServiceProvider' => __DIR__ . '/../..' . '/Providers/RouteServiceProvider.php',
        'Modules\\ManagerBot\\Services\\InlineButtonService' => __DIR__ . '/../..' . '/Services/InlineButtonService.php',
        'Modules\\ManagerBot\\Services\\ManagerBotService' => __DIR__ . '/../..' . '/Services/ManagerBotService.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit02fe12f1ad2c3319775c09667e36e7f3::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit02fe12f1ad2c3319775c09667e36e7f3::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit02fe12f1ad2c3319775c09667e36e7f3::$classMap;

        }, null, ClassLoader::class);
    }
}
