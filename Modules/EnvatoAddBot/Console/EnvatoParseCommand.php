<?php

namespace Modules\EnvatoAddBot\Console;

use Illuminate\Console\Command;
use Modules\EnvatoAddBot\Services\EnvatoBot;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class EnvatoParseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'envato:parse {--url=} {--count=} {--hashtag=} {--edit=}';

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
    public function handle(EnvatoBot $envatoBot)
    {
        $url = $this->option('url');
        $count = $this->option('count') ?? 1;
        $hashtags = explode("|",$this->option('hashtag'));

        $edit = $this->option('edit') ?? false;

        for($i = 1; $i <= $count; $i++) {
            $envatoBot->hashtags = $hashtags;
            $envatoBot->edit = $edit;

            $envatoBot->envatoParse($url);

        }
    }

    private function hashtag($hashtag) {
        $hashtags = explode("|", $hashtag);
        return "\n\n" . implode("\n", $hashtags);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['url', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
            ['count', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
            ['hashtag', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
            ['edit', null, InputOption::VALUE_OPTIONAL, 'An example option.', null]
        ];
    }
}
