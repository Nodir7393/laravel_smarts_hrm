<?php
declare(strict_types=1);

namespace Modules\Project\Services;


use danog\MadelineProto\API;
use Exception;
use Illuminate\Support\Arr;
use JsonException;
use Modules\ALL\Models\TgChannelText;
use Modules\ALL\Models\TgGroupText;
use Modules\ALL\Models\TgUser;
use RuntimeException;

class StatusService
{
    public const COMPLETED = '#Completed';
    public const NEED_TESTS = '#NeedTests';
    public const ACCEPTED = '#Accepted';
    public const REJECTED = "#Rejected";
    public const INPROCESS = "#InProcess";
    public const FIXED = '#fixed';
    public const READY = '#ready';
    public const REJECT = '#reject';
    public const ACCEPT = '#accept';

    public const QA = 'qa';
    public const PM = 'pm';

    public const DEADLINE = '#deadline[ \d-]+#';
    public const PATERN = '#\\n\#\w+#';
    public const USER = '#@[\w-]+#';
    public const STATUSES = ['ActiveTask', 'InProcess', 'NeedTests', 'Accepted', 'Completed', 'ALL', 'Rejected'];
    public const TYPE_STATUS = 'status';
    public const TYPE_USER = 'user';
    public const TYPE_DEADLINE = 'dead';
    public const ID = 'id';
    public const PEER = 'peer';
    public const MESSAGE = 'message';
    public const TG_ID = 'tg_id';
    public const PEER_ID_CHANNEL_ID = 'peer_id_channel_id';
    public const STATUS = 'status';
    public const USERS = 'users_';
    public const N = "\n";
    public const MENTIONNAME = 'messageEntityMentionName';
    public const MENTION = 'messageEntityMention';
    const REPLY_TO = 'reply_to_msg_id';

    /**
     * @var API
     */
    public API $mtp;

    /**
     * @var
     */
    private $userId;

    /**
     * @var
     */
    private $users;

    /**
     * @var
     */
    private $tgUsers;

    /**
     * @var
     */
    private $post_id;

    /**
     * @var
     */
    private $project;

    /**
     * @var
     */
    private $group;

    /**
     * @var
     */
    private $peer;

    /**
     * @var
     */
    private $type;

    /**
     * @var
     */
    private $message;

    /**
     * @var
     */
    private $post;

    /**
     * @var
     */
    private $data;

    /**
     * @var
     */
    private $comment;

    public function __construct()
    {
        $this->mtp = new API(env('SESSION_PATH'));
        $this->mtp->start();
    }

    /**
     *
     * Function  confirmStatus
     * @param $comment
     * @param $group
     * @throws Exception
     */
    public function confirmStatus($comment, $group): void
    {
        $this->tgUsers = TgUser::all();
        $data = TgChannelText::with('comments')->where(self::ID, '=', $comment->tg_channel_text_id)
            ->orWhere(self::PEER_ID_CHANNEL_ID, '=', $group->linked_channel_id)
            ->whereIn(self::MESSAGE, self::STATUSES)->get();
        $this->data = $data;
        $post = collect($data)->whereIn(self::ID, [$comment->tg_channel_text_id])->first();
        $this->project = $group->hrmProject;
        $this->group = $group;
        $this->post = $post;
        $this->userId = $comment->from_id_user_id;
        $this->message = $post->message;
        $this->comment = $comment;
        $this->post_id = $post->tg_id;
        $this->peer = $post->peer_id_channel_id;
        $status = strtolower($comment->message);
        $this->switchStatus($status);
    }

    /**
     *
     * Function  switchStatus
     * @param $status
     * @throws Exception
     */
    private function switchStatus($status): void
    {
        $users = $this->getUsers();
        $performer = $this->checkUser();
        //$user = preg_match_all(self::USER, $status, $m);
        $dead = preg_match_all(self::DEADLINE, $status, $deadline);
        $this->type = self::TYPE_STATUS;
        switch (true) {
            case ($status === self::FIXED && $this->role(self::QA)):
                $this->addTags(self::COMPLETED);
                break;
            case ($status === self::READY && $performer):
                $this->addTags(self::NEED_TESTS);
                break;
            case ($status === self::REJECT && $this->role(self::PM)):
                $this->addTags(self::REJECTED);
                break;
            case ($status === self::ACCEPT && $this->role(self::PM)):
                $this->addTags(self::ACCEPTED);
                break;
            case ($dead && $this->role(self::PM)):
                $this->type = self::TYPE_DEADLINE;
                $this->addDeadline(Arr::get($deadline, '0.0'));
                break;
            case ($users && $this->role(self::PM)):
                $this->type = self::TYPE_USER;
                $this->addUsers($users);
                break;
            case (!$users && !$dead):
                $this->addTags(self::INPROCESS);
                break;
            default:
                throw new RuntimeException('Unexpected value');
        }
    }

