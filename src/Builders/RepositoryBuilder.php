<?php
namespace Laravue3\ModuleBuilder\Builders;

use Exception;
use Illuminate\Support\Str;

class RepositoryBuilder extends \Laravue3\ModuleBuilder\Builder
{
    public static function build($config)
    {
        self::createFile(
            'repository', 
            ['{{ namespace }}', '{{ class }}', '{{ model }}', '{{ modelParam }}'],
            ['App\\' . $config['pluralName'], $config['moduleName'] . 'Repository', $config['moduleName'], $config['moduleNameLower']], 
            config:$config);
    }

    protected static function buildFields($model)
    {
        throw new Exception('not implemented');
    }
} 