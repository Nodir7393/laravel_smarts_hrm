<?php

namespace Modules\Envato\Services\PreviewVerifier;

use Modules\Envato\Services\EnvatoService;

class EnvatoSendMediaService {
    use EnvatoService;

    public function sendMessage($message, $comments) {
        $matches = $this->getLink($message);
        $a = true;
        foreach ($comments as $comment) {
            if (array_key_exists('message', $comment) && preg_match('#mediaPreviews#', $comment['message'], $arr)) {
                $a = false;
            }
        }
        if ($a && array_key_exists(0, $matches)) {
            $this->MadelineProto->messages->sendMedia(
                ['peer' => '-100' . env('GROUP_ID'),
                    'media' => [
                        '_' => 'inputMediaUploadedDocument',
                        'file' => $matches[0],
                        'attributes' => [
                            ['_' => 'documentAttributeVideo', 'round_message' => false, 'supports_streaming' => true]
                        ]
                    ],
                    'message' => 'mediaPreviews',
                    'reply_to_msg_id' => $comment['reply_to']['reply_to_msg_id']
                ]);
        }
    }

    protected function getComments($channel_id, $id, $replies, $message) {
        switch(true)
        {
            case $replies > 0:
                $comments = $this->MadelineProto->messages->getReplies(['peer' => -100 .$channel_id, 'msg_id'=> $id]);
                $this->sendMessage($message, $comments['messages']);
                break;
            default: $post_id = $this->MadelineProto->messages->getDiscussionMessage([
                'peer' => -100 . $channel_id, 'msg_id' => $id])['messages'][0]['id'];
                $this->MadelineProto->messages->sendMedia(
                    ['peer'=> -100 .env('GROUP_ID'),
                    'file'=> $this->getLink($message)[0],
                    'message'=> 'mediaPreviews',
                    'reply_to_msg_id'=> $post_id]);
                break;
        }
    }
}
