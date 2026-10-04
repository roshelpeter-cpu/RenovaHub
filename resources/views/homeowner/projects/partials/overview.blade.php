@php
    $meta = $project->workspace_meta ?? [];
    $completed = $project->isCompleted();
    $designer = $project->designer;
    $contractor = $project->contractor;
    $details = [
        ['Property Type', $project->metaString('property_label', $project->propertyTypeLabel())],
        ['Renovation Type', $project->metaString('renovation_label', $project->displayTypeLabel())],
        ['Location', $project->cataloguePlaceLabel()],
        ['Bedrooms', isset($meta['bedrooms']) ? (string) $meta['bedrooms'] : '—'],
        ['Bathrooms', isset($meta['bathrooms']) ? (string) $meta['bathrooms'] : '—'],
    ];
    if ($completed) {
        $details[] = ['Project Duration', $project->durationLabel() ?? '—'];
        $details[] = ['Completion Date', ($project->actual_completion_date ?? $project->expected_completion_date)?->format('j M Y') ?? '—'];
        $details[] = ['Designer', $designer?->professionalProfile?->displayName() ?? $designer?->name ?? '—'];
        $details[] = ['Contractor', $contractor?->professionalProfile?->displayName() ?? $contractor?->name ?? '—'];
        $details[] = ['Final Cost', 'LKR '.number_format($project->finalCostAmount(), 0)];
    } else {
        $details[] = ['Start Date', $project->expected_start_date?->format('j M Y') ?? '—'];
        $details[] = ['Expected Date', $project->expected_completion_date?->format('j M Y') ?? '—'];
        $details[] = ['Current Stage', $project->currentStageLabel()];
        $details[] = ['Designer', $designer?->professionalProfile?->displayName() ?? $designer?->name ?? '—'];
        $details[] = ['Contractor', $contractor?->professionalProfile?->displayName() ?? $contractor?->name ?? '—'];
        $details[] = ['Budget', 'LKR '.number_format($project->budgetTotalAmount(), 0)];
    }
@endphp

<div class="grid gap-6 lg:grid-cols-3">
    <section class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
        <h2 class="font-serif text-2xl text-[#123D2B]">Project Overview</h2>
        <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-[#66756C]">{{ $meta['overview'] ?? $project->requirements ?? $project->description }}</p>
    </section>

    <section class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
        <h2 class="font-serif text-2xl text-[#123D2B]">Key Details</h2>
        <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 text-sm sm:grid-cols-2">
            @foreach ($details as [$label, $value])
                <div>
                    <dt class="text-xs text-[#66756C]">{{ $label }}</dt>
                    <dd class="mt-0.5 font-medium text-[#123D2B]">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    <section class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
        <livewire:project-gallery :project-id="$project->id" :show-overview-grid="true" :key="'grid-'.$project->id" />
    </section>
</div>
