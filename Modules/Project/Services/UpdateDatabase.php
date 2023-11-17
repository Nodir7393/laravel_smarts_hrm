<?php

namespace Modules\Project\Services;


use danog\MadelineProto\API;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Modules\ALL\Models\TgGroupText;
use Modules\ALL\Models\TgChannelText;
use Monolog\Logger;
class UpdateDatabase
{
    const TG_ID = 'tg_id';
    public ChatService $service;

    public $mtp;

    public function __construct(ChatService $chatService)
    {
        $this->service = $chatService;
        $this->mtp = new API(env('SESSION_PATH'));
    }

    public function collector(?int $channelId, ?string $start, ?string $end)
    {
        $channels = $this->getPeerId($channelId);
        foreach ($channels as $id => $type) {$this->index((int)$id, $start, $end, $type);}
        return '';
    }

    public function getPeerId($channelId)
    {
        $chat = $this->mtp->getInfo($channelId);
        $type = (!empty($chat)) ? $chat['type'] : null;
        $id = Arr::get($chat, "$type".'_id') ?? Arr::get($chat, 'channel_id');

        switch (true) {
            case ($type === 'supergroup' || $type === 'chat'): $type = TgGroupText::class;break;
            case $type = 'channel': $type = TgChannelText::class;break;
        }

        return [$id => $type];
    }

    public function index(int $id, string $start, string $end, string $type)
    {
        $time_start = $start ?? '';
        $time_end = ($end === '') ? date("Y-m-d") : $end;
        $this->getMessages($id, $time_start, $time_end, $type);
    }

    /**
     *
     * Function  getMessages
     * @param int $id
     * @param string $start
     * @param string $end
     * @param string $type
     * @return  array|void
     */
    public function getMessages(int $id, string $start, string $end, string $type)
    {
        $messages = $type::whereBetween('date', [$start, $end])
            ->where('peer_id_channel_id', $id)
            ->where('type', 'message')->pluck(self::TG_ID,'id');

        $msgs = collect($messages)->chunk(200)->toArray();
        if (empty($msgs)) {return [];}
        foreach ($msgs as $msg) {
            sleep(1);
            $responses = $this->mtp->channels->getMessages(["channel" => -100 .$id, "id" => $msg]);
            $respons = collect($responses['messages'])->keyBy('id')->all();
            foreach ($msg as $key2 => $item) {
                if ($respons[$item]['_'] !== 'messageEmpty') {
                    $this->service::upDate($respons[$item], $key2, $type);
                    usleep(40000);
                } else {
                    TgGroupText::where('peer_id_channel_id', $id)->where('tg_id',$item)->delete();
                    TgChannelText::where('peer_id_channel_id', $id)->where('tg_id',$item)->delete();
                }
            }
        }
        $log = new Logger('name');
   //     $log->pushHandler(new StreamHandler('path/to/your.log'));

Log::info('asdfsafasf');
Log::error('asdfsafasf');
Log::debug('asdfsafasf');
Log::info('asdfsafasf');
Log::info($msgs);
// add records to the log
        $log->info('Debugtttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttttt');
        $this->mtp->stop();
    }
}

