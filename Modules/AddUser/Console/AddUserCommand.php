<?php

namespace Modules\AddUser\Console;

use Illuminate\Console\Command;
use Modules\AddUser\Services\AddUserService;
use function Symfony\Component\String\u;

/**
 * Class    AddUserCommand
 * @package App\Console\Commands
 */
class AddUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:add {--chat_id=} {--user_id=} {--permissions=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @void
     */
    public function handle(): void
    {
        $chat_id = $this->option('chat_id');
        $user_id = $this->option('user_id');
        $permissions = $this->option('permissions');
        $addUserService = new AddUserService();
        /** @var $addUserService kanal yoki gruppaga odam qo'shadigan servis */
        $addUserService->addUser($chat_id, $user_id, $permissions);
        /** @void odam qo'shadigan servissi metodi */

    }
}
