<?php

namespace Modules\Envato\Console;

use Illuminate\Console\Command;
use Modules\Envato\Services\ReplacerBot\ReplacerService;

class ReplacerCommand extends Command
{

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
     * The console command description.
     *
     * @var string
     */
    protected $signature = 'replacercommand:run {--channelId=} {--type=} {--search=} {--replace=} {--start=} {--end=}';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $replaceService = new ReplacerService(
            $this->option('channelId'),
            $this->option('search'),
            $this->option('type'),
            $this->option('replace')
        );

        $start = $this->option('start') ?? 1;
        $end = $this->option('end');

        $replaceService->editPosts($start, $end);
    }

}
