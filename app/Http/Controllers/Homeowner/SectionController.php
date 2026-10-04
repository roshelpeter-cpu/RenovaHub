<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class SectionController extends Controller
{
    /**
     * Sections that are visible in navigation but not built yet.
     *
     * @var array<string, string>
     */
    public const SECTIONS = [
        'tasks' => 'homeowner.tasks.index',
        'documents' => 'homeowner.documents.index',
        'mood-board' => 'homeowner.mood-board.index',
        'quotations' => 'homeowner.quotations.index',
        'change-requests' => 'homeowner.change-requests.index',
        'payments' => 'homeowner.payments.index',
        'notifications' => 'homeowner.notifications.index',
    ];

    /**
     * Older sidebar links now open the real workspace pages.
     */
    public function show(string $section): RedirectResponse
    {
        Gate::authorize('viewAny', \App\Models\Project::class);

        abort_unless(array_key_exists($section, self::SECTIONS), 404);

        return redirect()->route(self::SECTIONS[$section]);
    }
}
