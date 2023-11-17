<?php

namespace Modules\Import\Service;

use Modules\ALL\Models\TgChannelText;
use Modules\ALL\Models\TgGroupText;
use Modules\ALL\Services\LoginService;

class ImportService
{
    public LoginService $madelineproto;

    public function __construct()
    {
        $this->madelineproto = new LoginService();
    }

    function getChannelMessages($channel_id, $output): void
    {
        $messages = TgChannelText::where('type', 'message')->get();
        foreach ($messages as $message) {
            $update = $this->madelineproto->madelineproto->messages->sendMessage(['peer' => $channel_id, 'message' => $message->message]);
            sleep(5);
            $discuss = $this->madelineproto->madelineproto->messages->getDiscussionMessage(['peer' => '-100' . $update['chats'][0]['id'], 'msg_id' => $update['updates'][0]['id']]);
            $comments = TgGroupText::where('tg_channel_texts_id', $message->id)->where('message', '!=', null)->get();
            $progressBar = $output->createProgressBar(count($comments));
            foreach ($comments as $comment) {
                $this->madelineproto->madelineproto->messages->sendMessage(['peer' => $discuss['messages'][0]['peer_id'], 'message' => $comment->message, 'reply_to_msg_id'=>$discuss['messages'][0]['id']]);
                usleep(500000);
                $progressBar->advance();
            }
            $progressBar->finish('Completed!');
            $output->newLine();
        }
    }
}
