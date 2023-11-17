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
use Modules\HHParse\Http\Controllers\HHParseController;
use Modules\HHParse\Services\HHParseService;
use Modules\HHParse\Services\HHResumesDownloader;
use Modules\HHParse\Services\HHUserService;

Route::get('/hhparse', [HHParseController::class, 'vacancy'])->name('hhparse');

Route::get('/getvacancy', [HHParseController::class, 'getVacancy'])->name('get-vacancy');

Route::get('/hhparse-user', [HHParseController::class, 'parseUser'])->name('hhparseUser');

Route::get('/hhparse-all', [HHParseController::class, 'parseAll'])->name('hhparseAll');

Route::get('/doc', [HHParseController::class, 'viewDoc'])->name('hhdoc');

Route::get('/pdf', [HHParseController::class, 'viewPdf'])->name('hhpdf');

Route::post('/hh-profile', [HHParseController::class, 'profile'])->name('hh-profile');

Route::get('/hh-profile', [HHParseController::class, 'showLogin'])->name('hh-register');

/*Route::prefix('hhparse')->group(function() {
    Route::get('/', 'HHParseController@index');
});*/
