<?php

namespace Modules\HHParse\Services;

use Modules\HHParse\Models\HhUser;

class HHHelper
{
    public function getHtml($url, $token)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: hhtoken=".$token));
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/111.0');
        $subject = curl_exec($ch);
        curl_close($ch);
        return $subject;
    }

    public function getUser()
    {
       return HhUser::all();
    }
}
