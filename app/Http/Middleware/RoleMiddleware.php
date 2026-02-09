<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (Auth::check()) {

            if (in_array(Auth::user()->roll, $roles)) {
                return $next($request);
            }
            return redirect('/')->with('error', 'Sie haben keine Berechtigung, diese Seite zu betreten.');
        }
        return redirect('login');
    }
}
