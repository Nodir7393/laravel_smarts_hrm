<?php

namespace Modules\ALL\Services;

use App\Models\Camera;
use SergiX44\Nutgram\Nutgram;


class NutgramService
{
    protected Nutgram $bot;
    protected FileSystemService $file_system;

    public function __construct()
    {
        $bot = new Nutgram(env('DROPPER_BOT_TOKEN'), ['timeout' => 60]);
        $this->bot = $bot;
        $this->file_system = new FileSystemService();
    }

    public function getCameraList()
    {
        $cameras = Camera::all();
        return $cameras;
    }

    public function getOfficeCameras($id)
    {
        $cameras = Camera::where('office_id', $id)->get();
        return $cameras;
    }

    public function getMessageId($text)
    {
        $updates = $this->bot->getUpdates();
        foreach ($updates as $update) {
            if ($update->message) {
                $test = $update->message;
                if ($test->text == $text) {
                    return $test->message_id;
                }
            }
        }
        sleep(3);
    }

    public function sendFile($camera, $video, $message_id, $caption, $folder): void
    {
        $this->bot->sendDocument([$video, $video], ['chat_id' => env('GROUP_ID'), 'reply_to_message_id' => $message_id, 'caption' => $caption]);
        $target = Camera::where('title', $camera->title);
        $target->update(['folder' => $folder, 'video' => $caption]);
        sleep(3);
    }

    public function sendDocument($file, $chat_id, $reply_to = null): void
    {
        $f = fopen($file, 'r+');
        $this->bot->sendDocument($f, ['chat_id' => $chat_id, 'reply_to_message_id' => $reply_to,
            'caption' => '#post_file']);
    }

    public function getActualData($camera): void
    {

        $camera_path = $this->file_system->path .'\\'. $camera->title;

        $camera_folder = scandir($camera_path);

        for ($i = array_search($camera->folder, $camera_folder); $i < count($camera_folder) - 2; $i++) {

            $current_dir = scandir($camera_path . '/' . $camera_folder[$i]);
            $q = (is_numeric(array_search($camera->video, $current_dir))) ? array_search($camera->video, $current_dir) : 1;
            for ($o = $q + 1; $o <= count($current_dir) - 1; $o++) {
                $path = $camera_path . '/' . $camera_folder[$i];
                $video = fopen($path . '/' . $current_dir[$o], 'r+');
                $text = "#" . $camera->name . "\n#" . $camera->title . "\n#D" . $camera_folder[$i];
                $message_id = $this->getMessageId($text);
                if ($message_id == null) {
                    $this->bot->sendMessage($text, ['chat_id' => env('CHANNEL_ID')]);
                    sleep(3);
                    $message_id = $this->getMessageId($text);
                }
                $this->sendFile($camera, $video, $message_id, $current_dir[$o], $camera_folder[$i]);
            }
        }
    }

}
