<?php
namespace Laravue3\ModuleBuilder;

use Illuminate\Support\Str;
use Symfony\Component\VarDumper\VarDumper;

class Helpers 
{
    public function __construct($name)
    {
        $this->disk = \Storage::build(['driver' => 'local', 'root' => './']);

        $this->moduleName = trim($name);
        $this->moduleNameLower = Str::lower(trim($name));
        $this->pluralName = Str::plural($this->moduleName);

        $this->fullPath = config('moduleBuilder.basePath') . '/' . $this->pluralName;

        $this->createProvider();
    }

    public function buildFromJson($jsonPath)
    {
        $jsonContent = file_get_contents($jsonPath);

        print($jsonContent);

        $content = json_decode($jsonContent, true);

        foreach($content as $model) {
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

            $this->createControllerApi();
            $this->createFactory();
            $this->createSeeder();
            $this->createResource();
            $this->createProvider();
            $this->createPolicy();
            $this->createRepository();

           
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

    public function createControllerApi()
    {
        $this->createFile('controller', ['{{ namespace }}', '{{ class }}', '{{ model }}', '{{ modelParam }}'],['App\\' . $this->pluralName, $this->moduleName . 'Controller', $this->moduleName, $this->moduleNameLower]);
    }

    public function createFactory()
    {
        $fullClassNamespace = '\\App\\' . $this->pluralName . '\\' . $this->moduleName . '::class';
        $fullPath = config('moduleBuilder.factoriesPath') . '/' . $this->pluralName;

        @mkdir($fullPath);

        $stubFileContent = \File::get(__DIR__ . '/stubs/factory.stub');

        $stubFileContent = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ classNamespace }}'],
            ['Database\\Factories\\' . $this->pluralName, $this->moduleName, $fullClassNamespace],
            $stubFileContent
        );
        $filename = $filename ?? $this->moduleName . Str::ucfirst('factory') . '.php';
        $this->disk->put($fullPath . '/' . $filename, $stubFileContent);
    }

    // public function createMigration()
    // {
    //     $name = Str::snake($this->moduleName);

    //     $path = '/migrations/' . $this->getDatePrefix() .'_create_' . $name.'.php';

    //     @mkdir($this->fullPath . '/migrations');
    //     $this->createFile(
    //         'migration.create',
    //         ['{{ namespace }}', '{{ table }}'],
    //         ['App\\' . $this->pluralName, Str::lower($this->pluralName)],
    //         $this->fullPath . '/' . $path);
    // }

    // public function createModel($fillable='')
    // {
    //     $this->createFile(
    //         'model',
    //         ['{{ namespace }}', '{{ class }}', '{{ fillable }}'],
    //         ['App\\' . $this->pluralName, $this->moduleName, $fillable],
    //         $this->fullPath . '/' . $this->moduleName . '.php');
    // }

    public function createRepository()
    {
        $this->createFile('repository', ['{{ namespace }}', '{{ class }}', '{{ model }}', '{{ modelParam }}'],['App\\' . $this->pluralName, $this->moduleName . 'Repository', $this->moduleName, $this->moduleNameLower]);
    }

    public function createPolicy()
    {
        $this->createFile(
            'policy',
            ['{{ namespace }}', '{{ class }}', '{{ model }}', '{{ modelVariable }}'],
            ['App\\' . $this->pluralName, $this->moduleName . 'Policy', $this->moduleName, Str::lower($this->moduleName)]);
    }

    public function createProvider()
    {
        $this->createFile(
            'provider',
            ['{{ namespace }}', '{{ class }}'],
            ['App\\' . $this->pluralName, $this->moduleName . 'ServiceProvider'],
            $this->fullPath . '/' . $this->moduleName . 'ServiceProvider.php');

    }

    public function createResource()
    {
        $this->createFile('resource', ['{{ namespace }}', '{{ class }}', '{{ modelVariable }}'],['App\\' . $this->pluralName, $this->moduleName . 'Resource', $this->moduleName]);
        $this->createFile(
            'resource-collection',
            ['{{ namespace }}', '{{ class }}', '{{ modelVariable }}'],
            ['App\\' . $this->pluralName, $this->moduleName . 'ResourceCollection', $this->moduleName],
            $this->fullPath . '/' . $this->moduleName . 'ResourceCollection.php');
    }

    public function createSeeder()
    {
        $directory = $this->fullPath . '/seeders';
        @mkdir($directory);

        $path = $directory . '/' . $this->moduleName . 'Seeder.php';

        $this->createFile(
            'seeder',
            ['{{ class }}', '{{ namespace }}'],
            [$this->moduleName, $this->pluralName],
            $path);
    }

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