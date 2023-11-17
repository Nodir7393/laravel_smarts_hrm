<?php

namespace Modules\Notification\Services;


use Carbon\Carbon;
use danog\MadelineProto\API;
use Modules\ALL\Models\HrmProject;
use Modules\ALL\Models\TgChannel;
use Modules\ALL\Models\TgChannelText;
use Modules\ALL\Models\TgUser;

/**
 * Class    NotificationService
 * @package Modules\Notification\Services
 */
class NotificationService
{
    /**
     * @var API
     */
    public $mtproto;

    /**
     *
     * Function  checkTagged
     * @return  array
     */
    public function checkTagged()
    {
        $after_ten = Carbon::now()->subMinutes(10)->format('Y-m-d H:i:s');

        $all = TgChannelText::all()->where('check_tagged_user', "!=", 1)->where('created_at', "<=", $after_ten)->toArray();

        foreach ($all as $item) {
            $special = '@';
            $message = $item['message'];

            if ($message) {
                if (str_contains($message, $special)) {
                    TgChannelText::where('id', $item['id'])->update([
                        "check_tagged_user" => 1
                    ]);
                } else {
                    $channels_id[] = TgChannelText::where('id', $item['id'])->value('tg_channel_id');
                }
            }
        }

        return $channels_id;
    }

    /**
     * @return mixed
     */
    public function getTaskCreator()
    {
        $channels_id = $this->checkTagged();

        foreach ($channels_id as $channel_id) {
            if ($channel_id) {
                $pms_id = HrmProject::where('tasklist_channel_id', $channel_id)->value('users_pm');
                $arr[] = json_decode($pms_id, true, 512, JSON_THROW_ON_ERROR);
            }
        }

        return $arr;
    }

    /**
     *
     * Function  choosePerformer
     * @throws \JsonException
     */
    public function choosePerformer(): void
    {
        $pms_arr = $this->getTaskCreator();

        foreach ($pms_arr as $pms_id) {
            foreach ($pms_id as $pm_id) {

                $pm_info = $this->getUserInfo($pm_id);

                $sample = setting('notification.select_performers');
                $message = str_replace(['{creator_name}', '{creator_tg_username}'], [$pm_info['name'], $pm_info['username']], $sample);

                $this->sendMessage($pm_info['tg_id'], $message);
            }
        }

    }

    /**
     *
     * Function  getUserInfo
     * @param int|string $user_id
     * @return  array
     */
    public function getUserInfo(int|string $user_id): array
    {

        $user_info = TgUser::where('id', $user_id)->select(['first_name', 'last_name', 'username', 'tg_id']);

        $name = $user_info->value('first_name') . ' ' . $user_info->value('last_name') ?? '';
        $username = '@' . $user_info->value('username');
        $tg_id = $user_info->value('tg_id');

        return [
            "name" => $name,
            "username" => $username,
            "tg_id" => $tg_id
        ];
    }

    /**
     * Function getPerformers
     *
     * @throws \JsonException
     */
    public function taskForYou(): void
    {
        $all = TgChannelText::where('check_tagged_user', 1)->where('status', 'activetask')->get()->toArray();

        $special = '@';

        foreach ($all as $item) {
            $message = $item["message"];
            if ($message && str_contains($message, $special)) {
                $each_messages = explode("\n", $message);
                $users_id = [];
                $user_names = [];
                foreach ($each_messages as $each_message) {
                    if (str_contains($each_message, $special)) {
                        $user_name = trim($each_message, "@ \r");
                        $user_names[] = $user_name;
                        $users_id[] = TgUser::where('username', $user_name)->value('id');
                    }
                }
                $users_id = array_values(array_filter($users_id));
                if ($users_id) {
                    sort($users_id);

                    [$info_db] = TgChannelText::where('id', $item['id'])->get(['id', 'performers_id'])->toArray();

                    if ($info_db['performers_id'] !== $users_id) {
                        if (count($users_id) > count($info_db['performers_id'])) {
                            $differences = array_diff($users_id, $info_db['performers_id']);
                        } else {
                            $differences = array_diff($info_db['performers_id'], $users_id);
                        }
                        foreach ($differences as $difference) {

                            [$user_task] = TgUser::where('id', $difference)->get()->toArray();
                            $user_info = $this->getUserInfo($user_task['id']);

                            [$channel_info] = HrmProject::where('tasklist_channel_id', $item['tg_channel_id'])->get()->toArray();

                            $project_name = $channel_info['name'];

                            $sample = setting('notification.task_for_you');
                            $message = str_replace(['{performer_name}', '{performer_tg_username}', '{project_name}', '{task_tg_link}'], [$user_info['name'], $user_info['username'], $project_name, $item['full_url']], $sample);

                            $this->sendMessage($user_info['tg_id'], $message);
                        }
                        TgChannelText::where('id', $item['id'])->first()->update([
                            'id' => $item['id'],
                            "performers_id" => $users_id,
                        ]);
                    }
                }
            }
        }
    }

