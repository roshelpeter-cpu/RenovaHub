<?php

namespace App\Http\Controllers\Contractor;

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
            ->where('role', 'contractor')
            ->where('status', $tab)
            ->with(['project.homeowner', 'project.referenceImages'])
            ->latest()
            ->get();

        return view('contractor.invitations.index', compact('invitations', 'tab'));
    }

    public function show(Request $request, ProjectInvitation $invitation): View
    {
        abort_unless($invitation->user_id === $request->user()->id && $invitation->role === 'contractor', 404);
        $invitation->load(['project.homeowner', 'project.referenceImages']);

        return view('invitations.professional-show', [
            'invitation' => $invitation,
            'project' => $invitation->project,
            'layout' => 'contractor-layout',
            'roleLabel' => 'Contractor',
        ]);
    }

    public function accept(Request $request, ProjectInvitation $invitation, ProjectInvitationService $invitations): RedirectResponse
    {
        $this->authorize('respond', $invitation);
        abort_unless($invitation->role === 'contractor', 404);

        $invitations->respond($invitation, $request->user(), ProjectInvitation::STATUS_ACCEPTED);

        return redirect()
            ->route('contractor.projects.show', $invitation->project_id)
            ->with('status', 'Offer accepted. The project is now in My Projects.');
    }

    public function reject(Request $request, ProjectInvitation $invitation, ProjectInvitationService $invitations): RedirectResponse
    {
        $this->authorize('respond', $invitation);
        abort_unless($invitation->role === 'contractor', 404);

        $invitations->respond($invitation, $request->user(), ProjectInvitation::STATUS_DECLINED);

        return redirect()
            ->route('contractor.invitations.index', ['tab' => 'declined'])
            ->with('status', 'Offer declined. The homeowner can invite another contractor.');
    }
}
