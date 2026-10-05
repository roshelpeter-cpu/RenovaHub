<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Homeowners see their own project summary. Designers and contractors
     * are sent to their own workspaces.
     */
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->isDesigner()) {
            return redirect()->route('designer.dashboard');
        }

        if ($user->isContractor()) {
            return redirect()->route('contractor.dashboard');
        }

        if (! $user->isHomeowner()) {
            return view('dashboard');
        }

        return view('homeowner.home', app(DashboardService::class)->home($user));
    }
}
