<?php

namespace Modules\Instagram\Service;

use App\Imports\InstaUserImport;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Instagram\Entities\InstaBot;
use Modules\Instagram\Entities\InstaUser;

/**
 * Class    InstaTagService
 * @package Modules\Instagram\Service
 */
class InstaTagService
{
    const INSTA_URL = 'https://www.instagram.com';
    /**
     * @var InstaService
     */
    public $instagram;

    /**
     *
     */
    public function __construct()
    {
//        $this->instagram = new InstaService();
    }

    /**
     *
     * Function  get
     * @param $username
     * @throws \InstagramScraper\Exception\InstagramException
     */
    public function get($username)
    {
        $tags = $this->instagram->instagram->getUserTags($username, 100)['items'];
    }

    /**
     *
     * Function  importFromCsv
     * @param string $file
     * @return  array
     */
    public function importFromCsv(string $file): array
    {
        $users = [];

        if (($open = fopen(storage_path('app/') . $file, "r")) !== FALSE) {

            while (([$data] = fgetcsv($open, 1000, ",")) !== FALSE) {
                $user = InstaUser::firstOrCreate(
                    ["username" => $data],
                    ["username" => $data]
                );
                $users[] = $user;
            }

            fclose($open);
            return $users;
        }
    }

    public function importFromTxt(string $file): array
    {
        $filename = storage_path('app/') . '/' . $file;
        $user_names = file($filename, FILE_IGNORE_NEW_LINES);

        foreach ($user_names as $user_name) {
            $user = InstaUser::firstOrCreate(
                ["username" => $user_name],
                ["username" => $user_name]
            );
            $users[] = $user;
        }

        return $users;
    }


    public function excelImport($file){
        Excel::import(new InstaUserImport, $file);
    }

    public function tagUsers($bot, $link)
    {
        $service = new LoginService();
        $service->user = InstaBot::find($bot);
        $service->login($bot);
        sleep(5);

        $rowCount = InstaUser::count();
        for ($i = 0; $i < $rowCount; $i+=10){
            $data = InstaUser::skip($i)->take(10)->get();
            $usernames = "";
            foreach ($data as $datum) {
                $usernames = $usernames."@".$datum->username." _ ";
            }
            $service->getHtml($link);
            $service->driver->findElement(WebDriverBy::cssSelector('._akhn > textarea'))->sendKeys($usernames);
            $service->driver->findElement(WebDriverBy::cssSelector('._akhn > div[class=""] > div'))->click();
            sleep(10);
        }



    }
}
