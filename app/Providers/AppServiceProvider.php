<?php

namespace App\Providers;

use App\Events\LeadCreated;
use App\Listeners\DispatchLeadAutomation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        Event::listen(
            LeadCreated::class,
            DispatchLeadAutomation::class,
        );

        RateLimiter::for('lead-intake', function (Request $request) {
            return Limit::perMinute(15)->by($request->ip());
        });

        RateLimiter::for('lead-retry', function (Request $request) {
            return Limit::perMinute(20)->by($request->ip());
        });
    }
}
