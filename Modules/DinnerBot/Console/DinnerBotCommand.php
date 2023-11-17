<?php

namespace Modules\DinnerBot\Console;

use Illuminate\Console\Command;
use Modules\DinnerBot\Services\DinnerBotService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class DinnerBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dinner:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @void
     */
    public function handle()
    {
        $dinner = new DinnerBotService(); /** @var  $dinner ovqat zakazlarini hisoblab beradigan servis */
        $dinner->console();
    }
}
