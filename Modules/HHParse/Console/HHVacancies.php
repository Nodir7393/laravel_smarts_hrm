<?php

namespace Modules\HHParse\Console;

use Illuminate\Console\Command;
use Modules\HHParse\Services\HHParseService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class HHVacancies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'hhparse:vacancies';

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
    public function handle(HHParseService $HHParseService)
    {
        $user_id = $this->option('user_id') ?? 1;
        $HHParseService->getVacanciesParameters($user_id);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['user_id', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
