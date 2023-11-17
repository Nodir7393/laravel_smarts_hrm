<?php

namespace Modules\ManagerBot\Console;

use Illuminate\Console\Command;
use Modules\ManagerBot\Services\ManagerBotService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ManagerBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'manager:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';


    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $manager = new ManagerBotService();
        $manager->console();
    }
}
