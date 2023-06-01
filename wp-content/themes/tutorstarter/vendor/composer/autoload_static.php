<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit21216c5cca557dc9cca0e29eab9d963a
{
    public static $files = array (
        '4523fe586493a1899da6464972930f4f' => __DIR__ . '/../..' . '/inc/Helpers.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Tutor_Starter\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Tutor_Starter\\' => 
        array (
            0 => __DIR__ . '/../..' . '/inc',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit21216c5cca557dc9cca0e29eab9d963a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit21216c5cca557dc9cca0e29eab9d963a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit21216c5cca557dc9cca0e29eab9d963a::$classMap;

        }, null, ClassLoader::class);
    }
}
