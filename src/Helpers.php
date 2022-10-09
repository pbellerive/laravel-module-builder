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

        print($jsonContent);

        $content = json_decode($jsonContent, true);

        foreach($content as $model) {
            $config = $this->getConfig($model);

            $this->createSeeder();
            ResourceBuilder::build($config);
            FactoryBuilder::build($config);
            PolicyBuilder::build($config);
            RepositoryBuilder::build($config);
            ControllerBuilder::build($config);
            ProviderBuilder::build($config);            
            ModelBuilder::build($config);            
            MigrationBuilder::build($config);
        }
    }

    public function buildFromOptions($options) 
    {
        var_dump($options);
        if (array_key_exists('controller', $options)) {
            $this->createControllerApi();
        }

        if (array_key_exists('factory', $options)) {
            $this->createFactory();
        }

        if (array_key_exists('migration', $options)) {
            $this->createMigration();
        }

        if (array_key_exists('model', $options)) {
            $this->createModel();
        }

        if (array_key_exists('policy', $options)) {
            $this->createPolicy();
        }

        if (array_key_exists('repository', $options)) {
            $this->createRepository();
        }

        if (array_key_exists('resource', $options)) {
            $this->createResource();
        }


        if (array_key_exists('seed', $options)) {
            $this->createSeeder();
        }

        if (array_key_exists('viewjs', $options)) {
            $this->createVuejsModule();
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

    // public function createSeeder()
    // {
    //     $directory = $this->fullPath . '/seeders';
    //     @mkdir($directory);

    //     $path = $directory . '/' . $this->moduleName . 'Seeder.php';

    //     $this->createFile(
    //         'seeder',
    //         ['{{ class }}', '{{ namespace }}'],
    //         [$this->moduleName, $this->pluralName],
    //         $path);
    // }

    function createVuejsModule()
    {
        $path = config('moduleBuilder.viewjs') . '/' . Str::lower($this->pluralName);
        @mkdir($path);

        $this->createFile(
            'list',
            ['{{ class }}'],
            [Str::lower($this->moduleName)],
            $path . '/list.vue'
        );

        $this->createFile(
            'edit',
            ['{{ class }}'],
            [Str::lower($this->moduleName)],
            $path . '/edit.vue'
        );
    }

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

    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }
    
}