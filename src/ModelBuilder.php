<?php
namespace Laravue3\ModuleBuilder;

use Illuminate\Support\Str;

class ModelBuilder extends Builder
{
    public static function build($config)
    {
        $fillable = self::buildFields($config['model']);
        self::createFile(
            'model',
            ['{{ namespace }}', '{{ class }}', '{{ fillable }}'],
            ['App\\' . $config['pluralName'], $config['moduleName'], $fillable],
            $config['fullPath'] . '/' . $config['moduleName'] . '.php',
            $config);
    }

   private static function buildFields($model)
   {
        $fillableString = 'protected $fillable = [';
        foreach($model['fields'] as $field) {
            if (!array_key_exists('fillable', $field)  || $field['fillable']) {
                $fillableString .= "'" . $field['name'] . "', ";
            }
        }

        $fillableString .= '];';
        return $fillableString;
   }

   private static function buildFieldString($field) 
   {
        return  "\t\t\t\$table->string('" . $field['name'] . "');";
   }
} 