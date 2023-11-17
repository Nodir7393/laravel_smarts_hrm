<?php

namespace App\Services\MadelineProto\Database;

use App\Services\ALL\MTProtoService;
use Modules\Common\Entities\TgUser;

class UserService
{
    public function updateAll(){
        $MTProto = new MTProtoService();
        $users = TgUser::where('mtproto', null)->get();
        foreach ($users as $user){
            $temp = [];
            $chat = $MTProto->MadelineProto->getPwrChat($user->tg_id);
            $temp['type'] = $chat['type'];
            /*if(User){
                $temp['first_name'] = $chat['first_name'];
                $temp['last_name'] = array_key_exists('last_name', $chat) ? $chat['last_name'] : null;
            }else{
                $temp['title'] =
            }*/
            $temp['username'] = array_key_exists('username', $chat) ? $chat['username'] : null;
            $temp['status'] = $chat['status']['_'];
            $temp['access_hash'] = $chat['access_hash'];
            $temp['phone'] = array_key_exists('phone', $chat) ? $chat['phone'] : null;
            $temp['bot_nochats'] = $chat['bot_nochats'];
            $temp['phone_calls_available'] = $chat['phone_calls_available'];
            $temp['phone_calls_private'] = $chat['phone_calls_private'];
            $temp['common_chats_count'] = $chat['common_chats_count'];
            $temp['can_pin_message'] = $chat['can_pin_message'];
            $temp['notify_settings'] = json_encode($chat['notify_settings']);
            $temp['photo'] = array_key_exists('photo', $chat) ? json_encode($chat['photo']) : null;
            $temp['mtproto'] = json_encode($chat);
            $post = TgUser::find($user->id);
            $post->update($temp);
        }
    }
}
