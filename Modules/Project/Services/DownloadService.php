<?php

namespace Modules\Project\Services;
use danog\MadelineProto\API;
use Illuminate\Support\Arr;
use Modules\ALL\Models\TgChannelText;
use Modules\ALL\Models\TgGroupText;

class DownloadService
{
    const PHOTO = 'messageMediaPhoto';
    const DOCUMENT = 'messageMediaDocument';
    const TG_ID = 'tg_id';

    public $mtp;

    private $path;

    public function __construct()
    {
        $this->mtp = new API(env('SESSION_PATH'));
        $this->path = env('DOWNLOAD_PATH');
    }

    public function collector(?int $channelId, ?string $start, ?string $end)
    {
        $channels = $this->getPeerId($channelId);
        foreach ($channels as $id => $type) {$this->index((int)$id, $start, $end, $type);}
    }

    public function getPeerId($channelId)
    {
            $chat = $this->mtp->getInfo($channelId);
            $type = (!empty($chat)) ? $chat['type'] : null;
            $id = Arr::get($chat, "$type".'_id') ?? Arr::get($chat, 'channel_id');

            switch (true) {
                case $type === 'supergroup':$type = 'supers';break;
                case $type === 'chat':$type = 'groups';break;
                case $type === 'user':$type = 'users';break;
                case $type === 'bot':$type = 'botapp';break;
            }

            return [$id => $type];
    }

    public function index(int $id, string $start, string $end, string $type)
    {
        $time_start = $start ?? '';
        $time_end = ($end === '') ? date("Y-m-d") : $end;
        $messages = $this->getMessages($id, $time_start, $time_end, $type);
        $path = $this->path . "\\$type\\$id";
        is_dir($path) || mkdir($path, 0777, true);

        foreach ($messages as $message) {$this->download($message, $path);}
    }

    public function getMessages(int $id, string $start, string $end, string $type)
    {
        $bot = $id;
        switch (true) {
            case ($type === 'channel'):$model = TgChannelText::class;$bot='-100'.$id;break;
            case ($type === 'supers'):$model = TgGroupText::class;$bot='-100'.$id;break;
            case ($type === 'groups'):$model = TgGroupText::class;$bot='-'.$id;break;
            case ($type === 'botapp'):$model = TgChannelText::class;break;
            case ($type === 'users'):$model = TgChannelText::class;break;
        }

        $msgs = $model::whereBetween('date', [$start, $end])
            ->whereNull('reply_to_reply_to_msg_id')
            ->whereIn('media__', [self::DOCUMENT, self::PHOTO])
            ->where('peer_id_channel_id', $id)->pluck(self::TG_ID);

        $msgs = collect($msgs)->all();
        if (empty($msgs)) {return [];};
        $response = $this->mtp->channels->getMessages(["channel" => $bot, "id" => $msgs]);
        return collect($response['messages'])->where('_', '!=', 'messageEmpty')->all();
    }

    public function download(array $message, string $path)
    {
        $media = Arr::get($message, 'media');
        $atributes = Arr::get($media, 'document.attributes');
        $downloadable = true;

        foreach ($atributes as $atribute) {
            if (array_key_exists('file_name', $atribute)) {$file = $atribute['file_name'];}
            if($atribute['_'] === 'documentAttributeSticker') {$downloadable = false;}
        }

        if ($downloadable) {
            $path = "$path\\" . $message['id'];
            is_dir($path) || mkdir($path, 0777, true);

            if (!is_file("$path\\" . $file)) {
                $this->mtp->downloadToFile($media, "$path\\" . $file);
            }
        }
    }
}





/*
 *  if ($channelId) {
            $chat = $this->mtp->getInfo($channelId);
            $type = (!empty($chat)) ? $chat['type'] : null;
            $id = Arr::get($chat, 'channel_id') ?? Arr::get($chat, "$type".'_id');

            switch (true) {
                case $type === 'supergroup':$type = 'supers';break;
                case $type === 'chat':$type = 'groups';break;
                case $type === 'user':$type = 'users';break;
                case $type === 'bot':$type = 'botapp';break;
            }

            return [$id => $type];
        } else {

            $dialogs = $this->mtp->getDialogs();
            $dialogs = collect($dialogs)->groupBy('_');
            $chats = [];

            foreach ($dialogs->get('peerChannel') as $dialog) {
                $chats[] = $dialog['channel_id'];
            }

            $channels = TgChannel::whereIn(self::TG_ID, $chats)->pluck(self::TG_ID);
            $groups = TgGroup::whereIn(self::TG_ID, $chats)->pluck(self::TG_ID);
            $items = [];

            foreach ($channels as $channel) {
                $items[$channel] = 'channel';
            }

            foreach ($groups as $group) {
                $items[$group] = 'groups';
            }

            return $items;
        }
 */
