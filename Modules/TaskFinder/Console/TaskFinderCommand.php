<?php

namespace Modules\TaskFinder\Console;

use Illuminate\Console\Command;
use Modules\TaskFinder\Services\TaskFinderService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TaskFinderCommand extends Command
{

    protected $name = 'tasks:find';

    protected $description = 'Command description.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $service = new TaskFinderService();

        $result = $service->findTasks(-1001736380151, $this->output);
        print_r(PHP_EOL);
        dump('Finish');
        $service->editStatus(-1001866423428, $result, $this->output);
    }
}
