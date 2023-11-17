<?php

namespace Modules\Instagram\Service;

use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\Log;

class AutoAccept
{
    const BASE_URL = "https://instagram.com";
    public function autoAccept($bot)
    {
        $service = new LoginService();
        $service->login($bot);
        sleep(3);
         $notificationBtns = $service->driver->findElements(WebDriverBy::cssSelector('div[class="xh8yej3 x1iyjqo2"] > div:nth-child(6) > div > a'));
//        $notificationBtns = $service->driver->findElements(hasXPath('div'));
        count($notificationBtns)? ($notificationBtns[0]->click()) : Log::debug("No any notification button was found :(");
        sleep(3);
        $accountBtns = $service->driver->findElements(WebDriverBy::cssSelector('div[role="button"]'));
        if (count($accountBtns))
        {
            foreach ($accountBtns as $accountBtn)
            {
                $accountBtn->click();
                sleep(5);
                $badConfirmBtns = $service->driver->findElements(WebDriverBy::cssSelector('div[data-pressable-container="true"] > div > div > div[role="button"]'));
                foreach ($badConfirmBtns as $badConfirmBtn) {
                    $badConfirmBtn->click();
                    sleep(1);
                }
                Log::debug(json_encode($badConfirmBtns));
            }
        }
    }
}
