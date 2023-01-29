<?php
namespace Laravue3\ModuleBuilder\Builders;

use Exception;
use Illuminate\Support\Str;

class ResourceBuilder extends \Laravue3\ModuleBuilder\Builder
{
    public static function build($config)
    {
        self::createFile(
            'resource', 
            ['{{ namespace }}', '{{ class }}', '{{ modelVariable }}'],
            ['App\\' . $config['pluralName'], $config['moduleName'] . 'Resource', $config['moduleName']], 
            null,
            config:$config);

        self::createFile(
            'resource-collection',
            ['{{ namespace }}', '{{ class }}', '{{ modelVariable }}'],
            ['App\\' . $config['pluralName'], $config['moduleName'] . 'ResourceCollection', $config['moduleName']],
            $config['fullPath'] . '/' . $config['moduleName'] . 'ResourceCollection.php',
            config:$config);
    }

    protected static function buildFields($model)
    {
        throw new Exception('not implemented');
    }
} 