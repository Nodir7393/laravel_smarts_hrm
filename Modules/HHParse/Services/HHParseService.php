<?php
declare(strict_types=1);

namespace Modules\HHParse\Services;

use DiDom\Document;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\Log;
use Modules\HHParse\Models\HhResponse;
use Modules\HHParse\Models\HhResume;
use Modules\HHParse\Models\HhUser;
use Modules\HHParse\Models\HhVacancie;

class HHParseService
{
    const SERVER_URL = 'http://localhost:9515';
    const DOMEN = 'https://tashkent.hh.uz';
    const RESPONSESS = 'https://tashkent.hh.uz/employer/vacancyresponses?vacancyId=';
    const ASSESMENT = '&hhtmFromLabel=assessment&collection=assessment&hhtmFrom=employer_vacancy_responses';
    const HH_UZ = 'https://tashkent.hh.uz/employer/archivedvacancies?hhtmFrom=main&hhtmFromLabel=header';
    const ARCHIVED = '/employer/archivedvacancies';
    const NON_ARCHIVED = '/employer/vacancies';
    const WITH_PASSWORD = "if(document.querySelector('button[data-qa=\"expand-login-by-password\"]')){document.querySelector('button[data-qa=\"expand-login-by-password\"]').click()}";
    const PHOTO_SCRIPT = "if(document.querySelector('button[data-qa=\"response-resume_show-phone-number\"]')){document.querySelector('button[data-qa=\"response-resume_show-phone-number\"]').click()}";
    const DOWNLOAD_SCRIPT = "if(document.querySelector('button[data-qa=\"resume-download-button\"]')){document.querySelector('button[data-qa=\"resume-download-button\"]').click()}";

    public HHParseVacanciesService $service;
    public HHHelper $helper;
    public $user;
    public $driver;

    public function __construct()
    {
        $this->service = new HHParseVacanciesService();
        $this->helper = new HHHelper();
    }

    public function vacancy($vacancy_id): void
    {
        $vacancy_db = HhVacancie::find($vacancy_id);
        Log::debug("vacancy \$vacancydb = $vacancy_db");
        $this->user = HhUser::find($vacancy_db->hh_user_id);
        Log::debug("vacancy \$user = $this->user");
        $this->runDriver();
        $this->login();
        $url = $vacancy_db->curent_page ?? self::RESPONSESS . $vacancy_db->hh_id . self::ASSESMENT;
        Log::debug("vacancy \$url = $url");
        $this->withPagination($url, $vacancy_db->hh_id);
        $url = self::RESPONSESS . $vacancy_db->hh_id;
        $this->withPagination($url, $vacancy_db->hh_id);
    }

    public function vacancyAll($users_id ,bool $db = false): void
    {
        if ($db) {
            $vacancy_db = HhVacancie::whereIn('hh_user_id', $users_id)->get();
            foreach ($vacancy_db as $item) {
                $this->vacancy($item->id);
                $this->driver->quit();
            };
        } else {
            $users = HhUser::whereIn('id', $users_id)->get();
            $this->parseVacancyAll($users);
        }
    }

    public function parseVacancyAll($users): void
    {
        foreach ($users as $user) {
            $this->user = $user;
            $this->runDriver();
            $user_id = $user->id;
            $html = $this->getHtml(self::HH_UZ);
            $doc = new Document($html);
            $docs = $doc->find('.adaptive-table-item.adaptive-table-item_clickable');
            if (empty($docs)) {
                $this->login();
                $html = $this->getHtml(self::HH_UZ);
                $doc->loadHtml($html);
                $docs = $doc->find('.adaptive-table-item.adaptive-table-item_clickable');
            }
            $token = $this->driver->manage()->getCookieNamed('hhtoken');
            $this->user->token = $token->toArray()['value'];
            $this->user->save();
            foreach ($docs as $doc) {
                $vacancyBadUrl = $doc->find('a[data-qa="vacancy-name"]')[0]->getAttribute('href');
                $vacancy_id = str_replace('/vacancy/', '', $vacancyBadUrl);
                $url = self::DOMEN . $vacancyBadUrl;

                $vacancy = new HhVacancie();
                $hhVacancy = HhVacancie::where('url', '=', self::DOMEN.$vacancyBadUrl)->first();
                if (!$hhVacancy) {
                    $vacancy->region = $this->sort($doc, '.adaptive-table-row__section_region');
                    $vacancy->manager = $this->sort($doc,'.adaptive-table-row__section_manager');
                    $vacancy->responses = $this->sort($doc, '.vacancy-responses > a > span[data-qa="topics-count"]');
                    $vacancy->publication_period = $this->sort($doc, '.vacancy-duration');

                    $this->parseVacancy($url,$user_id, $vacancy);
                }

                $unsorted_link = self::RESPONSESS.$vacancy_id;
                $this->withPagination($unsorted_link, $vacancy_id);
                $assesment_link = self::RESPONSESS . $vacancy_id . self::ASSESMENT;
                $this->withPagination($assesment_link, $vacancy_id);
            }
            $this->driver->quit();
        }
    }

