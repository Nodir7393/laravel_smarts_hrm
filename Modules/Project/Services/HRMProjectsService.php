<?php

namespace Modules\Project\Services;

use App\Services\MadelineProto\ChannelService;
use Closure;
use danog\MadelineProto\API;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Modules\ALL\Models\HrmProject;
use Modules\ALL\Models\TgChannel;
use Modules\ALL\Models\TgChannelText;
use Modules\ALL\Models\TgGroup;
use Modules\Project\View\Components\editProject;

class HRMProjectsService
{

    /**
     * @var API
     */
    public API $madelineProto;

    /**
     * @var
     */
    private $taskList_id;

    public function __construct()
    {
        $this->madelineProto = new API(env('SESSION_PATH'));
        $this->madelineProto->start();
    }

    /**
     *
     * Function  CreateProject
     * @param string $name
     * @param string $type
     */
    public function CreateProject(string $name, string $type): void
    {
        $channenls = ['tasklist', 'internal', 'partners'];
        $statuses = ['ActiveTask', 'InProcess' , 'NeedTests', 'Accepted', 'Completed', 'ALL'];
        $project = new HrmProject;
        $project->name = $name . ' ' . $type;
        $project->save();
        foreach ($channenls as $key => $item) {
            $channel = $this->CreateChannel($name, $type, ucfirst($item), $project->id);
            $project->{$item . '_channel_id'} = $channel->id;
            usleep(400000);
            $group = $this->CreateGroup($name, $type, ucfirst($item), $project->id);
            $project->{$item . '_group_id'} = $group->id;
            $this->madelineProto->channels->setDiscussionGroup(['broadcast' => -100 . $channel->tg_id, 'group' => -100 . $group->tg_id]);
            if (!$key) {
                $this->taskList_id = $channel->tg_id;
                foreach ($statuses as $status) {
                    $post_id = $this->madelineProto->messages->sendMessage(['peer' => -100 . $channel->tg_id, 'message' => $status]);
                    sleep(2);
                    $post = TgChannelText::where('peer_id_channel_id', '=', $channel->tg_id)
                        ->where('tg_id', '=', $post_id['updates'][0]['id'])->first();
                    $post->status = strtolower($status);
                    $post->save();
                }
            }
            $group->linked_channel_id = $this->taskList_id;
            $group->save();
            $channel->save();

        }
        $project->save();
    }

    /**
     *
     * Function  editProject
     * @param editProject $appendGrid
     * @param Request $request
     * @return Application|Closure|Factory|Htmlable|string|View
     */
    public function editProject(editProject $appendGrid, Request $request): View|Factory|Htmlable|string|Closure|Application
    {
        if ($request->has('name') && $request->has('user')) {
            $this->updateProject($request);
        }
        $appendGrid->project = $request->user;
        return $appendGrid->render();
    }

    /**
     *
     * Function  updateProject
     * @param $request
     */
    private function updateProject($request): void
    {
        $project = HrmProject::find($request->user);
        $project->name = $request->name;
        $project->tasklist_channel_id = $request->tasklist_channel;
        $project->internal_channel_id = $request->internal_channel;
        $project->partners_channel_id = $request->partners_channel;
        $project->tasklist_group_id = $request->tasklist_group;
        $project->internal_group_id = $request->internal_group;
        $project->partners_group_id = $request->partners_group;
        $project->users_pm = json_encode($request->pm);
        $project->users_dev = json_encode($request->dev);
        $project->users_qa = json_encode($request->qa);
        $project->save();
    }

    /**
     *
     * Function  inviteToChannel
     * @param $users
     * @param $groups
     */
    public function inviteToChannel($users, $groups): void
    {
        foreach ($groups as $group) {
            $Updates = $this->madelineProto->channels->inviteToChannel(['channel' => $group, 'users' => $users]);
        }
    }

    /**
     *
     * Function  CreateChannel
     * @param string $name
     * @param string $type
     * @param string $item
     * @param int $project_id
     * @return  TgChannel
     */
    public function CreateChannel(string $name, string $type, string $item, int $project_id): TgChannel
    {
        $channel = $this->madelineProto->channels->createChannel(['title' => $type . " $item " . $name])['chats'][0];
        $tg_channel = new TgChannel;
        $tg_channel->name = $channel['title'];
        $tg_channel->tg_id = $channel['id'];
        $tg_channel->type = $item;
        $tg_channel->hrm_project_id = $project_id;
        $tg_channel->save();
        return $tg_channel;
    }

    /**
     *
     * Function  CreateGroup
     * @param string $name
     * @param string $type
     * @param string $item
     * @param int $project_id
     * @return  TgGroup
     */
    public function CreateGroup(string $name, string $type, string $item, int $project_id): TgGroup
    {
        $group = $this->madelineProto->channels->createChannel(['title' => $type . " $item" .'Group ' . $name, 'megagroup' => true])['chats'][0];
        $tg_group = new TgGroup;
        $tg_group->name = $group['title'];
        $tg_group->tg_id = $group['id'];
        $tg_group->type = $item;
        $tg_group->hrm_project_id = $project_id;
        $tg_group->save();
        return $tg_group;
    }
}
