<?php
namespace Laravue3\ModuleBuilder\Builders;

use Exception;
use Illuminate\Support\Str;

class ProviderBuilder extends \Laravue3\ModuleBuilder\Builder
{
    public static function build($config)
    {
        self::createFile(
            'provider',
            ['{{ namespace }}', '{{ class }}'],
            ['App\\' . $config['pluralName'], $config['moduleName'] . 'ServiceProvider'],
            $config['fullPath'] . '/' . $config['moduleName'] . 'ServiceProvider.php',
            $config);
    }

    protected static function buildFields($model)
    {
        throw new Exception('not implemented');
    }
} 