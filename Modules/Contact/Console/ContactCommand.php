<?php

namespace Modules\Contact\Console;

use Illuminate\Console\Command;
use Modules\Contact\Service\ContactsService;
use Symfony\Component\Console\Input\InputOption;

class ContactCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'contacts:get';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $contactsService = new ContactsService(); /** @var  $dialogs */
        $dialogs = $contactsService->getDialogs();
        $chats_info = $contactsService->getChatInfo((array)$dialogs);
        $contactsService->storeChatIdToDb($chats_info);
    }


    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
