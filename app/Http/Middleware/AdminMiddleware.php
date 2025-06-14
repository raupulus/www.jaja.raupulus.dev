<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {


        // TODO: Implementar roles de usuario y actualizar este control a algo más firme

        if (auth()->id() && auth()->user()->isAdmin) {
            return $next($request);
        } else if (auth()->id()) {
            return redirect()->route('filament.user.pages.dashboard');
        }

        return $next($request);
    }
}
