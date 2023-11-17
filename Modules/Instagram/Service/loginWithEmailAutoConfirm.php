<?php


use Illuminate\Support\Facades\Cache;

$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'user', 'password', new Cache());

$emailVecification = new \Modules\Instagram\EmailVerification(
    'hamidullayevhabibulloh55@gmail.com',
    'imap.gmail.com',
    'theeagle2003'
);

$instagram->login(false, $emailVecification);

$account = $instagram->getAccount('habibulloh2003');
echo $account->getUsername();
