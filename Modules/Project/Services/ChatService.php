<?php

namespace Modules\Project\Services;

use danog\MadelineProto\API;
use Illuminate\Support\Arr;
use Modules\ALL\Models\TgChannel;
use Modules\ALL\Models\TgChannelText;
use Modules\ALL\Models\TgGroup;

class ChatService
{
    const TG_GROUP_TEXT = 'Modules\ALL\Models\TgGroupText';
    const TG_CHANNEL_TEXT = 'Modules\ALL\Models\TgChannelText';
    const SAVED_ID = 'fwd_from_saved_from_msg_id';
    const SAVED_PEER = 'fwd_from_saved_from_peer';
    /**
     * @var API
     */
    public API $MadelineProto;

    public function __construct()
    {
        $this->MadelineProto = new API(env('SESSION_PATH'));
        $this->MadelineProto->start();
        $this->tg_channel = new TgChannel;
        $this->tg_group = new TgGroup;
    }

    /**
     * @var TgChannel
     */
    protected TgChannel $tg_channel;
    /**
     * @var TgGroup
     */
    protected TgGroup $tg_group;

    private $event;
    /**
     *
     * Function  getAllMessages
     */
    public function getAllMessages(): void
    {
        $this->event = true;
        $peers = $this->MadelineProto->getDialogs();
        $items = collect($peers)->groupBy('_');
        $chats = [];
        foreach ($items->get('peerChannel') as $channel) {
            $chats['channel'][] = $channel['channel_id'];
        }
        $channels_id = $this->tg_channel->pluck('tg_id');
        $model = self::TG_CHANNEL_TEXT;
        foreach ($channels_id as $channel_id) {
            if (in_array($channel_id, $chats['channel'])) {
                $this->getMessages($channel_id, $model);
            }
        }
        $groups_id = $this->tg_group->pluck('tg_id');
        $model = self::TG_GROUP_TEXT;
        foreach ($groups_id as $group_id) {
            if (in_array($group_id, $chats['channel'])) {
                $this->getMessages($group_id, $model);
            }
        }
    }

    /**
     *
     * Function  getMessages
     * @param int $channel_id
     * @param $model
     */

/*$chats_id = $model::select('tg_id')->where('peer_id_channel_id', '=', $channel_id)->orderByDesc('tg_id')->first();
$start = $chats_id ? 1 : $chats_id['tg_id'] + 1;*/
    public function getMessages(int $channel_id, $model): void
    {
        $chats_id = $model::select('tg_id')->where('peer_id_channel_id', '=', $channel_id)
        ->orderByDesc('tg_id')->first();
        $start = $chats_id ? $chats_id['tg_id'] + 1: 1;
        $end = $this->MadelineProto->messages
        ->getHistory(['peer' => -100 . $channel_id, 'limit' => 1])['messages'][0]['id'];
        for ($i = $start; $i <= $end; $i += 200) {
            $messages = $this->MadelineProto->channels->getMessages([
            "channel" => -100 . $channel_id,
            "id" => range($i, $end)])['messages'];
            foreach ($messages as $item) {
                self::create($item, $model, $this->event);
            }
        }
    }

    /**
     *
     * Function  save_db
     * @param array $item
     * @param $model
     */
    public static function create(array $item, $model, $event = false): void
    {
        $chatText = new $model;
        self::save($item, $chatText, $model, $event);
    }

    /**
     *
     * Function  upDate
     * @param array $item
     * @param $model
     */
    public static function upDate(array $item, $id, $model): void
    {
        $chatText = $model::find($id);
        self::save($item, $chatText, $model);
    }

