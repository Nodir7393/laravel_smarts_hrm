<?php

namespace Modules\Instagram\Console\InstaComments;

use Illuminate\Console\Command;
use Modules\Instagram\Service\InstaCommentService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class AddCommentCommand extends Command
{
    protected $name = 'insta:comment-add';

    protected $description = 'Command description.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $comment = new InstaCommentService();
        $comment->addComment($this->option('postid'), $this->option('comment'));
    }

    protected function getOptions()
    {
        return [
            ['postid', null, InputOption::VALUE_REQUIRED, 'Post ID to comment.', null],
            ['comment', null, InputOption::VALUE_REQUIRED, 'Text to comment.', null],
        ];
    }
}
