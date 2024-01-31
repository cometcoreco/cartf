<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictApi
{
    public function handle(Request $request, Closure $next)
    {
        return response()->json([
            __('Mobile API is under maintenance for development. Another update will release for the script API and mobile application')
        ]);
    }
}
