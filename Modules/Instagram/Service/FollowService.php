<?php

namespace Modules\Instagram\Service;

use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Entities\InstaUser;

class FollowService
{
    const BASE_URL = "https://www.instagram.com";
    public function follow($bot, $count)
    {
        $service = new LoginService();
        $service->login($bot);
        sleep(3);
        $users = InstaUser::inRandomOrder()->limit($count)->get();
        foreach ($users as $user)
        {
            $service->driver->get(self::BASE_URL.'/'.$user->username);
            sleep(3);
            $followBtn = $service->driver->findElements(WebDriverBy::cssSelector('button[class="_acan _acap _acas _aj1-"]'));
//            dd($followBtn);
            if(count($followBtn)) // class="_acan _acap _acat _aj1-"
            {
                $followBtn[0]->click();
            }else{
                Log::debug('Follow button was not found :(');
            }
            sleep(5);
        }
    }
    public function unFollow($bot, $count)
    {
        $service = new LoginService();
        $service->login($bot);
        sleep(3);
        $users = InstaUser::inRandomOrder()->limit($count)->get();
        foreach ($users as $user)
        {
            $service->driver->get(self::BASE_URL.'/'.$user->username);
//            $service->driver->get('https://www.instagram.com/mascanuz/');
            sleep(3);
            $followBtn = $service->driver->findElements(WebDriverBy::cssSelector('header > section > div > div > div > div > button[class="_acan _acap _acat _aj1-"]')); // [class="_acan _acap _acat _aj1-"]
            if(count($followBtn))
            {
//                sleep(1);
//                $followBtn = $service->driver->findElements(WebDriverBy::cssSelector('button')); // [class="_acan _acap _acat _aj1-"]
                $followBtn[0]->click();
                sleep(3);
                $unFollowBtn = $service->driver->findElements(WebDriverBy::cssSelector('div[role="dialog"] div[role="button"]:last-of-type'));
                if(count($unFollowBtn))
                {
                    $unFollowBtn[0]->click();
                    Log::info('Successfully unfollowed! :)');
                }else{
                    Log::debug('No any UnFollow button was found :(');
                }
            }else{
                Log::debug('Follow button was not found :(');
            }
            sleep(5);
        }
    }
}