    public function parseVacancy($url, $user_id, $vacancy): void
    {
        $hhId = substr($url, strpos($url, 'vacancy/')+8);
        $hh_vacancy = HhVacancie::where('hh_id', '=', $hhId)->first();
        if ($hh_vacancy){return;}

        $html = $this->getHtml($url);
        $doc = new Document($html);
        $title = $this->sort($doc, 'h1[data-qa="vacancy-title"]');
        if (empty($title)) {
            $this->login();
            $html = $this->getHtml($url);
            $doc->loadHtml($html);
            $title = $this->sort($doc, 'h1[data-qa="vacancy-title"]');
        }
        $vacancy->archivation_time = $this->sort($doc, '.bloko-text.bloko-text_small.bloko-text_strong');
        $vacancy->client_id = $this->user->client_id;
        $vacancy->title = $title;
        $vacancy->hh_id = $hhId;
        $vacancy->url = $url;
        $vacancy->hh_user_id = $user_id;
        $vacancy->archived = true;
        $vacancy->save();
    }

    private function resumeParse($url, $resume_id, $responded, $res = null): void
    {
        $html = $this->getHtml($url);
        $doc = new Document($html);
        $resume = new HhResume();
        if ($doc->has('.resume-block__title-text_sub.resume-block__title-text_sub > span')) {
            $experience_html = $doc->find('.resume-block__title-text.resume-block__title-text_sub > span');
            foreach ($experience_html as $key => $item) {
                $html = explode(" ", $item->text());
                $first = $key ? 0:(int)$html[0];$secon = $key ?(int)$html[0]:0;
                $resume->experience = $secon + $first * 12;
            }
        }
        $fff = $doc->has('div[data-qa="bloko-drop-menu"]');
        $file = $doc->has('a.bloko-drop-menu-item');
        if ($file) {
            $test = $doc->find('a.bloko-drop-menu-item');
            $hrefDoc = $test[0]->getAttribute('href');
            $hrefPdf = $test[2]->getAttribute('href');
            $resume->pdf_url = self::DOMEN.$hrefPdf;
            $resume->doc_url = self::DOMEN.$hrefDoc;
            ($resume->pdf_url != 0) ?? Log::info($hrefPdf);
            ($resume->doc_url != 0) ?? Log::info($hrefDoc);
        }
        if (!$file || !$fff) {
            Log::debug('file yo"q');
        }
        $title =                    $this->sort($doc,'span[data-qa="resume-block-title-position"]');
        if ($title === '0') {
            Log::debug("Kunlik 500 ta limit'ingiz tugadi. Yangi limit Maskva vaqti bilan yarim tunda boshlanadi.");
            exit(0);
        }
        $resume->title =            $title;
        $resume->resume_url =       $url;
        $resume->resume_id =        $resume_id;
        $resume->phone =            $this->sort($doc, 'div[data-qa="resume-contacts-phone"] > span');
        $resume->email =            $this->sort($doc, 'div[data-qa="resume-contact-email"] > a > span');
        $resume->full_name =        $this->sort($doc,'.resume-header-name > .bloko-header-1');
        $resume->gender =           $this->sort($doc,'span[data-qa="resume-personal-gender"]');
        $resume->specialization =   $this->sort($doc,'.resume-block__specialization', true);
        $resume->experiences =      $this->sort($doc,'.bloko-text.bloko-text_strong', true);
        $resume->key_skills =       $this->sort($doc,'.bloko-tag.bloko-tag_inline.bloko-tag_countable > span[data-qa="bloko-tag__text"]', true);
        $resume->education =        $this->sort($doc,'div[data-qa="resume-block-education-name"]', true);
        $resume->languages =        $this->sort($doc,'p[data-qa="resume-block-language-item"]', true);
        $resume->nationality =      $this->sort($doc,'div[data-qa="resume-block-additional"] > .resume-block-item-gap > .bloko-columns-row > .bloko-column.bloko-column_xs-4.bloko-column_s-8.bloko-column_m-9.bloko-column_l-12 > .resume-block-container', true);
        $resume->online_status =    $this->sort($doc,'.resume-online-status');
        $resume->resume_updated =   $this->sort($doc,'.resume-header-additional__update-date > .bloko-text.bloko-text_small.bloko-text_tertiary');
        $birth_day =                $this->sort($doc,'span[data-qa="resume-personal-birthday"]');
        $text =                     $this->sort($doc,'.resume-block-letter > .resume-block-letter__full-text');
        $age =                      $this->sort($doc,'span[data-qa="resume-personal-age"]');
        $resume->birthday =         (int)substr($birth_day, -4);
        $resume->age =              substr($age, 0, 2);
        $resume->is_pdf =    0;
        $resume->is_doc =    0;
        $resume->statuses = "New";
        Log::debug('res = ' . $res);
        if (!$res){
            $resume->save();
            $downloader = new HHResumesDownloader();
            $token = $this->driver->manage()->getCookieNamed('hhtoken');
            $t = $token->toArray()['value'];
            $this->user->token = $t;
            Log::debug('token = '. $t);
            $this->user->save();
            $downloader->callForDownload($resume_id, $resume->id, $resume->pdf_url, $t);
            $downloader->callForDownload($resume_id, $resume->id, $resume->doc_url, $t, 'doc');
        }
        $this->responses($url, $resume_id, $responded, $text);
    }

