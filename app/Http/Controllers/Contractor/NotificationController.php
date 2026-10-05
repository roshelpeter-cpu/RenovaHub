<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        return view('contractor.notifications.index', [
            'notifications' => $request->user()->notifications()->latest()->limit(40)->get(),
        ]);
    }

    public function read(Request $request, string $notification): RedirectResponse
    {
        $row = $request->user()->notifications()->where('id', $notification)->firstOrFail();
        $row->markAsRead();

        $url = $row->data['url'] ?? null;

        return $url ? redirect($url) : back();
    }
}
