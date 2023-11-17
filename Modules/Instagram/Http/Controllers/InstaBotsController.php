<?php

namespace Modules\Instagram\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Modules\Instagram\Entities\InstaUser;
use Modules\Instagram\Jobs\ParseUserCommand;
use Modules\Instagram\Jobs\ParseUserJob;
use Modules\Instagram\Service\LoginService;
use Modules\Instagram\Service\ParseService;

class InstaBotsController extends Controller
{
    protected $loginService;
    protected $parseService;

    public function __construct(LoginService $loginService, ParseService $parseService) {
        $this->loginService = $loginService;
        $this->parseService = $parseService;
    }

    /**
     * @param Request $request
     * @return void
     */
    public function addBot(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $username = $validated['username'];
        $password = $validated['password'];
        $login = $request['login'];
        $this->parseService->createBot($username, $password, $login);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function botLogin(Request $request) {
        $validated = $request->validate([
            'id' => 'required',
        ]);
        $id = $validated['id'];
        $this->loginService->login($id);
        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function addUser(Request $request) {
        $validated = $request->validate(['username' => 'required',]);
        $username = $validated['username'];
        $this->parseService->addUser($username);
        return redirect('insta-users');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function parseUser(Request $request)
    {
        $validated = $request->validate(['users' => 'required', 'bot' =>'required']);
        $fullUsers = [];
        foreach ($validated['users'] as $vUser) {
            $fullUsers[] = explode("|", $vUser);
        }
        $userIds = Arr::get($fullUsers, '0');
        $id = $validated['bot'];
        $userNames = InstaUser::whereIn('id', $userIds)->get();
        foreach ($fullUsers as $fullUser) {
            ParseUserJob::dispatch($fullUser, $id);
        }
        return redirect('/admin/insta-users/datatable');
    }
}
