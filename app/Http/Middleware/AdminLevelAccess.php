<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Http\Request;

class AdminLevelAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next)
    {
        if ( auth()->user() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        return response()->json([
            'success' => false,
            'message' => 'NOT AUTHORIZED',
            'detail'  => 'Only admin can perform this action'
        ]);
    }
}
