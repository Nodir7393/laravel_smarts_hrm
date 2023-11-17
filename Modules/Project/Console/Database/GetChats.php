<?php

namespace Modules\Project\Console\Database;

use Illuminate\Console\Command;
use Modules\Project\Services\ChatService;

class GetChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chats:run {--chatId=}';

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
    public function handle(ChatService $chatService)
    {
        if ($this->option('chatId')) {
            $chatService->getMessages($this->option('chatId'), 'Modules\TgGroupText');
        } else {
            $chatService->getAllMessages();
        }
    }
}
