<?php

namespace App\Services\MadelineProto;

use danog\MadelineProto\API;
use Modules\ALL\Models\TgChannel;
use Modules\ALL\Models\TgGroup;

class ChannelService
{

    /**
     * @var API
     */
    public API $madelineProto;

    public function __construct()
    {
        $this->madelineProto = new API(env('SESSION_PATH'));
        $this->madelineProto->start();
    }

    /**
     *
     * Function  inviteToChannel
     * @param $users
     * @param $groups
     */
    public function inviteToChannel($users, $groups): void
    {
        foreach ($groups as $group) {
            $Updates = $this->madelineProto->channels->inviteToChannel(['channel' => $group, 'users' => $users]);
            //echo "\nUsers added to  -----   $group \n";
        }
    }

    /**
     *
     * Function  CreateChannel
     * @param string $name
     * @param string $type
     * @param string $item
     * @param int $project_id
     * @return  array
     */
    public function CreateChannel(string $name, string $type, string $item, int $project_id): array
    {
        /*$userId = [1307688882, 798946526, 1244414566, 1373029083, 1021190972]; //IDs of users to add
        $creations = ['Mobile Internal', 'Doc User', 'Web App Internal']; //Channels to create

        $GroupsId = [];*/

        /*foreach ($creations as $creation){
            $Channel = $this->mtproto->MadelineProto->channels->createChannel(['title'=>$creation." ".$name, 'about'=>$about]);
            $ChannelId = '-100'.$Channel['chats'][0]['id'];
            $Group = $this->mtproto->MadelineProto->channels->createChannel(['title'=>$creation."Group ".$name, 'about'=>$about, "megagroup"=>true]);
            $GroupId = '-100'.$Group['chats'][0]['id'];
            $Connection = $this->mtproto->MadelineProto->channels->setDiscussionGroup(['broadcast'=>$ChannelId, 'group'=>$GroupId]);

            $GroupsId[] =  $ChannelId;
            $GroupsId[] =  $GroupId;
            echo "\nChannel created!     ------      $creation \n";
        }*/
        //$this->inviteToChannel($userId, $GroupsId);
        $channel = $this->madelineProto->channels->createChannel(['title' => $type . " $item " . $name])['chats'][0];
        $tg_channel = new TgChannel;
        $tg_channel->name = $channel['title'];
        $tg_channel->tg_id = $channel['id'];
        $tg_channel->hrm_projects_id = $project_id;
        $tg_channel->save();
        return ['id' => $tg_channel->id, 'tg_id' => $tg_channel->tg_id];
    }

    /**
     *
     * Function  CreateGroup
     * @param string $name
     * @param string $type
     * @param string $item
     * @param int $project_id
     * @return  array
     */
    public function CreateGroup(string $name, string $type, string $item, int $project_id): array
    {
        $group = $this->madelineProto->channels->createChannel(['title' => $type . " $item" .'Group ' . $name, 'megagroup' => true])['chats'][0];
        $tg_group = new TgGroup;
        $tg_group->name = $group['title'];
        $tg_group->tg_id = $group['id'];
        $tg_group->hrm_projects_id = $project_id;
        $tg_group->save();
        return ['id' => $tg_group->id, 'tg_id' => $tg_group->tg_id];
    }
}
