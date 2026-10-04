<?php

namespace App\Livewire;

use App\Models\ProfessionalProfile;
use App\Services\ExploreProfessionalsService;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Explore filtering, tabs, sort and favourites belong in Livewire so the
 * Blade template stays presentational. Query work stays in ExploreProfessionalsService.
 */
class ExploreProfessionals extends Component
{
    #[Url]
    public string $type = 'designer';

    public string $search = '';

    public string $location = '';

    public string $projectType = '';

    public string $priceRange = '';

    public string $sort = 'relevant';

    public function mount(): void
    {
        Gate::authorize('viewAny', ProfessionalProfile::class);

        $requested = request()->string('search')->toString();
        if ($requested !== '') {
            $this->search = $requested;
        }
    }

    public function updatedType(): void
    {
        $this->resetPageFiltersKeepType();
    }

    public function applyFilters(): void
    {
        // Filters already live on the component. This method exists so the
        // Filter button has a real Livewire action rather than a dead control.
    }

    public function toggleFavourite(int $profileId, ExploreProfessionalsService $explore): void
    {
        $explore->toggleFavourite(auth()->user(), $profileId);
    }

    public function render(ExploreProfessionalsService $explore)
    {
        $directory = $explore->directory(
            $this->type,
            trim($this->search),
            $this->location,
            $this->projectType,
            $this->priceRange,
            $this->sort,
            auth()->user(),
        );

        return view('livewire.explore-professionals', $directory);
    }

    private function resetPageFiltersKeepType(): void
    {
        $this->search = '';
        $this->location = '';
        $this->projectType = '';
        $this->priceRange = '';
        $this->sort = 'relevant';
    }
}
