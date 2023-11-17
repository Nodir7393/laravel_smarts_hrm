<?php

namespace Modules\Export\Service;

use danog\MadelineProto\API;
use Illuminate\Support\Arr;
use Modules\ALL\Services\FileSystemService;

class ExportService
{
    public $mtproto;
    public FileSystemService $filesystem;


    /**
     * Fetching messages for specific time interval from $start to $end
     */
    public function getMessages(int $id, $start, $end): array
    {
        $messages = $this->mtproto->messages->getHistory(['peer' => $id, 'offset_date' => $end, 'limit' => 100]);
        $update = [];
        foreach ($messages['messages'] as $message) {
            if ($message['date'] > (int)$start) {
                $update[] = $message;
            }
        }
        return $update;
    }

    /**
     * Downloading files from telegram to specific folders as files, rounded_video_messages, photos, voice_messages
     */
    public function downloadMedia($messages, $path): void
    {
        foreach ($messages as $message) {
            $filePath = $path;
            if (Arr::exists($message, 'media')) {
                $type = $message['media']['_'];
                switch ($type) {
                    case 'messageMediaDocument':
                        $attributes = $message['media']['document']['attributes'];
                        foreach ($attributes as $attribute) {
                            $video = false;
                            switch ($attribute['_']) {
                                case 'documentAttributeVideo':
                                    if ($attribute['round_message']) {
                                        if (!is_dir($filePath . 'rounded_video_messages')) {
                                            mkdir($filePath . 'rounded_video_messages');
                                        }
                                        $this->mtproto->downloadToDir($message['media'], $path . 'rounded_video_messages/');
                                    } else {
                                        if (!is_dir($filePath . 'videos_files')) {
                                            mkdir($filePath . 'videos_files');
                                        }
                                        $this->mtproto->downloadToDir($message['media'], $path . 'videos_files/');
                                        $video = true;
                                    }
                                    break;
                                case 'documentAttributeAudio':
                                    if (!is_dir($filePath . 'voice_messages')) {
                                        mkdir($filePath . 'voice_messages');
                                    }
                                    $filePath = $path . 'voice_messages/';
                                    break;
                                case 'documentAttributeFilename':
                                    if (!is_dir($filePath . 'files')) {
                                        mkdir($filePath . 'files');
                                    }
                                    $this->mtproto->downloadToDir($message['media'], $path . 'files/');
                                    break;
                            }
                            if($video) return;
                        }

                        break;
                    case 'messageMediaPhoto':
                        if (!is_dir($filePath . 'photos')) {
                            mkdir($filePath . 'photos');
                        }
                        $filePath = $path . 'photos/';
                        $this->mtproto->downloadToDir($message['media'], $filePath);

                        break;
                    case 'messageMediaWebPage':
                        if (!is_dir($filePath . 'files')) {
                            mkdir($filePath . 'files');
                        }
                        $filePath = $path . 'files/';
                        $url = array_key_exists('url', $message['media']['webpage']) ? $message['media']['webpage']['url']: $message['message'];
                        $this->filesystem->createUrlFile($filePath, $url, $this->filesystem->generateUrlTitle($url));
                        continue 2;
                    default:
                        if (!is_dir($filePath . 'files')) {
                            mkdir($filePath . 'files');
                        }
                        $filePath = $path . 'files/';
                        $this->mtproto->downloadToDir($message['media'], $filePath);
                        break;
                }
            }
        }
    }

    /**
     * Creating folder structure
     * Eg.: {chat_id}/{YYY}/{MM}/{DD}/{HH}(if applicable)
     */
    public function folderPath($id, $path, $date): string
    {
        if (!str_ends_with($path, '/') || !str_ends_with($path, '\\')) {
            $path .= '/';
        }
        $chat = $this->mtproto->getPwrChat($id);
        $title = $chat['id'];
        //Title
        if (!is_dir($path . $title)) {
            mkdir($path . $title);
        }
        $path .= $title . '/';
        //Year
        if (!is_dir($path . $date['year'])) {
            mkdir($path . $date['year']);
        }
        $path .= $date['year'] . '/';
        //month
        if (!is_dir($path . $date['month'])) {
            mkdir($path . $date['month']);
        }
        $path .= $date['month'] . '/';
        //day
        if (!is_dir($path . $date['day'])) {
            mkdir($path . $date['day']);
        }
        $path .= $date['day'] . '/';
        //Hours
        if ($date['hour'] != "") {
            if (!is_dir($path . $date['hour'])) {
                mkdir($path . $date['hour']);
            }
            $path .= $date['hour'] . '/';
        }
        return $path;
    }