    public function responses($url, $resumeId, $responded, $text): void
    {
        $vacancies = HhVacancie::all();
        foreach ($vacancies as $vacancy) {
            $hhId = $vacancy->hh_id;
            $response = strpos($url, (string)$hhId);
            if ($response){
                $hh_responses = new HhResponse();
                $hh_resume = HhResume::select('id', 'resume_id')->where('resume_id', $resumeId)->first();
                $hh_responses->client_id = $this->user->client_id;
                $hh_responses->resume_id = $hh_resume->resume_id;
                $hh_responses->hh_resume_id = $hh_resume->id;
                $hh_responses->hh_vacancy_id = $vacancy->id;
                $hh_responses->vacancy_id = $hhId;
                $hh_responses->user_id = $this->user->id;
                $hh_responses->time = $responded;
                $hh_responses->text = $text;
                Log::debug('resume_id = ' . $hh_resume->resume_id);
                $hh_responses->save();
                $wcv = $hh_responses->id;
                Log::debug('respose_id = '. $wcv);
            }
        }
    }

    private function withPagination(string $url, $hh_id): void
    {
        $html = $this->getHtml($url);
        $doc = new Document($html);
        $vacancy = HhVacancie::where('hh_id', $hh_id)->first();
        $vacancy->curent_page = $url;
        Log::debug("withPagination \$vacancy->curent_page = ". $vacancy->curent_page);
        $vacancy->save();
        if (!$doc->has('div[data-qa="vacancy-real-responses"] > div[data-qa="resume-serp__resume"]')) {return;}
        $resume_html = $doc->find('div[data-qa="vacancy-real-responses"] > div[data-qa="resume-serp__resume"]');
        foreach ($resume_html as $item) {
            $links = $item->find('a[data-qa="serp-item__title"]');
            $link = $links[0]->getAttribute('href');
            $responded = $this->sort($item,'div[data-qa="resume-serp__resume-additional"] > div > span:nth-child(2)');
            $resume_link = self::DOMEN . $link;
            $resume_id = str_replace('/resume/', '', $link);
            $resume_id = explode('?' , $resume_id)[0];
            $resume = HhResume::where('resume_id', '=', $resume_id)->first();
            if ($resume){
                $response = HhResponse::where('resume_id', $resume->resume_id)->where('vacancy_id', $vacancy->hh_id)->first();
                if(!$response) {
                    Log::debug("withPagination !\$response = true");
                    $this->resumeParse($resume_link, $resume_id, $responded, true);
                }
            } else {
                Log::debug('resume yo\'q');
                $this->resumeParse($resume_link, $resume_id, $responded);
            }
        }
        $next = $doc->has('a[data-qa="pager-next"]');
        if (!$next){
            Log::debug("withPagination \$next = false");
            $vacancy->parsed = true;
            $vacancy->save();
        } else {
            $page_html = $doc->find('a[data-qa="pager-next"]');
            $page_link = self::DOMEN.$page_html[0]->getAttribute('href');
            Log::debug("withPagination \$page_link = $page_link");
            $this->withPagination($page_link, $hh_id);
        }
    }

