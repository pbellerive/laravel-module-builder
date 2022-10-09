<?php
namespace Laravue3\ModuleBuilder\Builders;

use Exception;
use Illuminate\Support\Str;

class VuejsBuilder extends \Laravue3\ModuleBuilder\Builder
{
    public static function build($config)
    {
        $path = config('moduleBuilder.viewjs') . '/' . $config['moduleNameLower'];
        @mkdir($path);

        self::createFile(
            'list',
            ['{{ class }}'],
            [Str::lower($config['moduleName'])],
            $path . '/list.vue',
            config:$config
        );

        self::createFile(
            'edit',
            ['{{ class }}', '{{ plural }}'],
            [$config['moduleNameLower'], Str::lower($config['pluralName'])],
            $path . '/edit.vue',
            config:$config
        );
    }

    protected static function buildFields($model)
    {
        throw new Exception('not implemented');
    }
} 