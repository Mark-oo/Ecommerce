<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfc1f869a173ff00d5498fcb696d91f55
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Stripe\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfc1f869a173ff00d5498fcb696d91f55::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfc1f869a173ff00d5498fcb696d91f55::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
