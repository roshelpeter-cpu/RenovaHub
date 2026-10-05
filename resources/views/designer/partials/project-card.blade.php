@php
    $meta = $project->workspace_meta ?? [];
    $progress = (int) ($meta['design_progress'] ?? $project->progress);
    $homeowner = $project->homeowner?->name;
    $contractor = $project->contractor?->professionalProfile?->displayName() ?? $project->contractor?->name;
@endphp
<article class="overflow-hidden rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
    <div class="relative">
        <img src="{{ $project->coverUrl() ?: asset('images/renova/about-interior.jpg') }}" alt="" class="h-44 w-full object-cover">
        <span class="absolute left-3 top-3 rounded-full bg-white/95 px-2.5 py-1 text-xs font-medium text-[#123D2B]">{{ $project->status === 'completed' ? 'Completed' : 'In Progress' }}</span>
    </div>
    <div class="p-4">
        <h3 class="font-serif text-xl text-[#123D2B]">{{ $project->name }}</h3>
        <p class="mt-1 text-xs text-[#66756C]">{{ $project->cataloguePlaceLabel() }}</p>
        <p class="mt-1 text-xs text-[#66756C]">{{ $project->displayTypeLabel() }}</p>
        <dl class="mt-3 space-y-1 text-sm text-[#66756C]">
            <div class="flex justify-between gap-3"><dt>Homeowner</dt><dd class="text-[#123D2B]">{{ $homeowner }}</dd></div>
            <div class="flex justify-between gap-3"><dt>Contractor</dt><dd class="text-[#123D2B]">{{ $contractor ?: 'Not assigned' }}</dd></div>
        </dl>
        <p class="mt-3 text-xs text-[#66756C]">Design progress {{ $progress }}%</p>
        <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-[#F3EFE4]"><div class="h-full rounded-full bg-[#123D2B]" style="width: {{ $progress }}%"></div></div>
        <p class="mt-3 text-xs text-[#66756C]">Next: {{ $meta['next_milestone'] ?? 'Continue design' }}</p>
        <p class="text-xs text-[#66756C]">Due {{ isset($meta['design_due']) ? \Illuminate\Support\Carbon::parse($meta['design_due'])->format('j M Y') : ($project->expected_completion_date?->format('j M Y') ?? '—') }}</p>
        <a href="{{ route('designer.projects.show', $project) }}" class="mt-3 inline-flex text-sm font-medium text-[#123D2B] hover:underline">View Project</a>
    </div>
</article>
