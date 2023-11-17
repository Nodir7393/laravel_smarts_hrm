<?php

namespace Modules\Sync\Console;

use Illuminate\Console\Command;
use Modules\Sync\Services\SyncService;
use Symfony\Component\Console\Input\InputOption;

class SyncCommand extends Command
{
    protected $name = 'sync:sync {--path=}';

    protected $description = 'Passes an option "path" and syncs files including sub folders';

    protected SyncService $sync;

    public function __construct(){
        parent::__construct();
    }

    public function handle()
    {
        $this->sync = new SyncService();
        if (empty($this->option('path'))) {
            $this->output->error('Path is required');
            return;
        }

        $path = $this->option('path');
        $this->sync->synchronize($path, $this->output);
    }

    public function getOptions(): array
    {
        return [
            ['path', null, InputOption::VALUE_OPTIONAL, 'Target path to sync in local storage.', null]
        ];
    }

}
