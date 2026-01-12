<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\SupportTicket;

class CheckForBugReply
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            // Legacy placeholder: could be repurposed or removed.
        }
        return $next($request);
    }
}
