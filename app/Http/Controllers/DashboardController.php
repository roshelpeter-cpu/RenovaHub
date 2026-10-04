<?php

namespace App\Http\Controllers;

use App\Models\Project;
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

        $projects = $user->projects()->latest()->limit(5)->get();

        return view('homeowner.dashboard', [
            'projects' => $projects,
            'counts' => [
                'active' => $user->projects()->where('status', Project::STATUS_IN_PROGRESS)->count(),
                'planning' => $user->projects()->where('status', Project::STATUS_PLANNING)->count(),
                'completed' => $user->projects()->where('status', Project::STATUS_COMPLETED)->count(),
                // Quotations, payments and change requests are not stored yet.
                'quotations' => 0,
                'payments' => 0,
                'changes' => 0,
            ],
        ]);
    }
}
