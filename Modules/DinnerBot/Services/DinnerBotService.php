<?php

namespace Modules\DinnerBot\Services;

use danog\MadelineProto\bots;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;

/**
 * Class    DinnerBotService
 * @package App\Services\DinnerBot
 * coreTeamda bo'gan zakazlani yeg'ib
 * uni narxlarini va h.k lani chiqarib
 * beradi
 */
class DinnerBotService
{
    /**
     * @var Nutgram
     */
    public Nutgram $bot;

    /**
     *
     * Function  web
     * @void webhook ishlatilganda running mode ni
     * webhook ga o'zgartirip botni ishga tushiradi
     */
    public function web()
    {
        $this->bot->setRunningMode(Webhook::class);
        $this->handle($this->bot);

    }

    /**
     *
     * Function  console
     * @void artisan komanda bilan ishga
     * tushirilganda ishlatiladi
     */
    public function console()
    {
        $this->bot->deleteWebhook();
        $this->handle($this->bot);
    }

    /**
     *
     * Function  handle
     * @param Nutgram $bot
     * @void hamma botga keladigan requestlaga tipiga
     * qarap jovob qaytaradi
     */
    public function handle(Nutgram $bot)
    {
        $bot->onCommand('start', function (Nutgram $bot) {
            $bot->sendMessage('Bu bot orqali siz SmartSoftwareda tushlik buyurtma qilishingiz mumkin');
        });

        $bot->onMessage(function (Nutgram $bot) {
            $message = $bot->message();
            if (property_exists($message, 'forward_from_chat') && $message->forward_from_chat !== null) {
                if ($message->forward_from_chat->id === -1001652566931) {
                    $msg_caption = $message->caption;
                    $msg_caption = explode(' ', $msg_caption);
                    foreach ($msg_caption as $i => $key) {
                        if (str_starts_with("\\", $key)) {
                            echo "$key $i";
                            unset($msg_caption[$i]);
                        }
                    }
                    $bot->sendMessage(implode($msg_caption));
                }
            }
        });

        $bot->run();
    }

    /**
     * @void constructor
     */
    public function __construct()
    {
        $this->bot = new Nutgram(env('DINNER_BOT_TOKEN'));
    }
}

