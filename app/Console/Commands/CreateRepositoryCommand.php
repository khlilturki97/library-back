<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class CreateRepositoryCommand extends GeneratorCommand {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:repository';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';
    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';
    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
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
        return  app_path() . '/Console/Commands/Stubs/make-repository.stub';
    }
    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Repositories';
    }
    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the repository.']
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
            $repositoryNamePrefix = explode("Repository", $this->argument("name"))[0];
            // If repository name does not follow convention "{modelName}Repository"
            if (!strcmp($repositoryNamePrefix, $this->argument("name"))) {
                $modelName = '';
            } else if ($repositoryNamePrefix) {
                $modelName = $repositoryNamePrefix;
            }
        }
        if (!$modelName) {
            $modelName = 'Model';
        }
        $class = str_replace('DummyModel', $modelName, $class);
        $camelCaseModelName = $modelName;
        $camelCaseModelName[0] = strtolower($modelName[0]);
        $class = str_replace('dummyModel', $camelCaseModelName, $class);
        return $class;
    }

}
