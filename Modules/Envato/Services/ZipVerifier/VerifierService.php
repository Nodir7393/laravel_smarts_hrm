<?php

namespace Modules\Envato\Services\ZipVerifier;

use Modules\Envato\Services\EnvatoService;

class VerifierService
{
    use EnvatoService;

    /**
     *
     * Function  getComments
     * @param $channel_id
     * @param $id
     * @param $replies
     * @param $url
     * @param $message
     */
    protected function getComments($channel_id, $id, $replies, $url, $message): void
    {
        switch (true) {
            case $replies > 0:
                $comments = $this->MadelineProto->messages->getReplies(['peer' => -100 . $channel_id, 'msg_id' => $id]);
                $this->sortMessage($message, $comments['messages'], $id);
                break;
            default:
                $this->addTags($message, $id);
                break;
        }
    }

    /**
     *
     * Function  sortMessage
     * @param $message
     * @param $comments
     * @param $id
     */
    public function sortMessage($message, $comments, $id): void
    {
        $a = true;
        foreach ($comments as $comment)
        {
            if (array_key_exists('media', $comment)
                && array_key_exists('document', $comment['media'])
            ) {
                $a = false;
            }
        }
        if ($a) {
            $this->addTags($message, $id);
        } else {
            $this->removeTags($message, $id);
        }
    }

    /**
     *
     * Function  addTags
     * @param $message
     * @param $id
     */
    protected function addTags($message, $id): void
    {
        if (str_contains(strtolower($message), '#new')) {
            return;
        }

        $newMessage = $message . "\n#New";
        $this->MadelineProto->messages->editMessage(
            [
                'peer' => -100 . env('CHANNEL_ID'),
                'id' => $id,
                'message' => $newMessage
            ]
        );
        usleep(400000);
    }

    /**
     *
     * Function  removeTags
     * @param $message
     * @param $id
     */
    protected function removeTags($message, $id): void
    {
        if (!str_contains(strtolower($message), '#new')) {
            return;
        }

        $newMessage = str_replace('#New', '', $message);
        $this->MadelineProto->messages->editMessage(
            [
                'peer' => -100 . env('CHANNEL_ID'),
                'id' => $id,
                'message' => $newMessage
            ]
        );
        usleep(400000);
    }
}
