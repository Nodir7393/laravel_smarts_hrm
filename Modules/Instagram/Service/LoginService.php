<?php

namespace Modules\Instagram\Service;

use DiDom\Document;
use Facebook\WebDriver\Chrome\ChromeDevToolsDriver;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Entities\InstaBot;
use Modules\Instagram\Entities\InstaUser;
use Modules\Instagram\Models\InstaFollowing;

class LoginService
{
    const INSTAGRAM_COM = 'https://www.instagram.com';

    public $user;
    public RemoteWebDriver $driver;

    public function login($userId): void
    {
        $this->user = InstaBot::find($userId);
        $this->runDriver();
        $this->fillForm();
        $this->user->logined = true;
        sleep(10);
        $this->user->save();
    }

    public function fillForm(): void
    {
        $html = $this->getHtml(self::INSTAGRAM_COM);
        $doc = new Document($html);
        $loginBtn = $doc->has('button[type="submit"]');
        if ($loginBtn) {
            Log::debug("login \$loginBtn = true");
            //$this->driver->executeScript(self::WITH_PASSWORD, []);
            $this->driver->findElement(WebDriverBy::cssSelector('input[name="username"]'))->clear()->sendKeys($this->user->user_name);
            $this->driver->findElement(WebDriverBy::cssSelector('input[name="password"]'))->clear()->sendKeys($this->user->password);
            $this->driver->findElement(WebDriverBy::cssSelector('button[type="submit"]'))->click();
        }
    }

    public function getHtml($url) {
        $this->driver->get($url);
        sleep(2);
        return $this->driver->getPageSource();
    }

    public function runDriver(): void
    {
        if (!empty($this->user)) {
            $id = $this->user->id;
            Log::debug("runDriver \$id = $id");
            $chromeOptions = new ChromeOptions();
            $chromeOptions->addArguments(["--user-data-dir=" . env('INSTA_SESSION_CHROME'), "--profile-directory=Profile $id"]);
            $capabilities = DesiredCapabilities::chrome();
            $capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $chromeOptions);
            putenv('WEBDRIVER_CHROME_DRIVER=C:\chromedriver\chromedriver.exe');
            $driver = ChromeDriver::start($capabilities);
            $this->driver = $driver;
        }
    }
}