    /**
     * Generating JSON file similar to Telegram's one
     */
    public function formatJson($id, $messages): array
    {
        $update = [];
        $chat = $this->mtproto->getPwrChat($id);

        if ($chat['type'] == 'user') {
            $mess['name'] = $chat['first_name'];
        } else {
            $mess['name'] = $chat['title'];
        }

        $update['type'] = $chat['type'];
        $update['id'] = $chat['id'];
        $update['messages'] = [];

        for ($i = count($messages) - 1; $i > -1; $i--) {
            $message = $messages[$i];
            $mess = [];
            $mess['id'] = $message['id'];
            $mess['type'] = $message['_'];
            $mess['date'] = date("Y-n-j", $message['date']) . 'T' . date("H:i:s", $message['date']);
            $mess['date_unixtime'] = (string)$message['date'];
            if (Arr::exists($message, 'media')) {
                if (Arr::exists($message['media'], 'document')) {
                    foreach ($message['media']['document']['attributes'] as $attribute) {
                        if ($attribute['_'] == 'documentAttributeFilename') {
                            $mess['file'] = 'files/' . $attribute['file_name'];
                        }
                        if ($attribute['_'] == 'documentAttributeAudio') {
                            $mess['media_type'] = 'voice_message';
                        }
                    }
                    $mess['mime_type'] = $message['media']['document']['mime_type'];
                }
                if ($message['media']['_'] == 'messageMediaPhoto') {
                    $mess['photo'] = 'Photo';
                }
            }
            if (Arr::exists($message, 'fwd_from')) {
                if (Arr::exists($message['fwd_from'], 'from_id')) {
                    $mess['forwarded_from'] = $message['fwd_from']['from_id'][Arr::exists($message['fwd_from']['from_id'], 'user_id') ? 'user_id' : 'channel_id'];
                }
            }
            if (Arr::exists($message, 'edit_date')) {
                $mess['edited'] = date("Y-n-j", $message['edit_date']) . 'T' . date("H:i:s", $message['edit_date']);
                $mess['edited_unixtime'] = (string)$message['edit_date'];
            }
            if (Arr::exists($message, 'from_id')) {
                if (Arr::exists($message['from_id'], 'user_id')) {
                    $chat = $this->mtproto->getPwrChat($message['from_id']['user_id']);
                    $mess['from'] = $chat['first_name'];
                    $mess['from_id'] = $chat['type'] . $message['from_id']['user_id'];
                } else {
                    $chat = $this->mtproto->getPwrChat('-100' . $message['from_id']['channel_id']);
                    $mess['from'] = $chat['title'];
                    $mess['from_id'] = $chat['type'] . $message['from_id']['channel_id'];
                }
            } else {
                $chat = $this->mtproto->getPwrChat(Arr::exists($message['peer_id'], 'channel_id') ? '-100' . $message['peer_id']['channel_id'] : $message['peer_id']['user_id']);
                $mess['from'] = $chat[Arr::exists($chat, 'title') ? 'title' : 'first_name'];
                $mess['from_id'] = $chat['type'] . $message['peer_id'][Arr::exists($message['peer_id'], 'channel_id') ? 'channel_id' : 'user_id'];
            }
            if (Arr::exists($message, 'reply_to')) {
                $mess['reply_to_message_id'] = $message['reply_to']['reply_to_msg_id'];
            }
            $mess['text'] = Arr::exists($message, 'message') ? $message['message'] : '';
            $update['messages'][] = $mess;
        }
        return $update;
    }

    /**
     * Common
     */
    public function export($channel_id, $unix_start, $end, $date, $path): void
    {
        $path = $this->folderPath($channel_id, $path, $date);
        if (is_file($path . 'end.txt')) {
            return;
        }
        $update = $this->getMessages($channel_id, $unix_start, $end);
        file_put_contents($path . 'result.json', json_encode($update));
        $telegram = $this->formatJson($channel_id, $update);
        file_put_contents($path . 'telegram.json', json_encode($telegram));
        $this->downloadMedia($update, $path);
        fopen($path . "end.txt", "w");
    }

    public function command($channel_id, $date_start, $date_end, $output, $path)
    {

        $this->mtproto = new API(env('SESSION_PATH'));
        $this->filesystem = new FileSystemService();

        $unix_end = strtotime($date_end == "" ? "now" : $date_end);
        $unix_start = strtotime($date_start);
        $date = date_parse_from_format("j.n.Y H:iP", $date_start);
        $max = $date['hour'] == "" ? ($unix_end - $unix_start) / 86400 : ($unix_end - $unix_start) / 3600;
        $progressbar = $output->createProgressBar((int)$max);
        $progressbar->start();
        while ($unix_end > $unix_start) {
            if ($date['hour'] == false) {
                if ($unix_start + 86400 <= $unix_end) {
                    $date = date_parse_from_format("j.n.Y H", date("j.n.Y", $unix_start));
                    $end = $unix_start + 86400;
                    $this->export($channel_id, $unix_start, $end, $date, $path);
                    $unix_start += 86400;
                    $progressbar->advance(1);
                    if ($unix_end - $unix_start < 86400) return 0;

                }
            } else {
                if ($unix_start + 3600 <= $unix_end) {
                    $date = date_parse_from_format("j.n.Y H:i", gmdate("j.n.Y H:i", $unix_start));
                    $end = $unix_start + 3600;
                    $this->export($channel_id, $unix_start, $end, $date, $path);
                    $unix_start += 3600;
                    $progressbar->advance(1);
                    if ($unix_end - $unix_start < 3600) return 0;
                }
            }
        }
    }
}
