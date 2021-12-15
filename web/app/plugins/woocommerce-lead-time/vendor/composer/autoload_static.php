<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc572d611de8a6d7a6fdc781e0a0ba599
{
    public static $files = array (
        '29acfeed39d95458ef5c16682645f6ae' => __DIR__ . '/../..' . '/src/wclt-functions.php',
        '380d479b20f5f1708fe2152e62a5d776' => __DIR__ . '/../..' . '/lib/class-wc-settings-plugin-promo.php',
        '20b479fccbc16c0764f71bc1cd440cef' => __DIR__ . '/../..' . '/lib/class-wc-settings-additional-field-types.php',
    );

    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'WPTRT\\AdminNotices\\' => 19,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
        'B' => 
        array (
            'Barn2\\WLT_Lib\\' => 14,
            'Barn2\\Plugin\\WC_Lead_Time\\' => 26,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'WPTRT\\AdminNotices\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib/vendor/admin-notices/src',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
        'Barn2\\WLT_Lib\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib',
        ),
        'Barn2\\Plugin\\WC_Lead_Time\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc572d611de8a6d7a6fdc781e0a0ba599::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc572d611de8a6d7a6fdc781e0a0ba599::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc572d611de8a6d7a6fdc781e0a0ba599::$classMap;

        }, null, ClassLoader::class);
    }
}
