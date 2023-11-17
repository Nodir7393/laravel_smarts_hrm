<?php

namespace Modules\Database\Services;

use Modules\ALL\Models\TgChannelText;

class MessageService
{

    const MODULES_ALL_MODELS_TG_GROUP_TEXT = 'Modules\ALL\Models\TgGroupText';

    public function messageValidator($message, $model = NULL)
    {
        $validated = [];
        $validated['type'] = $message['_'] ?? NULL;
        //$validated['full_url'] = 'https://t.me/c/' . $message['peer_id']['channel_id'] . '/' . $message['id'];
        $validated['out'] = $message['out'] ?? NULL;
        $validated['mentioned'] = $message['mentioned'];
        $validated['media_unread'] = $message['media_unread'];
        $validated['silent'] = $message['silent'];
        $validated['post'] = $message['post'];
        $validated['from_scheduled'] = array_key_exists('from_scheduled', $message) ? $message['from_scheduled'] : NULL;
        $validated['legacy'] = $message['legacy'];
        $validated['edit_hide'] = array_key_exists('edit_hide', $message) ? $message['edit_hide'] : NULL;
        $validated['pinned'] = array_key_exists('pinned', $message) ? $message['pinned'] : NULL;
        $validated['noforwards'] = array_key_exists('noforwards', $message) ? $message['noforwards'] : NULL;
        $validated['tg_id'] = $message['id'];
        if (array_key_exists('from_id', $message)) {
            $validated['from_id_user_id'] = array_key_exists('user_id', $message['from_id']) ? $message['from_id']['user_id'] : $message['from_id']['channel_id'];
            $validated['from_id__'] = $message['from_id']['_'];
        }
        $validated['peer_id__'] = $message['peer_id']['_'];
        $validated['peer_id_channel_id'] = array_key_exists('channel_id', $message['peer_id']) ? $message['peer_id']['channel_id'] : NULL;
        if (array_key_exists('fwd_from', $message)) {
            $validated['fwd_from__'] = $message['fwd_from']['_'];
            $validated['fwd_from_imported'] = $message['fwd_from']['imported'];
            $validated['fwd_from_from_id'] = json_encode($message['fwd_from']['from_id']);
            $validated['fwd_from_date'] = date("Y-m-d H:i:s", $message['fwd_from']['date']);
            $validated['fwd_from_saved_from_peer'] = json_encode($message['fwd_from']['saved_from_peer']);
            $validated['fwd_from_saved_from_msg_id'] = $message['fwd_from']['saved_from_msg_id'];
            if ($model === self::MODULES_ALL_MODELS_TG_GROUP_TEXT) {
                $validated['tg_channel_text_id'] = TgChannelText::where('tg_id', '=', $validated['fwd_from_saved_from_msg_id'])->where('peer_id_channel_id', '=', $message['fwd_from']['saved_from_peer']['channel_id'])->value('id');
            }
        }
        $validated['entities'] = array_key_exists('entities', $message) ? json_encode($message['entities']) : NULL;
        $validated['views'] = array_key_exists('views', $message) ? json_encode($message['views']) : NULL;
        $validated['reply_to__'] = array_key_exists('reply_to', $message) ? $message['reply_to']['_'] : NULL;
        $validated['reply_to_reply_to_scheduled'] = array_key_exists('reply_to', $message) ? $message['reply_to']['reply_to_scheduled'] : NULL;
        if (array_key_exists('reply_to', $message)) {
            $validated['reply_to_reply_to_msg_id'] = $message['reply_to']['reply_to_msg_id'];
            $validated['full_url'] .= '?thread=' . $message['reply_to']['reply_to_msg_id'];
            if ($model === self::MODULES_ALL_MODELS_TG_GROUP_TEXT) {
                $post = collect($model::where('tg_id', '=', $message['reply_to']['reply_to_msg_id'])->where('peer_id_channel_id', '=', $validated['peer_id_channel_id'])->first())->all();
                $tgId = array_key_exists('fwd_from_saved_from_msg_id', $post) ? $post['fwd_from_saved_from_msg_id'] : NULL;
                $channelId = json_decode($post['fwd_from_saved_from_peer'])->channel_id;
                $validated['tg_channel_text_id'] = TgChannelText::where('tg_id', '=', $tgId)->where('peer_id_channel_id', '=', $channelId)->value('id');
            }
        }
        $validated['date'] = date("Y-m-d H:i:s", $message['date']);
        $validated['message'] = array_key_exists('message', $message) ? $message['message'] : NULL;
        if (array_key_exists('replies', $message)) {
            $validated['replies__'] = $message['replies']['_'];
            $validated['replies_replies'] = $message['replies']['replies'];
            $validated['replies_comments'] = $message['replies']['comments'];
            $validated['replies_replies_pts'] = $message['replies']['replies_pts'];
            $validated['replies_max_id'] = array_key_exists('max_id', $message['replies']) ? $message['replies']['max_id'] : NULL;
            $validated['replies_read_max_id'] = array_key_exists('read_max_id', $message['replies']) ? $message['replies']['read_max_id'] : NULL;
            $validated['replies_channel_id'] = array_key_exists('channel_id', $message['replies']) ? $message['replies']['channel_id'] : NULL;
        }
        $validated['media__'] = array_key_exists('media', $message) ? $message['media']['_'] : NULL;
        if (array_key_exists('media', $message)) {
            $validated['media'] = json_encode($message['media']);
            $validated['dl_file_size'] = array_key_exists('document', $message['media']) ? $message['media']['document']['size'] : NULL;
        }
        $validated['action__'] = array_key_exists('action', $message) ? $message['action']['_'] : NULL;
        $validated['action'] = array_key_exists('action', $message) ? json_encode($message['action']) : NULL;
        $validated['mtproto'] = json_encode($message);
        return $validated;
    }

    public function chatValidator($chat){
        foreach ($chat as $key => $value){
            if(gettype($value) == 'array'){
                $chat[$key] = json_encode($value);
            }
            if($key == 'id'){
                unset($chat[$key]);
                $chat['tg_id'] = $value;
            }
            if($key == 'title'){
                unset($chat[$key]);
                $chat['name'] = $value;
            }
        }
        return $chat;
    }
}
