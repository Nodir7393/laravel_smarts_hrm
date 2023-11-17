<?php

namespace Modules\Envato\Console;

use Illuminate\Console\Command;
use Modules\Envato\Services\ZipVerifier\VerifierService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CheckNew extends Command
{

    protected $signature = 'envato:checknew {--start=} {--end=}';
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
     * @return void
     */
    public function handle() :void
    {
        $start = $this->option('start') ?? 1;
        $end = $this->option('end');

        (new VerifierService())->getPosts($start, $end);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions() :array
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
