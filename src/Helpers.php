<?php
namespace Laravue3\ModuleBuilder;

use Illuminate\Support\Str;
use Symfony\Component\VarDumper\VarDumper;
use Laravue3\ModuleBuilder\Builders\ResourceBuilder;
use Laravue3\ModuleBuilder\Builders\ModelBuilder;
use Laravue3\ModuleBuilder\Builders\ControllerBuilder;
use Laravue3\ModuleBuilder\Builders\FactoryBuilder;
use Laravue3\ModuleBuilder\Builders\MigrationBuilder;
use Laravue3\ModuleBuilder\Builders\PolicyBuilder;
use Laravue3\ModuleBuilder\Builders\ProviderBuilder;
use Laravue3\ModuleBuilder\Builders\RepositoryBuilder;
use Laravue3\ModuleBuilder\Builders\SeederBuilder;
use Laravue3\ModuleBuilder\Builders\VuejsBuilder;

class Helpers 
{
    public function __construct($name)
    {
        $this->disk = \Storage::build(['driver' => 'local', 'root' => './']);

        $this->moduleName = trim($name);
        $this->moduleNameLower = Str::lower(trim($name));
        $this->pluralName = Str::plural($this->moduleName);

        $this->fullPath = config('moduleBuilder.basePath') . '/' . $this->pluralName;

    }

    public function buildFromJson($jsonPath)
    {
        $jsonContent = file_get_contents($jsonPath);
        $content = json_decode($jsonContent, true);

        foreach($content as $model) {
            $config = $this->getConfig($model);

            SeederBuilder::build($config);
            ResourceBuilder::build($config);
            FactoryBuilder::build($config);
            PolicyBuilder::build($config);
            RepositoryBuilder::build($config);
            ControllerBuilder::build($config);
            ProviderBuilder::build($config);            
            ModelBuilder::build($config);            
            MigrationBuilder::build($config);
            VuejsBuilder::build($config);
        }
    }

    public function buildFromOptions($options) 
    {
        $model = [
            'name' => $this->moduleName
        ];

        $config = $this->getConfig($model);

        if (array_key_exists('controller', $options)) {
            ControllerBuilder::build($config);
        }

        if (array_key_exists('factory', $options)) {
            FactoryBuilder::build($config);
        }

        if (array_key_exists('migration', $options)) {
            MigrationBuilder::build($config);
        }

        if (array_key_exists('model', $options)) {
            ModelBuilder::build($config); 
        }

        if (array_key_exists('policy', $options)) {
            PolicyBuilder::build($config);
        }

        if (array_key_exists('repository', $options)) {
            RepositoryBuilder::build($config);
        }

        if (array_key_exists('resource', $options)) {
            ResourceBuilder::build($config);
        }


        if (array_key_exists('seed', $options)) {
            SeederBuilder::build($config);
        }

        if (array_key_exists('viewjs', $options)) {
            VuejsBuilder::build($config);
        }

    }

    public function getConfig($model) 
    {
        $name = trim($model['name']);
        $this->moduleName = $name;
        $this->moduleNameLower = Str::lower($name);
        $this->pluralName = Str::plural($this->moduleName);
        $this->fullPath = config('moduleBuilder.basePath') . '/' . $this->pluralName;

        $config = [
            'name' => $name,
            'moduleName' => $this->moduleName,
            'moduleNameLower' =>  $this->moduleNameLower,
            'pluralName' => $this->pluralName,
            'fullPath' => $this->fullPath,
            'disk' => $this->disk,
            'model' => $model
        ];

        return $config;
    }

    // function createVuejsModule()
    // {
    //     $path = config('moduleBuilder.viewjs') . '/' . Str::lower($this->pluralName);
    //     @mkdir($path);

    //     $this->createFile(
    //         'list',
    //         ['{{ class }}'],
    //         [Str::lower($this->moduleName)],
    //         $path . '/list.vue'
    //     );

    //     $this->createFile(
    //         'edit',
    //         ['{{ class }}'],
    //         [Str::lower($this->moduleName)],
    //         $path . '/edit.vue'
    //     );
    // }

    public function createFile($type, $needles, $replacements, $filename = null) {
        $stubFileContent = \File::get(__DIR__ . '/stubs/'. $type .'.stub');

        $stubFileContent = str_replace(
            $needles,
            $replacements,
            $stubFileContent
        );

        $filename = $filename ?? $this->fullPath . '/' . $this->moduleName . Str::ucfirst($type) . '.php';
        $this->disk->put($filename, $stubFileContent);
    }
}