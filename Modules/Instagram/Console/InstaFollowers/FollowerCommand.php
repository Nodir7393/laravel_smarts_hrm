<?php

namespace Modules\Instagram\Console\InstaFollowers;

use Illuminate\Console\Command;
use Modules\Instagram\Entities\InstaFollowers;
use Modules\Instagram\Service\InstaFollowersService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class FollowerCommand extends Command
{
    protected $name = 'insta:followers-get';

    protected $description = 'Command description.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $followers = new InstaFollowersService();
        $followers->get($this->option('username'));
    }
    protected function getOptions(): array
    {
        return [
            ['username', null, InputOption::VALUE_REQUIRED, 'Instagram username (required).', null],
        ];
    }
}
