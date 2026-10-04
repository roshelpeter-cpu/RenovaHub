<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Project::class);

        $filter = $request->string('filter')->toString();
        $query = $request->user()->notifications();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif (in_array($filter, ['projects', 'quotations', 'payments', 'messages', 'design'], true)) {
            $query->where('data->category', $filter);
        }

        return view('homeowner.notifications.index', [
            'notifications' => $query->paginate(15)->withQueryString(),
            'filter' => $filter ?: 'all',
        ]);
    }

    public function read(Request $request, string $notification): RedirectResponse
    {
        Gate::authorize('viewAny', Project::class);

        $record = $request->user()->notifications()->where('id', $notification)->firstOrFail();
        $record->markAsRead();

        $url = $record->data['url'] ?? null;

        return $url ? redirect($url) : back();
    }

    public function readAll(Request $request): RedirectResponse
    {
        Gate::authorize('viewAny', Project::class);

        $request->user()->unreadNotifications->markAsRead();

        return back()->with('status', 'All notifications marked as read.');
    }
}
