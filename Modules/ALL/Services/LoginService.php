<?php

namespace Modules\ALL\Services;

use danog\MadelineProto\API;
use danog\MadelineProto\Logger;
use danog\MadelineProto\Settings\Logger as LoggerSettings;
use danog\MadelineProto\Settings;
use Illuminate\Support\Arr;

class LoginService
{
    public API $madelineproto;
    public $settings;

    public function __construct()
    {
        $this->madelineproto = new API(env('SESSION_PATH'));
        $this->madelineproto->start();
        //$this->settings = new Settings;
        /*$this->settings = (new LoggerSettings)
            ->setType(Logger::FILE_LOGGER)
            ->setExtra('custom.log')
            ->setMaxSize(50 * 1024 * 1024);*/
        // $this->madelineproto = new API(env('SESSION_PATH'), $this->settings);

        //$this->madelineproto = new API(env('SESSION_PATH'));
    }

    public function login(): void
    {
        //$this->madelineproto->start();
    }

    public function searchForPath($comments): array
    {
        $paths = [];
        foreach ($comments as $comment) {
            if (str_contains($comment['message'], 'Path:')) {
                $paths[] = $comment;
            }
        }
        return $paths;
    }

    public function getComments($url)
    {
        $split = explode("/", $url);
        $messages = $this->madelineproto->messages->getReplies(['peer' => '-100' . $split[count($split) - 2], 'msg_id' => $split[count($split) - 1]]);
        return $messages['messages'];
    }

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
                        $files[json_encode($message['media'])] = 'photo_' . date('Y-m-d_H-i-s', $message['date']) . '.jpg';
                        break;
                    default:
                        foreach ($message['media']['document']['attributes'] as $item) {
                            if (str_contains($message['media']['document']['mime_type'], 'video')) {
                                if (array_key_exists('file_name', $item)) {
                                    if (strlen($message['message']) > 0) {
                                        $pattern = "/[^a-zA-Z0-9 ]+/";
                                        $messageText = preg_replace($pattern, "", explode("\n", $message['message'])[0]);
                                        $fileExt = explode('.', $item['file_name'])[count(explode('.', $item['file_name'])) - 1];
                                        $fileName = $messageText . '.' . $fileExt;
                                        $files[json_encode($message['media'])] = $fileName;
                                    }
                                }
                            } else {
                                if ($item['_'] === 'documentAttributeFilename') {
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
            if (array_key_exists('entities', $message) && !array_key_exists('media', $message)) {
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

