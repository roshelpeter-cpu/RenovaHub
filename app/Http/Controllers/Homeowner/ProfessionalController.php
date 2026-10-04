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
     * Profile overview is a single continuous page. Inactive sub-tabs were
     * removed because they had no working content behind them.
     */
    public function show(User $professional): View
    {
        $profile = $this->publicProfile($professional);

        return view('homeowner.professionals.show', [
            'professional' => $professional,
            'profile' => $profile,
            'caseStudies' => $profile->caseStudies,
            'favourite' => $profile->favourites()->where('user_id', auth()->id())->exists(),
        ]);
    }

    /**
     * More Projects is the same profile content with a larger grid, still
     * without the inactive Overview/Reviews/FAQs strip.
     */
    public function portfolio(User $professional): View
    {
        $profile = $this->publicProfile($professional);

        return view('homeowner.professionals.portfolio', [
            'professional' => $professional,
            'profile' => $profile,
            'caseStudies' => $profile->caseStudies,
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
            'gallery' => $project->galleryPaths(),
            'more' => $profile->caseStudies
                ->where('id', '!=', $project->id)
                ->reject(fn ($item) => $item->category === 'Commercial')
                ->take(4)
                ->values(),
            'testimonial' => $project->client_body ? $project : $profile->reviews->first(),
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
