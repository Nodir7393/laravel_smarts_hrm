<?php

use App\Services\TestService;
use Illuminate\Support\Facades\Artisan;
use Modules\Instagram\Http\Controllers\InstagramController;
use Modules\Instagram\Service\InstaFollowService;
use Modules\Instagram\Service\InstaService;
use Modules\Instagram\Service\InstaTagService;
use Modules\RemoveJoinLeave\Services\RemoveJoinLeaveService;
use Modules\DinnerBot\Services\DinnerBotService;
use Modules\ManagerBot\Services\ManagerBotService;
use Illuminate\Support\Facades\Route;
use TCG\Voyager\Facades\Voyager;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', function(){
    \Illuminate\Support\Facades\Http::post('https://api.tlgr.org/bot5755283417:AAH8KVN-rXRaJI-4uuYhytfwnMVPiS1dys0/sendMessage', [
        'chat_id' => 5489929315,
        'text' => 'Hello'
    ]);
});
Route::get('/debug-sentry', function () {
    throw new Exception('My first Sentry error!');
});

Route::get('/test', function () {
    Artisan::call('test:run');
});

Route::post('/webhook', [ManagerBotService::class, 'web']);
Route::post('/webhook-dinner', [DinnerBotService::class, 'web']);
Route::post('/webhook-remover', [RemoveJoinLeaveService::class, 'web']);

Route::group(['prefix' => 'admin'], function () {
    Route::group(['prefix' => 'instagram', 'middleware' => 'admin.user', 'as' => 'instagram.'], function () {
        Route::get('/login', [InstagramController::class, 'login'])->name('view.login');
        Route::post('/login', [InstagramController::class, 'formLogin'])->name('login');
        Route::get('/import-username', [InstagramController::class, 'import'])->name('view.import');
        Route::post('/import-file', [InstagramController::class, 'fileUpload'])->name('import');
    });
    Voyager::routes();
});
