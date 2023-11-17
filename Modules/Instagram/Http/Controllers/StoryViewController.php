<?php

namespace Modules\Instagram\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Instagram\Entities\InstaBot;
use Modules\Instagram\Jobs\StoryViewJob;

class StoryViewController extends Controller
{
    public function storyview()
    {
        $bots = InstaBot::pluck('user_name', 'id');
        return view('instagram::storyview', ['bots' => $bots]);
    }

    public function storyviewPost(Request $request)
    {
        $bot = $request['bot'];
        $count = $request['number'];
        StoryViewJob::dispatch($bot, $count);
        return redirect('admin/insta-followers/datatable');
    }
}
