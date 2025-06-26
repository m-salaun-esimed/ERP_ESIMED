<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectFirstLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (
            auth()->check() &&
            !$request->is('first-login') &&
            !$request->is('login') &&
            !$request->is('register') &&
            !auth()->user()->first_login_completed
        ) {
            return redirect()->route('filament.admin.pages.first-login');
        }

        return $next($request);
    }
}