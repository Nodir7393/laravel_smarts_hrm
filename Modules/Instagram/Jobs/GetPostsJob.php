<?php

namespace Modules\Instagram\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Instagram\Models\InstaJob;
use Modules\Instagram\Service\InstaPostsService;

class GetPostsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $bot;
    public $user;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bot, $user)
    {
        $this->user = $user;
        $this->bot = $bot;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $jobId = $this->job->getJobId();
        $name = $this->job->getName();
        $uuId = $this->job->uuid();
        $instaJob = new InstaJob();
        $instaJob->status = 'running';

        $service = new InstaPostsService();
        $service->getPost($this->user, $this->bot, $uuId, $jobId, $name, $instaJob);

        $instaJob->status = 'Completed';
        if ($this->job->hasFailed()){
            $instaJob->status = 'Failed';
        }
        $instaJob->save();
    }
}
