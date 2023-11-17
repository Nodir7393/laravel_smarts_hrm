<?php

namespace Modules\Instagram\Service;

use DiDom\Document;
use DiDom\Exceptions\InvalidSelectorException;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Entities\InstaBot;
use Modules\Instagram\Entities\InstaUser;
use Modules\Instagram\Models\InstaFollower;
use Modules\Instagram\Models\InstaFollowing;

class ParseService
{
    const INSTAGRAM_COM = 'https://www.instagram.com';

    public $job_id;
    public $parsingUser;
    public $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    /**
     * @param string $username
     * @param string $password
     * @param string|null $login
     * @return void
     */
    public function createBot(string $username, string $password, ?string $login = null): void
    {
        $user = InstaBot::where('user_name', $username)->first();
        if (!$user) {
            $newUser = new InstaBot;
            $newUser->user_name = $username;
            $newUser->password = $password;
            $newUser->save();
            if ($login) {
                $this->loginService->login($newUser->id);
            }
        }
    }

    /**
     * @param string $username
     * @param string|null $full_name
     * @return void
     */
    public function addUser(string $username, ?string $full_name = null): void
    {
        $user = InstaUser::where('username', $username)->first();
        if (!$user) {
            $newUser = new InstaUser;
            $newUser->username = $username;
            $newUser->fullName = $full_name;
            $newUser->save();
        }
    }

    /**
     * @param array $user
     * @return void
     */
    public function setUser(array $user): void
    {
        Log::info('setUser $user =', $user);
        $username = $user['username'];
        $full_name = $user['full_name'];
        $userDb = InstaUser::where('username', $username)->first();
        if (!$userDb) {
            $newUser = new InstaUser;
            $newUser->username = $username;//
            $newUser->fullName = $full_name;//
            Log::info($user['is_private']);
            $newUser->is_private = $user['is_private'];//
            $newUser->is_verified = $user['is_verified'];//
            $newUser->profile_pic_url = $user['profile_pic_url'];//
            $newUser->fbid = $user['fbid_v2'];//
            $newUser->insta_id = $user['pk_id'];//
            $newUser->save();
        }
    }

    /**
     * @param string $username
     * @param int $id
     * @param int|null $job_id
     * @return void
     * @throws InvalidSelectorException
     */
    public function parseUser(string $username, int $id, ?int $job_id = null): void
    {
        $this->job_id = $job_id;
        $this->loginService->login($id);
        $this->parsingUser = InstaUser::where('username', $username)->first();
        Log::info('User: ' . $this->parsingUser->username);
        Log::info('User: ' . $this->parsingUser->id);
        if (!$this->parsingUser) {
            $this->parsingUser = new InstaUser;
        }
        $html = $this->loginService->getHtml(self::INSTAGRAM_COM . '/' . $username);
        $doc = new Document($html);
        $test = $doc->has('header li[class]');
        if ($test) {
            preg_match_all('/(?<=X-IG-App-ID":")\d+/', $html, $pregAppId);
            preg_match_all('/(?<="id":")\d+/', $html, $pregId);
            $appId = $pregAppId[0][0];
            $userId = $pregId[0][0];
            $this->parsingUser->insta_id = $userId;
            $this->parsingUser->app_id = $appId;
            $this->parsingUser->save();
            $this->getUserData($doc, (string)$appId, (string)$userId);
        }
        $this->loginService->driver->quit();
    }

    /**
     * @param Document $docZ
     * @param string $appId
     * @param string $userId
     * @return void
     * @throws InvalidSelectorException
     */
    public function getUserData(Document $doc, string $appId, string $userId): void
    {
        $post_count = $doc->find('header li[class]:nth-child(1)')[0]->text();
        $following_count = $doc->find('header li[class]:nth-child(3)')[0]->text();
        $followers_count = $doc->find('header li[class] span[title]')[0]->getAttribute('title');
        $followers_count = explode(',', $followers_count);
        $followers = '';
        foreach ($followers_count as $follow) {
            $followers .= $follow;
        }
        $user_data = [
            'posts' => Arr::get(explode(' ', $post_count), '0'),
            'following' => Arr::get(explode(' ', $following_count), '0'),
            'followers' => $followers
        ];

        $devTools = $this->loginService->driver->getDevTools();
        $devTools->execute('Network.enable');
        foreach ($user_data as $type => $data) {
            if ($type == 'posts') {continue;}

            $count = config('instagram.user_count');
            Log::info($data);
            for ($i = 0; $i < $data; $i += $count) {
                $devTools->execute('Network.setExtraHTTPHeaders', ['headers' => (object)[
                    'X-IG-App-ID' => $appId,
                    'Referer' => self::INSTAGRAM_COM . '/' . $this->parsingUser->username . '/' . $type . '/']]);
                $query = self::INSTAGRAM_COM . "/api/v1/friendships/$userId/$type/?count=$count&max_id=$i";
                $consol = $this->loginService->driver->get($query);
                $consol = $consol->findElement(WebDriverBy::cssSelector('body pre'))->getText();
                $json = json_decode($consol, true);
                Log::info('$json ' . $consol);
                $i = array_key_exists('next_max_id', $json) ? (int)$json['next_max_id'] : $data;
                if (array_key_exists('user', $json)) {
                    $this->setUserData($json, $type);
                } else {
                    Log::info("setUserData = users not found");
                }
            }
        }
    }

    /**
     * @param array $json
     * @param string $type
     * @return void
     */
    public function setUserData(array $json, string $type): void
    {
        $users = Arr::get($json, 'users');
        foreach ($users as $user) {
            $this->setUser($user);

            // addFollow = following and followers
            $this->addFollow($user, $type);
        }
    }

    /**
     * @param array $user
     * @param string $type
     * @return void
     */
    public function addFollow(array $user, string $type): void
    {
        switch (true) {
            case ($type === 'following');
                $follow_user = InstaFollowing::select('id')->where('following_user_id', $user['pk_id'])->first();
                $follow = new InstaFollowing;
                break;
            case ($type === 'followers');
                $follow_user = InstaFollower::select('id')->where('following_user_id', $user['pk_id'])->first();
                $follow = new InstaFollower;
                break;
            default;
                $follow = '';
                $follow_user = true;
                break;
        }
        if (!$follow_user) {
            $follow->username = $user['username'];
            $follow->fullName = $user['full_name'];
            $follow->following_user_id = $user['pk_id'];
            $follow->insta_job_id = $this->job_id;
            $follow->insta_bot_id = $this->loginService->user->id;
            $follow->insta_user_id = $this->parsingUser->insta_id;
            $follow->save();
            Log::info("$type added " . $follow->username);
        }
    }
}
