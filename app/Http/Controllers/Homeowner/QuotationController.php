<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Http\Requests\DecideQuotationRequest;
use App\Models\Project;
use App\Models\Quotation;
use App\Services\QuotationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class QuotationController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', Project::class);

        $quotations = Quotation::query()
            ->whereIn('project_id', request()->user()->projects()->select('id'))
            ->with(['project', 'contractor.professionalProfile'])
            ->latest()
            ->paginate(10);

        return view('homeowner.quotations.index', [
            'quotations' => $quotations,
            'project' => null,
        ]);
    }

    public function project(Project $project): View
    {
        Gate::authorize('view', $project);

        return view('homeowner.quotations.index', [
            'quotations' => $project->quotations()->with(['contractor.professionalProfile', 'project'])->latest()->paginate(10),
            'project' => $project,
        ]);
    }

    public function show(Project $project, Quotation $quotation): View
    {
        Gate::authorize('view', $quotation);
        abort_unless($quotation->project_id === $project->id, 404);

        $quotation->load(['items', 'contractor.professionalProfile']);

        return view('homeowner.quotations.show', [
            'project' => $project,
            'quotation' => $quotation,
        ]);
    }

    public function decide(DecideQuotationRequest $request, Project $project, Quotation $quotation, QuotationService $quotations): RedirectResponse
    {
        abort_unless($quotation->project_id === $project->id, 404);

        $quotations->decide(
            $quotation,
            $request->user(),
            $request->string('decision')->toString(),
            $request->string('notes')->toString() ?: null,
        );

        return back()->with('status', 'Quotation updated.');
    }
}
