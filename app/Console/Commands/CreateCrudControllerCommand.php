<?php


namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CreateCrudControllerCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:crud-controller';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new crud controller class';
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'CrudController';

    /**
     * Replace the class name for the given stub.
     *
     * @param string $stub
     * @param string $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);
        return str_replace('DummyClass', $this->argument('name'), $stub);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return app_path() . '/Console/Commands/Stubs/make-crud-controller.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers';
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the crud controller.']
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The name of the model']
        ];
    }

    /**
     * Build the class with the given name.
     *
     * @param string $name
     * @return string
     * @throws FileNotFoundException
     */
    protected function buildClass($name)
    {
        $class = parent::buildClass($name);
        // If model argument has been specified
        if ($this->option('model')) {
            $modelName = $this->option('model');
        } else {
            $controllerNamePrefix = explode("Controller", $this->argument("name"))[0];
            // If repository name does not follow convention "{modelName}Repository"
            if (!strcmp($controllerNamePrefix, $this->argument("name"))) {
                $modelName = '';
            } else if ($controllerNamePrefix) {
                $modelName = $controllerNamePrefix;
            }
        }
        $repositoryName = $modelName."Repository";
        $class = str_replace('DummyRepository', $repositoryName, $class);
        $camelCaseRepositoryName = $repositoryName;
        $camelCaseRepositoryName[0] = strtolower($repositoryName[0]);
        $class = str_replace('dummyRepository', $camelCaseRepositoryName, $class);
        return $class;
    }

}
