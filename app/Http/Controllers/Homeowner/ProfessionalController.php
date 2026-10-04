<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Models\ProfessionalProfile;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProfessionalController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', ProfessionalProfile::class);

        return view('homeowner.explore');
    }

    /**
     * Portfolio pages are limited to designer and contractor profiles.
     */
    public function show(Request $request, User $professional): View
    {
        Gate::authorize('viewAny', ProfessionalProfile::class);

        abort_unless($professional->isDesigner() || $professional->isContractor(), 404);

        $professional->load(['professionalProfile.portfolioItems.images', 'professionalProfile.reviews']);

        abort_unless($professional->professionalProfile !== null, 404);

        Gate::authorize('view', $professional->professionalProfile);

        $professional->professionalProfile->setRelation('user', $professional);

        $project = null;
        if ($request->filled('project')) {
            $project = Project::query()->findOrFail($request->integer('project'));
            Gate::authorize('update', $project);
        }

        $category = $request->string('category')->toString();

        return view('homeowner.professionals.show', [
            'professional' => $professional,
            'profile' => $professional->professionalProfile,
            'project' => $project,
            'category' => $category,
        ]);
    }
}
