@php
    $meta = $project->workspace_meta ?? [];
    $progress = (int) ($meta['design_progress'] ?? $project->progress);
@endphp
<section class="overflow-hidden rounded-[1.6rem] border border-[#ece7dc] bg-white shadow-sm">
    <div class="grid gap-0 lg:grid-cols-[18rem_1fr]">
        <img src="{{ $project->coverUrl() ?: asset('images/renova/about-interior.jpg') }}" alt="" class="h-52 w-full object-cover lg:h-full">
        <div class="p-5 sm:p-6">
            <p class="text-sm text-[#66756C]"><a href="{{ route('designer.projects.index') }}" class="hover:text-[#123D2B]">My Projects</a> <span class="mx-1">›</span> {{ $project->name }}</p>
            <div class="mt-2 flex flex-wrap items-center gap-2">
                <h1 class="font-serif text-3xl text-[#123D2B] sm:text-4xl">{{ $project->name }}</h1>
                <span class="rounded-full bg-[#F3EFE4] px-2.5 py-1 text-xs text-[#123D2B]">{{ $project->status === 'completed' ? 'Completed' : 'In Progress' }}</span>
            </div>
            <p class="mt-2 text-sm text-[#66756C]">{{ $project->cataloguePlaceLabel() }} · {{ $project->displayTypeLabel() }}</p>
            <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-3">
                <div><dt class="text-xs text-[#66756C]">Homeowner</dt><dd class="text-[#123D2B]">{{ $project->homeowner?->name }}</dd></div>
                <div><dt class="text-xs text-[#66756C]">Contractor</dt><dd class="text-[#123D2B]">{{ $project->contractor?->professionalProfile?->displayName() ?? $project->contractor?->name ?? 'Not assigned' }}</dd></div>
                <div><dt class="text-xs text-[#66756C]">Design progress</dt><dd class="text-[#123D2B]">{{ $progress }}% · {{ $meta['design_stage'] ?? 'Design' }}</dd></div>
            </dl>
            <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-[#F3EFE4]"><div class="h-full bg-[#123D2B]" style="width: {{ $progress }}%"></div></div>
        </div>
    </div>
    @include('designer.partials.project-nav')
</section>
