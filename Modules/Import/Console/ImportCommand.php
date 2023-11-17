<?php

namespace Modules\Import\Console;

use Illuminate\Console\Command;
use Modules\Import\Service\ImportService;
use Symfony\Component\Console\Input\InputOption;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'import:msg';
    protected $description = 'Imports messages with comments in it';
    private ImportService $import;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->import = new ImportService();
        $channelId = $this->option('channelId');
        $this->import->getChannelMessages($channelId, $this->output);
    }


    protected function getOptions(): array
    {
        return [
            ['channelId', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
