<?php

namespace Modules\Instagram\Service;

use Illuminate\Support\Facades\Artisan;
use Modules\Instagram\Entities\InstaFollow;

class InstaFollowService
{
    public InstaService $instagram;

    public function __construct()
    {
        $this->instagram = new InstaService();
//        $this->instagram->login('smarts_hrm', 'instalogin');
    }

    public function get($username)
    {
        $account = $this->instagram->instagram->getAccountInfo($username);
        $follows = $this->instagram->instagram->getFollowing($account->getId(), 1000, 1000, true);
        foreach ($follows['accounts'] as $follow) {
            InstaFollow::where('insta_id', $follow['id'])->firstOr(function () use ($follow) {
                $follow_info = $this->instagram->instagram->getAccountInfo($follow['username']);
                $acc = $this->instagram->validate($follow_info);
                return InstaFollow::create($acc);
            });
        }
    }

    public function follow($username)
    {
        $user_id = $this->instagram->instagram->getAccountInfo($username)->getId();
        $this->instagram->instagram->follow((string)$user_id);
    }

    public function unFollow($username)
    {
        sleep(2);
        $user_id = $this->instagram->instagram->getAccountInfo($username)->getId();
        return Artisan::call('insta:unfollowing', ['username' => $username]);
        $this->instagram->instagram->unfollow($user_id);
    }

    public function followTags($tag = 'php', $count = 10)
    {
        $medias = $this->instagram->instagram->getMediasByTag($tag, $count);
        foreach ($medias as $media) {
            $user_name = $media->getOwner()->getUsername();
            $this->follow($user_name);
        }
    }

    public function getUserFollowers($username)
    {
        $account = $this->instagram->instagram->getAccountInfo($username);
        return $this->instagram->instagram->getFollowers($account->getId(), 1000, 10);
    }

    public function getUserFollows($username)
    {
        $account = $this->instagram->instagram->getAccountInfo($username);
        return $this->instagram->instagram->getFollowing($account->getId(), 1000);
    }

    public function followUserFollowers($username)
    {
        $followers = $this->getUserFollowers($username);

        foreach ($followers as $follower) {
            $this->follow($follower['username']);
        }
    }

    public function followUserFollows($username)
    {
        $follows = $this->getUserFollows($username);

        foreach ($follows['accounts'] as $follow) {
            sleep(random_int(2, 5));
            $this->follow($follow['username']);
        }
    }

    public function unFollowUserFollowers($username)
    {
        $followers = $this->getUserFollowers($username);

        foreach ($followers as $follower) {
            $this->unFollow($follower['username']);
        }
    }

    public function unFollowUserFollows($username)
    {
        $follows = $this->getUserFollows($username);
        foreach ($follows['accounts'] as $follow) {
            try {

                file_put_contents('getusername.json', $follow['username'] . ",\n", FILE_APPEND);
                $this->unFollow($follow['username']);
            } catch (\Exception $e) {
                dump($e->getMessage());
            }
        }
    }

    public function searchLocation($link)
    {
        $i = $this->instagram->instagram->getMediaByUrl($link);
        $lc = $this->instagram->instagram->getMediasByLocationId($i->getLocationId(), 5);
        echo count($lc) . "<br />";
        foreach ($lc as $item) {
            dump($item->getLink());
        }

        return $lc;
    }
}
