<?php

namespace Modules\EnvatoAddBot\Services;

use danog\MadelineProto\API;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Modules\EnvatoAddBot\Models\EnvatoLink;
use function League\Uri\UriTemplate\toString;



class ChatService
{

    const PATERN = '#\#\w+#';
    const PATTERN_LINK = '#https://elements.envato.com([a-zA-Z-_/\.0-9+_]+)#';
    /**
     * @var API
     */

    protected EnvatoBot $envatoBot;




    public API $MadelineProto;

    public function __construct()
    {
        $this->MadelineProto = new API(env('SESSION_PATH'));
        $this->MadelineProto->start();
    }

    /**
     *
     * Function  getMessages
     * @param int $channel_id
     * @param $model
     */



    public function getMessages(int $channel_id, $model): void
    {
        $chats_id = collect($model::orderBy('tg_id', 'desc')->pluck('tg_id'))->all();
        $start = $chats_id === [] ? 1 : $chats_id[0] + 1;
        $end = $this->MadelineProto->messages->getHistory(['peer' => -100 . $channel_id, 'limit' => 1])['messages'][0]['id'];
        for ($i = $start; $i <= $end; $i += 200) {
            $messages = $this->MadelineProto->channels->getMessages(["channel" => -100 . $channel_id, "id" => range($i, $end)])['messages'];
            foreach ($messages as $item) {
                self::create($item, $model);
            }
        }
    }

    public function listProcessor(int $group_id)
    {

        $messagesSearch1 = $this->MadelineProto->messages->search(['peer'=> -100 . $group_id , 'q'=>'#list', 'limit'=>100 ]);
        $messagesSearch3=[];
        $i=0;
        foreach($messagesSearch1['messages'] as $msg_id) {
            $messagesSearch2 = $this->MadelineProto->messages->search(['peer' => -100 . $group_id , 'top_msg_id' => $msg_id['id'], 'filter'=>['_'=>'inputMessagesFilterUrl'], 'limit' => 100, ]);

            $link_parse = ($messagesSearch2['messages'][0]['media']['webpage']['url']);
            preg_match(self::PATTERN_LINK, $link_parse, $match);
            $messagesSearch3[$i]=$match[1];
            if (Str::is('/ru/*', $messagesSearch3[$i])){
                Str::replace('/ru', '', $messagesSearch3[$i]);
            }
            $i++;

        }

        return $messagesSearch3;

    }

    public function linksProcessor(int $group_id)
    {
        $messagesSearch1 = $this->MadelineProto->messages->search(['peer'=> -100 . $group_id , 'q'=>'#links', 'limit'=>100 ]);
        $messagesSearch3=[];
        $hashtags = $messagesSearch1['messages'][0]['message'];
        $hashtags = explode("\n", $hashtags);
        $hashtagsNew =[];
        for ($i=0, $j=0; $i<count($hashtags); $i++){
            if($hashtags[$i] !== '' && $hashtags[$i] !== '#Links' && Str::is('#*', $hashtags[$i])){
                $hashtagsNew[$j] = $hashtags[$i];
                $j++;
            }
        }
        $i=0;
        foreach($messagesSearch1['messages'] as $msg_id) {
            $messagesSearch2 = $this->MadelineProto->messages->search(['peer' => -100 . $group_id , 'top_msg_id' => $msg_id['id'], 'filter'=>['_'=>'inputMessagesFilterUrl'], 'limit' => 100, ]);

            $messagesSearch3[$i] = ($messagesSearch2['messages'][0]['message']);
            $i++;
        }

        $i=0;
        $j=0;
        foreach($messagesSearch3 as $arr1)
        {
            $messagesSearch3[$i] = explode("\n", $messagesSearch3[$i]);
            $arr1 = explode("\n", $arr1);
            foreach($arr1 as $arr2) {
                if (!Str::is('https:*', $arr2)) {
                    $messagesSearch3[$i][$j] = null;
                } elseif (Str::is('/ru/*', $arr2)) {
                    Str::replace('/ru', '', $messagesSearch3[$i][$j]);
                }
                $j++;
            }
            $i++;
        }
        $messagesSearch3 = Arr::flatten($messagesSearch3);
        $result[0] = Arr::whereNotNull($messagesSearch3);
        $result[1] = $hashtagsNew;
        return $result;
    }

    public function editProcessor(int $messageId, array $value)
    {
        $val = implode("\n", $value);
        $channelId = env('CHANNEL_ID');
        $message = $this->MadelineProto->messages->getHistory(['peer'=> -100 . $channelId, 'offset_id'=>$messageId +1, 'limit'=>1])['messages'][0]['message'];
        $mj = '';
        if(Str::endsWith($message, '#New')){
            $mj = Str::substrReplace($message, "\n".$val, -6, 0);
        }
        else{
            $mj = $message."\n".$val;
        }
        $this->MadelineProto->messages->editMessage(['peer'=>-100 . $channelId, 'id'=>$messageId, 'message'=>$mj]);
        $item = $this->MadelineProto->messages->getHistory(['peer'=> -100 . $channelId, 'offset_id'=>$messageId +1, 'limit'=>1])['messages'][0];

        $channelId = $item['peer_id']['channel_id'];
        if ($channelId === (int)env('CHANNEL_ID')) {
            $id = EnvatoLink::where('tg_id', '=', $item['id'])->value('id');
            ChatService::upDate($item, $id, 'Modules\EnvatoAddBot\Models\EnvatoLink');
        }
    }

    /**
     *
     * Function  sendMessage
     * @param int $channel_id
     * @param string $message
     */
    public function sendMessage(array $message): void
    {
        $this->MadelineProto->messages->sendMessage($message);
    }



    /**
     *
     * Function  editMessage
     * @param int $channel_id
     * @param int $id
     * @param string $message
     */
    public function editMessage(int $channel_id, int $id, string $message): void
    {
        $this->MadelineProto->messages->editMessage(['peer' => $channel_id, 'id' => $id, 'message' =>$message]);
    }

    /**
     *
     * Function  save_db
     * @param array $item
     * @param $model
     */
    public static function create(array $item, $model): void
    {
        $chatText = new $model;
        self::save($item, $chatText, $model);
    }


    /**
     *
     * Function  upDate
     * @param array $item
     * @param $model
     */
    public static function upDate(array $item, $id, $model): void
    {
        $chatText = $model::where('id', '=', $id)->first();
        self::save($item, $chatText, $model);
    }

    /**
     *
     * Function  save
     * @param array $item
     * @param $chatText
     */
    private static function save(array $item, $chatText, $model)
    {
        $media = array_key_exists('message', $item);
        if ($media && preg_match('#https://elements.envato.com/[a-z-_/\.0-9]+-([A-Z0-9]+)#', $item['message'], $matches)) {
            preg_match_all(self::PATERN, $item['message'], $hashtag);
            $chatText->tg_id = $item['id'];
            $chatText->hash_id = $matches[1];
            $chatText->envato_link = $matches[0];
            $chatText->message = $item['message'];
            $chatText->hashtag = json_encode($hashtag[0]);
            $chatText->tg_link = 'https://t.me/c/' . $item['peer_id']['channel_id'] . '/' . $item['id'];
            $chatText->save();
        }
    }
}
