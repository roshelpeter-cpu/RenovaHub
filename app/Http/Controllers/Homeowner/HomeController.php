<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Homeowner Home is the signed-in landing page.
     * Project modules stay as shortcuts here rather than top-level navigation.
     */
    public function __invoke(Request $request, DashboardService $dashboard): View
    {
        return view('homeowner.home', $dashboard->home($request->user()));
    }
}
