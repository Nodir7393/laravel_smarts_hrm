<?php

namespace Modules\HHParse\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Modules\HHParse\Services\HHUserService;

class HHParseController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function vacancy(Request $request)
    {
        logger($request);
        $vacancy_id = $request->vacancy;
        Log::info('vacancy_id: '. $vacancy_id);
        dispatch( function() use ($vacancy_id){
            Artisan::call("hhparse:run --vacancy=$vacancy_id");
            logger('job done!');
        });
        return Redirect::back();
    }


    public function parseAll()
    {
        dispatch( function() {
            Artisan::call('hhparse:run');
            logger('job done!');
        });
        return Redirect::back();
    }

    public function parseUser(Request $request) {
        $user_id = $request->user_id;
        dispatch( function() use ($user_id) {
            Artisan::call("hhparse:run --users=$user_id");
            logger('job done!');
        });
        return Redirect::back();
    }

    public function getVacancy(Request $request) {
        $user_id = $request->user_id;
        dispatch(function() use ($user_id) {
                Artisan::call("hhparse:vacancies --user_id=$user_id");
                logger('job done!');
            }
        );
        return Redirect::back();
    }

    public function viewDoc(Request $request)
    {
        $headers = [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="'.$request->resume_id.'.doc"'
        ];
        return response()->file(env('RESUMES_PATH')."\doc\\$request->resume_id.doc", $headers);
    }

    public function viewPdf(Request $req)
    {
        return response()->file(env('RESUMES_PATH')."\pdf\\$req->resume_id.pdf");
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('hhparse::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('hhparse::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('hhparse::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function profile(Request $req)
    {
        $service = new HHUserService();
        $service->saveUser($req->input('name'), $req->input('email'), $req->input('password'));
        return redirect('/admin/hh-users/datatable');
    }

    public function showLogin()
    {
        return view('hhparse::hhLogin');
    }
}
