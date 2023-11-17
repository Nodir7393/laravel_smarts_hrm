<?php

namespace App\Services;


use Illuminate\Support\Facades\Artisan;
use Modules\Instagram\Service\InstaPostsService;
use Modules\Instagram\Service\InstaService;
use Phpfastcache\Helper\Psr16Adapter;

class TestService
{
    public function set_Cookie()
    {
        $instagram_username = 'smarts_hrm';
        $cacheKey = md5($instagram_username);

// Fill-in cookies from browser here
        $cookies = [
            "urlgen" => 'https://www.instagram.com/',
            "rur" => 'NCG\05457980941143\0541707816370:01f71d665a842e25b10570adbd8be1d169efe31fb7686612aed92649c53c0cf86567d2e5e6',
            "csrftoken" => 'kH1zsn2EbRdp7mpHCes40tthwdwpH8BwSW',
            "mid" => 'Y-nRogALAAFivYvVvbZ17_q4Fda63P',
            "sessionid" => '57980941143%3AI31U1Pf5jTzAPC%3A11%3AAYcDWJbcZIUNLgTeg-VjRX1zchZDFgbb3rxzJHWwzQAq',
            "ds_user_id" => '5798094114326',
            "ig_did" => '8D5FD843-FA0D-4495-9028-6FFC950B63D7F5',
        ];

        \Phpfastcache\CacheManager::setDefaultConfig(new \Phpfastcache\Config\ConfigurationOption([
            'defaultTtl' => (60 * 60 * 24 * 365), # One Year
            'path' => './cookies',
        ]));

        $instanceCache = \Phpfastcache\CacheManager::getInstance('Files');

        $cachedString = $instanceCache->getItem($cacheKey);

        $instanceCache->save($cachedString);

        return view('welcome');
    }

    public function phpLogin() {
        $username = "smarts_hrm";
        $password = "smarts03";
        $useragent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36";
        $cookie=$username.".txt";

        @unlink(dirname(__FILE__)."/".$cookie);

        $url="https://www.instagram.com/accounts/login/?force_classic_login";

        $ch  = curl_init();

        $arrSetHeaders = array(
            "User-Agent: $useragent",
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: deflate, br',
            'Connection: keep-alive',
            'cache-control: max-age=0',
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $arrSetHeaders);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__)."/".$cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/".$cookie);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

        $page = curl_exec($ch);
        curl_close($ch);

// try to find the actual login form
        if (!preg_match('/<form data-encrypt method="POST" id="login-form" class="adjacent".*?<\/form>/is', $page, $form)) {
            die('Failed to find log in form!');
        }

        $form = $form[0];

// find the action of the login form
        if (!preg_match('/action="([^"]+)"/i', $form, $action)) {
            die('Failed to find login form url');
        }

        $url2 = $action[1]; // this is our new post url
// find all hidden fields which we need to send with our login, this includes security tokens
        $count = preg_match_all('/<input type="hidden"\s*name="([^"]*)"\s*value="([^"]*)"/i', $form, $hiddenFields);

        $postFields = array();

// turn the hidden fields into an array
        for ($i = 0; $i < $count; ++$i) {
            $postFields[$hiddenFields[1][$i]] = $hiddenFields[2][$i];
        }

// add our login values
        $postFields['username'] = $username;
        $postFields['password'] = $password;

        $post = '';

// convert to string, this won't work as an array, form will not accept multipart/form-data, only application/x-www-form-urlencoded
        foreach($postFields as $key => $value) {
            $post .= $key . '=' . urlencode($value) . '&';
        }

        $post = substr($post, 0, -1);

        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $page, $matches);

        $cookieFileContent = '';

        foreach($matches[1] as $item)
        {
            $cookieFileContent .= "$item; ";
        }

        $cookieFileContent = rtrim($cookieFileContent, '; ');
        $cookieFileContent = str_replace('sessionid=; ', '', $cookieFileContent);

        $oldContent = file_get_contents(dirname(__FILE__)."/".$cookie);
        $oldContArr = explode("\n", $oldContent);

        if(count($oldContArr))
        {
            foreach($oldContArr as $k => $line)
            {
                if(strstr($line, '# '))
                {
                    unset($oldContArr[$k]);
                }
            }

            $newContent = implode("\n", $oldContArr);
            $newContent = trim($newContent, "\n");

            file_put_contents(
                dirname(__FILE__)."/".$cookie,
                $newContent
            );
        }

        $arrSetHeaders = array(
            'origin: https://www.instagram.com',
            'authority: www.instagram.com',
            'upgrade-insecure-requests: 1',
            'Host: www.instagram.com',
            "User-Agent: $useragent",
            'content-type: application/x-www-form-urlencoded',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: deflate, br',
            "Referer: $url",
            "Cookie: $cookieFileContent",
            'Connection: keep-alive',
            'cache-control: max-age=0',
        );

        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_COOKIEJAR, dirname(__FILE__)."/".$cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, dirname(__FILE__)."/".$cookie);
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $arrSetHeaders);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        sleep(5);
        $page = curl_exec($ch);


        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $page, $matches);
        $cookies = array();
        foreach($matches[1] as $item) {
            parse_str($item, $cookie1);
            $cookies = array_merge($cookies, $cookie1);
        }
        var_dump($page);

        curl_close($ch);
    }

    public function login(){
        $instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'smarts_hrm', 'smarts03', new Psr16Adapter('Files'));
        $instagram->login();
        $info = $instagram->getAccountInfo('habibulloh2003');

        dd($info);
        /*$account = $instagram->getAccountById(2);
        echo $account->getUsername();*/
    }

    public function getPostInfo(){
        exec('php artisan insta:posts --username=khabib_nurmagomedov', $output);
        return $output;
        /*return \Modules\Instagram\Service\InstaService::login('smarts_hrm', 'smarts03');
        $inSer = new InstaPostsService();
        $links = $inSer->fillFromLink();
        $info = $inSer->instagram->instagram->getMediaByUrl('https://www.instagram.com/p/CozZYJvoltw');
        dd($info);
        $insta = new InstaService();
        $user_id = $insta->instagram->getAccountInfo('khabib_nurmagomedov')->getId();
        $medias = $insta->instagram->getMediasByUserId($user_id, 100);
        $info  = $insta->instagram->getMediaByUrl('https://www.instagram.com/p/Coq_qtBKXj3');
        $u_info = $insta->fillPostsTable('ibrohim__6171');
        dump($u_info);*/
    }

    public function deleteCache(){

    }
}
