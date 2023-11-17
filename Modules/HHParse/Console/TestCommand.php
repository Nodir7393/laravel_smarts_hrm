<?php

namespace Modules\HHParse\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\HHParse\Models\HhUser;
use Modules\HHParse\Services\HHParseService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'rge';

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
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(HHParseService $HHParseService)
    {
        $user_id = $this->option('users');
        $vacansy_id = $this->option('vacancy') ?? 'all';
        Log::info(HhUser::pluck('id'));
        $users = $user_id ? [$user_id] : (collect(HhUser::pluck('id')));
        $database = $this->option('db') ?? false;
        Log::info($users);
        if ($vacansy_id === 'all') {$HHParseService->vacancyAll($users, $database);}
        else {$HHParseService->vacancy($vacansy_id);}

//        $HHParseService->getVacanciesParameters(1);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['vacancy', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
            ['users', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
            ['db', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
