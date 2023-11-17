<?php

namespace Modules\BotButton\Console;

use danog\MadelineProto\Settings;
use Illuminate\Console\Command;
use Modules\BotButton\Service\BotButtonService;
use Modules\EnvatoAddBot\Services\BotMessages;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class SendButtonCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'testbutton';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bot should send a keyboard button';

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
        BotButtonService::startAndLoopBot(
            'D:\Develop\Session/index.madeline',
            '5757752738:AAEjFYkPsPY3w7p9-x5gZO5n3al-ki7fUFs', $settings);
    }

}
