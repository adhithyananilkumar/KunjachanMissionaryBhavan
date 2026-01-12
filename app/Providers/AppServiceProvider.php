<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use App\View\Composers\NotificationComposer;
use App\Services\Pdf\PdfRenderer;
use App\Services\Pdf\DompdfRenderer;
use App\Services\Pdf\PdfManager;
use Illuminate\Support\Facades\Storage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerPdfBindings();
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

    protected function registerPdfBindings(): void
    {
        $this->app->bind(PdfRenderer::class, function () {
            $driver = config('pdf.default_renderer', 'dompdf');
            $map = config('pdf.renderers', []);
            $class = $map[$driver] ?? DompdfRenderer::class;

            return $this->app->make($class);
        });

        $this->app->singleton(PdfManager::class, function ($app) {
            return new PdfManager(
                $app->make(PdfRenderer::class),
                Storage::disk(config('filesystems.default')),
            );
        });
    }
}
