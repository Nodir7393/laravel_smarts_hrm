<?php

namespace Modules\EnvatoAddBot\Console;

use Illuminate\Console\Command;
use Modules\EnvatoAddBot\Services\EnvatoBot;

class Envato extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'envato:run';

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
    public function handle(EnvatoBot $envatoBot)
    {
        $envatoBot->setAllMessages(env('CHANNEL_ID'));
    }
}
