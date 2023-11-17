<?php

namespace Modules\Project\Console;

use Illuminate\Console\Command;
use Modules\Project\Services\UpdateDatabase;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class UpdateDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'update:db {--channel_id=} {--start=} {--end=}';

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
    public function handle(UpdateDatabase $updateDatabase)
    {
        $channel_id = $this->option('channel_id');
        $start = $this->option('start');
        $the_end = $this->option('end');
        $updateDatabase->collector((int)$channel_id, (string)$start, (string)$the_end);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['channel_id', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
            ['start', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
            ['end', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
