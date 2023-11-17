<?php

namespace Modules\ALL\Observers;

use Modules\ALL\Models\TgGroup;
use Modules\ALL\Models\TgGroupText;
use Modules\Project\Services\StatusService;

class GroupMessageObserver
{
    const TASKLIST = 'tasklist';

    /**
     * @var StatusService
     */
    private $taskStatus;

    public function __construct(StatusService $taskStatus) {
        $this->taskStatus = $taskStatus;
    }

    /**
     *
     * Function  updated
     * @param TgGroupText $tgGroupText
     */
    public function updated(TgGroupText $tgGroupText): void
    {

    }

    /**
     *
     * Function  created
     * @param TgGroupText $tgGroupText
     * @throws \Exception
     */
    public function created(TgGroupText $tgGroupText): void
    {
        $group = TgGroup::with('hrmProject')->where('tg_id', '=', $tgGroupText['peer_id_channel_id'])->first();
        if (!empty($group)) {$type = strtolower($group['type']);}
        switch (true) {
            case ($tgGroupText->reply_to_reply_to_msg_id && $type !== self::TASKLIST):
                $this->taskStatus->confirmStatus($tgGroupText, $group);break;
        }
    }

    public function delete()
    {

    }
}
