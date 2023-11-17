<?php

namespace Modules\Instagram\Service;

use Facebook\WebDriver\WebDriverBy;
use Modules\Instagram\Entities\InstaUser;

class StoryViewer
{
    const BASE_URL = "https://instagram.com";
    public function storyView($bot, $count)
    {
        $service = new LoginService();
        $service->login($bot);
        $users = InstaUser::inRandomOrder()->limit($count)->get();
        foreach ($users as $user)
        {
            $service->driver->get(self::BASE_URL.'/'.$user->username);
            sleep(3);
            $viewedAndNotViewed = $service->driver->findElements(WebDriverBy::cssSelector('header > div > div[class="_aarf _aarg"]'));
            if(count($viewedAndNotViewed))
            {
                $notViewed = $viewedAndNotViewed[0]->findElements(WebDriverBy::cssSelector('canvas[style="position: absolute; top: -9px; left: -9px; width: 168px; height: 168px;"]'));
                if(count($notViewed))
                {
                    $viewedAndNotViewed[0]->click();
                    sleep(3);
                    while (true) {
                        $next = $service->driver->findElements(WebDriverBy::cssSelector('button[class="_ac0d"]'));
                        if (!count($next)) { break;}
                        $next[0]->click();
                        sleep(1);
                    }
                }
            }
            sleep(5);
        }
    }
}
