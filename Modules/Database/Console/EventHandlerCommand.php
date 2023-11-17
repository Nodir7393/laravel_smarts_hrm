<?php

namespace Modules\Database\Console;

use danog\MadelineProto\Settings;
use Illuminate\Console\Command;
use Modules\ALL\Models\TgChannel;
use Modules\ALL\Models\TgGroup;
use Modules\ALL\Services\LoginService;
use Modules\Database\Services\MessageService;
use Modules\Database\Services\MessageHandler;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use danog\MadelineProto\Logger;
use danog\MadelineProto\Settings\Logger as LoggerSettings;

class EventHandlerCommand extends Command
{

    protected $name = 'proto:listen';

    protected $description = 'Command description.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        /*$madelineproto = new LoginService();
        $madelineproto->madelineproto->stop();*/
        $messageService = new MessageService();
        $settings = new Settings;
        /*$settings = (new LoggerSettings)
            ->setExtra('custom.log')
            ->setMaxSize(50 * 1024 * 1024);*/
        //MessageHandler::startAndLoop(env('SESSION_PATH'), $settings);

        /*$madelineproto = new LoginService();
        $chat = $madelineproto->madelineproto->getPwrChat(-1001819802158);
        print_r($chat);
        $validated = $messageService->chatValidator($chat);
        print_r($validated);
        TgChannel::create($validated);
        die();
        $validated = $messageService->messageValidator($chat, 'Modules\ALL\Models\TgChannelText');
        print_r($validated);*/

        //$updates = $madelineproto->madelineproto->messages->getHistory(['peer'=>827299105]);

        /*foreach ($updates['messages'] as $message){
            if(array_key_exists('media',$message)){
            if($message['media']['_'] == 'messageMediaDocument'){
                $madelineproto->madelineproto->downloadToDir($message['media'], 'D:\folder/');
            }
        }
        }*/
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
    /*protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }*/
}