    /**
     *
     * Function  checkUser
     * @return  bool|void
     * @throws JsonException
     */
    private function checkUser()
    {
        $post_user['user_name'] = [];
        $post_user['id'] = [];

        if (isset($this->post->entities)) {
            $post_entitles = json_decode($this->post->entities);

            $post_user['id'] = collect($post_entitles)->where('_', '=', self::MENTIONNAME)
                ->pluck('user_id');

            $post_mention = collect($post_entitles)->where('_', '=', self::MENTION);

            foreach ($post_mention as $item) {
                $post_user['user_name'][] = str_replace('@', '', substr($this->post->message, $item->offset, $item->length));
            }

            $users = collect($this->tgUsers)->whereIn('username', $post_user['user_name'])->pluck('tg_id')->all();
            foreach ($post_user['id'] as $item) {
                $users[] = $item;
            }
            return in_array($this->userId, $users);
        }
        return false;
    }

    /**
     *
     * Function  getUsers
     * @throws JsonException
     */
    private function getUsers()
    {

        $user['user_name'] = [];
        $user['id'] = [];

        if (isset($this->comment->entities)) {
            $entitles = json_decode($this->comment->entities, false);

            $user['id'] = collect($entitles)->where('_', '=', self::MENTIONNAME)
                ->pluck('user_id')->all();

            $mention = collect($entitles)->where('_', '=', self::MENTION)->all();

            foreach ($mention as $item) {
                $user['user_name'][] = str_replace('@', '', substr($this->comment->message, $item->offset, $item->length));
            }

            $users = collect($this->tgUsers)->whereIn('username', $user['user_name'])->pluck('tg_id')->all();
            foreach ($user['id'] as $item) {
                $users[] = $item;
            }
            return $users;
        }
        return false;
    }

    /**
     *
     * Function  role
     * @param $role
     * @return  bool
     * @throws JsonException
     */
    private function role($role): bool
    {
        $users = collect($this->tgUsers)->where(self::TG_ID, '=', $this->userId)->value(self::ID);
        $role = self::USERS . $role;
        $roles = $this->project->$role;
        //$roles = json_encode($data);
        return in_array((string)$users, (array)$roles, true);
    }

    /**
     *
     * Function  addTags
     * @param string $hashtag
     * @throws JsonException
     */
    private function addTags(string $hashtag): void
    {
        $this->replace($hashtag, self::PATERN);
    }

    /**
     *
     * Function  addDeadline
     * @param string $hashtag
     * @throws JsonException
     */
    private function addDeadline(string $hashtag): void
    {
        //$this->replace($hashtag, self::DEADLINE);
        $match = preg_match_all(self::DEADLINE, $this->message, $m);
        $edited_message = $this->message;
        if ($match) {
            $edited_message = str_ireplace($m[0], $hashtag, $edited_message);
        } else {
            $edited_message .= self::N . $hashtag;
        }
        $entities = $this->post->entities ? json_decode($this->post->entities, true) : null;
        $this->edit($edited_message, $entities);
    }

    /**
     *
     * Function  addUsers
     * @param array $users
     * @throws JsonException
     */
    private function addUsers(array $users): void
    {
        $edited_message = $this->message;
        $entities = $this->post->entities ? json_decode($this->post->entities, true) : null;
        foreach ($users as $item) {
            $user = collect($this->tgUsers)->where('tg_id', $item)->first();
            $name = $user->full_name ?? $user->first_name . $user->last_name;
            $edited_message .= self::N . $name;
            $a['_'] = self::MENTIONNAME;
            $a['length'] = strlen($name);
            $a['offset'] = strripos($edited_message, $name);
            $a['user_id'] = $user->tg_id;
            $entities[] = $a;
        }
        $this->edit($edited_message, $entities);
    }

    /**
     *
     * Function  replace
     * @param string $hashtag
     * @param string $pattern
     */
    private function replace(string $hashtag, string $pattern): void
    {
        $match = preg_match_all($pattern, $this->message, $m);
        $edited_message = $this->message;
        if ($match) {
            $edited_message = str_ireplace($m[0], '*', $edited_message);
            $edited_message = preg_replace(['/[*]+/'], self::N . $hashtag, $edited_message);
            $this->removestatus($m[0]);
            $this->addstatus($hashtag);
        } else {
            $edited_message .= self::N . $hashtag;
            $this->addstatus($hashtag);
        }
        $entities = $this->post->entities ? json_decode($this->post->entities, true) : null;
        $this->edit($edited_message, $entities);
    }

