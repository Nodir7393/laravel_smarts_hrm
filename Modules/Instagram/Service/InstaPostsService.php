<?php

namespace Modules\Instagram\Service;

use DiDom\Document;
use Facebook\WebDriver\Chrome\ChromeDevToolsDriver;
use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Interactions\Internal\WebDriverMouseMoveAction;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverHasInputDevices;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Entities\InstaBot;
use Modules\Instagram\Entities\InstaPost;
use Modules\Instagram\Entities\InstaSidecar;
use Modules\Instagram\Entities\InstaUser;
use Modules\Instagram\Models\FailedJob;
use Modules\Instagram\Models\InstaJob;
use Modules\Instagram\Models\Job;
use PhpOffice\PhpSpreadsheet\Calculation\Web;
use function Symfony\Component\String\b;
use function Symfony\Component\String\u;

class InstaPostsService
{
    public $instagram;

    const POSTS_URL = 'https://www.instagram.com/api/v1/feed/user';
    const INSTA_URL = 'https://www.instagram.com';
    const PROFILE_URL = 'https://www.instagram.com/api/v1/users/web_profile_info/?username=';

    public function __construct()
    {
//        $this->instagram = new InstaService();
//        $this->instagram->loginWithCredentials();
    }

    public function get($username)
    {
        $username = is_string($username) ? $username : $this->instagram->instagram->getAccountById($username)->getUsername();
        $medias = $this->instagram->instagram->getMedias($username, 50);
        foreach ($medias as $media) {
            InstaPost::create($this->instagram->validate((array)$media));
            foreach ($media->getSidecarMedias() as $sideCar) {
                $temp = $this->instagram->validate($sideCar);
                $temp['insta_post_id'] = $media->getId();
                InstaSidecar::create($temp);
            }
        }
    }

    public function fillFromLink()
    {
        $posts = InstaPost::whereNotNull('link')->whereNull('insta_id')->get();

        foreach ($posts as $post) {
            $info = $this->instagram->instagram->getMediaByUrl($post->link);
            $valid = $this->instagram->validate($info);

            [$id, $user_id] = explode('_', $info->getId());
            $valid['insta_id'] = $id;
            $valid['insta_user_id'] = $user_id;

            $post->update($valid);
        }
    }

    public function likePost($media_link)
    {
        [$media_id] = explode('_', $this->instagram->instagram->getMediaByUrl($media_link)->getId());
        $this->instagram->instagram->like($media_id);
    }

    public function likeTags($tag = 'php', $count = 10)
    {
        $medias = $this->instagram->instagram->getMediasByTag($tag, $count);
        foreach ($medias as $media) {
            [$id] = explode("_", $media->getId());
            $this->instagram->instagram->like($id);
        }
    }

    public function getLikers($link)
    {
        $media_code = $this->instagram->instagram->getMediaByUrl($link)->getShortCode();
        $likers = $this->instagram->instagram->getMediaLikesByCode($media_code, 1000);

        $user_names = [];

        foreach ($likers as $liker) {
            $user_names[] = $liker['username'];
        }

        return $user_names;
    }

    public function getCommentedUsers($link)
    {
        $media_code = $this->instagram->instagram->getMediaByUrl($link)->getId();
        $comments = $this->instagram->instagram->getMediaCommentsById($media_code, 1000);
        $user_names = [];

        foreach ($comments as $comment) {
            $user_names[] = $comment->getOwner()->getUsername();
        }

        return $user_names;
    }

    public function likeThisPost($bot, $posts, $jobId, $name, $uuId, $instaJob)
    {
        $failedJobId = FailedJob::find($uuId);
        $failedId = $failedJobId->id;

        $instaJob->name = $name;
        $instaJob->job_id = $jobId;
        $instaJob->failed_jobs_id = $failedId;

        $data = new LoginService();
        $data->user = InstaBot::find($bot);
        $instaJob->insta_bot_id = $data->user->id;
        $data->runDriver();
        foreach ($posts as $post) {
            $link = InstaPost::find($post);
            $data->getHtml($link->link);
            $data->driver->findElement(WebDriverBy::cssSelector('span[class=_aamw] > ._abl-'))->click();
            $link->hasLiked = true;
            $link->save();
            sleep(10);
        }
        return $instaJob;
    }

