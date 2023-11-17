<?php

namespace Modules\Instagram\Console;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use InstagramScraper\Instagram;
use Modules\Instagram\Service\InstaLoginService;
use Modules\Instagram\Service\InstaService;
use Phpfastcache\CacheManager;
use Phpfastcache\Helper\Psr16Adapter;
use Symfony\Component\Console\Input\InputOption;

class LoginCommand extends Command
{

    protected $name = 'insta:login';

    protected $description = 'Command description.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {


        $username = $this->option('username');
        $password = $this->option('password');

        $insta_service = new InstaLoginService();
        $cache = $insta_service->login($username, $password);


        foreach ($cache->get(md5($username)) as $key => $value) {
            $this->output->write($key . ' = ' . $value);
            $this->output->newLine();
        }
    }

    protected function getOptions(): array
    {
        return [
            ['username', null, InputOption::VALUE_OPTIONAL, 'Username for Instagram.', null],
            ['password', null, InputOption::VALUE_OPTIONAL, 'Password for Instagram.', null],
        ];
    }

}
