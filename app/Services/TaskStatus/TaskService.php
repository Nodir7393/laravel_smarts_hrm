<?php

namespace App\Services\TaskStatus;

use App\Services\MadelineProto\MTProtoService;
use App\Services\SearchService;

class TaskService
{
    public $MTProto;
    public $search;
    public $statuses;

    public function __construct()
    {
        $this->statuses = [
            '#ActiveTask',
            '#ActiveBug',
            '#Rejected',
            '#InProcess',
            '#NeedTests',
            '#NeedTests',
            '#Completed',
            '#Accepted'
        ];
        $this->MTProto = new MTProtoService();
        $this->search = new SearchService();
    }

    public function tasksGet($channel_id)
    {
        $updates = $this->MTProto->MadelineProto->messages->getMessages(["peer" => $channel_id, 'limit' => 100])['messages'];
        $tasks = [];
        foreach ($updates as $update) {
            if (str_contains($update['message'], '#ActiveTask')) {
                $tasks[] = $update;
            }
            return $tasks;
        }
    }

    public function createLink($update)
    {
        $message = $this->MTProto->MadelineProto->channels->getMessages(['channel'=>'-100' . $update['peer_id']['channel_id'], 'id'=>[$update['id']]])['messages'][0];

        return 'https://t.me/c/' . $message['from_id'][array_search('peerUser', $message['from_id']) ? 'user_id' : 'channel_id'] . '/' . $message['fwd_from']['channel_post'];
    }

    public function deleteTask($channel_id, $link)
    {
        $updates = $this->MTProto->MadelineProto->messages->getHistory(['peer' => $channel_id, 'limit' => 100])['messages'];
        foreach ($updates as $update) {
            $link = 'https://t.me/c/1807426588/720';
            if (array_key_exists('message', $update) && str_contains($update['message'], $link)) {
                $this->MTProto->MadelineProto->messages->editMessage(['peer' => $channel_id, 'id' => $update['id'], 'message' => str_replace($link, '', $update['message'])]);
                return;
            }
        }
    }

    public function addTask($channel_id, $link, $status)
    {
        $updates = $this->MTProto->MadelineProto->messages->getMessages(["peer" => $channel_id, 'limit' => 100])['messages'];
        print_r('Adding task');
        foreach ($updates as $update) {
            if (str_contains($update['message'], substr($status, 1))) {
                $this->MTProto->MadelineProto->messages->editMessage(['peer' => $channel_id, 'id' => $update['id'], 'message' => $update['message'] . "\r\n" . $link]);
            }
        }
    }

    public function onUpdateTask ($update) {

        $link = $this->createLink($update);
        $this->deleteTask(-1001807426588, $link);
        $this->addTask(-1001807426588, $link, $update['message']);
    }

}
