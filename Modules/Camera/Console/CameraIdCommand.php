<?php

namespace Modules\Camera\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Camera\Service\NutgramService;
use Symfony\Component\Console\Input\InputArgument;


class CameraIdCommand extends Command
{
    protected $name = 'xiaomi:id {id}';
    protected $description = 'xiaomi:bot dan so\'ng bo\'sh joy tashlab office idsini kiriting';
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
     *
     * @return void
     */
    public function handle(): void
    {
        $id = $this->argument('id');
        $services = new NutgramService();
        $cameras = $services->getOfficeCameras((int)$id);
        foreach ($cameras as $camera) {
            Log::info('Camera: ' . $camera->title);
            Log::info(PHP_EOL);
            $services->getActualData($camera, $this->output);
        }
    }

    public function getArguments()
    {
        return [
            ['id', InputArgument::REQUIRED, 'An example argument.'],
            ];
    }

}
