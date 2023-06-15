# Laravel Module Builder


Module builder can be called with params only or with a json description file.
The command will not create anything if the folder exist. This is de default behaviour.
If you use the force param, it will override the module content.

You can use the force param in combinaison of a specific file. This will create only that file en prevent de override of the others.

## Available params

- 'all', 'a', InputOption::VALUE_NONE, 'Generate a migration, seeder, factory, policy, and resource controller for the model'
- 'controller', 'c', InputOption::VALUE_NONE, 'Create a new controller for the model'
- 'factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'
- 'force', null, InputOption::VALUE_NONE, 'Create the class even if the model already exists'
- 'json', null, InputOption::VALUE_REQUIRED, 'Create based on json description'
- 'migration', 'm', InputOption::VALUE_NONE, 'Create a new migration file for the model'
- 'model', 'M', InputOption::VALUE_NONE, 'Create a new model file '
- 'policy', null, InputOption::VALUE_NONE, 'Create a new policy for the model'
- 'seed', 's', InputOption::VALUE_NONE, 'Create a new seeder for the model'
- 'resource', 'r', InputOption::VALUE_NONE, 'Indicates if the generated controller should be a resource controller'
- 'repository', 'l', InputOption::VALUE_NONE, 'Create new repository class for the model'
- 'viewjs', 'j', InputOption::VALUE_NONE, 'Create new vue folder for the module'


## Json description file
{
    {modelName}: {
        fields: [
            {
                name: {thename},
                value: {thevalue},
                precision: {thevalue},
                scale: {thevalue},
                places: {thevalue},
                total: {thevalue},
                length: {thevalue},
            }
        ]
    }
}