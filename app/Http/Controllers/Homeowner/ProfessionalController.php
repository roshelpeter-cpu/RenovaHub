<?php

namespace App\Http\Controllers\Homeowner;

use App\Http\Controllers\Controller;
use App\Models\ProfessionalProfile;
use App\Models\User;
use App\Services\ConversationService;
use App\Services\ExploreProfessionalsService;
use Illuminate\Http\RedirectResponse;
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
     * Profile overview matches the directory card destination (View Profile).
     * Private contact details stay off this page on purpose.
     */
    public function show(User $professional): View
    {
        $profile = $this->publicProfile($professional);
        $tab = request()->string('tab')->toString() ?: 'overview';

        return view('homeowner.professionals.show', [
            'professional' => $professional,
            'profile' => $profile,
            'tab' => $tab,
            'caseStudies' => $profile->caseStudies,
            'favourite' => $profile->favourites()->where('user_id', auth()->id())->exists(),
        ]);
    }

    /**
     * Case-study pages are bound through the profile so a slug from another
     * designer cannot be opened by swapping the URL id.
     */
    public function project(User $professional, string $caseStudy): View
    {
        $profile = $this->publicProfile($professional);
        $project = $profile->caseStudies()->where('slug', $caseStudy)->firstOrFail();
        $project->load('images');

        return view('homeowner.professionals.project', [
            'professional' => $professional,
            'profile' => $profile,
            'caseStudy' => $project,
            'gallery' => collect([$project->hero_image])->merge($project->images->pluck('path'))->unique()->values(),
            'more' => $profile->caseStudies
                ->where('id', '!=', $project->id)
                ->reject(fn ($item) => $item->category === 'Commercial')
                ->take(4)
                ->values(),
            'testimonial' => $profile->reviews->first(),
        ]);
    }

    public function favourite(User $professional, ExploreProfessionalsService $explore): RedirectResponse
    {
        $explore->toggleFavourite(auth()->user(), $this->publicProfile($professional)->id);

        return back();
    }

    /**
     * Contact never leaves RenovaHub. Find the existing thread for this
     * homeowner, professional and villa (when the homeowner owns one) so a
     * second click does not invent a duplicate inbox row.
     */
    public function contact(User $professional, ConversationService $conversations): RedirectResponse
    {
        $this->publicProfile($professional);
        $conversation = $conversations->findOrCreate(
            request()->user(),
            $professional,
            $conversations->projectFor(request()->user()),
        );

        return redirect()->route('homeowner.messages.show', $conversation);
    }

    private function publicProfile(User $professional): ProfessionalProfile
    {
        Gate::authorize('viewAny', ProfessionalProfile::class);
        abort_unless($professional->isDesigner() || $professional->isContractor(), 404);

        $professional->load(['professionalProfile.caseStudies.images', 'professionalProfile.reviews', 'professionalProfile.services', 'professionalProfile.portfolioItems']);
        abort_unless($professional->professionalProfile !== null, 404);

        Gate::authorize('view', $professional->professionalProfile);
        $professional->professionalProfile->setRelation('user', $professional);

        return $professional->professionalProfile;
    }
}
