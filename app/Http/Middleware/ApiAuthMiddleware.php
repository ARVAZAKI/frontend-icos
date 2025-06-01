<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ApiAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('api_token')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
            
            return redirect()->route('login')
                ->with('error', 'Anda harus login terlebih dahulu.');
        }

        return $next($request);
    }
}