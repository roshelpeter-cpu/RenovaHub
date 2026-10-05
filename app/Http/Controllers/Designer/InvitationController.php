<?php

namespace App\Http\Controllers\Designer;

use App\Http\Controllers\Controller;
use App\Models\ProjectInvitation;
use App\Services\ProjectInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function index(Request $request): View
    {
        $tab = $request->string('tab')->toString();
        $tab = in_array($tab, ['pending', 'accepted', 'declined'], true) ? $tab : 'pending';

        $invitations = ProjectInvitation::query()
            ->where('user_id', $request->user()->id)
            ->where('role', 'designer')
            ->where('status', $tab)
            ->with(['project.homeowner', 'project.referenceImages'])
            ->latest()
            ->get();

        return view('designer.invitations.index', compact('invitations', 'tab'));
    }

    public function accept(Request $request, ProjectInvitation $invitation, ProjectInvitationService $invitations): RedirectResponse
    {
        $this->authorize('respond', $invitation);
        $invitations->respond($invitation, $request->user(), ProjectInvitation::STATUS_ACCEPTED);

        return redirect()
            ->route('designer.projects.show', $invitation->project_id)
            ->with('status', 'Offer accepted. The project is now in My Projects.');
    }

    public function reject(Request $request, ProjectInvitation $invitation, ProjectInvitationService $invitations): RedirectResponse
    {
        $this->authorize('respond', $invitation);
        $invitations->respond($invitation, $request->user(), ProjectInvitation::STATUS_DECLINED);

        return redirect()
            ->route('designer.invitations.index', ['tab' => 'declined'])
            ->with('status', 'Offer declined. The homeowner can invite another designer.');
    }
}
