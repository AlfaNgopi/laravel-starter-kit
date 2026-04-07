<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('x-api-key');

        // In production, store keys in a database or .env
        if ($apiKey !== config('app.api_key')) {
            return response()->json(['error' => 'Invalid API Key', ], 401);
        }

        return $next($request);
    }
}
