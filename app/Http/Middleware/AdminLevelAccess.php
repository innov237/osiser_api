<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;
use Illuminate\Http\Request;

use Illuminate\Contracts\Auth\Guard;

class AdminLevelAccess
{   
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next)
    {
        //if ( auth()->user() && auth()->user()->isAdmin()) {
        if (1 == 1){
            return $next($request);
        }
        

        return response()->json([
            'success' => false,
            'message' => 'NOT AUTHORIZED',
            'detail'  => 'Only admin can perform this action',
            'user' => $this->auth->user()
        ]);
    }
}
