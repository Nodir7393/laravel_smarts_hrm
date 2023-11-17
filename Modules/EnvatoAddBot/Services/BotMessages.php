<?php

namespace Modules\EnvatoAddBot\Services;

use danog\MadelineProto\EventHandler;
use Modules\ALL\Models\TgChannel;
use Modules\ALL\Models\TgChannelText;
use Modules\ALL\Models\TgGroup;
use Modules\ALL\Models\TgGroupText;
use Modules\EnvatoAddBot\Models\EnvatoLink;

class BotMessages extends EventHandler
{
    /**
     * @var int|string Username or ID of bot admin
     */
    const ADMIN = "firefox109"; // Change this

    /**
     * List of properties automatically stored in database (MySQL, Postgres, redis or memory).
     * @see https://docs.madelineproto.xyz/docs/DATABASE.html
     * @var array
     */

    protected static array $dbProperties = [
        'dataStoredOnDb' => 'array'
    ];

    /**
     * @var DbArray<array>
     */
    protected $dataStoredOnDb;

    /**
     * Get peer(s) where to report errors
     *
     * @return int|string|array
     */
    public function getReportPeers()
    {
        return [self::ADMIN];
    }

    /**
     * @var ChatService
     */
    public ChatService $chatService;

    /**
     * Called on startup, can contain async calls for initialization of the bot
     */
    public function onStart()
    {
    }

    /**
     *
     * Function  onUpdateEditChannelMessage
     * @param array $update
     * @return  \Generator
     */
    public function onUpdateEditChannelMessage(array $update): \Generator
    {
        $item = $update['message'];
        $channelId = array_key_exists('channel_id', $item['peer_id']) ? $item['peer_id']['channel_id'] : NULL;
        if ($channelId === (int)env('CHANNEL_ID')) {
            $id = EnvatoLink::where('tg_id', '=', $item['id'])->value('id');
            ChatService::upDate($item, $id, 'Modules\EnvatoAddBot\Models\EnvatoLink');
        }
        yield;
    }

    /**
     * Handle updates from supergroups and channels
     *
     * @param array $update Update
     */
    public function onUpdateNewChannelMessage(array $update): \Generator
    {
        return $this->onUpdateNewMessage($update);
    }

    /**
     * Handle updates from users.
     *
     * @param array $update Update
     *
     * @return \Generator
     */
    public function onUpdateNewMessage(array $update): \Generator
    {
        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            return;
        }

        $item = $update['message'];
        $channelId = array_key_exists('channel_id', $item['peer_id']) ? $item['peer_id']['channel_id'] : NULL;
        $media = array_key_exists('media', $item);
        $envatoBot = new EnvatoBot;
        if ($channelId === (int)env('CHANNEL_ID')) {
            ChatService::create($item, 'Modules\EnvatoAddBot\Models\EnvatoLink');
        } elseif ($media && array_key_exists('webpage', $item['media'])) {
            $resault = $envatoBot->sendMessage([$item['message']]);
            $this->messages->sendMessage(['peer' => $update, 'message' => $resault]);
        } else {
            $resaults = $envatoBot->envatoParse($item['message']);
            foreach ($resaults as $resault) {
                $this->messages->sendMessage(['peer' => $update, 'message' => $resault]);
            }
        }
        yield;
    }
}
//$users = collect(TgUser::pluck('tg_id'))->all();
//in_array($item['from_id']['user_id'], $users, true)
