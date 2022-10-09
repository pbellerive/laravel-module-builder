<?php
namespace Laravue3\ModuleBuilder\Builders;

use Exception;
use Illuminate\Support\Str;

class SeederBuilder extends \Laravue3\ModuleBuilder\Builder
{
    public static function build($config)
    {
        $directory = $config['fullPath'] . '/seeders';
        @mkdir($directory);

        $path = $directory . '/' . $config['moduleName'] . 'Seeder.php';

        self::createFile(
            'seeder',
            ['{{ class }}', '{{ namespace }}'],
            [$config['moduleName'], $config['pluralName']],
            $path,
            config:$config);
    }

    protected static function buildFields($model)
    {
        throw new Exception('not implemented');
    }
} 