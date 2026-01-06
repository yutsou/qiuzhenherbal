<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureEmailIsValid
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
        if(isset(Auth::user()->oauth_type)) {
            return $next($request);
        } else {
            if(isset(Auth::user()->email_verified_at)) {
                return $next($request);
            } else {
                return redirect()->route('account.email_verification.request');
            }
        }
    }
}
