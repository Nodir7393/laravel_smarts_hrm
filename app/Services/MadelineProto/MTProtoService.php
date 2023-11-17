<?php

namespace App\Services\MadelineProto;

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use Exception;

class MTProtoService
{
    public $MadelineProto;
    public $settings;

    /**
     * Creating a MadelineProto client
     */
    public function __construct()
    {
        $this->settings = new Settings;
        $this->MadelineProto = new API(env('SESSION_PATH'), $this->settings);
        $this->MadelineProto->start();
    }

    /**
     * Getting all comments for specific channel post by @param $url
     */
    public function getComments($url)
    {
        $split = explode("/", $url);
        $messages = $this->MadelineProto->messages->getReplies(['peer' => '-100' . $split[count($split) - 2], 'msg_id' => $split[count($split) - 1]]);
        return $messages['messages'];
    }

    /**
     * Getting all files from @param array $comments returned by function above
     */
    public function getFiles($comments)
    {
        $files = [];
        if(count($comments) === 0){
            return $files = [];
        }
        foreach ($comments as $message) {
            if (array_key_exists('media', $message)) {
                if(array_key_exists('document', $message['media'])){
                    foreach ($message['media']['document']['attributes'] as $item) {
                        if ($item['_'] == 'documentAttributeFilename') {
                            $files[json_encode($message['media'])] = $item['file_name'];
                        }
                    }
                }
            }
        }
        return $files;
    }
}
