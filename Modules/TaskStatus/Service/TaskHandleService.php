<?php

namespace Modules\TaskStatus\Service;

use danog\MadelineProto\EventHandler;

class TaskHandleService extends EventHandler
{
    public function onUpdateNewMessage(array $update) {
        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            return;
        }
        $task = new TaskService();

        if($update['peer_id']['channel_id'] === 1732713545){
            $this->messages->sendMessage(['peer' =>$update, 'message' => 'You have sent a new message']);
            $task->onUpdateTask($update);
        }
    }

    public function onUpdateNewChannelMessage(array $update) {
        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            return;
        }
        $task = new TaskService();

        if($update['peer_id']['channel_id'] === 1807426588){
            $task->onUpdateTask($update);
        }
    }
}
