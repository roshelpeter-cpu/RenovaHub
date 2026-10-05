<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsDesigner
{
    /**
     * The designer workspace is a separate site section.
     * The role check lives here so editing the URL cannot open another role's pages.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->isDesigner()) {
            abort(403);
        }

        return $next($request);
    }
}
