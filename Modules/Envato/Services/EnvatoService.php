<?php

namespace Modules\Envato\Services;

use danog\MadelineProto\API;

trait EnvatoService
{
    public function __construct()
    {
        $this->MadelineProto = new API(env('SESSION_PATH'));
        $this->MadelineProto->start();
    }

    /**
     * @var API
     */
    public API $MadelineProto;

    /**
     *
     * Function  getLink
     * @param $postLink
     * @return  mixed
     */
    protected function getLink($postLink): mixed
    {
        // 1. инициализация
        $ch = curl_init();
        // 2. указываем параметры, включая url
        curl_setopt($ch, CURLOPT_URL, $postLink);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98)');
        // 3. получаем HTML в качестве результата
        $subject = curl_exec($ch);
        // 4. закрываем соединение
        curl_close($ch);
        // 5. получаем url ссылки
        preg_match(
            '#(https://video-previews[a-z-_/\.0-9]+)|((?<=src=")https://elements-cover-images[a-zA-Z-_/\.0-9\?%&;=]+)#',
            preg_replace('#amp;#', '', $subject), $matches);
        return $matches;
    }

    /**
     *
     * Function  getPosts
     * @param $start
     * @param $end
     */
    public function getPosts($start = 1, $end = null): void
    {
        $channel_id = env('CHANNEL_ID');
        $end = $end ?? $this->MadelineProto->messages->getHistory(['peer' => -100 . $channel_id, 'limit' => 1])['messages'][0]['id'];

        for ($i = $start; $i <= $end; $i += 200) {
            $messages = $this->MadelineProto->channels->getMessages([
                "channel" => -100 . $channel_id,
                "id" => range($i, $end)]);
            foreach ($messages['messages'] as $item) {
                $webPage = array_key_exists('media', $item) && array_key_exists('webpage', $item['media']);
                if ($webPage) {
                    if (array_key_exists('url', $item['media']['webpage'])) {
                        var_dump($item['id']);
                        $this->getComments(
                            $channel_id,
                            $item['id'],
                            $item['replies']['replies'],
                            $item['media']['webpage']['url'],
                            $item['message']);
                    } else {
                        $this->MadelineProto->messages->sendMessage([
                            'peer' => '-100' . env('REPORT_CHANNEL_ID'),
                            'message' => 'https://t.me/c/' . $channel_id . '/' . $item['id'] . ' 404 not found']);
                    }
                }
            }
        }
    }
}
