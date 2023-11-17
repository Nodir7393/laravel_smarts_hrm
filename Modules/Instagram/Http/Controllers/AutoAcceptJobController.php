<?php

namespace Modules\Instagram\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Instagram\Entities\InstaBot;
use Modules\Instagram\Jobs\AutoAcceptJob;

class AutoAcceptJobController extends Controller
{
    public function autoaccept()
    {
        $bots  =InstaBot::pluck('user_name', 'id');
        return view('instagram::autoaccept', ['bots' => $bots]);
    }

    public function autoacceptPost(Request $request)
    {
        $bot = $request['bot'];
        AutoAcceptJob::dispatch($bot);
        return redirect('admin/insta-followers/datatable');
    }
}
