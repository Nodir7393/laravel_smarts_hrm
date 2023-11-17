<?php

namespace Modules\Instagram\Console\InstaComments;

use Illuminate\Console\Command;
use Modules\Instagram\Service\InstaCommentService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class TagUserByLinkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'insta:tag-by-link {postlink}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

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
        $post_link = $this->argument('postlink');
        $instagram = new InstaCommentService();

        $text = $instagram->tagUser();
        $post_id = $instagram->instagram->instagram->getMediaByUrl($post_link)->getId();
        $instagram->addComment($post_id, $text);

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['postlink', InputArgument::REQUIRED, 'An example argument.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
