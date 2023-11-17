<?php

namespace Modules\EnvatoAddBot\Services;

use Illuminate\Support\Str;
use Modules\EnvatoAddBot\Console\EnvatoParseCommand;
use Modules\EnvatoAddBot\Models\EnvatoLink;
use Wa72\HtmlPageDom\HtmlPage;
use function Safe\event_set;

class EnvatoBot
{
    const MODEL = 'Modules\EnvatoAddBot\Models\EnvatoLink';
    const LINK = 'https://elements.envato.com';
    const PATTERN = '#(?:<li class="[- a-zA-Z0-9_]+"><div class="[a-zA-Z]+" data-test-selector="[- a-zA-Z]+"><a title="[- a-zA-Z0-9&()&amp;\.\| &\#x27;\+\?=]+" class="[a-zA-Z_0-9 ]+" href=")([\/a-zA-Z0-9-]+)"><\/a>#';
    const PATTERN_CU = '#<a class="[- a-zA-Z0-9_]+" href="([- /a-zA-Z0-9_]+)"><span class="[- a-zA-Z0-9_]+"><span class="[- a-zA-Z0-9_]+">[- a-zA-Z0-9_]+</span><span class="[- a-zA-Z0-9_]+">[- a-zA-Z0-9_]+</span></span></a>#';
    const HASH_ID = '#https://[a-z-_/\.0-9]+-([A-Z0-9]+)#';

    /*$itemPattern = '#(https://video-previews[a-z-_/\.0-9]+)|((?<=src=")https://elements-cover-images[- a-zA-Z0-9&()&amp;\.\| &\#x27;\+\?=]+)|((?:<h1 class="[- a-zA-Z0-9]+">)[- a-zA-Z0-9&()&amp;\.\| &\#x27;\+\?=]+(?=</h1>))#';*/

    /**
     * @var
     */
    public $hashtags;

    /**
     * @var
     */
    public $edit ;
    /**
     * @var ChatService
     */
    protected ChatService $chatService;
    protected BotMessages $botMessages;

    public function __construct()
    {
        $this->chatService = new ChatService;
    }

    /**
     *
     * Function  envatoParse
     * @param string|NULL $category
     * @return  array
     */

    public function go(int $group_id){
        $this->chatService->linkService($group_id);
    }


    public function envatoParse( string $category = NULL): array
    {
        $resault = [];
        $url = self::LINK;
        $hashtags = [];
        $urlcategory = $category;
        $length = Str::of($urlcategory)->length();
        for($k=0 ; $k<$length; $k++ ){
            if($urlcategory[$k-1] === '-' || $urlcategory[$k-1] ==='+' || $urlcategory[$k-1] ==='/'){
                $urlcategory[$k] = strtoupper($urlcategory[$k]);
            }
        }
        $urlcategory = Str::replace('-' ,'',$urlcategory);
        $urlcategory = Str::replace('+' ,'',$urlcategory);
        //$urlcategory = Str::camel($urlcategory);

        $urlcategory = explode('/', $urlcategory);
        for($j=1 ; $j<count($urlcategory) && $j<4; $j++){
            $hashtags[$j-1] = "#".$urlcategory[$j];
        }
        $this->hashtags =  $hashtags;
        if ($category) {$url  .= $category;}
        for ($i = 1; $i <= 50; $i++) {
            if ($i > 1) {
                if ($urlcategory[1] !== "User") {
                    $url .= '/pg-' . $i;
                } else {
                    $url .= '?page=' . $i;
                }
            }

            $parsed = self::parse($url, self::PATTERN, true);
            if (count($parsed[1])) {
                $links = [];
                foreach ($parsed[1] as $item) {
                    preg_match(self::HASH_ID, self::LINK . $item, $match);
                    $links[$match[1]] = $match[0];
                }
                $resault[] = $this->sendMessage($links);
            } else {
                return $resault;
            }
            sleep(1);
        }
        return $resault;
    }

    /**
     * @var
     */
    public $dublikat;
    public $hash_id;


    public function envatoListProcessor(int $group_id){
        $links = $this->chatService->listProcessor($group_id);
        $this->filter($links);
    }

    public function envatoLinksProcessor(int $channelId)
    {
        $groupId = env('GROUP_ID');
        $parsed = $this->chatService->linksProcessor($groupId);
        $this->hashtags = $parsed[1];
        $links =[];
        foreach ($parsed[0] as $item) {
            preg_match(self::HASH_ID, self::LINK . $item, $match);
            $links[$match[1]] = $match[0];
        }
        $resault[] = $this->sendMessage($links);
        $this->setAllMessages($channelId);
        return $resault;
    }


    public function envatoCategoriesProcessor(string $url )
    {
        $fullUrl = self::LINK.$url;
        $page = new HtmlPage(self::getHtml($fullUrl));

        $page = $page->filter('div[data-test-selector="categories"]')->first();
        $categories = $page->filter('div[data-test-selector="categories"] ~ div>div>a>div ~ div');

        $urlc = explode('/', $url);
        $urlcategories=[];
        $i=0;
        foreach ($categories as $category) {
            $urlc[1] .='/'.Str::lower(Str::kebab($category->nodeValue));
            $urlcategories[$i] = implode('/',$urlc);
            $i++;
        }

        if ($urlcategories === []){$urlcategories[0] = $url;}
        foreach ($urlcategories as $urlcategory){
            $this->envatoParse($urlcategory);
        }
        $this->setAllMessages(env('CHANNEL_ID'));

    }

