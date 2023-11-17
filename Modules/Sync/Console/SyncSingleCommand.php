<?php

namespace Modules\Sync\Console;

use Illuminate\Console\Command;
use Modules\Sync\Services\SyncService;
use Symfony\Component\Console\Input\InputOption;

class SyncSingleCommand extends Command
{
    protected $name = 'sync:single {--path=} {--tg=}';

    protected $description = 'Passes path or tg or both together and syncs files in path to tg.';

    protected SyncService $sync;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->sync = new SyncService();
        $path = $this->option('path');
        $tg = $this->option('tg');
        switch (true) {
            case !empty($tg):
                if (empty($path)) {
                    $this->sync->sync(null, $this->output, $tg);
                } else {
                    $this->sync->sync($path, $this->output, $tg);
                }
                break;
            default:
                if (!empty($this->option('path'))) {
                    $this->sync->sync($path, $this->output);
                }
                break;
        }
    }

    public function getOptions(): array
    {
        return [
            ['path', null, InputOption::VALUE_OPTIONAL, 'Target path to sync in local storage.', null],
            ['tg', null, InputOption::VALUE_OPTIONAL, 'Target post link to sync in specific tg channel.', null]
        ];
    }

}
