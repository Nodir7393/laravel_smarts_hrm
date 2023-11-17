<?php

namespace Modules\RemoveJoinLeave\Console;

use Illuminate\Console\Command;
use Modules\RemoveJoinLeave\Services\RemoveJoinLeaveService;

class RemoveJoinLeaveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:join';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handles and remove all join/leave messages';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $manage = new RemoveJoinLeaveService();
        $manage->console();
    }
}
