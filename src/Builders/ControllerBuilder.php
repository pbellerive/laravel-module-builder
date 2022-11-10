<?php
namespace Laravue3\ModuleBuilder\Builders;

use Exception;
use Illuminate\Support\Str;

class ControllerBuilder extends \Laravue3\ModuleBuilder\Builder
{
    public static function build($config)
    {
       self::createFile(
        'controller',
        ['{{ namespace }}', '{{ class }}', '{{ model }}', '{{ modelParam }}'],
        ['App\\' . $config['pluralName'], 
        $config['moduleName'] . 'Controller', $config['moduleName'], $config['moduleNameLower']], 
        config:$config);
    }

    protected static function buildFields($model)
    {
        throw new Exception('not implemented');
    }
} 