<?php

namespace Modules\TaskFinder\Services;



use Modules\Common\Services\LoginService;

class TaskFinderService
{
    public $madelineproto;
    public $task;

    public function __construct()
    {
        $this->madelineproto = new LoginService();
        $this->task = ['ActiveTask', 'WebApp', 'BookForMe', 'Modules'];
    }

    public function lastMsgId($channel)
    {
        return $this->madelineproto->madelineproto->messages->getHistory(['peer' => $channel, 'limit' => 1])['messages'][0]['id'];
    }

    public function findTasks($channel, $output)
    {
        $result = [];
        $lastMsg = $this->lastMsgId($channel);
        $progressBar = $output->createProgressBar($lastMsg);
        while ($lastMsg > 1) {
            $messages = $lastMsg == 1 ? $lastMsg = 0 : $this->madelineproto->madelineproto->messages->getHistory(['peer' => $channel, 'limit' => 100, 'offset_id' => $lastMsg + 1])['messages'];
            foreach ($messages as $message) {
                if ($message['_'] === "message") {
                    $msgword = explode(' ', $message['message']);
                    $id = $message['id'];
                    foreach ($this->task as $hashtag) {
                        if (!array_key_exists($hashtag, $result)) {
                            $result[$hashtag] = "";
                        }
                        if (str_contains($message['message'], "#" . $hashtag)) {
                            $tag = '';
                            foreach ($msgword as $word) {
                                if (str_starts_with($word, '@')) {
                                    $tag .= " " . $word;
                                }
                            }
                            $link = $this->madelineproto->madelineproto->channels->exportMessageLink(['channel' => $channel, 'id' => $id])['link'];
                            $result[$hashtag] .= $link . $tag . "\n";
                        }
                    }
                }
                $lastMsg = $message['id'];
                $progressBar->advance();
            }
        }
        $progressBar->finish();
        return $result;
    }

    public function editStatus($channel, $tasks, $output)
    {
        $messages = $this->madelineproto->madelineproto->messages->getHistory(['peer' => $channel, 'limit' => 100])['messages'];
        $progressBar = $output->createProgressBar(count($messages));
        foreach ($messages as $message) {
            foreach ($tasks as $key => $task) {
                if($message['_'] === "message"){
                    if (str_contains($message['message'], $key)) {
                        $this->madelineproto->madelineproto->messages->editMessage(['peer' => $channel, 'id'=>$message['id'], 'message' => $key . "\r\n\r\n" . $task]);
                    }
                    $progressBar->advance();
                }

            }
        }
        $progressBar->finish();
    }
}
