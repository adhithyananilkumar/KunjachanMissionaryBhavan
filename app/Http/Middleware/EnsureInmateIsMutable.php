<?php

namespace App\Http\Middleware;

use App\Models\Inmate;
use Closure;
use Illuminate\Http\Request;

class EnsureInmateIsMutable
{
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        if (!$route) {
            return $next($request);
        }

        $inmate = $route->parameter('inmate');
        if (!$inmate instanceof Inmate) {
            return $next($request);
        }

        if (in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return $next($request);
        }

        $routeName = (string) ($route->getName() ?? '');
        if ($routeName !== '' && str_contains($routeName, '.inmates.status.')) {
            return $next($request);
        }

        $status = $inmate->status ?: Inmate::STATUS_PRESENT;
        if ($status === Inmate::STATUS_PRESENT) {
            return $next($request);
        }

        $message = match ($status) {
            Inmate::STATUS_DECEASED => 'This inmate is marked as deceased. Changes are not allowed.',
            Inmate::STATUS_DISCHARGED => 'This inmate is discharged. Changes are not allowed until re-joined.',
            Inmate::STATUS_TRANSFERRED => 'This inmate is transferred. Changes are not allowed until re-joined.',
            default => 'This inmate is not active. Changes are not allowed.',
        };

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'ok' => false,
                'message' => $message,
            ], 403);
        }

        return back()->with('error', $message);
    }
}
