<?php

namespace Modules\Instagram\Http\Controllers;

use Amp\Ipc\Sync\PanicError;
use App\Imports\InstaUserImport;
use danog\MadelineProto\users;
use GuzzleHttp\Client;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Instagram\Entities\InstaBot;
use Modules\Instagram\Entities\InstagramUser;
use Modules\Instagram\Entities\InstaPost;
use Modules\Instagram\Entities\InstaUser;
use Modules\Instagram\Jobs\CommentPostsJob;
use Modules\Instagram\Jobs\CommentRandomJob;
use Modules\Instagram\Jobs\GetPostsJob;
use Modules\Instagram\Jobs\LikeOnePostJob;
use Modules\Instagram\Jobs\LikeRandomJob;
use Modules\Instagram\Jobs\LikeTagSearchJob;
use Modules\Instagram\Jobs\TagSearchJob;
use Modules\Instagram\Jobs\TagUsersJob;
use Modules\Instagram\Service\InstaLoginService;
use Modules\Instagram\Service\InstaPostsService;
use Modules\Instagram\Service\InstaService;
use Modules\Instagram\Service\InstaTagService;
use Phpfastcache\Helper\Psr16Adapter;

class InstagramController extends Controller
{
    public InstaLoginService $insta_service;

    public $user_name;

    public function __construct()
    {
        $this->insta_service = new InstaLoginService();
    }

    public function login(Request $request)
    {
        $session_id = Cookie::get('insta_session_id');
        if (empty($session_id)) {
            return view('instagram::login2');
        }
        return view('instagram::logged');


        /*$response = new Response();

        return $response->withCookie(cookie('name', 'Xabibulloh', 2));*/

        /*$this->insta_service->isLoggedIn();
        if ($this->insta_service->isLoggedIn()) {
            return 'logged in';
        }
        return view('instagram::login');*/
    }

    public function formLogin(Request $request)
    {

        $validated = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $username = $validated['username'];
        $password = $validated['password'];

        $datas = [];

        try {

            $cache =$this->insta_service->login($username, $password);
            foreach ($cache->get(md5($username)) as $key => $value) {
                $datas[$key] = $value;
            }
        } catch (\Exception $e) {

            $error = \Illuminate\Validation\ValidationException::withMessages([
                'login_error' => [$e->getMessage()],
            ]);

            throw $error;
        }

        if ($username && $password) {

            //dd($datas);
            $upd = InstagramUser::updateOrCreate(
                [
                    "user_name" => $username,
                ],
                [
                    "password" => $password,
                    "csrf_token" => $datas['csrftoken'],
                    "rur" => $datas['rur'],
                    "ds_user_id" => $datas['ds_user_id'],
                    "ig_did" => $datas['ig_did'],
                    "session_id" => $datas['sessionid'],
                    "ig_cb" => $datas['ig_cb'],
                ]
            );
        }

        return response()->view('instagram::logged')->withCookie(cookie('insta_session_id', $datas['sessionid'], 2));
    }

