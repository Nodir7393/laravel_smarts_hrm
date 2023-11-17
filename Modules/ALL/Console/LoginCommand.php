<?php

namespace Modules\ALL\Console;

use Illuminate\Console\Command;
use Modules\ALL\Services\LoginService;

class LoginCommand extends Command
{
    protected $name = 'proto:login';
    protected $description = 'Login to MadelineProto.';
    protected LoginService $mtproto;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->mtproto = new LoginService();
        $this->mtproto->madelineproto->start();
        $this->mtproto->madelineproto->stop();
    }

}
