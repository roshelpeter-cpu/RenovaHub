<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\ProjectReferenceImage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * Gallery state stays in Livewire so prev/next/thumbnail clicks do not
 * reload the project workspace. Images are always loaded from this project's
 * reference_images rows — never from another project's gallery.
 */
class ProjectGallery extends Component
{
    public int $projectId;

    #[Url(as: 'photo')]
    public int $index = 0;

    public bool $showOverviewGrid = false;

    public function mount(int $projectId): void
    {
        $this->projectId = $projectId;
        Gate::authorize('view', $this->project());
        $this->clampIndex();
    }

    public function next(): void
    {
        $count = $this->images()->count();
        if ($count === 0) {
            return;
        }

        $this->index = ($this->index + 1) % $count;
        $this->dispatch('project-gallery-index', index: $this->index);
    }

    public function previous(): void
    {
        $count = $this->images()->count();
        if ($count === 0) {
            return;
        }

        $this->index = ($this->index - 1 + $count) % $count;
        $this->dispatch('project-gallery-index', index: $this->index);
    }

    public function select(int $index): void
    {
        $this->index = $index;
        $this->clampIndex();
        $this->dispatch('project-gallery-index', index: $this->index);
    }

    #[On('project-gallery-index')]
    public function syncIndex(int $index): void
    {
        $this->index = $index;
        $this->clampIndex();
    }

    public function render()
    {
        $project = $this->project();
        Gate::authorize('view', $project);

        $images = $this->images();
        $this->clampIndex();

        return view('livewire.project-gallery', [
            'project' => $project,
            'images' => $images,
            'current' => $images->get($this->index),
            'count' => $images->count(),
        ]);
    }

    private function project(): Project
    {
        return Project::query()->findOrFail($this->projectId);
    }

    /**
     * @return Collection<int, ProjectReferenceImage>
     */
    private function images(): Collection
    {
        return ProjectReferenceImage::query()
            ->where('project_id', $this->projectId)
            ->orderBy('id')
            ->get();
    }

    private function clampIndex(): void
    {
        $count = $this->images()->count();
        if ($count === 0) {
            $this->index = 0;

            return;
        }

        if ($this->index < 0 || $this->index >= $count) {
            $this->index = 0;
        }
    }
}
