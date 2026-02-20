<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\PostTooLargeException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Add your middleware alias here
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckUserRole::class,
            'bugreply' => \App\Http\Middleware\CheckForBugReply::class,
        ]);
        // Apply bug reply checker to web group globally
        $middleware->appendToGroup('web', [
            'bugreply'
        ]);

        // Trust reverse proxies (e.g., Cloudflare) so HTTPS and host
        // are detected correctly when generating URLs and assets.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (PostTooLargeException $e, Request $request) {
            $maxPost = (string) ini_get('post_max_size');
            $maxUpload = (string) ini_get('upload_max_filesize');
            $message = "Upload too large. Server limits: post_max_size={$maxPost}, upload_max_filesize={$maxUpload}.";

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => $message,
                    'errors' => ['file' => [$message]],
                ], 413);
            }

            return back()->withErrors(['file' => $message]);
        });
    })->create();
