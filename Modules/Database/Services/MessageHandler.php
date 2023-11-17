<?php

namespace Modules\Database\Services;

use danog\MadelineProto\EventHandler;
use Modules\ALL\Models\TgChannel;
use Modules\ALL\Models\TgChannelText;
use Modules\ALL\Models\TgGroup;
use Modules\ALL\Models\TgGroupText;

class MessageHandler extends EventHandler
{
    public $messageService;


    public function onUpdateNewMessage($update)
    {
        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            return;
        }
        print_r($update['message']);

        $this->messageService = new MessageService();

        $tgChannel = TgChannel::where('tg_id', 'like', '%' . (int)$update['message']['peer_id']['channel_id'])->get();
        $tgGroup = TgGroup::pluck('tg_id')->toArray();
        dump(in_array((int)('-100' . $update['message']['peer_id']['channel_id']), $tgGroup));
        dump($tgChannel->isEmpty());
        if(!$tgChannel->isEmpty()) {
            $this->messages->sendMessage(['peer' => 1244414566, 'message' => 'Sent to Channel']);

            $validated = $this->messageService->messageValidator($update['message'], 'Modules\ALL\Models\TgGroupText');
            TgChannelText::create($validated);
        }else if(in_array((int)('-100' . $update['message']['peer_id']['channel_id']), $tgGroup)){
                $this->messages->sendMessage(['peer' => 1244414566, 'message' => 'Sent to Group']);
                $validated = $this->messageService->messageValidator($update['message']);
                TgGroupText::create($validated);
        }
    }

    public function onUpdateNewChannelMessage($update)
    {
        if ($update['message']['_'] === 'messageEmpty' || $update['message']['out'] ?? false) {
            return;
        }
        $this->onUpdateNewMessage($update);
    }
}
