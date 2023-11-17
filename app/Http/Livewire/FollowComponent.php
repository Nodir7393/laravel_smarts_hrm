<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Modules\Instagram\Service\FollowService;

class FollowComponent extends Component
{
    public $bot;
    public $count;
    public function form()
    {
        $service = new FollowService();
        $service->follow($this->bot, $this->count);
    }
    public function render()
    {
        return view('livewire.follow-component', ['bot' => $this->bot]);
    }
}
