@php
    $wide = $wide ?? false;
    $designer = $project->designer;
    $contractor = $project->contractor;
    $dateLabel = $project->status === \App\Models\Project::STATUS_COMPLETED
        ? ($project->actual_completion_date?->format('M Y') ?? $project->expected_completion_date?->format('M Y'))
        : $project->expected_completion_date?->format('M Y');
    $dateCaption = $project->status === \App\Models\Project::STATUS_COMPLETED ? 'Completed On' : 'Expected Completion';
    $completed = $project->status === \App\Models\Project::STATUS_COMPLETED;
@endphp

{{-- Completed projects stay as portrait cards. In-progress projects use a wider split so the catalogue matches the reference layout. --}}
<article class="group overflow-hidden rounded-[1.7rem] border border-[#ece7dc] bg-white shadow-[0_12px_32px_-24px_rgba(18,61,43,0.55)] transition duration-500 hover:-translate-y-0.5 hover:shadow-[0_22px_44px_-26px_rgba(18,61,43,0.45)] {{ $wide ? 'grid lg:grid-cols-[minmax(17rem,0.42fr)_minmax(0,1fr)]' : 'flex h-full flex-col' }}">
    <div class="relative overflow-hidden {{ $wide ? 'h-52 lg:h-full lg:min-h-[17rem]' : 'h-48 sm:h-[13.5rem]' }}">
        @if ($project->coverUrl())
            <img src="{{ $project->coverUrl() }}" alt="" class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
        @else
            <div class="h-full w-full bg-[#EFE8DA]"></div>
        @endif
        <span class="absolute right-4 top-4 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium shadow-sm {{ $completed ? 'bg-[#e7f0e4] text-[#123D2B]' : ($project->status === 'in_progress' ? 'bg-[#e4eef6] text-[#215a78]' : 'bg-white text-[#123D2B]') }}">
            @if ($completed)
                <svg viewBox="0 0 16 16" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="8" cy="8" r="6"/><path d="m5.2 8.1 1.8 1.8 3.8-3.8"/></svg>
            @elseif ($project->status === 'in_progress')
                <svg viewBox="0 0 16 16" class="h-3.5 w-3.5" fill="currentColor"><circle cx="8" cy="8" r="6" fill="none" stroke="currentColor" stroke-width="1.4"/><path d="M6.4 5.2 11 8 6.4 10.8Z"/></svg>
            @endif
            {{ $project->statusLabel() }}
        </span>
    </div>

    <div class="flex flex-1 flex-col px-5 pb-5 pt-4 sm:px-6">
        <h2 class="font-serif text-[1.45rem] leading-tight text-[#123D2B]">{{ $project->name }}</h2>
        <p class="mt-1.5 flex items-center gap-1.5 text-sm text-[#66756C]">
            <svg viewBox="0 0 24 24" class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 21s7-5.4 7-11a7 7 0 1 0-14 0c0 5.6 7 11 7 11Z"/><circle cx="12" cy="10" r="2.2"/></svg>
            {{ $project->locationLabel() ?: 'Location not added yet' }}
        </p>
        <p class="mt-3 line-clamp-2 text-sm leading-relaxed text-[#66756C]">{{ $project->description }}</p>

        <div class="mt-4 flex flex-wrap items-end gap-x-6 gap-y-3 text-sm">
            <div class="flex items-start gap-2">
                <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 text-[#66756C]" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3.5" y="6.5" width="17" height="12" rx="2"/><path d="M8 10h.01M3.5 10h17"/></svg>
                <div>
                    <p class="text-xs text-[#66756C]">Budget</p>
                    <p class="font-medium text-[#18352A]">{{ $project->estimated_budget !== null ? 'LKR '.number_format((float) $project->estimated_budget, 0) : 'Not set' }}</p>
                </div>
            </div>
            <div class="flex items-start gap-2">
                <svg viewBox="0 0 24 24" class="mt-0.5 h-4 w-4 text-[#66756C]" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/></svg>
                <div>
                    <p class="text-xs text-[#66756C]">{{ $dateCaption }}</p>
                    <p class="font-medium text-[#18352A]">{{ $dateLabel ?: 'Not set' }}</p>
                </div>
            </div>
            @if ($project->progress !== null)
                <div class="min-w-[9rem] flex-1">
                    <div class="mb-1 flex items-center justify-between text-xs text-[#66756C]">
                        <span class="sr-only">Progress</span>
                        <span class="ml-auto font-medium text-[#123D2B]">{{ $project->progress }}%</span>
                    </div>
                    <div class="h-1.5 overflow-hidden rounded-full bg-[#EFE8DA]" role="progressbar" aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="h-full rounded-full bg-[#123D2B]" style="width: {{ $project->progress }}%"></div>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-auto pt-5">
            <div class="flex flex-wrap gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <img src="{{ $designer?->professionalProfile?->avatarUrl() ?: $designer?->profile_photo_url }}" alt="" class="h-8 w-8 rounded-full object-cover">
                    <div>
                        <p class="text-[11px] text-[#66756C]">Designer</p>
                        <p class="font-medium text-[#18352A]">{{ $designer?->professionalProfile?->displayName() ?? $designer?->name ?? 'Not selected' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <img src="{{ $contractor?->professionalProfile?->avatarUrl() ?: $contractor?->profile_photo_url }}" alt="" class="h-8 w-8 rounded-full object-cover">
                    <div>
                        <p class="text-[11px] text-[#66756C]">Contractor</p>
                        <p class="font-medium text-[#18352A]">{{ $contractor?->professionalProfile?->displayName() ?? $contractor?->name ?? 'Not selected' }}</p>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between gap-2">
                <a href="{{ route('homeowner.projects.show', $project) }}" class="inline-flex items-center gap-1 rounded-full border border-[#123D2B] px-4 py-2 text-sm font-medium text-[#123D2B] transition hover:bg-[#123D2B] hover:text-[#F6F1E7]">View Details <span aria-hidden="true">→</span></a>
                <details class="relative">
                    <summary class="flex h-9 w-9 list-none cursor-pointer items-center justify-center rounded-full text-[#66756C] marker:content-none hover:bg-[#F6F1E7] [&::-webkit-details-marker]:hidden" aria-label="Project actions">
                        <span class="text-lg leading-none">⋯</span>
                    </summary>
                    <div class="absolute right-0 z-20 mt-1 w-44 overflow-hidden rounded-2xl border border-[#ece7dc] bg-white py-1 text-sm shadow-lg">
                        <a href="{{ route('homeowner.projects.show', $project) }}" class="block px-4 py-2 hover:bg-[#F6F1E7]">View</a>
                        <a href="{{ route('homeowner.projects.edit', $project) }}" class="block px-4 py-2 hover:bg-[#F6F1E7]">Edit</a>
                        @include('homeowner.projects.partials.delete-form', ['project' => $project, 'buttonLabel' => 'Delete Project', 'class' => 'block w-full px-4 py-2 text-left text-red-700 hover:bg-red-50'])
                    </div>
                </details>
            </div>
        </div>
    </div>
</article>
