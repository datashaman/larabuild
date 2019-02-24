<?php

namespace App\Providers;

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
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
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
