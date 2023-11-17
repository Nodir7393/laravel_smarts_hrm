<?php

namespace Modules\TaskStatus\Service;

use danog\MadelineProto\API;

class EditStatusService
{
    public $MadelineProto;

    public function __construct()
    {
        $this->MadelineProto = new API(env('SESSION_PATH'));
        $this->MadelineProto->start();
    }

    public $status;

    public function getPostId($start, $end)
    {
        $channel_id = -100 . env('STATUS_CHANNEL_ID');

        for ($i = $start; $i <= $end; $i++) {

            $item = $this->MadelineProto->channels->getMessages([
                "channel" => $channel_id,
                "id" => [$i]])['messages'];
            if (array_key_exists('message', $item[0]) && $item[0]['replies']['replies'] > 0) {
                $comments = $this->MadelineProto->channels->getMessages([
                    'channel' => -1001711427913,
                    'id' => [$item[0]['replies']['max_id']]]);

                $this->confirmStatus($comments['messages'][0], $item[0]['message'], $item[0]['id']);
            }
        }
    }

    protected $to;

    public function getHistory()
    {
        $posts = $this->MadelineProto->messages->getHistory(['peer' => -1001711427913, 'limit' => 10])['messages'];
        $this->to = $posts[0]['id'];
        foreach ($posts as $item) {
            if (array_key_exists('reply_to', $item) && $this->to <= $item['id']) {
                $this->to = $item['id'];
                $this->confirmStatus($item);
            }
        }
    }

    public function confirmStatus($comment, $message = NULL, $id = NULL)
    {
        $tag = true;
        foreach ($this->status as $key => $item) {
            if ($comment['message'] === $key &&
                (array_key_exists('from_id', $comment) &&
                    in_array((string)$comment['from_id']['user_id'], $item['id']))) {
                if (!$id) {
                    $post = $this->MadelineProto->channels->getMessages(['channel' => -1001711427913,
                        'id' => [$comment['reply_to']['reply_to_msg_id']]])['messages'][0];
                    if (array_key_exists('fwd_from', $post)) {
                        $this->addTags($post['message'], $post['fwd_from']['saved_from_msg_id'], '/\s#(\w+)/', $item['tag']);
                    }
                } else {
                    $this->addTags($message, $id, '/\s#(\w+)/', $item['tag']);
                }
            }
            $tag = false;
        }
        if ($tag) {
            $this->addTags($message, $id, '/\s#(\w+)/', '#InProcess');
        }
    }

    protected function addTags($message, $id, $pattern, $status)
    {
        $newMessage = preg_replace($pattern, '', $message);
        $newMessage .= '
' . $status;
        if ($newMessage !== $message) {
            $this->MadelineProto->messages->editMessage(
                ['peer' => -100 . env('STATUS_CHANNEL_ID'),
                    'id' => $id,
                    'message' => $newMessage]);
        }
    }
}
