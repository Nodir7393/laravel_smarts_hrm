<?php


namespace Modules\AddUser\Services;


use danog\MadelineProto\API;

class AddUserService
{
    /**
     * @var API
     */
    public API $mtproto;


    public function __construct()
    {
        $this->mtproto = new API(env('session_path'));
        $this->mtproto->start();
    }

    /**
     *
     * Function  addUser
     * userni kanal yoki gruppaga qo'shib beradi
     * @param $chat_id
     * @param $user_id
     */
    public function addUser($chat_id, $user_id)
    {
        $this->mtproto->channels->inviteToChannel([
            "channel" => $chat_id,
            "users" => [$user_id]
        ]);

    }
}

