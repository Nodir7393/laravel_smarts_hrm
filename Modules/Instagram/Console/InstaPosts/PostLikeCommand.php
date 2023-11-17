<?php

namespace Modules\Instagram\Console\InstaPosts;

use Illuminate\Console\Command;
use Modules\Instagram\Service\InstaPostsService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class PostLikeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'insta:post-like {--post-link}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Do like to instagram post';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $post_link = $this->option('post-link');
        $post_service = new InstaPostsService();
        $post_service->likePost($post_link);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['post-link', null, InputOption::VALUE_REQUIRED, 'Post link to like', null],
        ];
    }
}
