<?php

namespace App\Services\MadelineProto\Database;

use App\Services\ALL\MTProtoService;
use Illuminate\Support\Arr;

class DownloadMediaService
{
    public $MTProto;

    public function __construct()
    {
        $this->MTProto = new MTProtoService();
    }

    /**
     * Creating folder structure for downloaded files
     * Eg.: {chat_id}/{message_id}/{file}
     */
    public function folderPath($post, $path)
    {
        $chat_id = !is_null($post->peer_id_user_id) ? $post->peer_id_user_id : $post->peer_id_channel_id;

        if (!is_dir($path . '/' . $chat_id)) {
            mkdir($path . '/' . $chat_id);
        }
        $path .= '/' . $chat_id . '/';

        if (!is_dir($path . '/' . $post->tg_id)) {
            mkdir($path . '/' . $post->tg_id);
        }
        $path .= $post->tg_id . '/';
        return $path;
    }

    /**
     * Downloading files to dir created above
     */
    public function downloadMedia($path, $post)
    {
        $message = $this->MTProto->MadelineProto->channels->getMessages(['channel' => '-100' . $post->peer_id_channel_id, 'id' => [$post->tg_id]]);
        if (array_key_exists('document', Arr::get($message['messages'], 0)['media'])) {
            $attributes = [];
            foreach (Arr::get($message['messages'], 0)['media']['document']['attributes'] as $attribute) {
                $attributes[] = $attribute['_'];
            }
            if (in_array('documentAttributeFilename', $attributes)) {
                $this->MTProto->MadelineProto->downloadToFile(Arr::get($message['messages'], 0)['media'], $path . Arr::get($message['messages'], 0)['media']['document']['attributes'][array_search('documentAttributeFilename', Arr::get($message['messages'], 0)['media']['document']['attributes'])]['file_name']);
                return $path . Arr::get($message['messages'], 0)['media']['document']['attributes'][array_search('documentAttributeFilename', Arr::get($message['messages'], 0)['media']['document']['attributes'])]['file_name'];
            } else {
                $mime = explode('/', Arr::get($message['messages'], 0)['media']['document']['mime_type']);
                $this->MTProto->MadelineProto->downloadToFile(Arr::get($message['messages'], 0)['media'], $path . Arr::get($message['messages'], 0)['media']['document']['id'] . '.' . $mime[1]);
                return $path . Arr::get($message['messages'], 0)['media']['document']['id'] . '.' . Arr::get($mime, 1);
            }

        }

    }
}
