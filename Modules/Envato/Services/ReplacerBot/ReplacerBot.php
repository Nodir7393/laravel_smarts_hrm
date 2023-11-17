<?php

namespace Modules\Envato\Services\ReplacerBot;

use danog\MadelineProto\EventHandler;
use Modules\EnvatoAddBot\Models\EnvatoLink;
use Modules\EnvatoAddBot\Services\ChatService;
use Modules\EnvatoAddBot\Services\EnvatoBot;
use Modules\Envato\Services\ReplacerBot\ScriptManagerResponseDTO;

class ReplacerBot extends EventHandler
{
    /**
     * @var int|string Username or ID of bot admin
     */
    const ADMIN = "firefox109"; // Change this

    /**
     * Get peer(s) where to report errors
     *
     * @return int|string|array
     */

    /**
     * Available commands
     *
     * @var array|array[]
     */
    public static array $commands = [
        'replace' => [
            'optionsCount' => 5,
            'options' => [
                'channelId',
                'type',
                'search',
                'replace',
                'start',
                'end'
            ]
        ]
    ];

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
    public function onUpdateNewChannelMessage(array $update): \Generator
    {
        return $this->onUpdateNewMessage($update);
    }

    /**
     * Handle updates from users.
     *
     * @param array $update Update
     *
     * @return \Generator
     */
    public function onUpdateNewMessage(array $update): \Generator
    {
        RequestParser::setRequest($update);

        if (!RequestParser::checkIsEmpty()) {
            return;
        }

        if (RequestParser::checkIsCommand() & RequestParser::checkIsMatchedCommand()) {
            ScriptManager::startManage(RequestParser::getCommand());
        }

        $res = ScriptManager::manage($update);

        if($res->isError) {
            $this->messages->sendMessage(['peer' => $update, 'message' => $res->errorMessage]);
            yield;
        }

        $this->messages->sendMessage(['peer' => $update, 'message' => $res->getMessage()]);

        if ($res->isEnd) {
            $data = $res->data;

            // run ReplacerCommand with $data -> exec();
        }

        yield;
    }
}
