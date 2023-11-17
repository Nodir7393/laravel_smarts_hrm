<?php

namespace Modules\Export\Console;

use Illuminate\Console\Command;
use Modules\Export\Service\ExportService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'command:export {--channelid=} {--startdate=} {--enddate=} {--path=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export Command Channel ID Start Date End Date and Path.';

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
     * @return int
     */
    public function handle()
    {
        $export = new ExportService();
        $channel_id = $this->option('channelid') ?? readline('Enter channel_id: ');
        $date_start = $this->option('startdate') ?? readline('Enter start date: ');
        $date_end   = $this->option('enddate')   ?? date("d.m.Y");
        $path       = $this->option('path')      ?? setting('file-system.tg_export');

        $export->command($channel_id, $date_start, $date_end, $this->output, $path);
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['channelid', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
            ['startdate', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
            ['enddate', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
            ['path', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
