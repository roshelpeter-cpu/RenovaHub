<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProfessionalController extends Controller
{
    /**
     * Portfolio pages are limited to designer and contractor profiles.
     */
    public function show(User $professional): View
    {
        Gate::authorize('viewAny', Project::class);

        abort_unless($professional->isDesigner() || $professional->isContractor(), 404);

        $professional->load('professionalProfile.portfolioItems');

        abort_unless($professional->professionalProfile !== null, 404);

        $professional->professionalProfile->setRelation('user', $professional);

        return view('homeowner.professionals.show', [
            'professional' => $professional,
            'profile' => $professional->professionalProfile,
        ]);
    }
}
