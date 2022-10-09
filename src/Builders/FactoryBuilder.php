<?php
namespace Laravue3\ModuleBuilder\Builders;

use Exception;
use Illuminate\Support\Str;

class FactoryBuilder extends \Laravue3\ModuleBuilder\Builder
{
    public static function build($config)
    {
        $fullClassNamespace = '\\App\\' . $config['pluralName'] . '\\' . $config['moduleName'] . '::class';
        $fullPath = config('moduleBuilder.factoriesPath') . '/' . $config['pluralName'];

        @mkdir($fullPath);

        $stubFileContent = \File::get(__DIR__ . '/../stubs/factory.stub');

        $stubFileContent = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ classNamespace }}'],
            ['Database\\Factories\\' . $config['pluralName'], $config['moduleName'], $fullClassNamespace],
            $stubFileContent
        );
        $filename = $filename ?? $config['moduleName'] . Str::ucfirst('factory') . '.php';
        $config['disk']->put($fullPath . '/' . $filename, $stubFileContent);
    }

    protected static function buildFields($model)
    {
        throw new Exception('not implemented');
    }
} 