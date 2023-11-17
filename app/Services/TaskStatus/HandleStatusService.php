<?php


namespace App\Services\TaskStatus;


use App\Services\ALL\MTProtoService;
use App\Services\SearchService;


class HandleStatusService
{

    public $Madeline;

    public $Search;

    public function onUpdateNewMessage(array $update)
    {
        $this->Madeline = new MTProtoService();
        $this->Search = new SearchService();
        $g_history = $this->Madeline->MadelineProto->messages->getHistory(["peer" => -1001851760117]);
        foreach ($g_history['messages'] as $message) {
            if (array_key_exists('reply_to', $message)) {
                $g_post = $this->Madeline->MadelineProto->channels->getMessages(["channel" => -1001851760117, "id" => [$message["reply_to"]["reply_to_msg_id"]]]);
                $g_post_msg = $g_post["messages"][0]["message"];
                if (strpos($g_post_msg, "#Task")) {
                    $ch_post_id = $this->Search->searchMessage(-1001715385949, $g_post_msg);
                    $g_post_msg;  /* gruppadig kanal posti #task borligi */
                    $message['message'];  /* uni commenti */
                    if (array_key_exists("from_id", $g_history['messages'][0])) {
//                        dump($g_history['messages'][0]['message']);
//                        dump($message["from_id"]["user_id"]);
                        switch (true) {
                            case ($g_history['messages'][0]['message'] == '#Redy' && $g_history['messages'][0]["from_id"]["user_id"] == 5466804391 && !strpos($g_post_msg, "#NeedTests")):
                                $ch_post_id; /* kanal post id */
                                $ch_post = $this->Madeline->MadelineProto->channels->getMessages(["channel" => -1001715385949, "id" => [$ch_post_id]]);
                                $ch_post;
                                $editted = $g_post_msg . "\n#NeedTests";
                                $this->Madeline->MadelineProto->messages->editMessage(["peer" => -1001715385949, "id" => $ch_post_id, "message" => $editted]);
                                break;
                        }
                    }
                }
            }
        }
    }

    public function __construct($API)
    {
        $this->Madeline = new MTProtoService();
        $this->Search = new SearchService();
    }

}


