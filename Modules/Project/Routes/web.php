<?php

use Illuminate\Support\Facades\Route;
use Modules\Project\Services\HRMProjectsService;

Route::match(['get', 'post'] ,'/project', [HRMProjectsService::class, 'editProject'])->name('project');
/*Route::prefix('project')->group(function() {
    Route::get('/', 'ProjectController@index');`
});*/