    /**
     *
     * Function  save
     * @param array $item
     * @param $chatText
     */
    private static function save(array $item, $chatText, $model, bool $event = false)
    {
        if ($item['_'] !== 'messageEmpty') {
            $chatText->type = $item['_'];
            $chatText->full_url = 'https://t.me/c/' . $item['peer_id']['channel_id'] . '/' . $item['id'];
            $chatText->out = $item['out'];
            $chatText->mentioned = $item['mentioned'];
            $chatText->media_unread = $item['media_unread'];
            $chatText->silent = $item['silent'];
            $chatText->post = $item['post'];
            $chatText->from_scheduled = array_key_exists('from_scheduled', $item) ? $item['from_scheduled'] : NULL;
            $chatText->legacy = $item['legacy'];
            $chatText->edit_hide = array_key_exists('edit_hide', $item) ? $item['edit_hide'] : NULL;
            $chatText->pinned = array_key_exists('pinned', $item) ? $item['pinned'] : NULL;
            $chatText->noforwards = array_key_exists('noforwards', $item) ? $item['noforwards'] : NULL;
            $chatText->tg_id = $item['id'];
            if (array_key_exists('from_id', $item)) {
                $chatText->from_id_user_id = array_key_exists('user_id', $item['from_id']) ? $item['from_id']['user_id'] : $item['from_id']['channel_id'];
                $chatText->from_id__ = $item['from_id']['_'];
            }
            $chatText->peer_id__ = $item['peer_id']['_'];
            $chatText->peer_id_channel_id = array_key_exists('channel_id', $item['peer_id']) ? $item['peer_id']['channel_id'] : NULL;
            if (array_key_exists('fwd_from', $item)) {
                $chatText->fwd_from__ = $item['fwd_from']['_'];
                $chatText->fwd_from_imported = $item['fwd_from']['imported'];
                $chatText->fwd_from_from_id = json_encode(Arr::get($item['fwd_from'],'from_id'));
                $chatText->fwd_from_date = date("Y-m-d H:i:s", $item['fwd_from']['date']);
                if (array_key_exists('saved_from_peer', $item['fwd_from'])) {

                    $chatText->fwd_from_saved_from_peer = json_encode($item['fwd_from']['saved_from_peer']);
                }
                $chatText->fwd_from_saved_from_msg_id = array_key_exists('saved_from_msg_id', $item['fwd_from']) ?  $item['fwd_from']['saved_from_msg_id'] : NULL;

                if ($model === self::TG_GROUP_TEXT) {
                    $chatText->tg_channel_text_id = TgChannelText::where('tg_id', '=', $chatText->fwd_from_saved_from_msg_id)->where('peer_id_channel_id', '=', $item['fwd_from']['saved_from_peer']['channel_id'])->value('id');
                }
            }
            $chatText->entities = array_key_exists('entities', $item) ? json_encode($item['entities']) : NULL;
            $chatText->views = array_key_exists('views', $item) ? json_encode($item['views']) : NULL;
            $chatText->reply_to__ = array_key_exists('reply_to', $item) ? $item['reply_to']['_'] : NULL;
            $chatText->reply_to_reply_to_scheduled = array_key_exists('reply_to', $item) ? $item['reply_to']['reply_to_scheduled'] : NULL;
            if (array_key_exists('reply_to', $item)) {
                $chatText->reply_to_reply_to_msg_id = $item['reply_to']['reply_to_msg_id'];
                $chatText->full_url .= '?thread=' . $item['reply_to']['reply_to_msg_id'];

                if ($model === self::TG_GROUP_TEXT) {
                    $post = $model::select(self::SAVED_ID, self::SAVED_PEER)->where('tg_id', '=', $item['reply_to']['reply_to_msg_id'])
                        ->where(self::SAVED_ID,'!=', null)
                    ->where('peer_id_channel_id', '=', $chatText->peer_id_channel_id)->first();
                    if($post) {
                        $channel_id = json_decode($post[self::SAVED_PEER])->channel_id;
                        $chatText->tg_channel_text_id = TgChannelText::where('tg_id', '=', $post[self::SAVED_ID])->where('peer_id_channel_id', '=', $channel_id)->value('id');
                    }
                }
            }
            $chatText->date = date("Y-m-d H:i:s", $item['date']);
            $chatText->message = array_key_exists('message', $item) ? $item['message'] : NULL;
            if (array_key_exists('replies', $item)) {
                $chatText->replies__ = $item['replies']['_'];
                $chatText->replies_replies = $item['replies']['replies'];
                $chatText->replies_comments = $item['replies']['comments'];
                $chatText->replies_replies_pts = $item['replies']['replies_pts'];
                $chatText->replies_max_id = array_key_exists('max_id', $item['replies']) ? $item['replies']['max_id'] : NULL;
                $chatText->replies_read_max_id = array_key_exists('read_max_id', $item['replies']) ? $item['replies']['read_max_id'] : NULL;
                $chatText->replies_channel_id = array_key_exists('channel_id', $item['replies']) ? $item['replies']['channel_id'] : NULL;
            }
            $chatText->media__ = array_key_exists('media', $item) ? $item['media']['_'] : NULL;
            if (array_key_exists('media', $item)) {
                $chatText->media = json_encode($item['media']);
                $chatText->dl_file_size = array_key_exists('document', $item['media']) ? $item['media']['document']['size'] : NULL;
            }
            $chatText->action__ = array_key_exists('action', $item) ? $item['action']['_'] : NULL;
            $chatText->action = array_key_exists('action', $item) ? json_encode($item['action']) : NULL;
            $chatText->mtproto = json_encode($item);
            $save = $event ? $chatText->saveQuietly() : $chatText->save();
        }
    }
}
