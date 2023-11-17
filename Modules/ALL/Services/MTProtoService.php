<?php

namespace Modules\ALL\Services;

use danog\MadelineProto\API;
use danog\MadelineProto\Logger;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\Logger as LoggerSettings;
use Illuminate\Support\Arr;

class MTProtoService
{
    public API $madelineproto;
    public  $settings;

    /**
     * Creating a MadelineProto client
     */
    public function __construct()
    {
        $this->settings = new Settings;
        $this->settings =  (new LoggerSettings)
            ->setType(Logger::FILE_LOGGER)
            ->setExtra('custom.log')
            ->setMaxSize(50*1024*1024);
        $this->madelineproto = new API(env('SESSION_PATH'), $this->settings);
    }

    public function searchForPath($comments): array
    {
        $paths = [];
        foreach ($comments as $comment){
            if(str_contains($comment['message'], 'Path:')){
                $paths[] = $comment;
            }
        }
        return $paths;
    }

    public function searchMessage($channel, $searched)
    {

        $messages = [];
        $offset_id = 0;
        $limit = 100;

        do {
            $messages_Messages = $this->madelineproto->messages->getHistory(['peer' => $channel, 'offset_id' => $offset_id, 'offset_date' => 0, 'add_offset' => 0, 'limit' => $limit, 'max_id' => 0, 'min_id' => 0, 'hash' => 0]);
            if (count($messages_Messages['messages']) == 0) break;
            foreach ($messages_Messages['messages'] as $message) {
                if (array_key_exists('message', $message)) {
                    $messages["message"][] = $message["message"];
                    $messages["id"][] = $message["id"];
                }
            }
            $offset_id = end($messages_Messages['messages'])['id'];
            sleep(2);
        } while (true);
        foreach ($messages['message'] as $message => $key) {
            if ($key === $searched) {
                return $messages['id'][$message];
            }
        }
    }

    /**
     * Getting all comments for specific channel post by
     * @param $url
     */
    public function getComments($url)
    {
        $split = explode("/", $url);
        $messages = $this->madelineproto->messages->getReplies(['peer' => '-100' . $split[count($split) - 2], 'msg_id' => $split[count($split) - 1]]);
        return $messages['messages'];
    }

    /**
     * Getting all files from @param array $comments returned by function above
     */
    public function getFiles(array $comments): array
    {
        $files = [];
        if (count($comments) === 0) {
            return $files;
        }
        foreach ($comments as $message) {
            if (Arr::exists($message, 'media') && Arr::exists($message['media'], 'document')) {
                $type = $message['media']['_'];
                switch ($type) {
                    case 'messageMediaPhoto':
                        $files[json_encode($message['media'])] = 'photo_'. date('Y-m-d_H-i-s', $message['date']) . '.jpg';
                        break;
                    default:
                        foreach ($message['media']['document']['attributes'] as $item) {
                            if (str_contains($message['media']['document']['mime_type'], 'video')) {
                                if(array_key_exists('file_name', $item)){
                                    if (strlen($message['message']) > 0) {
                                        $pattern = "/[^a-zA-Z0-9 ]+/";
                                        $messageText = preg_replace($pattern, "", explode("\n", $message['message'])[0]);
                                        $fileExt = explode('.', $item['file_name'])[count(explode('.', $item['file_name'])) - 1];
                                        $fileName = $messageText . '.' . $fileExt;
                                    }
                                }
                            } else {
                                if ($item['_'] == 'documentAttributeFilename') {
                                    $fileName = $item['file_name'];
                                    $files[json_encode($message['media'])] = $fileName;
                                }
                            }
                        }
                }
            }
        }
        return $files;
    }

    public function getLinks($comments): array
    {
        $links = [];
        if (count($comments) === 0) {
            return $links = [];
        }
        foreach ($comments as $message) {
            if (array_key_exists('entities', $message)) {
                foreach ($message['entities'] as $entity) {
                    if ($entity['_'] === 'messageEntityUrl') {
                        $links[] = $message['message'];
                    }
                }
            }
        }
        return $links;
    }
}