    public function comment($commentData, $bot, $posts, $jobId, $name, $uuId, $instaJob)
    {
        $failedJobId = FailedJob::find($uuId);
        $failedId = $failedJobId->id;

        $instaJob->name = $name;
        $instaJob->job_id = $jobId;
        $instaJob->failed_jobs_id = $failedId;

        $driver = new LoginService();
        $driver->user = InstaBot::find($bot);
        $instaJob->insta_bot_id = $driver->user->id;
        $driver->runDriver();
        foreach ($posts as $post) {
            $post = InstaPost::find($post);
            $driver->getHtml($post->link);
            sleep(2);
            $driver->driver->findElement(WebDriverBy::cssSelector('span[class=_aamx] > button[class="_abl-"]'))->click();
            $driver->driver->findElement(WebDriverBy::cssSelector('textarea[aria-label="Добавьте комментарий..."]'))->clear()->sendKeys($commentData);
            $driver->driver->findElement(WebDriverBy::cssSelector('._akhn div:nth-child(3) > div'))->click();
        }
        return $instaJob;
    }

    public function getPost($user, $bot, $jobId, $uuId, $name, $instaJob)
    {
        $failedJobId = FailedJob::find($uuId);
//        $failedId = $failedJobId->id;

//        $instaJob->name = $name;
//        $instaJob->job_id = $jobId;
//        $instaJob->failed_jobs_id = $failedId;

        $service = new LoginService();
        $service->user = InstaBot::find($bot);
        $service->login($bot);
        sleep(5);
        $instaJob->insta_bot_id = $service->user->id;
        $driver = $service->driver;
        $u = InstaUser::find($user);
        $html = $service->getHtml(self::INSTA_URL.'/'.$u->username);
        sleep(2);
        $count = $driver->findElement(WebDriverBy::cssSelector('li > span[class="_ac2a"]'))->getText();

        $dev_tools = $service->driver->getDevTools();
        $dev_tools->execute('Network.enable');
        preg_match_all('/(?<=X-IG-App-ID":")\d+/', $html, $pregAppId);
        $appId = $pregAppId[0][0];
        $dev_tools->execute('Network.setExtraHTTPHeaders', ['headers' => (object) [
            'X-IG-App-ID' => $appId
        ]]);

        $url = self::POSTS_URL."/".$u->username."/username/?count=12";
        do {
            $service->getHtml($url);
            $json = $service->driver->findElement(WebDriverBy::cssSelector('body > pre'))->getText();
            $array = json_decode($json, true);
            $pkId = $array['user']['pk_id'];
            $bool = isset($array['next_max_id']);
            if ($bool){
                $maxId = $array['next_max_id'];
                $url = self::POSTS_URL."/".$pkId."/?count=12&max_id=".$maxId;
            }

            foreach ($array['items'] as $item) {
                $standardVideo = null;
                if (isset($item['carousel_media'][0]['video_versions'][0]['url'])){
                    $standardVideo = $item['carousel_media'][0]['video_versions'][0]['url'];
                }
                elseif (isset($item['carousel_media'][0]['video_versions2'][0]['url'])){
                    $standardVideo = $item['carousel_media'][0]['video_versions2'][0]['url'];
                }
                elseif (isset($item['video_versions'][0]['url'])){
                    $standardVideo = $item['video_versions'][0]['url'];
                }

                $standardImage = null;
                if (isset($item['image_versions']['candidates'][0]['url'])){
                    $standardImage = $item['image_versions']['candidates'][0]['url'];
                }
                elseif (isset($item['image_versions2']['candidates'][0]['url'])){
                    $standardImage = $item['image_versions2']['candidates'][0]['url'];
                }
                $model = new InstaPost();
                !$standardVideo ?: $model->videoStandardResolutionUrl = $standardVideo;
                !$standardImage ?: $model->imageHighResolutionUrl = $standardImage;
                !isset($item['comment_count']) ?: $model->commentsCount = $item['comment_count'];
                !isset($item['caption']['text']) ?: $model->caption = $item['caption']['text'];
                !isset($item['location']) ?: $model->locationName = $item['location']['name'];
                !isset($item['like_count']) ?: $model->likesCount = $item['like_count'];
                !isset($item['play_count']) ?: $model->videoViews = $item['play_count'];
                !isset($item['has_liked']) ?: $model->hasLiked = $item['has_liked'];
                !isset($pkId) ?: $model->ownerId = $pkId;
                if (isset($item['code'])) {
                    $model->shortCode = $item['code'];
                    $model->link = self::INSTA_URL."/p/".$item['code'];
                }
                $model->save();
            }
        } while ($bool);
    }

