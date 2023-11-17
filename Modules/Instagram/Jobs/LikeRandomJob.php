<?php

namespace Modules\Instagram\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Instagram\Service\LikeCommentService;

class LikeRandomJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $bot;
    public $num;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bot, $num)
    {
        $this->bot = $bot;
        $this->num = $num;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(LikeCommentService $service)
    {
        $service->like($this->bot, $this->num);
    }
}
