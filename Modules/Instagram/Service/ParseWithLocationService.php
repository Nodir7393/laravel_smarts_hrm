<?php

namespace Modules\Instagram\Service;

class ParseWithLocationService
{
    const location = "https://www.instagram.com/api/v1/web/search/topsearch/?query=";
    public function parse($name)
    {
        $service = new LoginService();
        $service->login(21);

        $service->getHtml(self::location.'');
    }
}
