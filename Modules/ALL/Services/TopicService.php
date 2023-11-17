<?php


namespace Modules\ALL\Services;

class TopicService
{
    public LoginService $mtproto;

    public function __construct()
    {
        $this->mtproto = new LoginService();
    }


}
