<?php

namespace Modules\Instagram\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Instagram\Entities\InstaBot;
use Modules\Instagram\Entities\InstaUser;
use Modules\Instagram\Models\InstaJob;
use Modules\Instagram\Service\JobsService;

class JobsController extends Controller
{
    public function create()
    {
        $jobs = InstaJob::all();
        $bots = InstaBot::all();
        $users = InstaUser::all();
        return view('instagram::jobs', ['jobs' => $jobs, 'bots' => $bots, 'users' => $users]);
    }

    public function selectJob(Request $req)
    {
        $job = $req['job'];
        $service = new JobsService();
        $service->runWithDatabase($job);

        return redirect()->back();
    }

    public function createJob(Request $req)
    {
        $jobName = $req['job_name'];
        $instaUser = $req['insta_user_id'];
        $instaBot = $req['insta_bot_id'];
        $type = $req['type'];
        $text = $req['text'];
        $count = $req['count'];
        $service = new JobsService();
        $service->saveToDatabase($jobName, $instaUser, $instaBot, $type, $text, $count);
        return redirect()->back();
    }
}
