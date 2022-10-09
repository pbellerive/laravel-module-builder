<?php
namespace Laravue3\ModuleBuilder\Builders;

use Exception;
use Illuminate\Support\Str;

class PolicyBuilder extends \Laravue3\ModuleBuilder\Builder
{
    public static function build($config)
    {
        self::createFile(
            'policy',
            ['{{ namespace }}', '{{ class }}', '{{ model }}', '{{ modelVariable }}'],
            ['App\\' . $config['pluralName'], $config['moduleName'] . 'Policy', $config['moduleName'], Str::lower($config['moduleName'])],
            config:$config);
    }

    protected static function buildFields($model)
    {
        throw new Exception('not implemented');
    }
} 