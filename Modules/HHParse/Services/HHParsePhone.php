<?php

namespace Modules\HHParse\Services;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Modules\HHParse\Models\HhResume;
use Modules\HHParse\Models\HhUser;

class HHParsePhone
{
    const SERVER_URL = 'http://localhost:9515';
    const DEFAULT_PROFILE = 'Default';

    public function index() {
        $users = HhUser::all();
        foreach ($users as $user) {
            $chromeProfile = $user->chrome_profile ?? self::DEFAULT_PROFILE;
            $chromeOptions = new ChromeOptions();
            $chromeOptions->addArguments(["--user-data-dir=" . env('CHROME_USER_DATA') .'\\'. $chromeProfile]);
            $capabilities = DesiredCapabilities::chrome();
            $capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $chromeOptions);
            $driver = RemoteWebDriver::create(self::SERVER_URL, $capabilities);
            //$driver->get('https://tashkent.hh.uz/employer/archivedvacancies');
            $resume_links = HhResume::whereNull('contact_parsed')->get();
            foreach ($resume_links as $resume_link) {
                $driver->get($resume_link->resume_url);
                $driver->executeScript("if(document.querySelector('button[data-qa=\"response-resume_show-phone-number\"]')){document.querySelector('button[data-qa=\"response-resume_show-phone-number\"]').click()}", []);
                $phone = $driver->findElement(WebDriverBy::cssSelector('div[data-qa="resume-contacts-phone"] > span'))->getText();
                $email = $driver->findElement(WebDriverBy::cssSelector('div[data-qa="resume-contact-email"] > a > span'))->getText();
                $resume_link->phone = $phone;
                $resume_link->email = $email;
                $resume_link->contact_parsed = true;
                $resume_link->save();
            }
            $driver->quit();
        }
    }
}
