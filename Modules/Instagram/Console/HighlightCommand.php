<?php

namespace Modules\Instagram\Console;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use InstagramScraper\Instagram;
use Modules\Instagram\Service\LoginService;
use Modules\Instagram\Service\ParseWithLocationService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class HighlightCommand extends Command
{
    protected $name = 'insta:highlight';
    protected $description = 'Command description.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

//        $dev_tools = $service->driver->getDevTools();
//        $dev_tools->execute('Network.enable');
//        preg_match_all('/(?<=X-IG-App-ID":")\d+/', $html, $pregAppId);
//        $appId = $pregAppId[0][0];
//        $coo = $service->driver->manage()->getCookies();
//        $csrf = '';
//        foreach ($coo as $item) {
//            if ($item->getName() == 'csrftoken'){
//                $csrf = $item->getValue();
//            }
//        }
//        $dev_tools->execute('Network.setExtraHTTPHeaders', ['headers' => (object) [
//            'X-IG-App-ID' => $appId,
//            'X-CSRFToken' => $csrf
//        ]]);
//
//        $service->getHtml('https://www.instagram.com/api/v1/feed/user/brknsergio/username/?count=12');

//        $json = $service->driver->findElement(WebDriverBy::cssSelector('body > pre'))->getText();
//        $items = json_decode($json, true);
//        foreach ($items['items'] as $item) {
//            $url = $item['video_versions'][0]['url'];
//            $fileContent = file_get_contents($url);
//            $localFilePath = 'C:/users/Gaara/videos/'.time().'video.mp4';
//            $result = file_put_contents($localFilePath, $fileContent);
//            Log::debug($result);
//            sleep(5);
//        }
    }

}
