<?php

namespace Modules\TaskStatus\Console;

use Illuminate\Console\Command;
use Modules\TaskStatus\Service\DuplicateService;
use Symfony\Component\Console\Input\InputOption;

class DuplicateCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'duplicate {--channelid=} {--start=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Duplicate';

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
     * @return void
     */
    public function handle(): void
    {
        $duplicate = new DuplicateService();
        $duplicate->Duplicate($this->output,$this->option('channelid'), $this->option('start'));
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['channelid', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
            ['start', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
