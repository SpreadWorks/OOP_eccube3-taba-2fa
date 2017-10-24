<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit9de6902da8e6e45e11d973c0b6c2111c
{
    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'RobThree\\Auth\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'RobThree\\Auth\\' => 
        array (
            0 => __DIR__ . '/..' . '/robthree/twofactorauth/lib',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit9de6902da8e6e45e11d973c0b6c2111c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit9de6902da8e6e45e11d973c0b6c2111c::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
