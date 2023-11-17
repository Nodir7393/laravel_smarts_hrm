<?php

namespace Modules\ALL\Observers;

use Modules\ALL\Models\TgChannel;
use Modules\ALL\Models\TgChannelText;
use Modules\ALL\Models\TgGroup;
use Modules\ALL\Models\TgGroupText;
use Modules\Project\Services\StatusService;

class ChannelMessageObserver
{
    /**
     * @var StatusService
     */
    private StatusService $taskStatusService;

    /**
     * @var array|string[]
     */
    private array $availableType = [
        'internal',
        'partners'
    ];

    public function __construct(StatusService $taskStatus)
    {
        $this->taskStatusService = $taskStatus;
    }

    /**
     * @param TgChannelText $tgChannelText
     * @return void
     */
    public function created(TgChannelText $tgChannelText) :void
    {
        $this->activetask($tgChannelText);
    }

    private function activetask ($tgChannelText) {
        $channelType = TgChannel::with('hrmProject')->where('tg_id', $tgChannelText['peer_id_channel_id'])->first();
        $group_id = $channelType->hrmProject->tasklist_group_id;
        $task_list = TgGroup::find($group_id);
        $task_status = TgGroupText::where('peer_id_channel_id', $task_list['tg_id'])->where('message', 'ActiveTask')->first();
        if (!empty($channelType) && $this->isMatchedType($channelType->type)) {
            $editedMessage = $tgChannelText->message . "\n#ActiveTask";
            $this->taskStatusService->edit(
                $editedMessage, null, $tgChannelText['tg_id'], -100 .$tgChannelText['peer_id_channel_id']);
        }
        if(!empty($task_status)) {
            $this->taskStatusService->send(
                -100 .$task_list['tg_id'], $tgChannelText['full_url'], $task_status['tg_id']);
        }
    }
    protected function isMatchedType(string $type) :bool
    {
        return in_array(strtolower($type), $this->availableType);
    }

    public function updated(TgChannelText $tgChannelText) {

    }

    public function delete() {

    }
}
