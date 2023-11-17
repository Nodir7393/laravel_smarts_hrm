<?php

namespace Modules\Instagram\Service;


use EmailVerification;
use GuzzleHttp\Client;
use InstagramScraper\Instagram;

class InstaLoginService
{
    public Instagram $instagram;

    public function login(string $username, string $password): mixed
    {
        $cache = Cache();
        $this->instagram = Instagram::withCredentials(new Client(), $username, $password, $cache);

        $this->instagram->setUserAgent('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36');
        $this->instagram->login();
        $this->instagram->saveSession(604800016);
        return $cache;
    }

    public function loginWithSessionId($session_id)
    {
        $this->instagram = Instagram::withCredentials(new Client(), '', '', null);
        return $this->instagram->loginWithSessionId($session_id);
    }
}
