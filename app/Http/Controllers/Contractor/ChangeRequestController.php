<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contractor\RespondChangeRequestRequest;
use App\Models\ChangeRequest;
use App\Services\Contractor\ChangeRequestService;
use App\Services\Contractor\ContractorWorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChangeRequestController extends Controller
{
    public function index(Request $request, ContractorWorkspaceService $workspace): View
    {
        $projects = $workspace->accepted($request->user())->pluck('id');

        $changes = ChangeRequest::query()
            ->whereIn('project_id', $projects)
            ->with(['project', 'requester'])
            ->latest()
            ->get();

        return view('contractor.change-requests.index', compact('changes'));
    }

    public function show(ChangeRequest $changeRequest): View
    {
        $this->authorize('view', $changeRequest);
        $changeRequest->load(['project', 'requester']);

        return view('contractor.change-requests.show', ['change' => $changeRequest]);
    }

    public function respond(RespondChangeRequestRequest $request, ChangeRequest $changeRequest, ChangeRequestService $changes): RedirectResponse
    {
        $changes->respond($request->user(), $changeRequest, [
            'cost_impact' => $request->input('cost_impact'),
            'timeline_impact' => $request->string('timeline_impact')->toString(),
            'feasibility' => $request->string('feasibility')->toString(),
            'contractor_response' => $request->string('contractor_response')->toString(),
            'design_affected' => $request->boolean('design_affected'),
        ]);

        return redirect()
            ->route('contractor.change-requests.show', $changeRequest)
            ->with('status', 'Cost and time impact sent to the homeowner.');
    }
}
