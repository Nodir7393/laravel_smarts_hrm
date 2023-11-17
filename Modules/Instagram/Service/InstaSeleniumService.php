<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace Modules\Instagram\Service;


use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class InstaSeleniumService
{
    public $driver;

    public const SERVER_URL = 'http://localhost:9515';

    public function runDriver(){
        $chromeOptions = new ChromeOptions();
        $chromeOptions->addArguments(["--user-data-dir=" . env('CHROME_USER_DATA'), "profile-directory=Profile 1"]);
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $chromeOptions);
        $driver = RemoteWebDriver::create(self::SERVER_URL, $capabilities);
        $this->driver = $driver;
    }

    public function getHtml(string $url){
        return $this->driver->get($url);
    }

    public function instaLogin(){
        $this->runDriver();
        $this->getHtml('https://instagram.com');
        sleep(3);
        $this->driver->findElement(WebDriverBy::name('username'))
        ->sendKeys('smarts_hrm');
        $this->driver->findElement(WebDriverBy::name('password'))
        ->sendKeys('password')
        ->submit();
    }
}