    /**
     *
     * Function  edit
     * @param $editedMessage
     * @param $entities
     * @return  void
     */
    public function edit($editedMessage, $entities, null|int|string $id = null, null|int|string $peer = null): void
    {
        $param = [
            self::PEER => $peer ?? -100 . $this->peer,
            self::ID => $id ?? $this->post_id,
            self::MESSAGE => $editedMessage
        ];
        if($entities) {$param['entities'] = $entities;}
        if ($editedMessage !== $this->message) {
            $this->mtp->messages->editMessage($param);
        }
    }

    /**
     *
     * Function  addstatus
     * @param string $old_status
     */
    private function addstatus(string $old_status): void
    {
        $status = substr($old_status, 1);
        $task_status = collect($this->data)->where(self::MESSAGE, '=', $status)->first();
        if (empty($task_status)) {
            $task_status = $this->send(-100 . $this->group->linked_channel_id, $status);
            $item = Arr::get($task_status['updates'], '2.message');
            $list = $this->mtp->messages->getDiscussionMessage([self::PEER => -100 . $item['peer_id']['channel_id'], 'msg_id' => $item['id']]);
            $message = Arr::get($list, 'messages.0');
            $task = $this->send(-100 .$message['peer_id']['channel_id'], $this->post->full_url, $message[self::ID]);
            ChatService::create($item, 'Modules\ALL\Models\TgGroupText');
        } else {
            $test = $this->mtp->messages->getReplies([self::PEER => -100 . $task_status->peer_id_channel_id, 'msg_id' => $task_status->tg_id]);
            $messages = Arr::get($test, 'messages');
            $aded = false;
            foreach ($messages as $message) {
                $link = Arr::get($message, 'message');
                $peer = Arr::get($message, 'peer_id.channel_id');
                $to = Arr::get($message, 'reply_to.reply_to_msg_id');
                ($link === $this->post->full_url) || ($aded = true);
            }
            switch (true) {
                case (!$aded && !empty($messages)):
                    $task = $this->send(-100 .$peer, $this->post->full_url, $to);break;
                case (!empty($task_status)):
                    $task = $this->mtp->messages->getDiscussionMessage(
                        ['peer' => -100 .$task_status->peer_id_channel_id, 'msg_id' => $task_status->tg_id]);
                    $id = Arr::get($task, 'messages.0.id');
                    $peer = Arr::get($task, 'messages.0.peer_id.channel_id');
                    $this->send(-100 .$peer, $this->post->full_url, $id);
                    break;
            }
        }
    }

    private function removestatus($old_status): void
    {
        $status = str_ireplace("\n#", '', $old_status[0]);
        $task_status = collect($this->data)->where(self::MESSAGE, '=', $status)->first();
        if (!empty($task_status)) {
            $list = $this->mtp->messages->getReplies([self::PEER => -100 . $task_status->peer_id_channel_id, 'msg_id' => $task_status->tg_id]);
            $messages = Arr::get($list, 'messages');
            foreach ($messages as $message) {
                $link = Arr::get($message, 'message');
                $peer = Arr::get($message, 'peer_id.channel_id');
                if ($link === $this->post->full_url) {
                    $a = $this->mtp->channels->deleteMessages([
                        'channel' => -100 .$peer,
                        'id' => [$message[self::ID]]]);
                }
            }
        }
    }

    public function send ($p, $i, $r = null)
    {
        return $this->mtp->messages->sendMessage([self::PEER => $p, self::MESSAGE => $i, self::REPLY_TO => $r]);
    }
}


/*
 * private function removeTask(array $hashtags): void
    {
        $channelMessageLink = $this->data->tg_link;
        $groupMessageTexts = TgGroupText::where(self::MESSAGE, $channelMessageLink);

        foreach ($hashtags as $hashtag) {
            foreach ($groupMessageTexts as $groupMessageText) {
                $status = $this->mtp->messages->getMessages(['id' => $groupMessageText->reply_to])[0];

                if ($status['message']['message'] === $hashtag) {
                    $this->mtp->messages->deleteMessages(['revoke' => true, 'id' => [$groupMessageTexts->tg_id]]);
                }
            }
        }
    }
 */