    public function envatoCategoriesProcessorUser(string $url)
    {

        $pattern = self::PATTERN_CU;
        $fullUrl = self::LINK.$url;
        $html = new HtmlPage(self::getHtml($fullUrl));

        $categories = $html->filter('div[data-test-selector="item-type-tabs"]>div>a ');

        preg_match_all($pattern,
            preg_replace('#amp;#', '', $categories),
            $matches);

        $links = $matches[1];

        if ($links === []){$links[0] = $url;}
        foreach ($links as $link){
            $this->envatoParse($link);
        }
        $this->setAllMessages(env('CHANNEL_ID'));
    }

    public function filter(array $urls)
    {
        foreach ($urls as $url) {
            $urlcontrol = explode("/", $url);

            if ($urlcontrol[1] === 'user') {
                $this->envatoCategoriesProcessorUser($url);
            }
            else {
                $this->envatoCategoriesProcessor($url);
            }
        }
    }

    public static function parse(string $url, string $pattern, bool $mAll = false): mixed
    {
        $html = self::getHtml($url);
        if ($mAll) {
            preg_match_all($pattern,
                preg_replace('#amp;#', '', $html),
                $matches, PREG_PATTERN_ORDER);
            return $matches;
        }
        preg_match($pattern, preg_replace('#amp;#', '', $html), $matches);
        return $matches;
    }

    /**
     *
     * Function  getHtml
     * @param string $url
     * @return  bool|string
     */
    public static function getHtml(string $url): bool|string
    {
        // 1. инициализация
        $ch = curl_init();
        // 2. указываем параметры, включая url
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT,
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:107.0) Gecko/20100101 Firefox/109.0');
        // 3. получаем HTML в качестве результата
        $subject = curl_exec($ch);
        // 4. закрываем соединение
        curl_close($ch);
        return $subject;
    }

    /**
     *#Envato
     * #EnvatoAddBot
     * #Wordpress
     * #Elementor
     * Function  sendMessage
     * @param array $links
     * @return  string
     */
    public function sendMessage(array $links): string
    {
        $hash_ids = collect($links)->keys();
        $envato = collect(EnvatoLink::whereIn('envato_link', $links)->orWhereIn('hash_id', $hash_ids)->get())->all();
        $db_links = [];
        $db_posts = [];
        foreach ($envato as $db) {
            $db_links[] = $db->envato_link;
            $db_posts[$db->hash_id]['id'] = $db->id;
            $db_posts[$db->hash_id]['tg_id'] = $db->tg_id;
            $db_posts[$db->hash_id]['hashtag'] = $db->hashtag;
            $db_posts[$db->hash_id]['envato_link'] = $db->envato_link;
            $db_posts[$db->hash_id]['tg_link'] = $db->tg_link;
            $db_posts[$db->hash_id]['message'] = $db->message;
        }
        $this->dublikat = false;
        $resault = "Kiritib Bo'lingan linklar\n";
        $h_tg = $this->hashtags;
        $h_diff = 0;
        $h_db = 0;
        foreach ($links as $hash_id => $item) {
            $this->dublikat = !in_array($item, $db_links);
            $this->hash_id = !array_key_exists($hash_id, $db_posts);
            if(is_array($db_posts) && array_key_exists($hash_id, $db_posts)) {
                $h_db = Str::replace(['"','[',']', ' '], '', $db_posts[$hash_id]['hashtag']);
                $h_db = explode(',', $h_db);
                $h_diff = array_diff($h_tg, $h_db);
            }

            switch (true) {
                case ($this->dublikat && $this->hash_id):
                    $this->chatService->sendMessage(['peer' => -100 . env('CHANNEL_ID'),
                       'message' => $item . "\n\n" . implode("\n", $this->hashtags ?? ['']) . "\n\n#New"]);
                    break;
                case ($this->dublikat && !$this->hash_id):
                    $this->chatService->sendMessage(['peer' => -100 . env('CHANNEL_ID'),
                        'message' => $item . "\n\n" . implode("\n", $this->hashtags) .
                        "\n\n#Dublicate\n\n" . $db_posts[$hash_id]['tg_link'],
                        'reply_to_msg_id' => $db_posts[$hash_id]['tg_id']]);
                    break;
                case (!$this->dublikat && $this->edit):
                    $this->sortTegs($db_posts[$hash_id]);
                    $resault .= $db_posts[$hash_id]['tg_link'] . "\n";
                    break;
                case (!$this->dublikat && $h_diff):
                    $this->chatService->editProcessor($db_posts[$hash_id]['tg_id'], $h_diff);
            }
        }
        return $resault;
    }




    /**
     *
     * Function  sortTegs
     * @param $db_link
     */
    protected function sortTegs(array $db_link): void
    {
        $teg = false;
        $hashtags = json_decode($db_link['hashtag']);
        $new = in_array('#new', $hashtags, true);
        foreach ($this->hashtags as $hashtag) {
            if (!in_array($hashtag, $hashtags, true)) {
                $teg = true;
            }
        }

        $message = $db_link['envato_link'] . "\n\n" . implode("\n", $this->hashtags);
        $message .= $new ? "\n\n#New" : '';
        if ($teg && $message !== $db_link['message']) {
            echo($db_link['tg_id']);
            $this->chatService->editMessage(-100 . env('CHANNEL_ID'), $db_link['tg_id'], $message);
        }
    }

    /**
     *
     * Function  setAllMessages
     * @param int $channelId
     */
    public function setAllMessages(int $channelId): void
    {
        $this->chatService->getMessages($channelId, self::MODEL);
    }
}
