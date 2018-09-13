<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita77888e6c70ac88ad4d30b0fdc5b004d
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Models\\' => 7,
        ),
        'I' => 
        array (
            'Interfaces\\' => 11,
        ),
        'C' => 
        array (
            'Controllers\\' => 12,
        ),
        'B' => 
        array (
            'Bases\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Models\\' => 
        array (
            0 => __DIR__ . '/../..' . '/models',
        ),
        'Interfaces\\' => 
        array (
            0 => __DIR__ . '/../..' . '/core/interfaces',
        ),
        'Controllers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/controllers',
        ),
        'Bases\\' => 
        array (
            0 => __DIR__ . '/../..' . '/core/bases',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita77888e6c70ac88ad4d30b0fdc5b004d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita77888e6c70ac88ad4d30b0fdc5b004d::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
