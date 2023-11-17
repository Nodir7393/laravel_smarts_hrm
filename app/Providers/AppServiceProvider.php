<?php

namespace App\Providers;

use App\Actions\InstaLike;
use App\Actions\InstaLogin;
use App\Actions\InstaTagPost;
use App\Actions\ParseUser;
use App\Actions\Resume;
use App\Actions\ResumeDoc;
use App\Actions\ResumePdf;
use Illuminate\Support\ServiceProvider;
use Modules\ALL\Models\TgChannelText;
use Modules\ALL\Models\TgGroupText;
use Modules\ALL\Observers\ChannelMessageObserver;
use Modules\ALL\Observers\GroupMessageObserver;
use Modules\Instagram\Entities\InstaComment;
use Modules\Instagram\Observers\InstaCommentObserver;
use TCG\Voyager\Facades\Voyager;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Voyager::addAction(ParseUser::class);
        Voyager::addAction(InstaLogin::class);
        Voyager::addAction(ResumeDoc::class);
        Voyager::addAction(ResumePdf::class);
        Voyager::addAction(InstaLike::class);
        Voyager::addAction(InstaTagPost::class);
        Voyager::addAction(\App\Actions\HHUsers::class);
        Voyager::addAction(\App\Actions\MyAction::class);
        Voyager::addAction(\App\Actions\VacancyShow::class);
        Voyager::addAction(\App\Actions\VacancyParse::class);
        Voyager::addAction(\App\Actions\VacancyParsed::class);
        Voyager::addAction(\App\Actions\HHUsersVacansy::class);
        TgGroupText::observe(GroupMessageObserver::class);
        InstaComment::observe(InstaCommentObserver::class);
        TgChannelText::observe(ChannelMessageObserver::class);
    }
}
