<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Instagram\Service\SearchService;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(SearchService $searchService)
    {
        $searchService->search('test', 21);
    }
}
