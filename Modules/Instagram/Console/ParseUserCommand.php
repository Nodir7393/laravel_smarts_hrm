<?php

namespace Modules\Instagram\Console;

use Illuminate\Console\Command;
use Modules\Instagram\Service\LoginService;
use Modules\Instagram\Service\ParseService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ParseUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'insta-users:parse';

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
    public function handle(ParseService $parseService)
    {
        $user = $this->option('users');
        $id = $this->option('id');
        if (!empty($user) && !empty($id)) {
            $parseService->parseUser($user, $id);
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    /*protected function getArguments()
    {
        return [
            ['example', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }*/

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['users', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
            ['id', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
