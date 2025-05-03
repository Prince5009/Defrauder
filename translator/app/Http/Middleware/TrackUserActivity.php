<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserActivity;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            UserActivity::updateActivity(auth()->id());
        }

        return $next($request);
    }
} 