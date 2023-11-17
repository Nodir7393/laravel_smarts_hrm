<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Instagram\Service\LoginService;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xfdncf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(LoginService $loginService)
    {
        $loginService->login(16);
    }
}
