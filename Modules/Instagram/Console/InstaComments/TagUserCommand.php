<?php

namespace Modules\Instagram\Console\InstaComments;

use Illuminate\Console\Command;
use Modules\Instagram\Service\InstaCommentService;
use Modules\Instagram\Service\InstaService;
use Symfony\Component\Console\Input\InputOption;

class TagUserCommand extends Command
{
    protected $name = 'insta:comment-tag {--postid=}';

    protected $description = 'Command description.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $postId = $this->option('postid');
        $instagram = new InstaCommentService();
        $text = $instagram->tagUser();
        $instagram->addComment($postId, $text);
    }

    protected function getOptions()
    {
        return [
            ['postid', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
