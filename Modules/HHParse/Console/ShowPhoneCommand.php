<?php

namespace Modules\HHParse\Console;

use Illuminate\Console\Command;
use Modules\HHParse\Services\HHParsePhone;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ShowPhoneCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'hh:phone';

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
    /*public function __construct()
    {
        parent::__construct();
    }*/

    /**
     * Execute the console command.
     *
     */
    public function handle(HHParsePhone $HHParsePhone)
    {
        $HHParsePhone->index();
    }
}
