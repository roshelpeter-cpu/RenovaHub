<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SectionController extends Controller
{
    /**
     * Sections that are visible in navigation but not built yet.
     *
     * @var array<string, string>
     */
    public const SECTIONS = [
        'tasks' => 'Tasks',
        'documents' => 'Documents',
        'mood-board' => 'Mood Board',
        'quotations' => 'Quotations',
        'change-requests' => 'Change Requests',
        'payments' => 'Payments',
        'notifications' => 'Notifications',
    ];

    /**
     * Show a clear unavailable state instead of sample records.
     */
    public function show(string $section): View
    {
        Gate::authorize('viewAny', \App\Models\Project::class);

        abort_unless(array_key_exists($section, self::SECTIONS), 404);

        return view('homeowner.upcoming', [
            'title' => self::SECTIONS[$section],
        ]);
    }
}
