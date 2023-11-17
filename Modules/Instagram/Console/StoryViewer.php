<?php

namespace Modules\Instagram\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class StoryViewer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'story:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

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
        $service = new \Modules\Instagram\Service\StoryViewer();
        $service->storyView(21, 10);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
//    protected function getArguments()
//    {
//        return [
//            ['example', InputArgument::REQUIRED, 'An example argument.'],
//        ];
//    }

    /**
     * Get the console command options.
     *
     * @return array
     */
//    protected function getOptions()
//    {
//        return [
//            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
//        ];
//    }
}
