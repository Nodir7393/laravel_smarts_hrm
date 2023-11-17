<?php

namespace App\Services\MadelineProto;


use App\Services\ALL\MTProtoService;
use Modules\Common\Entities\TgChannel;
use Modules\Common\Entities\TgGroup;
use Modules\Common\Entities\TgUser;

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
    public function getDialogs()
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
    public function storeChatIdToDb(array $chats_info)
    {
        foreach ($chats_info as $key => $chat_info) {
            foreach ($chat_info as $item) {
                $id = array_key_exists("Chat", $chat_info) ? $item["Chat"]["id"] : $item["User"]["id"];
                switch ($key) {
                    case 'supergroup' || 'chat':
                        TgGroup::firstOrCreate(["tg_id" => $id]);
                        break;
                    case 'user':
                        TgUser::firstOrCreate(["tg_id" => $id]);
                        break;
                    case 'channel':
                        TgChannel::firstOrCreate(["tg_id" => $id]);
                        break;
                }
            }
        }
    }
}

