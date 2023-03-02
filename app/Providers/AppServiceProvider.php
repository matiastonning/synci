<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Enable stricter rate limiting for trial users. Paying users get a higher limit.

        RateLimiter::for('email_parse', function ($job) {
            if($job->user->count() && $job->user) {
                return ($job->user->subscribed('default') && !$job->user->onTrial('default'))
                    ? Limit::perMinute(30)->by($job->user->id)
                    : Limit::perHour(60)->by($job->user->id);
            } else {
                return null;
            }
        });
    }
}
