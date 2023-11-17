<?php

namespace Modules\Instagram\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Instagram\Entities\InstaBot;
use Modules\Instagram\Jobs\FollowJob;
use Modules\Instagram\Jobs\UnfollowJob;

class FollowJobController extends Controller
{
    public function follow()
    {
        $bots = InstaBot::pluck('user_name', 'id');
        return view('instagram::follow', ['bots' => $bots]);
    }

    public function followPost(Request $reqeust)
    {
        $count = $reqeust['number'];
        $bot = $reqeust['bot'];
        FollowJob::dispatch($bot,$count);
        return redirect('admin/insta-followers/datatable');
    }
    public function unfollow()
    {
        $bots = InstaBot::pluck('user_name', 'id');
        return view('instagram::unfollow', ['bots' => $bots]);
    }

    public function unfollowPost(Request $reqeust)
    {
        $count = $reqeust['number'];
        $bot = $reqeust['bot'];
        UnfollowJob::dispatch($bot,$count);
        return redirect('admin/insta-followers/datatable');
    }
}
