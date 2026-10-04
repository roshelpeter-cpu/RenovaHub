<?php

namespace App\Http\Controllers;

use App\Http\Requests\RespondInvitationRequest;
use App\Models\ProjectInvitation;
use App\Services\ProjectInvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * This is the professional's reply, not a designer or contractor dashboard.
 */
class InvitationController extends Controller
{
    public function show(ProjectInvitation $invitation): View
    {
        Gate::authorize('respond', $invitation);

        $invitation->load('project');

        return view('invitations.show', ['invitation' => $invitation]);
    }

    public function respond(RespondInvitationRequest $request, ProjectInvitation $invitation, ProjectInvitationService $invitations): RedirectResponse
    {
        $invitations->respond($invitation, $request->user(), $request->string('decision')->toString());

        return redirect()
            ->route('dashboard')
            ->with('status', 'Your response has been sent to the homeowner.');
    }
}
