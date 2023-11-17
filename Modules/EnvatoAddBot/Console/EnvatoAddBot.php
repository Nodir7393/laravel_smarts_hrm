<?php

namespace Modules\EnvatoAddBot\Console;

use danog\MadelineProto\Settings;
use Illuminate\Console\Command;
use Modules\EnvatoAddBot\Services\BotMessages;

class EnvatoAddBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'envatoAddBot:run';

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
    public function handle()
    {
        $settings = new Settings();
        BotMessages::startAndLoopBot(
            'C:/OSPanel/sessions/envato/bot.madeline',
            '5935155989:AAF2K8ZYAj7GvQ_eiauRzmpD1XZCUuG_q-I', $settings);
    }
}
