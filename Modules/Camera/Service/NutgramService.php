<?php

namespace Modules\Camera\Service;

use Modules\ALL\Services\FileSystemService;
use Illuminate\Database\Eloquent\Collection;
use Modules\Camera\Models\Camera;
use SergiX44\Nutgram\Nutgram;
use Symfony\Component\Finder\Finder;

class NutgramService
{
    public Nutgram $bot;
    public FileSystemService $file_system;
    public $path;


    public
    function __construct()
    {
        $bot = new Nutgram(env('TELEGRAM_TOKEN'), ['timeout' => 60]);
        $this->bot = $bot;
        $this->file_system = new FileSystemService();
        $this->path = $this->file_system->path;
    }

    public
    function getCameraList(): Collection
    {
        return Camera::all();
    }

    public
    function getOfficeCameras($id)
    {
        return Camera::where('office_id', $id)->get();
    }

    public
    function getMessageId($text)
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

    public
    function sendFile($camera, $video, $message_id, $caption, $folder): void
    {
        $this->bot->sendDocument($video, ['chat_id' => env('GROUP_ID'), 'reply_to_message_id' => $message_id, 'caption' => $caption]);
        $camera = Camera::where('title', $camera->title);
        $camera->update(['folder' => $folder, 'video' => $caption]);
        sleep(3);
    }

    public
    function getActualData($camera, $output): void
    {

        $camera_path = $this->file_system->path . '\\'.$camera->title;
        if (!file_exists($camera_path))
            return;

        $camera_folder = scandir($camera_path);
        if ($camera_folder[0] ==='.') unset($camera_folder[0]);
        if ($camera_folder[1] ==='.') unset($camera_folder[1]);
        $i = array_search($camera->folder, $camera_folder);

        if ($i === false) $i = 2;

        for ($i, $iMax = count($camera_folder); $i < $iMax; $i++) {

              $scanFolder = $this->file_system->path . '\\'.$camera->title . '/' . $camera_folder[$i];
            $current_dir = scandir($scanFolder);
            if ($current_dir[0] ==='.') unset($current_dir[0]);
            if ($current_dir[1] ==='.') unset($current_dir[1]);

            $q = (is_numeric(array_search($camera->video, $current_dir))) ? array_search($camera->video, $current_dir) : 2;
            $output->newLine();
            $text = "#" . $camera->name . "\n#" . $camera->title . "\n#D" . $camera_folder[$i];
            $message_id = $this->getMessageId($text);
            sleep(5);
            if ($message_id == null) {
                $this->bot->sendMessage($text, ['chat_id' => env('CHANNEL_ID')]);
                sleep(5);
                $message_id = $this->getMessageId($text);
            }
            $progressbar = $output->createProgressBar(count($current_dir)-$q);
            $progressbar->start();
            for ($o = $q, $oMax = count($current_dir); $o < $oMax; $o++) {
                $path = $this->file_system->path . '\\'.$camera->title . '/' . $camera_folder[$i];
                $video = fopen($path . '/' . $current_dir[$o], 'r+');
                $this->sendFile($camera, $video, $message_id, $current_dir[$o], $camera_folder[$i]);
                $this->softDeletes($camera_folder[$i], $current_dir[$o], $camera->title);
                $progressbar->advance(1);
            }
            $progressbar->finish();
            $output->newline();
        }
    }

    public
    function sendDocument($file, $chat_id, $reply_to = null): void
    {
        $f = fopen($file, 'r+');
        $this->bot->sendDocument($f, ['chat_id' => $chat_id, 'reply_to_message_id' => $reply_to,
            'caption' => '#post_file']);
    }

    public function softDeletes($data, $file, $camera) {
        $softdelete = $this->path . '/SoftDeleted/';
        if (!is_dir($softdelete)) {
            mkdir($softdelete);
        }
        if (!is_dir($softdelete . $camera )) {
            mkdir($softdelete . $camera );
        }
        if (!is_dir($softdelete . $camera. '/' . $data)) {
            mkdir($softdelete . $camera. '/' . $data);
        }
        rename($this->path . '/'.$camera . '/' . $data . '/' . $file, $softdelete . $camera . '/' . $data . '/' . $file);
    }
}

