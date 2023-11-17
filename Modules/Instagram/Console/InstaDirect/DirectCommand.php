<?php

namespace Modules\Instagram\Console\InstaDirect;

use Illuminate\Console\Command;
use Modules\Instagram\Service\InstaDirectService;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class DirectCommand extends Command
{
    protected $name = 'insta:direct';

    protected $description = 'Command description.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $direct = new InstaDirectService();
        $direct->get();
    }
}
