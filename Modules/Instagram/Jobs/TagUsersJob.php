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
use Modules\Instagram\Service\InstaTagService;

class TagUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $bot;
    public $link;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bot, $link)
    {
        $this->bot = $bot;
        $this->link = $link;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(InstaTagService $service)
    {
        $instaJob = new InstaJob;
        $instaJob->save();
        $instaJob->uuid = $this->job->uuid();
        $instaJob->job_id = $this->job->getJobId();
        $instaJob->insta_bot_id = $this->bot;
        $instaJob->status = 'working';
        $service->tagUsers($this->bot, $this->link);
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
