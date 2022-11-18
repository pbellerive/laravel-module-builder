<?php
namespace Laravue3\ModuleBuilder\Builders;

use Illuminate\Support\Str;

class MigrationBuilder extends \Laravue3\ModuleBuilder\Builder
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

     private static function buildField($field) 
     {
          if (method_exists(__CLASS__, 'buildField' . ucfirst($field['type']))){
               return call_user_func( 'Laravue3\ModuleBuilder\Builders\MigrationBuilder::' . 'buildField' . ucfirst($field['type']), $field);
          }

          $strField = "\t\t\t\$table->". $field['type'] ."('" . $field['name'];
          if (array_key_exists('params', $field)) {
               $strField .= ',' . $field['params'];
          }
          
          $strField .= "');";
          return $strField;
     }

    protected static function buildFields($model)
   {
          $content = "";
          if (array_key_exists('fields', $model)) {
               foreach($model['fields'] as $field) {
                    $content .= self::buildField($field) . "\n";
               }
          }

          return $content;
   }

   private static function buildFieldChar($field) 
   {
        $str = "\t\t\t\$table->char('" . $field['name'] ;
        if (array_key_exists('length', $field)) {
            $str .= ',' . $field['length'];
        }
        $str .= "');";
        return $str;
   }
} 