<?php

namespace Modules\Instagram\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Instagram\Models\FailedJob;
use Modules\Instagram\Models\InstaJob;
use Modules\Instagram\Service\InstaPostsService;

class LikePostsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $bot;
    private $post;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bot, $posts)
    {
        $this->bot = $bot;
        $this->post = $posts;
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
        $instaJob = $service->likeThisPost($this->bot, $this->post, $jobId, $name, $uuId, $instaJob);

        $instaJob->status = 'Completed';
        if ($this->job->hasFailed()){
            $instaJob->status = 'Failed';
        }
        $instaJob->save();
    }
}