    public function index()
    {
        $post = new InstaPostsService();
        //dd($post->instagram->instagram->getMediaByCode('CnYbCDtqPEv'));
        $post->index('telbozor.uz');
        $actuals = $post->instagram->instagram->getMediaByCode('CnV5JvboGsq')['carouselMedia'];
        $hcount = 0;
        foreach ($actuals as $actual) {
            foreach ((array)$actual as $key => $item) {
                if (gettype($item) != 'array' && gettype($item) != 'object') {
                    /*echo('$table->' . gettype($item) . '("' . Str::after($key, 'Media') . '")->nullable();');
                    echo('<br>');
                    echo("'".Str::after($key, 'Media')."',");
                    echo('<br>');*/
                }
            }
            //dd($actual);
        }

        $post = new InstaPostsService();
        foreach ((array)$post->instagram->instagram->getMediaByCode('CnV5JvboGsq')['carouselMedia'] as $media) {
            foreach ($media as $key => $value) {
                dump(1);
                echo('$table->' . gettype($value) . '("' . $key . '");');
                echo('<br>');
            }
            //dd($media);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('instagram::create');
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
        $post = new InstaPostsService();
        $post->show($id);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('instagram::edit');
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

    public function import()
    {
        return view('instagram::import');
    }

    public function fileUpload(Request $req)
    {
        $req->validate([
            'file' => 'required|mimes:csv,txt,xlsx|max:2048'
        ]);
        if ($req->file()) {
            $fileName = Carbon::now()->format('Y-m-d_H-i-s') . '_' . $req->file->getClientOriginalName();
            $file_ext = $req->file('file')->getClientOriginalExtension();
            $filePath = $req->file('file')->storeAs('/public', $fileName);
            $import = new InstaTagService();

            if ($file_ext === 'csv') {
                $import->importFromCsv($filePath);
            } elseif ($file_ext === 'txt') {
                $import->importFromTxt($filePath);
            } elseif ($file_ext === 'xlsx') {
                $import->excelImport($filePath);
            }
        }

        return back()
            ->with('success', 'File has been uploaded.')
            ->with('file', $fileName);
    }

    public function like(Request $request)
    {
        $service = new InstaPostsService();
        $service->likePost($request->link);
    }

    public function showLogin()
    {
        return view('instagram::login2');
    }

    public function showAddUser()
    {
        return view('instagram::adduser');
    }

    public function showParseUser()
    {
        $bots = InstaBot::pluck('user_name', 'id');
        $users = InstaUser::pluck('username', 'id');
        $fullUsers = [];
        foreach ($users as $key => $user) {
            $fullUsers[$key.'|'.$user] = $user;
        }
        return view('instagram::parseuser', ['bot' => $bots, 'users' => $fullUsers]);
    }

    public function showLike()
    {
        $bots = InstaBot::pluck('user_name', 'id');
        $posts = InstaPost::pluck('link', 'id');
        return view('instagram::like', ['bot' => $bots, 'post' => $posts]);
    }


    public function comment(Request $request)
    {
        $message = $request['message'];
        $bot = $request['bot'];
        $posts = $request['posts'];
        CommentPostsJob::dispatch($message, $bot, $posts);
        return redirect('admin/insta-posts/datatable');
    }

    public function showComment()
    {
        $bots = InstaBot::pluck('user_name', 'id');
        $posts = InstaPost::pluck('link', 'id');
        return view('instagram::comment', ['bot' => $bots, 'post' => $posts]);
    }

    public function showPost()
    {
        $bots = InstaBot::pluck('user_name', 'id');
        $instaUser = InstaUser::pluck('username', 'id');
        return view('instagram::post', ['bot' => $bots, 'user' => $instaUser]);
    }

    public function post(Request $req)
    {
        $bot = $req['bot'];
        $user = $req['user'];
        GetPostsJob::dispatch($bot, $user);
        return redirect('admin/insta-posts/datatable');
    }

    public function showTagUsers(Request $req)
    {
        $bots = InstaBot::pluck('user_name', 'id');
        $link = $req['link'];
        return view('instagram::tag_posts', ['bot' => $bots, 'link' => $link]);
    }

    public function tagUsers(Request $req)
    {
        $bot = $req['bot'];
        $link = $req['link'];
        TagUsersJob::dispatch($bot, $link);
        return redirect('admin/insta-posts/datatable');
    }

    public function oneLike(Request $req)
    {
        $bot = $req['bot'];
        $link = $req['link'];
        LikeOnePostJob::dispatch($bot, $link);
        return redirect('admin/insta-posts/datatable');
    }

    public function showOneLike(Request $req)
    {
        $bots = InstaBot::pluck('user_name', 'id');
        $link = $req['link'];
        return view('instagram::one_like', ['bot' => $bots, 'link' => $link]);
    }

    public function showTagSearch()
    {
        $bots = InstaBot::pluck('user_name', 'id');
        return view('instagram::tag_search', ['bot' => $bots]);
    }

    public function tagSearch(Request $req)
    {
        $tag = $req['tag'];
        $bot = $req['bot'];
        $num = $req['number'];
        TagSearchJob::dispatch($tag, $bot, $num);
        return redirect('admin/insta-posts/datatable');
    }

    public function showLikeRandom()
    {
        $bots = InstaBot::pluck('user_name', 'id');
        return view('instagram::like', ['bot' => $bots]);
    }
    public function likeRandom(Request $request)
    {
        $bot = $request['bot'];
        $num = $request['number'];
        LikeRandomJob::dispatch($bot, $num);
        return redirect('admin/insta-posts/datatable');
    }

    public function showCommentRandom()
    {
        $bot = InstaBot::pluck('user_name', 'id');
        return view('instagram::comment', ['bot' => $bot]);
    }

    public function commentRandom(Request $req)
    {
        $bot = $req['bot'];
        $num = $req['number'];
        $comment = $req['message'];
        CommentRandomJob::dispatch($bot, $num, $comment);
        return redirect('admin/insta-posts/datatable');
    }

    public function welcome()
    {
        $bots = InstaBot::pluck('user_name', 'id');
        return view('instagram::welcome', ['bot' => $bots]);
    }
}
