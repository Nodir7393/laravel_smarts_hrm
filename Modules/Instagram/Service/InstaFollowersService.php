<?php

namespace Modules\Instagram\Service;

use Modules\Instagram\Entities\InstaFollowers;

class InstaFollowersService
{
    public InstaService $instagram;

    public function __construct()
    {
        $this->instagram = new InstaService();
    }

    public function get($username)
    {
        $account = $this->instagram->instagram->getAccountInfo($username);
        $followers = $this->instagram->instagram->getFollowers($account->getId(), 1000, 10, true);
        foreach ($followers as $follower) {
            InstaFollowers::where('insta_id', $follower['id'])->firstOr(function () use ($follower) {
                $follower_info = $this->instagram->instagram->getAccountInfo($follower['username']);
                $acc = $this->instagram->validate($follower_info);
                return  InstaFollowers::create($acc);
            });
        }
    }
}
