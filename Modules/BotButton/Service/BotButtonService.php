<?php

namespace Modules\BotButton\Service;

use danog\MadelineProto\EventHandler;
use Modules\ALL\Services\LoginService;
use Modules\EnvatoAddBot\Services\ChatService;


class BotButtonService extends EventHandler
{

    const ADMIN = "eldorsobitov"; // Change this

    protected static array $dbProperties = [
        'dataStoredOnDb' => 'array'
    ];

    protected $dataStoredOnDb;

    public function getReportPeers()
    {
        return [self::ADMIN];
    }

    public ChatService $chatService;

    public function onStart()
    {
        $this->messages->sendMessage(['message'=>"workin"]);
    }

    public function onUpdateNewChannelMessage(array $update): \Generator
    {
        return $this->onUpdateNewMessage($update);
    }

    public function onUpdateNewMessage(array $update): \Generator
    {
        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            return;
        }
        $res = \json_encode($update, JSON_PRETTY_PRINT);

        yield $this->messages->sendMessage(['peer' => $update, 'message' => "This userbot is powered by MadelineProto!",
            'reply_to_msg_id' => isset($update['message']['id']) ? $update['message']['id'] : null, 'parse_mode' => 'HTML']);
        if (isset($update['message']['media']) && $update['message']['media']['_'] !== 'messageMediaGame') {
            yield $this->messages->sendMedia(['peer' => $update, 'message' => $update['message']['message'], 'media' =>
                $update]);
        }

        // You can also use the built-in MadelineProto MySQL async driver!

        // Can be anything serializable, an array, an int, an object
        $myData = [];

        // Use the isset method to check whether some data exists in the database
        if (yield $this->dataStoredOnDb->isset('yourKey')) {
            // Always yield when fetching data
            $myData = yield $this->dataStoredOnDb['yourKey'];
        }
        $this->dataStoredOnDb['yourKey'] = $myData + ['moreStuff' => 'yay'];

        $this->dataStoredOnDb['otherKey'] = 0;
        unset($this->dataStoredOnDb['otherKey']);

        $this->logger("Count: " . (yield $this->dataStoredOnDb->count()));

        // You can even use an async iterator to iterate over the data
        $iterator = $this->dataStoredOnDb->getIterator();
        while (yield $iterator->advance()) {
            [$key, $value] = $iterator->getCurrent();
            $this->logger($key);
            $this->logger($value);
        }
    }

    function botButton(){

        $replyKeyboardMarkup = ['_' => 'replyKeyboardMarkup', 'rows' => ["KeyboardButtonRow", "KeyboardButtonRow"], 'placeholder' => 'string'];


    }
}
