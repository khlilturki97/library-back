<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CrudMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('make:model', ['name' => $this->argument('name'), '-m' => true]);
        $this->call('make:repository', ['name' => $this->argument('name') . 'Repository']);
        $this->call('make:crud-controller', ['name' => $this->argument('name') . 'Controller']);
        $this->call('make:seeder', ['name' => $this->argument('name') . 'Seeder']);
        $this->call('make:factory', ['name' => $this->argument('name') . 'Factory', '--model' => $this->argument('name')]);
    }
}
