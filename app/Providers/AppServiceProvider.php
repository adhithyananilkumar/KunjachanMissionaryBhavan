<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
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
        // Use the app's compact, mobile-friendly pagination component as the default
        Paginator::defaultView('components.pagination.simple');
        Paginator::defaultSimpleView('components.pagination.simple');

        // Share notification data with navigation + sidebar + app layout
        View::composer([
            'layouts.navigation',
            'layouts.partials.sidebar',
            'layouts.app',
        ], NotificationComposer::class);
    }
}
