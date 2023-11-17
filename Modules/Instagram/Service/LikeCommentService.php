<?php

namespace Modules\Instagram\Service;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Entities\InstaPost;

class LikeCommentService
{
    public function like($bot, $num)
    {
        $posts = InstaPost::inRandomOrder()->limit($num)->get();
        $service = new LoginService();
        $service->login($bot);
        sleep(2);
        foreach ($posts as $post) {
            $html = $service->getHtml($post->link);
            $postData = $service->driver->findElements(WebDriverBy::cssSelector('div[class="x78zum5"] > span[class="xp7jhwk"] > button[class="_abl-"]'));
            $reelsData = $service->driver->findElements(WebDriverBy::cssSelector('div[class="x6s0dn4 x1ypdohk x78zum5 xdt5ytf xieb3on"] > span > button'));
            if (count($postData)){
                $postData[0]->click();
                $post->hasLiked = true;
                $post->save();
            } elseif (count($reelsData)) {
                $reelsData[0]->click();
                $post->hasLiked = true;
                $post->save();
            } else {
                Log::debug('Like button not found!');
            }
            sleep(5);
        }
        $service->driver->quit();
    }

    public function comment($bot, $num, $comment)
    {
        $posts = InstaPost::inRandomOrder()->limit($num)->get();
        $service = new LoginService();
        $service->login($bot);
        sleep(2);
        foreach ($posts as $post) {
            $html = $service->getHtml($post->link);
            $postData = $service->driver->findElements(WebDriverBy::cssSelector('div[class="x78zum5"] > span'));
            $reelsData = $service->driver->findElements(WebDriverBy::cssSelector('div[class="x6s0dn4 x1ypdohk x78zum5 xdt5ytf xieb3on"] > div > div[class="x78zum5 x6s0dn4 xl56j7k xdt5ytf"]'));
            if (count($postData) > 1){
                $postData[1]->click();
                $inputData = $service->driver->findElement(WebDriverBy::cssSelector('div[class="_akhn"] > textarea'));
                $inputData->sendKeys($comment);
                $inputData->sendKeys(WebDriverKeys::ENTER);
                sleep(4);
            } elseif (count($reelsData)) {
                $reelsData[0]->click();
                $inputData = $service->driver->findElement(WebDriverBy::cssSelector('textarea'));
                $inputData->sendKeys($comment);
                $inputData->sendKeys(WebDriverKeys::ENTER);
                sleep(4);
            } else {
                Log::debug('Comment button not found!');
            }
            sleep(5);
        }
        $service->driver->quit();
    }
}
