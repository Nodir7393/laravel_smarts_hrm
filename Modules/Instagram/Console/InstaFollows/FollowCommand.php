<?php

namespace Modules\Instagram\Console\InstaFollows;

use Illuminate\Console\Command;
use Modules\Instagram\Service\InstaFollowersService;
use Modules\Instagram\Service\InstaFollowService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class FollowCommand extends Command
{

    protected $name = 'insta:follows-get';

    protected $description = 'Command description.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $follows = new InstaFollowService();
        $follows->get($this->option('username'));
    }

    protected function getOptions(): array
    {
        return [
            ['username', null, InputOption::VALUE_REQUIRED, 'Username for Instagram.', null],
        ];
    }
}
