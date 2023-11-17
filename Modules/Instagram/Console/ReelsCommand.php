<?php

namespace Modules\Instagram\Console;

use Facebook\WebDriver\WebDriverBy;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Entities\InstaUser;
use Modules\Instagram\Service\InstaPostsService;
use Modules\Instagram\Service\InstaTagService;
use Modules\Instagram\Service\LoginService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ReelsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'insta:reels';

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
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
//        $service = new InstaPostsService();
//        $service->tagSearch('coffee', 21, 2);

        $service = new LoginService();
        $service->login(21);
        $html = $service->getHtml('https://www.instagram.com/explore/tags/coffee/');
//        $service->driver->executeScript('window.scrollTo(0, document.body.scrollHeight);');

        $dev_tools = $service->driver->getDevTools();
        $dev_tools->execute('Network.enable');
        preg_match_all('/(?<=X-IG-App-ID":")\d+/', $html, $pregAppId);
        $appId = $pregAppId[0][0];
        $coo = $service->driver->manage()->getCookies();
        $csrf = '';
        foreach ($coo as $item) {
            if ($item->getName() == 'csrftoken'){
                $csrf = $item->getValue();
            }
        }
        $dev_tools->execute('Network.setExtraHTTPHeaders', ['headers' => (object) [
            'X-IG-App-ID' => $appId,
            'Referer' => $service->driver->getCurrentURL(),
            'X-CSRFToken' => $csrf
        ]]);
        Log::debug($appId);
        $html = $service->getHtml('https://www.instagram.com/api/v1/tags/web_info/?tag_name=cristianoronaldo');
        sleep(2);
        $json = $service->driver->findElement(WebDriverBy::cssSelector('body > pre'))->getText();
        $arr = json_decode($json, true);
        $max_id = $arr['data']['recent']['next_max_id'];
        Log::debug($arr);
        $service->driver->executeScript("
        function post(path, params, method='post') {

          const form = document.createElement('form');
          form.method = method;
          form.action = path;

          for (const key in params) {
            if (params.hasOwnProperty(key)) {
              const hiddenField = document.createElement('input');
              hiddenField.type = 'hidden';
              hiddenField.name = key;
              hiddenField.value = params[key];

              form.appendChild(hiddenField);
            }
          }

          document.body.appendChild(form);
          form.submit();
        }

        post('/api/v1/tags/cristianoronaldo/sections/', [max_id => '".$max_id."', page => 1], 'post')
        return document.body;");
        sleep(20);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
//    protected function getArguments(): array
//    {
//        return [
//            ['example', InputArgument::REQUIRED, 'An example argument.'],
//        ];
//    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
        ];
    }
}
