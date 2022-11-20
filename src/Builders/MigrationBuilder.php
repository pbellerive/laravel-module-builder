<?php
namespace Laravue3\ModuleBuilder\Builders;

use Exception;
use Illuminate\Support\Str;

class MigrationBuilder extends \Laravue3\ModuleBuilder\Builder

{

    public static function build($config)
    {
        $name = Str::snake($config['moduleName']);

        $path = '/migrations/' . self::getDatePrefix() . '_create_' . $name . '.php';

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
        if (method_exists(__CLASS__, 'buildField' . ucfirst($field['type']))) {
            return call_user_func('Laravue3\ModuleBuilder\Builders\MigrationBuilder::' . 'buildField' . ucfirst($field['type']), $field);
        }

        $strField = "\t\t\t\$table->" . $field['type'] . "('" . $field['name'];
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
            foreach ($model['fields'] as $field) {
                $content .= self::buildField($field) . "\n";
            }
        }

        return $content;
    }

    private static function buildFieldWithPrecisionAndScale($field)
    {
        $str = "\t\t\t\$table->" . $field['type'] . "('" . $field['name'] . "'";
        if (array_key_exists('precision', $field)) {
            $str .= ', precision:' . $field['precision'];
        }
        if (array_key_exists('scale', $field)) {
            $str .= ', scale:' . $field['scale'];
        }
        $str .= ");";
        return $str;
    }

    private static function buildFieldWithTotalAndPlaces($field)
    {
        $str = "\t\t\t\$table->" . $field['type'] . "('" . $field['name'] . "'";
        if (array_key_exists('total', $field)) {
            $str .= ', total:' . $field['total'];
        }
        if (array_key_exists('places', $field)) {
            $str .= ', places:' . $field['places'];
        }
        $str .= ");";
        return $str;
    }

    private static function buildFieldChar($field)
    {
        $str = "\t\t\t\$table->char('" . $field['name'];
        if (array_key_exists('length', $field)) {
            $str .= ',' . $field['length'];
        }
        $str .= "');";
        return $str;
    }

    private static function buildFieldDateTimeTz($field)
    {
        return self::buildFieldWithPrecisionAndScale($field);
    }

    private static function buildFieldDateTime($field)
    {
        return self::buildFieldWithPrecisionAndScale($field);
    }

    private static function buildFieldDecimal($field)
    {
        return self::buildFieldWithPrecisionAndScale($field);
    }

    private static function buildFieldDouble($field)
    {
        return self::buildFieldWithTotalAndPlaces($field);
    }

    private static function buildFieldEnum($field)
    {
        if (!array_key_exists('value', $field)) {
            throw new Exception('Value is required for Enum type');
        }
        $str = "\t\t\t\$table->enum('" . $field['name'] . "," . $field['value'] . "');";

        return $str;
    }

    private static function buildFieldFloat($field)
    {
        return self::buildFieldWithTotalAndPlaces($field);

    }

    private static function buildFieldSoftDeletesTz($field)
    {
        return self::buildFieldWithPrecisionAndScale($field);
    }

    private static function buildFieldSoftDeletes($field)
    {
        return self::buildFieldWithPrecisionAndScale($field);
    }

    private static function buildFieldString($field)
    {
        $str = "\t\t\t\$table->string('" . $field['name'];
        if (array_key_exists('length', $field)) {
            $str .= ',' . $field['length'];
        }
        $str .= "');";
        return $str;
    }

    private static function buildFieldTimeTz($field)
    {
        return self::buildFieldWithPrecisionAndScale($field);
    }

    private static function buildFieldTime($field)
    {
        return self::buildFieldWithPrecisionAndScale($field);
    }
    
    private static function buildFieldTimestampTz($field)
    {
        return self::buildFieldWithPrecisionAndScale($field);
    }
    
    private static function buildFieldTimestamp($field)
    {
        return self::buildFieldWithPrecisionAndScale($field);
    }
    
    private static function buildFieldTimestampsTz($field)
    {
        return self::buildFieldWithPrecisionAndScale($field);
    }

    private static function buildFieldTimestamps($field)
    {
        return self::buildFieldWithPrecisionAndScale($field);
    }

    private static function buildFieldUnsignedDecimal($field)
    {
        return self::buildFieldWithPrecisionAndScale($field);
    }
}
