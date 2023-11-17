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

class TagSearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tag;
    public $bot;
    public $number;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tag, $bot, $number)
    {
        $this->tag = $tag;
        $this->bot = $bot;
        $this->number = $number;
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
        $service->tagSearch($this->tag, $this->bot, $this->number);
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
