<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita77888e6c70ac88ad4d30b0fdc5b004d
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MelhorEnvio\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MelhorEnvio\\' => 
        array (
            0 => __DIR__ . '/../..' . '/controllers',
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