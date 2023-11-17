<?php

namespace Modules\Instagram\Console;

use Illuminate\Console\Command;
use Modules\Instagram\Service\AutoAccept;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class AutoAcceptCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'accept:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto accept requests';

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
        $service = new AutoAccept();
        $service->autoAccept(21);
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
//
//    /**
//     * Get the console command options.
//     *
//     * @return array
//     */
//    protected function getOptions()
//    {
//        return [
//            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
//        ];
//    }
}
