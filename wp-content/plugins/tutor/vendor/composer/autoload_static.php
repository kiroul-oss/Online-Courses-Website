<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4a02c91445665def50d2b246ead953df
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Tutor\\Models\\' => 13,
            'Tutor\\Helpers\\' => 14,
            'Tutor\\Cache\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Tutor\\Models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/models',
        ),
        'Tutor\\Helpers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/helpers',
        ),
        'Tutor\\Cache\\' => 
        array (
            0 => __DIR__ . '/../..' . '/cache',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4a02c91445665def50d2b246ead953df::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4a02c91445665def50d2b246ead953df::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit4a02c91445665def50d2b246ead953df::$classMap;

        }, null, ClassLoader::class);
    }
}
