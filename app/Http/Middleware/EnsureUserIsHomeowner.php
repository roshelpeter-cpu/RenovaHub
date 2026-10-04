<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsHomeowner
{
    /**
     * Designer and contractor workspaces are separate.
     * Hiding the sidebar is not enough, because the URL can be edited.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isHomeowner()) {
            abort(403);
        }

        return $next($request);
    }
}
