<?php
namespace Laravue3\ModuleBuilder;

use Illuminate\Support\Str;

class MigrationBuilder extends Builder
{
    public static function build($config)
    {
        $name = Str::snake($config['moduleName']);

        $path = '/migrations/' . self::getDatePrefix() .'_create_' . $name.'.php';

        @mkdir($config['fullPath'] . '/migrations');

        $up = self::buildFields($config['model']);

        self::createFile(
            'migration.create',
            ['{{ namespace }}', '{{ table }}', '{{ up }}'],
            ['App\\' . $config['pluralName'], Str::lower($config['pluralName']), $up],
            $config['fullPath'] . '/' . $path,
            $config
        );
    }

   private static function buildFields($model)
   {
        $content = "";

        foreach($model['fields'] as $field) {
            $content .= call_user_func( 'Laravue3\ModuleBuilder\MigrationBuilder::' . 'buildField' . ucfirst($field['type']), $field) . "\n";
        }

        return $content;
   }

   private static function buildFieldString($field) 
   {
        return  "\t\t\t\$table->string('" . $field['name'] . "');";
   }
} 