<?php

namespace Modules\Instagram\Service;

use Modules\Instagram\Models\InstaJob;

class JobsService
{
    public function saveToDatabase($jobName, $instaUser, $instaBot, $type, $text, $count)
    {
        $insta_job = new InstaJob();
        !$jobName?: $insta_job->name = $jobName;
        !$instaUser?: $insta_job->insta_user_id = $instaUser;
        !$instaBot?: $insta_job->insta_bot_id = $instaBot;
        !$type?: $insta_job->type = $type;
        !$text?: $insta_job->text = $text;
        !$count?: $insta_job->count = $count;
        $insta_job->save();
    }

    public function runWithDatabase($job)
    {
        $model = InstaJob::find($job);
        $job = app()->make('\Modules\Instagram\Jobs\\'.$model->name, ['link' => "https://www.instagram.com/p/CteG4CBoAIF/", 'bot' => 21]);
        dispatch($job);
    }
}
