<?php

namespace App\Providers;

use App\Broadcasting\SmsChannel;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Notification::extend('sms', fn ($app) => new SmsChannel());
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('app.env') === 'production') {
            $this->app['request']->server->set('HTTPS', true);
        }
    }
}
