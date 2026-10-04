@php
    $completed = $project->isCompleted();
    $meta = $project->workspace_meta ?? [];
@endphp

<div>
    <h1 class="font-serif text-4xl leading-tight text-[#123D2B] sm:text-[2.6rem]">{{ $project->name }}</h1>
    <div class="mt-4 space-y-2 text-sm text-[#66756C]">
        <p class="flex items-center gap-2">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 21s7-6.2 7-11a7 7 0 1 0-14 0c0 4.8 7 11 7 11Z"/><circle cx="12" cy="10" r="2.2"/></svg>
            {{ $project->cataloguePlaceLabel() }}
        </p>
        <p class="flex items-center gap-2">
            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z"/></svg>
            {{ $project->displayTypeLabel() }}
        </p>
        @if ($completed)
            <p class="flex items-center gap-2">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg>
                Completed · {{ ($project->actual_completion_date ?? $project->expected_completion_date)?->format('j M Y') }}
            </p>
        @else
            <p class="flex items-center gap-2">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg>
                Started – {{ $project->expected_start_date?->format('M Y') ?? 'Not set' }}
            </p>
            <p class="flex items-center gap-2">
                <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg>
                Expected – {{ $project->expected_completion_date?->format('M Y') ?? 'Not set' }}
            </p>
        @endif
    </div>
    <p class="mt-4 text-sm leading-relaxed text-[#66756C]">{{ $project->description }}</p>
</div>

<div class="mt-5 grid grid-cols-2 gap-3">
    @include('homeowner.projects.partials.summary-cards', ['project' => $project, 'completed' => $completed])
</div>
