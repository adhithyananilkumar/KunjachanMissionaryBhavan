<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\NotificationComposer;

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
        // Share notification data with navigation + sidebar + app layout
        View::composer([
            'layouts.navigation',
            'layouts.partials.sidebar',
            'layouts.app',
        ], NotificationComposer::class);
    }
}
