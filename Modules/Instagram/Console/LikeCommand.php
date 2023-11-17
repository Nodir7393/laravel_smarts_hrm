<?php

namespace Modules\Instagram\Console;

use Illuminate\Console\Command;
use Modules\Instagram\Service\LikeCommentService;
use Modules\Instagram\Service\StoryViewer;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LikeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'like:run';

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
        $service = new StoryViewer();
        $service->storyView(21, 11);
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
