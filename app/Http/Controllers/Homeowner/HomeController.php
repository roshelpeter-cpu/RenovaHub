<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Home is the screenshot dashboard. Workspace modules stay linked from
     * the top nav and the Manage Your Project row; their full workflows stay elsewhere.
     */
    public function __invoke(Request $request, DashboardService $dashboard): View
    {
        return view('homeowner.home', $dashboard->home($request->user()));
    }
}