    public function likeOnePost($bot, $link)
    {
        $service = new LoginService();
        $service->user = InstaBot::find($bot);
        $service->login($bot);
        sleep(2);
        $service->getHtml($link);

        $service->driver->findElement(WebDriverBy::cssSelector('span > button[class="_abl-"]'))->click();
        $model = InstaPost::where('link', $link)->first();
        $model->hasLiked = true;
        $model->save();
        $service->driver->quit();
    }

    public function tagSearch($tag, $bot, $num)
    {

        $service = new LoginService();
        $service->user = InstaBot::find($bot);
        $service->login($bot);
        sleep(2);
        $html = $service->getHtml(self::INSTA_URL . "/explore/tags/" . $tag . '/');
        sleep(2);
        $i = 0;
        while (true) {
            $drivers = $service->driver->findElements(WebDriverBy::cssSelector('article[class="_aao7"] > div > div > div[class="_ac7v  _al3n"]'));
            if (count($drivers) * 3 >= $num) {
                $postUrls = [];
                foreach ($drivers as $driver) {
                    $badUrls = $driver->findElements(WebDriverBy::cssSelector('div[class="_aabd _aa8k  _al3l"] > a'));
                    foreach ($badUrls as $badUrl) {
                        if ($i >= $num) { break;}
                        $postUrls[$i] = $badUrl->getAttribute('href');
                        $post[$i] = new InstaPost();
                        sleep(1);

                        $service->driver->action()->moveToElement($badUrl)->perform();
                        $likeOrComment = $badUrl->findElements(WebDriverBy::cssSelector('div[class="_ac2d"] > ul[class="_abpo"] > li[class="_abpm"]'));
                        foreach ($likeOrComment as $item) {
                            $like = $item->findElements(WebDriverBy::cssSelector('span[class="_abpn _9-j_"]'));
                            $comment = $item->findElements(WebDriverBy::cssSelector('span[class="_abpn _9-k0"]'));
                            if (count($like) > 0) {
                                $post[$i]->likesCount = $item->getText();

                            }
                            if (count($comment) > 0) {
                                $post[$i]->commentsCount = $item->getText();
                            }

                        }
                        $i++;
                    }
                }
                $owners = [];
                foreach ($postUrls as $i => $postUrl) {
                    sleep(2);
                    $c = InstaPost::where('shortCode', $post[$i]->shortCode)->first();
                    if ($c) {continue;}
                    $service->driver->get(self::INSTA_URL.$postUrl);
                    sleep(6);

                    $badCaption = $service->driver->findElements(WebDriverBy::cssSelector('div[class="_a9zr"] > div[class="_a9zs"]'));
                    if (count($badCaption) > 0) {
                        $post[$i]->caption = $badCaption[0]->getText();
                    }
                    $badOwner = $service->driver->findElements(WebDriverBy::cssSelector('div[class="xt0psk2"]'));
                    if (count($badOwner) > 0) {
                        $owners[$i] = $badOwner[0]->getText();
                    }
                    $badLiked = $service->driver->findElements(WebDriverBy::cssSelector('span[class="xp7jhwk"] > button[class="_abl-"] > div[class="_abm0 _abl_"]'));
                    if (count($badLiked) > 0) {
                        $post[$i]->hasLiked = false;
                    } else {
                        $post[$i]->hasLiked = true;
                    }
                    $badImage = $service->driver->findElements(WebDriverBy::cssSelector('div[class="x1i10hfl"] > div[class="_aagu"] > div[class="_aagv"] > img[class="x5yr21d xu96u03 x10l6tqk x13vifvy x87ps6o xh8yej3"]'));
                    if (count($badImage) > 0){
                        $post[$i]->imageHighResolutionUrl = $badImage[0]->getAttribute('src');
                        $post[$i]->type = 'image';
                    }
                    else {
                        $badVideo = $service->driver->findElements(WebDriverBy::cssSelector('video[class="x1lliihq x5yr21d xh8yej3"]'));
                        if (count($badVideo) > 0) {
                            $post[$i]->videoStandardResolutionUrl = $badVideo[0]->getAttribute('src');
                            $post[$i]->type = 'video';
                        }
                    }
                    $post[$i]->shortCode = substr($postUrl, 3, strlen($postUrl)-4);
                    $post[$i]->link = self::INSTA_URL.$postUrl;
                    $post[$i]->save();
                }
                foreach ($owners as $owner) {
                    $dev_tools = $service->driver->getDevTools();
                    $dev_tools->execute('Network.enable');
                    preg_match_all('/(?<=X-IG-App-ID":")\d+/', $html, $pregAppId);
                    $appId = $pregAppId[0][0];
                    $dev_tools->execute('Network.setExtraHTTPHeaders', ['headers' => (object) [
                        'X-IG-App-ID' => $appId
                    ]]);
                    $service->getHtml(self::PROFILE_URL.$owner);
                    sleep(3);
                    $json = $service->driver->findElement(WebDriverBy::cssSelector('body > pre'))->getText();
                    $array = json_decode($json, true);
                    $instaUser = $array['data']['user'];
                    $model = new InstaUser();
                    $model->mediaCount = $instaUser['edge_owner_to_timeline_media']['count'] ?? 0;
                    $model->followedByCount = $instaUser['edge_followed_by']['count'] ?? 0;
                    $model->biography = $instaUser['biography'] ?? 0;
                    $model->fullName = $instaUser['full_name'] ?? 0;
                    $model->insta_id = $instaUser['id'] ?? 0;
                    $model->fbid = $instaUser['fbid'] ?? 0;
                    $model->followsCount = $instaUser['edge_follow']['count'] ?? 0;
                    $model->username = $instaUser['username'] ?? 0;
                    $model->profile_pic_url = $instaUser['profile_pic_url'] ?? 0;
                    $model->profilePicUrlHd = $instaUser['profile_pic_url_hd'] ?? 0;
                    $model->categoryName = $instaUser['category_name'] ?? 0;
                    $model->save();
                }
                break;
            } else {
                $service->driver->executeScript('window.scrollTo(0, document.body.scrollHeight);');
                sleep(3);
            }
        }
        $service->driver->quit();
    }

