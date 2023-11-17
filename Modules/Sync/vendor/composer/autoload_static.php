<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6a4e483ec86054706a5e2469a36e4311
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Modules\\Sync\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Modules\\Sync\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Modules\\Sync\\Console\\SyncCommand' => __DIR__ . '/../..' . '/Console/SyncCommand.php',
        'Modules\\Sync\\Console\\SyncSingleCommand' => __DIR__ . '/../..' . '/Console/SyncSingleCommand.php',
        'Modules\\Sync\\Console\\TestCommand' => __DIR__ . '/../..' . '/Console/TestCommand.php',
        'Modules\\Sync\\Database\\Seeders\\SyncDatabaseSeeder' => __DIR__ . '/../..' . '/Database/Seeders/SyncDatabaseSeeder.php',
        'Modules\\Sync\\Http\\Controllers\\SyncController' => __DIR__ . '/../..' . '/Http/Controllers/SyncController.php',
        'Modules\\Sync\\Providers\\RouteServiceProvider' => __DIR__ . '/../..' . '/Providers/RouteServiceProvider.php',
        'Modules\\Sync\\Providers\\SyncServiceProvider' => __DIR__ . '/../..' . '/Providers/SyncServiceProvider.php',
        'Modules\\Sync\\Services\\SyncService' => __DIR__ . '/../..' . '/Services/SyncService.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6a4e483ec86054706a5e2469a36e4311::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6a4e483ec86054706a5e2469a36e4311::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6a4e483ec86054706a5e2469a36e4311::$classMap;

        }, null, ClassLoader::class);
    }
}
