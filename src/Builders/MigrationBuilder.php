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

    protected static function buildFields($model)
   {
        $content = "";

        foreach($model['fields'] as $field) {
            $content .= call_user_func( 'Laravue3\ModuleBuilder\Builders\MigrationBuilder::' . 'buildField' . ucfirst($field['type']), $field) . "\n";
        }

        return $content;
   }

   private static function buildFieldString($field) 
   {
        return  "\t\t\t\$table->string('" . $field['name'] . "');";
   } 
   
   private static function buildFieldBigIncrements($field) 
   {
        return  "\t\t\t\$table->bigIncrements('" . $field['name'] . "');";
   }
   
   private static function buildFieldBigInteger($field) 
   {
        return  "\t\t\t\$table->bigInteger('" . $field['name'] . "');";
   } 

   private static function buildFieldBinary($field) 
   {
        return  "\t\t\t\$table->binary('" . $field['name'] . "');";
   }

   private static function buildFieldBoolean($field) 
   {
        return  "\t\t\t\$table->boolean('" . $field['name'] . "');";
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

   private static function buildFieldDateTimeTz($field) 
   {
        return  "\t\t\t\$table->dateTimeTz('" . $field['name'] . "');";
   } 
   
   private static function buildFieldDateTime($field) 
   {
        return  "\t\t\t\$table->dateTime('" . $field['name'] . "');";
   } 
   
   private static function buildFieldDate($field) 
   {
        return  "\t\t\t\$table->date('" . $field['name'] . "');";
   }
   
   private static function buildFieldDecimal($field) 
   {
        return  "\t\t\t\$table->decimal('" . $field['name'] . "');";
   }  

   private static function buildFieldDouble($field) 
   {
        return  "\t\t\t\$table->double('" . $field['name'] . "');";
   }

   private static function buildFieldFloat($field) 
   {
        return  "\t\t\t\$table->float('" . $field['name'] . "');";
   }

   private static function buildFieldForeignId($field) 
   {
        return  "\t\t\t\$table->foreignId('" . $field['name'] . "');";
   }

   private static function buildFieldId($field) 
   {
        return  "\t\t\t\$table->id('" . $field['name'] . "');";
   }

   private static function buildFieldInteger($field) 
   {
        return  "\t\t\t\$table->integer('" . $field['name'] . "');";
   }

   private static function buildFieldText($field) 
   {
        return  "\t\t\t\$table->text('" . $field['name'] . "');";
   }
} 