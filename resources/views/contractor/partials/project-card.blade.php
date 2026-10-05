@php
    $homeowner = $project->homeowner?->name ?? 'Homeowner';
    $designer = $project->designer?->professionalProfile?->displayName() ?? $project->designer?->name;
    $milestone = $project->metaString('next_milestone') ?? $project->milestones->firstWhere('completed_at', null)?->title ?? 'Next milestone';
    $due = $project->milestones->firstWhere('completed_at', null)?->due_on ?? $project->expected_completion_date;
@endphp
<article class="overflow-hidden rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
    <img src="{{ $project->coverUrl() ?: asset('images/renova/about-exterior.jpg') }}" alt="" class="h-44 w-full object-cover">
    <div class="p-5">
        <div class="flex items-start justify-between gap-3">
            <h3 class="rh-serif text-xl text-[#123D2B]">{{ $project->name }}</h3>
            <span class="rounded-full bg-[#F6F1E7] px-2.5 py-1 text-xs text-[#123D2B]">{{ $project->currentStageLabel() }}</span>
        </div>
        <p class="mt-1 text-sm text-[#66756C]">{{ $project->cataloguePlaceLabel() }}</p>
        <dl class="mt-4 space-y-1.5 text-sm text-[#66756C]">
            <div class="flex justify-between gap-3"><dt>Budget</dt><dd class="text-[#123D2B]">{{ $project->money($project->estimated_budget) }}</dd></div>
            <div class="flex justify-between gap-3"><dt>Progress</dt><dd class="text-[#123D2B]">{{ (int) $project->progress }}%</dd></div>
            <div class="flex justify-between gap-3"><dt>Materials</dt><dd class="text-[#123D2B]">{{ (int) ($project->material_requirements_count ?? $project->materialRequirements()->count()) }}</dd></div>
            <div class="flex justify-between gap-3"><dt>Homeowner</dt><dd class="text-[#123D2B]">{{ $homeowner }}</dd></div>
            <div class="flex justify-between gap-3"><dt>Designer</dt><dd class="text-[#123D2B]">{{ $designer ?: 'Not assigned' }}</dd></div>
            <div class="flex justify-between gap-3"><dt>Next milestone</dt><dd class="text-right text-[#123D2B]">{{ $milestone }}</dd></div>
            <div class="flex justify-between gap-3"><dt>Due</dt><dd class="text-[#123D2B]">{{ $due?->format('j M Y') ?? '—' }}</dd></div>
        </dl>
        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-[#F6F1E7]">
            <div class="h-full rounded-full bg-[#1F7A4D]" style="width: {{ min(100, (int) $project->progress) }}%"></div>
        </div>
        <a href="{{ route('contractor.projects.show', $project) }}" class="mt-4 inline-flex rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">View Project</a>
    </div>
</article>
