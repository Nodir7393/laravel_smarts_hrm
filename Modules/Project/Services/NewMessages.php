<?php

namespace Modules\Project\Services;

use danog\MadelineProto\EventHandler;
use Modules\ALL\Models\TgChannel;
use Modules\ALL\Models\TgChannelText;
use Modules\ALL\Models\TgGroup;
use Modules\ALL\Models\TgGroupText;

class NewMessages extends EventHandler
{
    /**
     * @var int|string Username or ID of bot admin
     */
    const ADMIN = "zarnigor_asatova"; // Change this
    const CHANNEL = 'Modules\ALL\Models\TgChannelText';
    const GROUP = 'Modules\ALL\Models\TgGroupText';

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
        $tgChats = TgChannel::pluck('tg_id')->all();
        $item = $update['message'];
        if (in_array($item['peer_id']['channel_id'], $tgChats)) {
            $id = TgChannelText::where('tg_id', '=', $item['id'])->where('peer_id_channel_id', '=', $item['peer_id']['channel_id'])->value('id');
            ChatService::upDate($item, $id, self::CHANNEL);
        } elseif (in_array($item['peer_id']['channel_id'], TgGroup::pluck('tg_id')->all())) {
            $id = TgGroupText::where('tg_id', '=', $item['id'])->where('peer_id_channel_id', '=', $item['peer_id']['channel_id'])->value('id');
            ChatService::upDate($item, $id, self::GROUP);
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
        $tgChats = TgChannel::pluck('id', 'tg_id')->all();
        $item = $update['message'];
        $channel_id = array_key_exists('channel_id', $item['peer_id']);

        if ($channel_id && array_key_exists($item['peer_id']['channel_id'], $tgChats)) {
            ChatService::create($item, self::CHANNEL);
        }

        return $this->onUpdateNewMessage($update);
    }

    /**
     *
     * Function  onUpdateDeleteMessages
     * @param array $update
     * @return  \Generator
     */
    public function onUpdateDeleteChannelMessages(array $update): \Generator
    {
        $items = $update['messages'];
        TgGroupText::where('peer_id_channel_id', $update['channel_id'])->whereIn('tg_id',$items)->delete();
        TgChannelText::where('peer_id_channel_id', $update['channel_id'])->whereIn('tg_id',$items)->delete();
        yield;
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
        $tgGroup = TgGroup::pluck('id','tg_id')->all();
        $item = $update['message'];
        $channel_id = array_key_exists('channel_id', $item['peer_id']);

        if ($channel_id && array_key_exists($item['peer_id']['channel_id'], $tgGroup)) {
            ChatService::create($item, self::GROUP);
        }
        yield;
    }
}
