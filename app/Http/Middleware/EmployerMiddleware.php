<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EmployerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!auth()->user()) return errorResponse("Unauthenticated", [], 321);
        if(auth()->user()->role !== 2) return errorResponse("Unauthorized", [], 321);
        return $next($request);
    }
}
