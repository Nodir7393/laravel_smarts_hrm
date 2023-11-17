<?php

namespace Modules\Instagram\Service;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use InstagramScraper\Instagram;

class InstaService
{
    public Instagram $instagram;

    public function __construct()
    {
        $this->instagram = Instagram::withCredentials(new Client(['headers' => [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36'
        ]]), '', '', null);
        $this->instagram->loginWithSessionId(env('INSTA_SESSION'));
    }

    public function getInfo()
    {
        $username = 'smarts_hrm';
        $password = 'theeagle03';
        $this->login($username, $password);
        $info = $this->instagram->getAccountInfo($username);
        $url = $info->getProfilePicUrl();


    }

    public function login($username, $password)
    {
        $cache = Cache();
        $this->instagram = Instagram::withCredentials(new Client(), $username, $password, $cache);
        $this->instagram->setUserAgent('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36');

        $cookie = [
            "csrftoken" => "SXiUhMPBAquoOGYJeyeRVtVX6bIskUZA",
            "datr" => "ewEoZN4CyK3UipI_eEj4sjOe",
            "ds_user_id" => "8474803369",
            "ig_did" => "2AA7D956-8C51-4FD9-A956-05044F9102C2",
            "ig_nrcb" => "1",
            "mid" => "ZCgBfAALAAHNpsUEefqN95-wiKS3",
            "rur" => "\"NCG\\0548474803369\\0541711879566:01f77110a8c9514e68b817ff57f3b028a863149290966b7d870fbb84507e73d41cb12d39\"",
            "sessionid" => "8474803369%3AthcJ7jXlmDwLIK%3A21%3AAYcTgBYnAVpWXVwGsp-YbH3P_NKc1FstCvBl0Vf1hQ",
            "shbid" => "\"4309\\0548474803369\\0541711879517:01f7dca09517671291600aa95a145388d4b116b3d230e6118172471010026143637ee40a\"",
            "shbts" => "\"1680343517\\0548474803369\\0541711879517:01f72d79a25c8e158b48fc0c0cbc74d266996282f7e461f795dc2cfa884f98fa2672688c\""
        ];
        $this->instagram->setUserAgent('User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36');
//        $this->instagram->setCustomCookies($cookie);
        $this->instagram->login();
        $this->instagram->saveSession(604800016);
        return $cache;
    }

    public function isLoggedIn(): bool{
        $ses = $this->instagram->getuse;
        return $this->instagram->isLoggedIn(Cache::get(md5('smarts_hrm')));
    }

    public function validate($temp): array
    {
        $post = [];
        foreach ((array)$temp as $key => $item) {
            $pattern = "/[^a-zA-Z0-9 ]+/";
            $messageText = preg_replace($pattern, "", $key);
            if (gettype($item) != 'array') {
                switch (true) {
                    case $messageText == 'id':
                        $post['insta_id'] = $item;
                        break;
                    default:
                        $post[$messageText] = $item;
                        break;
                }
            }
        }
        return $post;
    }
}
