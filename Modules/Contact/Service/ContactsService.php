<?php

namespace Modules\Contact\Service;

use Modules\ALL\Models\TgChannel;
use Modules\ALL\Models\TgGroup;
use Modules\ALL\Models\TgUser;
use Modules\ALL\Services\MTProtoService;

class ContactsService
{
    public MTProtoService $mtproto;

    public function __construct()
    {
        $this->mtproto = new MTProtoService();
    }

    /**
     * Getting full dialogs
     */
    public function getDialogs(): array
    {
        return $this->mtproto->madelineproto->getFullDialogs();
    }

    /**
     * Getting chats info from @param array $dialogs
     */
    public function getChatInfo(array $dialogs): array
    {
        $chats_info = [];
        foreach ($dialogs as $key => $dialog) {
            $chat_info = $this->mtproto->madelineproto->getInfo($key);
            $type = $chat_info['type'];
            $chats_info[$type][] = $chat_info;
        }
        return $chats_info;
    }

    /**
     * Storing data to DataBase to a specific table
     */
    public function storeChatIdToDb(array $chats_info): void
    {
        foreach ($chats_info as $key => $chat_info) {
            foreach ($chat_info as $item) {
                $id = array_key_exists("Chat", $item) ? $item["Chat"]["id"] : $item["User"]["id"];
                switch ($key) {
                    case 'supergroup':
                    case 'chat':
                        TgGroup::firstOrCreate(["tg_id" => $id]);
                        break;
                    case 'user':
                        TgUser::firstOrCreate(["tg_id" => $id]);
                        break;
                    case 'bot':
                        TgUser::firstOrCreate(["tg_id" => $id], ["is_bot" => 1]);
                        break;
                    case 'channel':
                        TgChannel::firstOrCreate(["tg_id" => $id]);
                        break;
                }
            }
        }
    }
}
