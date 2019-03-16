<?php

namespace App\Providers;

use App\Models\Build;
use App\Models\User;
use App\Observers\BuildObserver;
use App\Observers\UserObserver;
use Docker\Docker;
use GitWrapper\GitWrapper;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Build::observe(BuildObserver::class);
        User::observe(UserObserver::class);
    }

   /**
    * Register any application services.
    *
    * @return void
    */
    public function register()
    {
        $this->app->singleton(
            Docker::class,
            function ($app) {
                return Docker::create();
            }
        );

        $this->app->singleton(
            GitWrapper::class,
            function ($app) {
                putenv('PATH=/bin:/usr/bin');

                $wrapper = new GitWrapper();

                $home = storage_path('app/home');
                if (!is_dir($home)) {
                    mkdir($home);
                }

                $wrapper->setEnvVar('HOME', $home);

                return $wrapper;
            }
        );
    }
}
