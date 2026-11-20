<?php

namespace App\Providers;

use App\Models\DeliveryAssignment;
use App\Observers\DeliveryAssignmentObserver;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\URL;
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
        DeliveryAssignment::observe(DeliveryAssignmentObserver::class);
        Paginator::useBootstrapFive();

        // Force HTTPS on production to prevent Mixed Content errors
        if ($this->app->environment('production', 'staging')) {
            URL::forceScheme('https');
        }

        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
