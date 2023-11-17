<?php

namespace Modules\Project\Console;

use Modules\Project\Services\HRMProjectsService;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class HRMProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'create:project {--name=} {--type=}';

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
     * @return int
     */
    public function handle(HRMProjectsService $project)
    {
        $type = $this->option('type') ?? 'webApp';
        $project->CreateProject($this->option('name'), $type);
    }

    protected function getOptions()
    {
        return [
            ['name', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
            ['type', null, InputOption::VALUE_OPTIONAL, 'An example option.', null]
        ];
    }
}
