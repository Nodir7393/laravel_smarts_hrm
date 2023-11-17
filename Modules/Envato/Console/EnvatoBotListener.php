<?php

namespace Modules\Envato\Console;

use danog\MadelineProto\Settings;
use Illuminate\Console\Command;
use Modules\Envato\Services\ReplacerBot\ReplacerBot;
use Modules\EnvatoAddBot\Services\BotMessages;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class EnvatoBotListener extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    protected $name = 'replacerbotlistener:run';

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
        $settings = new Settings();
        ReplacerBot::startAndLoopBot(
            'D:/event/bot.madeline',
            env('REPLACERBOT_TOKEN'), $settings);
    }
}
