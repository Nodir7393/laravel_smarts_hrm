<?php

namespace Modules\RemoveJoinLeave\Services;

use TCG\Voyager\Models\Setting;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;
use SergiX44\Nutgram\Telegram\Attributes\MessageTypes;

/**
 * Class    RemoveJoinLeaveService
 * bu servis gruppadigi kirdi chiqtilani
 * tozalap turadi
 */
class RemoveJoinLeaveService
{
    /**
     * @var $bot Nutgram
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
            $bot->sendMessage('This bot handles and remove all join/leave messages');
        });

        /*$bot->onChannelPost(function (Nutgram $bot) {
            $chat_id = $bot->chatId();
            $dropper = new Nutgram(env('DROPPER_BOT_TOKEN'));
            $dropper_id = $dropper->getMe()->id;
            try {
                $member = $bot->getChatMember($chat_id, $dropper_id);
            } catch (\Throwable $e) {
                echo $e->getMessage();
                $member = null;
            }
            if ($member) {
                if ($member->status !== 'left' && $member->status !== 'kicked') {
                    $ch_list = $this->getDropperChats('site.tg_channel');
                    $value = $ch_list->get('value');
                    if (!str_contains($value[0]->value, $chat_id)) {
                        $ch_list->update(['value' => $value[0]->value . "\r\n$chat_id"]);
                    } else {
                        $bot->sendMessage('id alredy has');
                    }
                    $bot->sendMessage('bot in here');
                } else {
                    $bot->sendMessage('bot not in here');
                }
            }
        });*/

        $bot->onMessageType(MessageTypes::LEFT_CHAT_MEMBER, function (Nutgram $bot) {
            $chat_id = $bot->chatId();
            $msg_id = $bot->messageId();
            $bot->deleteMessage($chat_id, $msg_id);
        });

        $bot->onMessageType(MessageTypes::NEW_CHAT_MEMBERS, function (Nutgram $bot) {
            $member = $bot->message()->new_chat_members;
//            $member_id = $member[0]->id; /* yengi qo'wilgan user idsi */
//            $dropper = new Nutgram(env('DROPPER_BOT_TOKEN'));
//            $dropper_id = $dropper->getMe()->id; /*  dropper bot idsi */
            $chat_id = $bot->chatId();
            $msg_id = $bot->messageId();
            /*if ($member_id === $dropper_id) {
                $ch_list = $this->getDropperChats('site.tg_group');
                $value = $ch_list->get('value');
                if (!str_contains($value[0]->value, $chat_id)) {
                    $ch_list->update(['value' => $value[0]->value . "\r\n$chat_id"]);
                } else {
                    $bot->sendMessage('id alredy has');
                }
            }*/
            $bot->deleteMessage($chat_id, $msg_id);
        });

        $bot->onException(function (Nutgram $bot, \Throwable $exception) {
            echo $exception->getMessage();
            $bot->sendMessage($exception->getMessage());
        });

        $bot->run();
    }

    /**
     *
     * Function  getDropperChats
     * @param string $key
     * @return  mixed
     * dropper bor chatlani oberadi
     */
    public function getDropperChats(string $key)
    {
        return Setting::where('key', $key);
    }

    /**
     * @void constructor
     */
    public function __construct()
    {
        $this->bot = new Nutgram(env('REMOVE_JOIN_BOT_TOKEN'));
    }
}
