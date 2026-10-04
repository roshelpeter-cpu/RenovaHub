<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Homeowners see their own project summary. Other roles keep the
     * existing account dashboard until those workspaces are built.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if (! $user->isHomeowner()) {
            return view('dashboard');
        }

        return view('homeowner.home', app(DashboardService::class)->home($user));
    }
}