    /**
     *
     * Function  getQAs
     * @return  array
     * @throws \JsonException
     */
    public function getQAs(): array
    {
        $need_tests = TgChannelText::where('status', 'needTests')->get()->toArray();

        if (!empty($need_tests)) {
            foreach ($need_tests as $need_test) {
                if ($need_test) {
                    $qas_id = HrmProject::where('tasklist_channel_id', $need_test['tg_channel_id'])->value('users_qa');
                    $arr[] = [
                        "tg_channel_id" => $need_test['tg_channel_id'],
                        "qas_id" => json_decode($qas_id, true, 512, JSON_THROW_ON_ERROR),
                        "full_url" => $need_test['full_url'],
                    ];
                }
            }
        }

        return $arr;
    }

    /**
     *
     * Function  needTests
     * @throws \JsonException
     */
    public function needTests()
    {
        $arr = $this->getQAs();

        foreach ($arr as $qa_ids) {
            foreach ($qa_ids['qas_id'] as $i => $qa_id) {
                $qa_info = $this->getUserInfo($qa_id);
                $project_name = HrmProject::where('tasklist_channel_id', $qa_ids['tg_channel_id'])->value('name');

                $sample = setting('notification.need_tests');
                $message = str_replace(['{qa_name}', '{qa_username}', '{project_name}', '{task_tg_link}'], [$qa_info['name'], $qa_info['username'], $project_name, $qa_ids['full_url']], $sample);

                $this->sendMessage($qa_info['tg_id'], $message);
            }
        }

    }

    /**
     *
     * Function  activeBugs
     */
    public function activeBugs()
    {
        $bugs_arr = TgChannelText::where('status', 'activeBug')->get()->toArray();

        if (!empty($bugs_arr)) {
            foreach ($bugs_arr as $bugs) {
                $users_info = [];
                foreach ($bugs['performers_id'] as $performer_id) {
                    $performer_info = $this->getUserInfo($performer_id);
                    $users_info[] = $performer_info;
                }
                foreach ($users_info as $user_info) {
                    $project_name = HrmProject::where('tasklist_channel_id', $bugs['tg_channel_id'])->value('name');

                    $sample = setting('notification.active_bug');
                    $message = str_replace(['{performer_name}', '{performer_tg_username}', '{project_name}', '{task_tg_link}'], [$user_info['name'], $user_info['username'], $project_name, $bugs['full_url']], $sample);

                    $this->sendMessage($user_info['tg_id'], $message);

                }


            }
        }
    }

    /**
     *
     * Function  taskOk
     */
    public function taskOk(): void
    {
        $completes = TgChannelText::where('status', 'completed')->get()->toArray();

        foreach ($completes as $completed) {
            if (!empty($completed)) {
                $task = HrmProject::where('tasklist_channel_id', $completed['tg_channel_id']);

                $pms_id = $task->value('users_pm');
                $project_name = $task->value('name');


                $sample = setting('notification.task_completed');

                foreach ($pms_id as $pm_id) {
                    $pm_info = $this->getUserInfo($pm_id);
                    $message = str_replace(['{pm_name}', '{pm_tg_username}', '{project_name}', '{task_tg_link}'], [$pm_info['name'], $pm_info['username'], $project_name, $completed['full_url']], $sample);

                    $this->sendMessage($pm_info['tg_id'], $message);
                }
            }

        }

    }

    /**
     *
     * Function  sendMessage
     * @param int|string $user_id
     * @param string $message
     */
    public function sendMessage(int|string $user_id, string $message): void
    {
        $this->mtproto->messages->sendMessage([
            "peer" => $user_id,
            "message" => $message,
            "parse_mode" => 'markdown'
        ]);
    }

    /**
     * constructor
     *
     */
    public function __construct()
    {
        $this->mtproto = new API(env('SESSION_PATH'));
        $this->mtproto->start();
    }
}
