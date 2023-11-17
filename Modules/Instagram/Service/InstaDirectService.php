<?php

namespace Modules\Instagram\Service;

use Modules\Instagram\Entities\InstaChat;
use Modules\Instagram\Entities\InstaMessage;

class InstaDirectService
{
    public InstaService $instagram;

    public function __construct()
    {
        $this->instagram = new InstaService();
    }

    public function get(): void
    {
        $threads = $this->instagram->instagram->getThreads(100);
        foreach ($threads as $thread) {
            $chat = $this->instagram->validate($thread);
            $caht = InstaChat::firstOrCreate(['insta_id' => $chat['insta_id']], ['insta_id' => $chat['insta_id']]);
            $caht->update($chat);
            foreach ((array)$thread['items'] as $item) {
                $message = $this->instagram->validate($item);
                $message['insta_chat_id'] = $thread->getId();
                $mess = InstaMessage::firstOrCreate(['insta_id' => $message['insta_id']], ['insta_id' => $message['insta_id']]);
                $mess->update($message);
            }
        }

    }

    public function sendMessage($username, $message): void{
    }
}
