<?php

namespace Modules\Camera\Console;

use Illuminate\Console\Command;
use Modules\Camera\Service\NutgramService;

class XiaomiBotCommand extends Command
{
    protected $name = 'camera:all';
    protected $description = 'Command description.';
    protected NutgramService $services;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    /*public function __construct()
    {
        $this->services = new NutgramService();
        parent::__construct();
    }*/

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $cameras = $this->services->getCameraList();
        $this->withProgressBar($cameras, function($camera){
            $this->services->getActualData($camera, $this->output);
        });
    }
}