    public function sort($doc, $filter, $arr = false): mixed
    {
        $hasFilter = $doc->has($filter);
        if(!$arr) {
            if ($hasFilter){
                return $doc->find($filter)[0]->text();
            } else {
                Log::debug($filter . 'not found!');
                return '0';
            }
        }
        else {
            $array = [];
            $list = $doc->find($filter);
            foreach ($list as $item) {
                $array[] = $item->text();
            }
            return json_encode($array);
        }
    }

    public function login(): void
    {
        $service = new HHUserService();
        $html = $this->getHtml(self::HH_UZ);
        $doc = new Document($html);
        $decrypt = $service->decrypt($this->user->password);
        $loginBtn = $doc->has('a[data-qa="login"]');
        if ($loginBtn) {
            Log::debug("login \$loginBtn = true");
            $this->driver->executeScript(self::WITH_PASSWORD, []);
            $this->driver->findElement(WebDriverBy::cssSelector('input[data-qa="login-input-username"]'))->clear()->sendKeys($this->user->email);
            $this->driver->findElement(WebDriverBy::cssSelector('input[data-qa="login-input-password"]'))->clear()->sendKeys($decrypt);
            $this->driver->findElement(WebDriverBy::cssSelector('button[data-qa="account-login-submit"]'))->submit();
            $token = $this->driver->manage()->getCookieNamed('hhtoken');
            $this->user->token = $token->toArray()['value'];
            sleep(3);
            $badText = $this->driver->findElement(WebDriverBy::cssSelector('.supernova-navi-client-id > .supernova-link'));
            $text = (int)str_replace('№', '', $badText->getText());
            $this->user->client_id = $text;
            $this->user->save();
        }
    }

    public function runDriver(): void
    {
        $id = $this->user->id;
        Log::debug("runDriver \$id = $id");
        $chromeOptions = new ChromeOptions();
        $chromeOptions->addArguments(["--user-data-dir=" . env('CHROME_USER_DATA'), "profile-directory=Profile $id"]);
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY_W3C, $chromeOptions);
        $driver = RemoteWebDriver::create(self::SERVER_URL, $capabilities);
        $this->driver = $driver;
    }

    public function getHtml($url) {
        $this->driver->get($url);
        $this->driver->executeScript(self::PHOTO_SCRIPT, []);
        sleep(1);
        $this->driver->executeScript(self::DOWNLOAD_SCRIPT, []);
        sleep(1);
        return $this->driver->getPageSource();
    }

    public function getVacanciesParameters($users_id): void
    {
        $this->user = HhUser::find($users_id);
        Log::debug("getVacanciesParameters \$this->user->id = $this->user->id");
        $this->runDriver();
        $this->login();
        $arxived = $this->getHtml(self::DOMEN.self::ARCHIVED);
        $this->getVacancies($arxived, $users_id, true);
        $vacancy = $this->getHtml(self::DOMEN.self::NON_ARCHIVED);
        $this->getVacancies($vacancy, $users_id, false);
    }

    public function getVacancies($html, $users_id, $arxive): void
    {
        $doc = new Document($html);
        $docs = $doc->find('.adaptive-table-item.adaptive-table-item_clickable');

        foreach ($docs as $doc) {
            $vacancy = new HhVacancie();
            $url = $doc->find('a')[0]->getAttribute('href');
            Log::debug("getVacancies \$url = $url");
            $hhVacancy = HhVacancie::where('url', '=', self::DOMEN.$url)->first();
            if ($hhVacancy) {
                Log::debug("getVacancies \$hhVacancy->id = $hhVacancy->id");
                continue;
            }
            $vacancy->url = self::DOMEN . $url;
            $vacancy->title = $this->sort($doc,'a');
            $vacancy->hh_id = substr($url, strpos($url, 'vacancy/') + 8);
            $vacancy->hh_user_id = $users_id;
            $vacancy->region = $this->sort($doc, '.adaptive-table-row__section.adaptive-table-row__section_region');
            $vacancy->manager = $this->sort($doc,'.adaptive-table-row__section.adaptive-table-row__section_manager');
            $vacancy->responses = $this->sort($doc, '.vacancy-responses > a > span[data-qa="topics-count"]');
            $vacancy->publication_period = $this->sort($doc, '.vacancy-duration');
            $vacancy->archived = $arxive;
            $vacancy->archivation_time = $this->sort($doc,'.vacancy-archivation');
            $vacancy->save();
        }
    }
}
