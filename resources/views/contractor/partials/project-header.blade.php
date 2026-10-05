<section class="overflow-hidden rounded-[1.6rem] border border-[#ece7dc] bg-white shadow-sm">
    <div class="grid gap-0 lg:grid-cols-[1.3fr_0.9fr]">
        <img src="{{ $project->coverUrl() ?: asset('images/renova/about-exterior.jpg') }}" alt="" class="h-64 w-full object-cover lg:h-full">
        <div class="p-6">
            <p class="text-xs uppercase tracking-[0.14em] text-[#66756C]">{{ $project->propertyTypeLabel() }} · {{ $project->displayTypeLabel() }}</p>
            <h1 class="rh-serif mt-2 text-4xl text-[#123D2B]">{{ $project->name }}</h1>
            <p class="mt-2 text-sm text-[#66756C]">{{ $project->cataloguePlaceLabel() }}</p>
            <dl class="mt-5 grid gap-3 text-sm sm:grid-cols-2">
                <div><dt class="text-xs text-[#66756C]">Homeowner</dt><dd class="text-[#123D2B]">{{ $project->homeowner?->name ?? '—' }}</dd></div>
                <div><dt class="text-xs text-[#66756C]">Designer</dt><dd class="text-[#123D2B]">{{ $project->designer?->professionalProfile?->displayName() ?? $project->designer?->name ?? 'Not assigned' }}</dd></div>
                <div><dt class="text-xs text-[#66756C]">Budget</dt><dd class="text-[#123D2B]">{{ $project->money($project->estimated_budget) }}</dd></div>
                <div><dt class="text-xs text-[#66756C]">Progress</dt><dd class="text-[#123D2B]">{{ (int) $project->progress }}% · {{ $project->currentStageLabel() }}</dd></div>
                <div><dt class="text-xs text-[#66756C]">Expected completion</dt><dd class="text-[#123D2B]">{{ $project->expected_completion_date?->format('j M Y') ?? '—' }}</dd></div>
                <div><dt class="text-xs text-[#66756C]">Next milestone</dt><dd class="text-[#123D2B]">{{ $project->metaString('next_milestone', '—') }}</dd></div>
            </dl>
        </div>
    </div>
    @if ($project->gallery()->count() > 1)
        <div class="grid grid-cols-5 gap-2 p-4">
            @foreach ($project->gallery()->take(5) as $image)
                <img src="{{ asset($image->path) }}" alt="" class="h-20 w-full rounded-xl object-cover">
            @endforeach
        </div>
    @endif
</section>
@include('contractor.partials.project-nav', ['project' => $project, 'section' => $section ?? 'overview'])
