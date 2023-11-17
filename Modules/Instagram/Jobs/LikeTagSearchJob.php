<?php

namespace Modules\Instagram\Jobs;

use App\Models\FailedJob;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Models\InstaJob;
use Modules\Instagram\Service\InstaPostsService;

class LikeTagSearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $tag;
    public $bot;
    public $num;
    public function __construct($tag, $bot, $num)
    {
        $this->tag = $tag;
        $this->bot = $bot;
        $this->num = $num;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(InstaPostsService $service)
    {
        $instaJob = new InstaJob;
        $instaJob->save();
        $instaJob->uuid = $this->job->uuid();
        $instaJob->job_id = $this->job->getJobId();
        $instaJob->insta_bot_id = $this->bot;
        $instaJob->status = 'working';
        $service->likeTagSearch($this->tag, $this->bot, $this->num);
        $failed = $this->job->hasFailed();
        if ($failed){
            $instaJob->status = 'failed';
            $failedJob = FailedJob::where('uuid', $instaJob->uuid)->first()->value('id');
            Log::info($failedJob);
            $instaJob->failed_jobs_id = $failedJob;
        } else {$instaJob->status = 'completed';}
        $instaJob->save();
    }
}
