<?php

namespace Modules\ALL\Services;

class MessageService
{
    public LoginService $madelineproto;

    public function __construct()
    {
        $this->madelineproto = new LoginService();
    }

    public function getHistory($channelId, $limit = 100)
    {
        return $this->madelineproto->madelineproto->messages->getHistory(['peer' => $channelId, 'limit' => $limit])['messages'];
    }
}
