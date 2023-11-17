<?php

namespace Modules\Envato\Services\ReplacerBot;

use danog\MadelineProto\API;

class ReplacerService
{
    /**
     * @var API
     */
    public API $MadelineProto;
    /**
     * @var string
     */
    protected string $channelId;
    /**
     * @var string
     */
    protected string $search;
    /**
     * @var string
     */
    protected string $type;
    /**
     * @var string
     */
    protected string $replace;

    /**
     * @param string $channelId
     * @param string $search
     * @param string $type
     * @param string $replace
     */
    public function __construct(string $channelId, string $search, string $type, string $replace)
    {
        $this->MadelineProto = new API(env('SESSION_PATH'));
        $this->MadelineProto->start();
        $this->channelId = $channelId;
        $this->search = $search;
        $this->type = $type;
        $this->replace = $replace;
    }

    /**
     *
     * Function  process
     * @param string $message
     * @return  string
     */
    public function process(string $message) :string
    {
        return match ($this->type)
        {
            'strRep' => $this->strReplace($message),
            'strTr' => $this->strTr($message),
            'preg' => $this->pregReplaceAll($message),
        };
    }

    /**
     *
     * Function  editPosts
     * @param int $start
     * @param int|null $end
     */
    public function editPosts(int $start = 1, int|null $end = null) :void
    {
        $end = $end ?? $this->MadelineProto->messages->getHistory(['peer' => -100 . $this->channelId, 'limit' => 1])['messages'][0]['id'];
        $channel_id = env('CHANNEL_ID');
        for ($i = $start; $i <= $end; $i += 200) {
            $messages = $this->MadelineProto->channels->getMessages(["channel" => -100 . $this->channelId, "id" => range($i, $end)])['messages'];
            foreach ($messages as $message) {
                if (array_key_exists('message', $message)) {
                    $editedMessage = $this->process($message['message']);
                    if ($editedMessage !== $message['message']) {
                        $this->editMessage($editedMessage, $message['id']);
                    }
                }
            }
        }
    }

    /**
     *
     * Function  editMessage
     * @param string $editedMessage
     * @param string $id
     */
    protected function editMessage(string $editedMessage, string $id) :void
    {
        $this->MadelineProto->messages->editMessage([
            'peer' => -100 . $this->channelId,
            'id' => $id,
            'message' => $editedMessage
        ]);
        usleep(600000);
    }

    /**
     *
     * Function  strReplace
     * @param string $subject
     * @return  array|string
     */
    protected function strReplace(string $subject) :array | string
    {
        return str_replace($this->search, $this->replace, $subject);
    }

    /**
     *
     * Function  strTr
     * @param string $subject
     * @return  string
     */
    protected function strTr(string $subject) :string
    {
        return strtr($subject, [$this->search => $this->replace]);
    }

    /**
     *
     * Function  pregReplaceAll
     * @param string $subject
     * @return  string|array|null
     */
    protected function pregReplaceAll(string $subject) : string|array|null
    {
        $count = 1;

        if (preg_match_all($this->search, $subject) > 1) $count = preg_match_all($this->search, $subject);

        return preg_replace($this->search, $this->replace, $subject, count: $count);
    }
}
