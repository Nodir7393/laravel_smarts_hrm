<?php

namespace Modules\Instagram\Console\InstaPosts;

use Illuminate\Console\Command;
use Modules\Instagram\Service\InstaPostsService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class InstaPostsCommand extends Command
{
    protected $name = 'insta:posts {--username=}';


    protected $description = 'Instagram posts command';

    public function __construct()
    {
        parent::__construct();
    }


    public function handle(): void
    {
        $post = new InstaPostsService();
        $post->get($this->option('username'));
    }

    public function getOptions()
    {
        return [
            ['username', null, InputOption::VALUE_OPTIONAL, 'Username for Instagram.', null],
        ];
    }

}
