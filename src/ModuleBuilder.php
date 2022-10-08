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
    protected $moduleNameLower;
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
        $this->moduleNameLower = Str::lower(trim($this->argument('name')));
        $this->pluralName = Str::plural($this->moduleName);

        $this->fullPath = config('moduleBuilder.basePath') . '/' . $this->pluralName;

        if (file_exists($this->fullPath) && !$this->option('force')) {
            $this->line('Folder already exists, are you fucking crazy ?');
            return;
        }

        @mkdir($this->fullPath);
        
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

        $helper = new Helpers(trim($this->argument('name')));

        if ($this->option('json')) {
            print($this->option('json'));
            $helper->buildFromJson($this->option('json'));
        } else {
            $helper->buildFromOptions($this->options());
            return 0;
        }
    }

    // public function createFile($type, $needles, $replacements, $filename = null) {
    //     $stubFileContent = \File::get(__DIR__ . '/stubs/'. $type .'.stub');

    //     $stubFileContent = str_replace(
    //         $needles,
    //         $replacements,
    //         $stubFileContent
    //     );

    //     $filename = $filename ?? $this->fullPath . '/' . $this->moduleName . Str::ucfirst($type) . '.php';
    //     $this->disk->put($filename, $stubFileContent);
    // }


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
            ['json', null, InputOption::VALUE_REQUIRED, 'Create based on json description'],
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
