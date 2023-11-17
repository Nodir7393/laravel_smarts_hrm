<?php

namespace Modules\Instagram\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Modules\Instagram\Console\AutoAcceptCommand;
use Modules\Instagram\Console\FollowUserCommand;
use Modules\Instagram\Console\HighlightCommand;
use Modules\Instagram\Console\InstaComments\AddCommentCommand;
use Modules\Instagram\Console\InstaComments\TagUserByLinkCommand;
use Modules\Instagram\Console\InstaComments\TagUserCommand;
use Modules\Instagram\Console\InstaDirect\DirectCommand;
use Modules\Instagram\Console\InstaFollowers\FollowerCommand;
use Modules\Instagram\Console\InstaFollows\FollowCommand;
use Modules\Instagram\Console\InstaFollows\FollowUserComand;
use Modules\Instagram\Console\InstaFollows\UnfollowUserComand;
use Modules\Instagram\Console\InstaPosts\InstaPostsCommand;
use Modules\Instagram\Console\InstaPosts\PostLikeCommand;
use Modules\Instagram\Console\LikeCommand;
use Modules\Instagram\Console\LoginCommand;
use Modules\Instagram\Console\ParseUserCommand;
use Modules\Instagram\Console\ReelsCommand;
use Modules\Instagram\Console\StoryViewer;
use Modules\Instagram\Console\UserCommand;
use Modules\Instagram\Models\InstaJob;

class InstagramServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Instagram';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'instagram';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));
        $this->commands([
            DirectCommand::class,
            FollowCommand::class,
            FollowerCommand::class,
            ReelsCommand::class,
            UserCommand::class,
            InstaPostsCommand::class,
            HighlightCommand::class,
            LoginCommand::class,
            InstaPostsCommand::class,
            AddCommentCommand::class,
            TagUserCommand::class,
            TagUserByLinkCommand::class,
            PostLikeCommand::class,
            FollowUserComand::class,
            UnfollowUserComand::class,
            ParseUserCommand::class,
            LikeCommand::class,
            FollowUserCommand::class,
            StoryViewer::class,
            AutoAcceptCommand::class
        ]);

        /*Queue::before(function (JobProcessing $event) {
            $model = new InstaJob();

        });*/
        /*Queue::after(function (JobProcessing $event) {
            // $event->connectionName
            // $event->job
            // $event->job->payload()
        });*/
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath, $this->moduleNameLower);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
