<?php

namespace App\Providers;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Apple\Provider as AppleProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $systemSetting = SystemSetting::first();
            $view->with('systemSetting', $systemSetting);
        });

        Socialite::extend('apple', function ($app) {
            $config = $app['config']['services.apple'];
            return Socialite::buildProvider(
                AppleProvider::class,
                $config
            );
        });
    }
}
