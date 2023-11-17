<?php

namespace Modules\Instagram\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Instagram\Service\StoryViewer;

class StoryViewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $bot;
    public $count;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($bot, $count)
    {
        $this->bot = $bot;
        $this->count = $count;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = new StoryViewer();
        $service->storyView($this->bot, $this->count);
    }
}
