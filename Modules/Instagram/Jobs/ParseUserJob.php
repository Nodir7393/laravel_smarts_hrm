<?php

namespace Modules\Instagram\Jobs;

use App\Models\FailedJob;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Models\InstaJob;
use Modules\Instagram\Service\ParseService;

class ParseUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    protected $user_name;

    protected $id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $id)
    {
        $this->user_id = Arr::get($user, '0');
        $this->user_name = Arr::get($user, '1');
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ParseService $parseService)
    {
        $instaJob = new InstaJob;
        $instaJob->name = 'parse user';
        $instaJob->save();
        $instaJob->uuid = $this->job->uuid();
        $instaJob->job_id = $this->job->getJobId();
        $instaJob->insta_bot_id = $this->id;
        $instaJob->insta_user_id = $this->user_id;
        $instaJob->status = 'working';
        $parseService->parseUser($this->user_name, (int)$this->id, (int)$instaJob->id);
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
