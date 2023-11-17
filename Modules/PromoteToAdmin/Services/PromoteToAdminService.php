<?php


namespace Modules\PromoteToAdmin\Services;


use App\Services\ALL\MTProtoService;

class PromoteToAdminService
{
    public MTProtoService $mtproto;

    public function __construct()
    {
        $this->mtproto = new MTProtoService();
    }

    public function promoteToAdmin($chat_id, $user_id, $permissions = null)
    {

        $addUser = new AddUserService();
        $addUser->addUser($chat_id, $user_id);
        if ($permissions === null) {
            $chatAdminRights = ['_' => 'chatAdminRights', 'change_info' => true, 'post_messages' => true, 'edit_messages' => true, 'delete_messages' => true, 'ban_users' => true, 'invite_users' => true, 'pin_messages' => true, 'add_admins' => true, 'anonymous' => true, 'manage_call' => true, 'other' => true];
        }else{
            $adminRights = explode(',',$permissions[0]);
        }
        $chatAdminRights;
        $this->MTProto->MadelineProto->channels->editAdmin([
            "channel" => $chat_id,
            "user_id" => $user_id,
            "admin_rights" => $chatAdminRights
        ]);
    }
}
