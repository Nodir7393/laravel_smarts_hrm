<?php

namespace Modules\AddBot\Console;

use App\Services\ALL\MTProtoService;
use Illuminate\Console\Command;

class AddBotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:add';

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
        $mtp = new MTProtoService();
        $message = $mtp->MadelineProto->channels->getMessages(["channel" =>-1001715385949, "id" => [570]]);
        dd($message);
    }
}
