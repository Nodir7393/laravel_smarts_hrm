<?php

namespace Modules\Project\Console;

use Illuminate\Console\Command;
use Modules\Project\Services\NewMessages;
use danog\MadelineProto\Settings;

class TaskList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'taskList:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $settings = $settings = new \danog\MadelineProto\Settings\Database\Memory;
        NewMessages::startAndLoop(env('EVENT_HANDLER'), $settings);
    }
}