    public function likeTagSearch($tag, $bot, $num)
    {
        $service = new LoginService();
        $service->user = InstaBot::find($bot);
        $service->login($bot);
        sleep(2);
        $html = $service->getHtml(self::INSTA_URL . "/explore/tags/" . $tag . '/');
        sleep(2);
        $postUrls = [];
        $i = 0;
        while (true) {
            $drivers = $service->driver->findElements(WebDriverBy::cssSelector('article[class="_aao7"] > div > div > div[class="_ac7v  _al3n"]'));
            Log::debug(count($drivers));
            if (count($drivers) * 3 >= $num) {
                foreach ($drivers as $driver) {
                    $badUrls = $driver->findElements(WebDriverBy::cssSelector('div[class="_aabd _aa8k  _al3l"] > a'));
                    foreach ($badUrls as $badUrl) {

                        $postUrls[$i] = $badUrl->getAttribute('href');
                        sleep(1);
                        if ($i >= $num) {
                            break;
                        }
                        $i++;
                    }
                }
                break;
            } else {
                $service->driver->executeScript('window.scrollTo(0, document.body.scrollHeight);');
                sleep(3);
            }
        }
        foreach ($postUrls as $postUrl) {
            $html = $service->getHtml(self::INSTA_URL.$postUrl);
            Log::debug($html);
            $buttons = $service->driver->findElements(WebDriverBy::cssSelector('div[class="x78zum5"] > span[class="xp7jhwk"] > button'));
            if ($buttons){
                $buttons[0]->click();
            } else {
                Log::error("button doesn't exists");
            }
            sleep(10);
        }
        $service->driver->quit();
    }
}
