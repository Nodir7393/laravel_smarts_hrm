<?php

namespace Modules\Sync\Services;

use danog\MadelineProto\API;
use Illuminate\Support\Facades\Log;
use Modules\ALL\Services\FileSystemService;
use Modules\ALL\Services\LoginService;

class SyncService
{
    public $mtproto;
    public FileSystemService $fileSystem;
    protected int $messageId;
    protected int $channelId;
    protected string $path;

    /**
     * Calling all required Services
     */
    public function __construct()
    {
        $this->mtproto = new LoginService();
        $this->fileSystem = new FileSystemService();
    }

    /**
     * Uploading @param array $files to Telegram
     */
    public function filesUpload(array $files, $progress): void
    {
        foreach ($files as $file) {
            $filePath = $this->path . '/' . $file;
            $caption = $this->fileSystem->caption($filePath);
            $this->mtproto->madelineproto->messages->sendMedia([
                "peer" => $this->channelId,
                "reply_to_msg_id" =>$this->messageId,
                "media" => [
                    '_' => 'inputMediaUploadedDocument',
                    'file' => $filePath,
                    'attributes' => [
                            [
                                '_' => 'documentAttributeFilename', 'file_name' => $file
                            ]
                        ]],
                "message" => $caption]);
            $progress->advance();
        }
    }

    /**
     * Searching and updating PATHs in comments (if needed)
     */
    public function validatePath($comments, $realPath): void
    {
        $paths = $this->mtproto->searchForPath($comments);
        foreach ($paths as $key => $path) {
            $peer = '-100' . $path['peer_id']['channel_id'];
            if (!str_contains($path['message'], $realPath)) {
                $this->mtproto->madelineproto->channels->deleteMessages([
                    'channel' => $peer,
                    'id' => [$path['id']]
                ]);
                unset($paths[$key]);
            }
        }
        if (count($paths) === 0) {
            $this->mtproto->madelineproto->messages->sendMessage([
                "peer" => $this->channelId,
                "reply_to_msg_id" => $this->messageId,
                'message' => 'Path: ' . $this->path
            ]);
        }
    }

    /**
     * Getting Discussion message to reply to
     */
    public function getDiscussionMessage($url): void
    {
        $split = explode("/", $url);
        $message = $this->mtproto->madelineproto->messages->getDiscussionMessage(['peer' => '-100' . $split[count($split) - 2], 'msg_id' => $split[count($split) - 1]])['messages'];
        $this->channelId = '-100' . $message[0]['peer_id']['channel_id'];
        $this->messageId = $message[0]['id'];
    }

    /**
     * Downloading unprocessed files from Telegram
     */
    public function downloadFiles($files, $progressBar): void
    {
        foreach ($files as $key => $item) {
            $media = json_decode($key, true);
            $this->mtproto->madelineproto->downloadToFile($media, $this->path . '/' . $item);
            $progressBar->advance();
        }
    }

    /**
     * Common
     */
    public function sync($path,$output, $url=null): void
    {
        switch (true) {
            case is_null($url):
                $url_file = $this->fileSystem->searchForUrl($path);
                if ($url_file == null) {
                    Log::warning('URL file not found');
                    return;
                }
                $url = $this->fileSystem->readUrl($url_file);
                break;
            case is_null($path):
                $comments = $this->mtproto->getComments($url);
                $paths = $this->mtproto->searchForPath($comments);
                if (count($paths) != 0) {
                    $path = str_replace('Path: ', '', $paths[0]['message']);
                }
        }
        $this->path = $path;
        $comments = $this->mtproto->getComments($url);
        $tg_files = $this->mtproto->getFiles($comments);
        $storage_files = $this->fileSystem->getFiles($path);

        $fileNames = $this->fileSystem->getFileNames(   $storage_files);
        $links = $this->mtproto->getLinks($comments);
        $to_telegram = $this->fileSystem->compare($storage_files, $tg_files, true);
        $to_storage = $this->fileSystem->compare($storage_files, $tg_files, false);
        foreach ($links as $link) {
            $title = $this->fileSystem->generateUrlTitle($link);
            $this->fileSystem->createUrlFile($path, $link, $title);
        }
        $this->getDiscussionMessage($url);

        $progressFiles = $output->createProgressBar(count($to_telegram));
        $this->filesUpload($to_telegram, $progressFiles);
        $progressFiles->finish('Completed!');
        $output->newLine();

        $progressStorage = $output->createProgressBar(count($to_storage));
        $this->downloadFiles($to_storage, $progressStorage);
        $progressStorage->finish('Completed!');
        $output->newLine();
        $output->newLine();
        $this->validatePath($comments, $this->path);
    }

    /**
     * Console command
     */
    public function synchronize($path, $output): void
    {

        if (!is_null($this->fileSystem->searchForTxt($path))) {
            $txt_file = $this->fileSystem->searchForTxt($path);
        } else {
            $output->error('TXT file not found');
            $output->error('Shutting down.');
            return;
        }
        $txt_data = explode("\r\n", file_get_contents($txt_file, true));
        /**
         * Verifying ALL.txt data
         */
        if (count(explode(' | ', $txt_data[0])) > 1 && (int)$txt_data[1] != 0) {
            $this->fileSystem->sync($path, $txt_data, $output);
            $dirs = $this->fileSystem->getDirectories($path);
            foreach ($dirs as $dir) {
                $this->sync($dir->getRealPath(), $output);
            }
        } else {
            $output->error('TXT file type is not supported!');
            $output->error('Shutting down.');
        }
    }
}
