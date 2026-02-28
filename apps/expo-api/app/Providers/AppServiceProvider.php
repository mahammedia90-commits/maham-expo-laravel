<?php

namespace App\Providers;

use App\Services\OneSignal\OneSignalNotifier;
use App\Services\OneSignal\OneSignalUserManager;
use App\Services\NotificationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(OneSignalNotifier::class);
        $this->app->singleton(OneSignalUserManager::class);
        $this->app->singleton(NotificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
