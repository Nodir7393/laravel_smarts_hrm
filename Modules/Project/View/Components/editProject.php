<?php

namespace Modules\Project\View\Components;
use Illuminate\View\Component;
use Modules\ALL\Models\HrmProject;
use Modules\ALL\Models\TgChannel;
use Modules\ALL\Models\TgGroup;
use Modules\ALL\Models\TgUser;

class editProject extends Component
{
    /**
     * @var
     */
    public $project;

    public function render() {
        $users = TgUser::pluck('first_name', 'id')->toArray();
        $channels = TgChannel::pluck('name', 'id')->toArray();
        $groups = TgGroup::pluck('name', 'id')->toArray();
        $project_name = HrmProject::where('id', '=', $this->project)->value('name');
        return view('components.editProject', [
        'users' => $users,
        'channels' => $channels,
        'groups' => $groups,
        'project' => $this->project,
        'project_name' => $project_name
        ]);
    }
}
