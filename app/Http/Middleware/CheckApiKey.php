<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasHeader('x-api-key')) {
            if ($request->header('x-api-key') == env('ORDER_API_KEY')) {
                return $next($request);
            }
        }
        return response()->json(['message' => __('Invalid key')], 400);
    }
}
