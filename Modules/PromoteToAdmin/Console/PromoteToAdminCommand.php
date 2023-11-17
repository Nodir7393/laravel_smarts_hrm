<?php

namespace Modules\PromoteToAdmin\Console;

use Illuminate\Console\Command;
use Modules\PromoteToAdmin\Services\PromoteToAdminService;

class PromoteToAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:promote-to-admin {--chat_id=} {--user_id=} {--permissions=}';

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
        $chat_id = $this->option('chat_id');
        $user_id = $this->option('user_id');
        $permissions = $this->option('permissions');
        $toAdminServise = new PromoteToAdminService();
        $toAdminServise->promoteToAdmin($chat_id, $user_id, $permissions);
    }
}
