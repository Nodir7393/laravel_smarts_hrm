<?php

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

use Illuminate\Support\Facades\Route;
use Modules\Instagram\Http\Controllers\InstaBotsController;
use Modules\Instagram\Http\Controllers\InstagramController;
use Modules\Instagram\Http\Controllers\JobsController;

Route::get('/instagram-welcome', [InstagramController::class, 'welcome'])->name('instagram-welcome');

Route::prefix('instagram')->group(function() {
    Route::get('/', 'InstagramController@index');
    Route::get('/chat', [InstagramController::class, 'chat']);
});

Route::post('/instagram-like', [InstagramController::class, 'like'])->name('instagram-like');
Route::get('/instagram-like', [InstagramController::class, 'showLike'])->name('instagram.like-show');

Route::post('/insta-one-like', [InstagramController::class, 'oneLike'])->name('one-like');
Route::get('/insta-one-like', [InstagramController::class, 'showOneLike'])->name('show-one-like');

Route::get('/login', [InstagramController::class, 'showLogin'])->name('insta-bots');
Route::get('/add-user', [InstagramController::class, 'showAddUser'])->name('insta-users');

Route::get('/bot-login', [InstaBotsController::class, 'botLogin'])->name('bot-login');

Route::post('/parse-user', [InstaBotsController::class, 'parseUser'])->name('parse-users');
Route::get('/show-parse-user', [InstagramController::class, 'showParseUser'])->name('show-parse-users');
//Route::post('/show-parse-user', [InstagramController::class, 'showParseUser'])->name('show-parse-users');
Route::post('/user-add', [InstaBotsController::class, 'addUser'])->name('add-users');

Route::post('/add-bot', [InstaBotsController::class, 'addBot'])->name('add-bot');

Route::get('/comment-post', [InstagramController::class, 'showComment'])->name('comment-show');
Route::post('/comment-post', [InstagramController::class, 'comment'])->name('comment-post');


Route::get('/user-post', [InstagramController::class, 'showPost'])->name('user-post-show');
Route::post('/user-post', [InstagramController::class, 'post'])->name('user-post');

Route::get('/tag-users', [InstagramController::class, 'showTagUsers'])->name('tag-users-show');
Route::post('/tag-users', [InstagramController::class, 'tagUsers'])->name('tag-users');

Route::get('/tag-search', [InstagramController::class, 'showTagSearch'])->name('show-tag-search');
Route::post('/tag-search', [InstagramController::class, 'tagSearch'])->name('parse-with-tag-search');

Route::get('/random-like', [InstagramController::class, 'showLikeRandom'])->name('show-like-random');
Route::post('/random-like', [InstagramController::class, 'likeRandom'])->name('like-random');

Route::get('/random-comment', [InstagramController::class, 'showCommentRandom'])->name('show-comment-random');
Route::post('/random-comment', [InstagramController::class, 'commentRandom'])->name('comment-random');

Route::get('/follow', [\Modules\Instagram\Http\Controllers\FollowJobController::class, 'follow'])->name('follow');
Route::post('/follow', [\Modules\Instagram\Http\Controllers\FollowJobController::class, 'followPost'])->name('followPost');

Route::get('/unfollow', [\Modules\Instagram\Http\Controllers\FollowJobController::class, 'unfollow'])->name('unfollow');
Route::post('/unfollow', [\Modules\Instagram\Http\Controllers\FollowJobController::class, 'unfollowPost'])->name('unfollowPost');

Route::get('/storyview', [\Modules\Instagram\Http\Controllers\StoryViewController::class, 'storyview'])->name('storyview');
Route::post('/storyview', [\Modules\Instagram\Http\Controllers\StoryViewController::class, 'storyviewPost'])->name('storyviewPost');

Route::get('/autoaccept', [\Modules\Instagram\Http\Controllers\AutoAcceptJobController::class, 'autoaccept'])->name('autoaccept');
Route::post('/autoaccept', [\Modules\Instagram\Http\Controllers\AutoAcceptJobController::class, 'autoacceptPost'])->name('autoacceptPost');

Route::get('/admin/jobs', [JobsController::class, 'create'])->name('show.jobs');
Route::post('/admin/s-jobs', [JobsController::class, 'selectJob'])->name('select.job');
Route::post('/admin/c-jobs', [JobsController::class, 'createJob'])->name('create.job');
