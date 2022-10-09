<?php
namespace Laravue3\ModuleBuilder;

use Illuminate\Support\Str;
use Symfony\Component\VarDumper\VarDumper;

abstract class Builder 
{
    public abstract static function build($config);
    protected abstract static function buildFields($model);

    public static function createFile($type, $needles, $replacements, $filename = null, $config) {
        $stubFileContent = \File::get(__DIR__ . '/stubs/'. $type .'.stub');

        $stubFileContent = str_replace(
            $needles,
            $replacements,
            $stubFileContent
        );

        $filename = $filename ?? $config['fullPath'] . '/' . $config['moduleName'] . Str::ucfirst($type) . '.php';
        $config['disk']->put($filename, $stubFileContent);
    }

    protected  static function getDatePrefix()
    {
        return date('Y_m_d_His');
    }
} 