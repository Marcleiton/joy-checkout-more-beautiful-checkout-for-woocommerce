<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitdb8b1d0b2361652d9cb55901816eb4a2
{
    public static $files = array (
        '320cde22f66dd4f5d3fd621d3e88b98f' => __DIR__ . '/..' . '/symfony/polyfill-ctype/bootstrap.php',
        '68880a7c13dac3627ec61a147e407dee' => __DIR__ . '/../..' . '/includes/helpers/array.php',
        '78e603fcac5e8a6f695a5a0f6dc07b5a' => __DIR__ . '/../..' . '/includes/helpers/cache.php',
        'dd5ba3bd11f6fd6fd883ca842d57fbb3' => __DIR__ . '/../..' . '/includes/helpers/config.php',
        '708af8a96bb27bd1ec2ef283a8ac2cb1' => __DIR__ . '/../..' . '/includes/helpers/file.php',
        '55c6b9955b363e5805ceb5c4e104452c' => __DIR__ . '/../..' . '/includes/helpers/logger.php',
        '56ebee43b5dc5781c428807376385791' => __DIR__ . '/../..' . '/includes/helpers/misc.php',
        '6c38895f2bcd2aed00ca3fb5146541fd' => __DIR__ . '/../..' . '/includes/helpers/post.php',
        '8de1fcc0ebeaa2ca5392fa2f613cdd3f' => __DIR__ . '/../..' . '/includes/helpers/string.php',
        'f7734098358bae36b6f04a1f37402a1a' => __DIR__ . '/../..' . '/includes/helpers/template.php',
        '506d562de618995b7d122ea0dba25cdd' => __DIR__ . '/../..' . '/includes/helpers/user.php',
        '365aee3dae3cf07206a18a42adf41e1b' => __DIR__ . '/../..' . '/includes/helpers/woocommerce.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Ctype\\' => 23,
            'Symfony\\Component\\Yaml\\' => 23,
        ),
        'J' => 
        array (
            'Joy_Checkout\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Ctype\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-ctype',
        ),
        'Symfony\\Component\\Yaml\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/yaml',
        ),
        'Joy_Checkout\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes/classes',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitdb8b1d0b2361652d9cb55901816eb4a2::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitdb8b1d0b2361652d9cb55901816eb4a2::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
