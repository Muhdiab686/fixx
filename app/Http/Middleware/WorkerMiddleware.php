<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WorkerMiddleware
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
        $DealerRole = auth()->user()->role;
        if ($DealerRole === "worker") {
            return $next($request);
        }

        return response()->json(['msg' => 'Access Denied']);
    }
}
