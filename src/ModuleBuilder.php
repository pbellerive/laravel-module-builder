<?php

namespace Laravue3\ModuleBuilder;

use Illuminate\Console\Command;
use Illuminate\Support\Pluralizer;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Str;

class ModuleBuilder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'module:create {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module and its related files';

    protected $moduleName;
    protected $pluralName;
    protected $fullpath;
    protected $disk;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->disk = \Storage::build(['driver' => 'local', 'root' => './']);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->moduleName = trim($this->argument('name'));
        $this->pluralName = Str::plural($this->moduleName);

        $this->fullPath = config('moduleBuilder.basePath') . '/' . $this->pluralName;

        if (file_exists($this->fullPath) && !$this->option('force')) {
            $this->line('Folder already exists, are you fucking crazy ?');
            return;
        }

        @mkdir($this->fullPath);

        $this->createProvider();

        if ($this->option('all')) {
            $this->input->setOption('factory', true);
            // $this->input->setOption('seed', true);
            $this->input->setOption('migration', true);
            $this->input->setOption('model', true);
            $this->input->setOption('controller', true);
            $this->input->setOption('policy', true);
            $this->input->setOption('repository', true);
            $this->input->setOption('resource', true);
            $this->input->setOption('seed', true);
            $this->input->setOption('viewjs', true);
        }

        if ($this->option('controller')) {
            $this->createControllerApi();
        }

        if ($this->option('factory')) {
            $this->createFactory();
        }

        if ($this->option('migration')) {
            $this->createMigration();
        }

        if ($this->option('model')) {
            $this->createModel();
        }

        if ($this->option('policy')) {
            $this->createPolicy();
        }

        if ($this->option('repository')) {
            $this->createRepository();
        }

        if ($this->option('resource')) {
            $this->createResource();
        }


        if ($this->option('seed')) {
            $this->createSeeder();
        }

        if ($this->option('viewjs')) {
            $this->createVuejsModule();
        }

        return 0;
    }

    public function createControllerApi()
    {
        $this->createFile('controller', ['{{ namespace }}', '{{ class }}'],['App\\' . $this->pluralName, $this->moduleName . 'Controller']);
    }

    public function createFactory()
    {
        $fullClassNamespace = '\\App\\' . $this->pluralName . '\\' . $this->moduleName . '::class';
        $fullPath = config('moduleBuilder.factoriesPath') . '/' . $this->pluralName;

        @mkdir($fullPath);

        $stubFileContent = $this->disk->get('./ModuleBuilder/stubs/factory.stub');
        $stubFileContent = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ classNamespace }}'],
            ['Database\\Factories\\' . $this->pluralName, $this->moduleName, $fullClassNamespace],
            $stubFileContent
        );
        $filename = $filename ?? $this->moduleName . Str::ucfirst('factory') . '.php';
        $this->disk->put($fullPath . '/' . $filename, $stubFileContent);
    }

    public function createMigration()
    {
        $name = Str::snake($this->moduleName);

        $path = '/migrations/' . $this->getDatePrefix() .'_create_' . $name.'.php';

        @mkdir($this->fullPath . '/migrations');
        $this->createFile(
            'migration.create',
            ['{{ namespace }}', '{{ table }}'],
            ['App\\' . $this->pluralName, Str::lower($this->pluralName)],
            $this->fullPath . '/' . $path);
    }

    public function createModel()
    {
        $this->createFile(
            'model',
            ['{{ namespace }}', '{{ class }}'],
            ['App\\' . $this->pluralName, $this->moduleName],
            $this->fullPath . '/' . $this->moduleName . '.php');
    }

    public function createRepository()
    {
        $this->createFile('repository', ['{{ namespace }}', '{{ class }}'],['App\\' . $this->pluralName, $this->moduleName . 'Repository']);
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
        $directory = $this->fullPath . '/Seeders';
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
        $stubFileContent = $this->disk->get('./ModuleBuilder/stubs/'. $type .'.stub');
        $stubFileContent = str_replace(
            $needles,
            $replacements,
            $stubFileContent
        );

        $filename = $filename ?? $this->fullPath . '/' . $this->moduleName . Str::ucfirst($type) . '.php';
        $this->disk->put($filename, $stubFileContent);
    }



    protected function getArguments()
    {
        return [
            ['name']
        ];
    }

    protected function getOptions()
    {
        return [
            ['all', 'a', InputOption::VALUE_NONE, 'Generate a migration, seeder, factory, policy, and resource controller for the model'],
            ['controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the model'],
            ['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'],
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'],
            ['migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model'],
            ['model', 'M', InputOption::VALUE_NONE, 'Create a new model file '],
            ['policy', null, InputOption::VALUE_NONE, 'Create a new policy for the model'],
            ['seed', 's', InputOption::VALUE_NONE, 'Create a new seeder for the model'],
            ['resource', 'r', InputOption::VALUE_NONE, 'Indicates if the generated controller should be a resource controller'],
            ['repository', 'l', InputOption::VALUE_NONE, 'Create new repository class for the model'],
            ['viewjs', 'j', InputOption::VALUE_NONE, 'Create new vue folder for the module'],
        ];
    }

    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }
}
