<?php

namespace Modules\TaskStatus\Service;

use danog\MadelineProto\EventHandler;
use Modules\Common\Entities\TgChannelText;

class TaskListStatusService extends EventHandler
{
    /**
     * @var int|string Username or ID of bot admin
     */
    const ADMIN = "firefox109"; // Change this

    /**
     * List of properties automatically stored in database (MySQL, Postgres, redis or memory).
     * @see https://docs.madelineproto.xyz/docs/DATABASE.html
     * @var array
     */

    protected static array $dbProperties = [
        'dataStoredOnDb' => 'array'
    ];

    /**
     * @var DbArray<array>
     */
    protected $dataStoredOnDb;

    /**
     * Get peer(s) where to report errors
     *
     * @return int|string|array
     */
    public function getReportPeers()
    {
        return [self::ADMIN];
    }

    /**
     * Called on startup, can contain async calls for initialization of the bot
     */
    public function onStart()
    {
    }

    /**
     * Handle updates from supergroups and channels
     *
     * @param array $update Update
     */



    public function onUpdateEditChannelMessage(array $update): \Generator
    {
        $a = $update['message'];
        TgChannelText::where('fwd_from_saved_from_msg_id', '=', $a['id'])->
        where('peer_id_channel_id', '=', $a['replies']['channel_id'])->update(['message' => $a['message'],]);
        return $this->onUpdateNewMessage($update);
    }

    /**
     *
     * Function  onUpdateNewChannelMessage
     * @param array $update
     * @return  \Generator
     */
    public function onUpdateNewChannelMessage(array $update): \Generator
    {
        return $this->onUpdateNewMessage($update);
    }

    public static $status;
    public static $pm;

    /**
     * Handle updates from users.
     *
     * @param array $update Update
     *
     * @return \Generator
     */
    public function onUpdateNewMessage(array $update): \Generator
    {
        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            return;
        }
        $tgChats = TgChat::pluck('tg_id')->all();
        $item = $update['message'];
        if (in_array($item['peer_id']['channel_id'], $tgChats) &&
            array_key_exists('reply_to', $item) &&
            array_key_exists('reply_to_msg_id', $item['reply_to'])
        ) {
            $post = collect(TgChannelText::where('peer_id_channel_id', '=', $item['peer_id']['channel_id'])->
            where('tg_id', '=', $item['reply_to']['reply_to_msg_id'])->first())->all();

            $this->confirmStatus($item, $post['message'], $post['fwd_from_saved_from_msg_id'], $post['id']);
        }
        yield;
    }

    protected function confirmStatus($comment, $message, $tg_id, $id)
    {
        $tag = true;
        foreach (self::$status as $key => $item) {
            if ($comment['message'] === $key &&
                (array_key_exists('from_id', $comment) &&
                    in_array((string)$comment['from_id']['user_id'], $item['id']))) {
                $this->addTags($message, $tg_id, '/#(\w+)/', $item['tag'], $id);
                $tag = false;
            }
        }
        $usr = preg_match_all('#@[\w-]+#', $comment['message'], $m);
        $d = preg_match_all('#Deadline[ \d-]+#', $comment['message'], $deadline);
        switch (true) {
            case ($tag && $d && in_array((string)$comment['from_id']['user_id'], self::$pm)):
                $this->addDeadline($message, $tg_id, $deadline[0][0], $id);
            case ($tag && $usr && in_array((string)$comment['from_id']['user_id'], self::$pm)):
                $this->addUsers($message, $tg_id, $m[0], $id);
                break;
            case ($tag && !$usr && !$d):
                $this->addTags($message, $tg_id, '/#(\w+)/', '#InProcess', $id);
                break;
        }
    }

    protected function addTags(string $message, int $tg_id, string $pattern, string $status, int $id)
    {
        $a = preg_match_all('#\#[\w-]+#', $message, $match);
        $newMessage = $message;
        switch (true) {
            case (!$a):
                $newMessage .= '
' . $status;
                break;
            case ($a):
                $newMessage = preg_replace($pattern, $status, $message,);
                break;
        }
        if ($newMessage !== $message) {
            $this->messages->editMessage(
                ['peer' => -100 . env('STATUS_CHANNEL_ID'),
                    'id' => $tg_id,
                    'message' => $newMessage]);
            TgChannelText::where('id', '=', $id)->update(['message' => $newMessage]);
        }
    }

    protected function addUsers(string $message, int $tg_id, array $users, int $id)
    {
        $newMessage = $message;
        foreach ($users as $user) {
            $newMessage .= '
' . $user;
        }
        if ($newMessage !== $message) {
            $this->messages->editMessage(
                ['peer' => -100 . env('STATUS_CHANNEL_ID'),
                    'id' => $tg_id,
                    'message' => $newMessage]);
            TgChannelText::where('id', '=', $id)->update(['message' => $newMessage]);
        }
    }

    protected function addDeadline(string $message, int $tg_id, string $deadline, int $id)
    {
        $a = preg_match_all('#Deadline[ \d-]+#', $message, $match);
        $newMessage = $message;
        switch (true) {
            case (!$a):
                $newMessage .= '
' . $deadline;
                break;
            case ($a):
                $newMessage = preg_replace('#Deadline[ \d-]+#', $deadline, $message,);
                break;
        }
        if ($newMessage !== $message) {
            $this->messages->editMessage(
                ['peer' => -100 . env('STATUS_CHANNEL_ID'),
                    'id' => $tg_id,
                    'message' => $newMessage]);
            TgChannelText::where('id', '=', $id)->update(['message' => $newMessage]);
        }
    }
}
