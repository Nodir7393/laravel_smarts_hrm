<?php
declare(strict_types=1);

namespace Modules\Project\Console\Database;

use Illuminate\Console\Command;
use Modules\Project\Services\DownloadService;
use Symfony\Component\Console\Input\InputOption;

class DataBaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'database:download {--channelId=} {--start=} {--end=}';

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
     *
     * Function  handle
     * @param DownloadService $downloadService
     */
    public function handle(DownloadService $downloadService)
    {
        $channel_id = $this->option('channelId');
        $start = $this->option('start');
        $the_end = $this->option('end');
        $downloadService->collector((int)$channel_id, (string)$start, (string)$the_end);
    }


    /**
     *
     * Function  getOptions
     * @return  array[]
     */
    protected function getOptions(): array
    {
        return [
            ['channelId', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
            ['start', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
            ['end', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
