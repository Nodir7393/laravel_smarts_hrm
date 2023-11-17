<?php

namespace Modules\HHParse\Console;

use Illuminate\Console\Command;
use Modules\HHParse\Services\HHResumesDownloader;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class HHResumesDownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'run:resumes-downloader';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs ResumesDownloader.';

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
     * @return void
     */
    public function handle() :void
    {
        $downloader = new HHResumesDownloader();

        $downloader->download();

        var_dump('It is done');
    }
}
