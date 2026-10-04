@php
    $wide = $wide ?? false;
    $designer = $project->designer;
    $contractor = $project->contractor;
    $completed = $project->status === \App\Models\Project::STATUS_COMPLETED;
    $dateLabel = $completed
        ? ($project->actual_completion_date?->format('M Y') ?? $project->expected_completion_date?->format('M Y'))
        : $project->expected_completion_date?->format('M Y');
@endphp

<article class="overflow-hidden rounded-[1.5rem] border border-[#ece7dc] bg-white shadow-sm {{ $wide ? '' : 'flex h-full flex-col' }}">
    <div class="relative {{ $wide ? 'h-52' : 'h-44' }}">
        @if ($project->coverUrl())
            <img src="{{ $project->coverUrl() }}" alt="" class="h-full w-full object-cover">
        @else
            <div class="h-full w-full bg-[#EFE8DA]"></div>
        @endif
        <span class="absolute left-4 top-4 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium {{ $completed ? 'bg-[#E7F0E4] text-[#123D2B]' : 'bg-[#E7F0E4] text-[#123D2B]' }}">
            {{ $completed ? 'Completed' : 'In Progress' }}
        </span>
    </div>

    <div class="flex flex-1 flex-col p-5">
        <h2 class="font-serif text-[1.45rem] leading-tight text-[#123D2B]">{{ $project->name }}</h2>
        <p class="mt-1.5 text-sm text-[#66756C]">{{ $project->cataloguePlaceLabel() }}</p>
        @if (! $completed)
            <p class="text-xs text-[#66756C]">{{ $project->propertyTypeLabel() }} · {{ $project->displayTypeLabel() }}</p>
        @endif
        <p class="mt-3 line-clamp-2 text-sm leading-relaxed text-[#66756C]">{{ $project->description }}</p>

        @if (! $completed)
            <div class="mt-4">
                <div class="mb-1 flex justify-end text-sm font-medium text-[#123D2B]">{{ $project->progress }}%</div>
                <div class="h-1.5 overflow-hidden rounded-full bg-[#EFE8DA]" role="progressbar" aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="h-full rounded-full bg-[#123D2B]" style="width: {{ $project->progress }}%"></div>
                </div>
                <ul class="mt-3 grid grid-cols-2 gap-2 text-xs sm:grid-cols-4">
                    @foreach ($project->workspaceStages() as $stage)
                        <li class="{{ $stage->percent >= 100 ? 'text-[#2F6B49]' : ($stage->percent > 0 ? 'text-[#123D2B]' : 'text-[#66756C]') }}">
                            {{ $stage->percent >= 100 ? '✓' : '' }} {{ $stage->label }}
                            <span class="block text-[10px] text-[#66756C]">{{ $stage->status }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-4 flex flex-wrap gap-x-6 gap-y-2 text-sm">
            <div>
                <p class="text-xs text-[#66756C]">Budget</p>
                <p class="font-medium text-[#18352A]">{{ $project->estimated_budget !== null ? 'LKR '.number_format((float) $project->estimated_budget, 0) : 'Not set' }}</p>
            </div>
            <div>
                <p class="text-xs text-[#66756C]">{{ $completed ? 'Completed' : 'Expected Completion' }}</p>
                <p class="font-medium text-[#18352A]">{{ $dateLabel ?: 'Not set' }}</p>
            </div>
            @if ($completed)
                <div>
                    <p class="text-xs text-[#66756C]">{{ $project->propertyTypeLabel() }}</p>
                    <p class="font-medium text-[#18352A]">{{ $project->displayTypeLabel() }}</p>
                </div>
            @endif
        </div>

        @if (! $completed)
            <div class="mt-4 flex flex-wrap gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <img src="{{ $designer?->professionalProfile?->avatarUrl() ?: $designer?->profile_photo_url }}" alt="" class="h-8 w-8 rounded-full object-cover">
                    <div>
                        <p class="text-[11px] text-[#66756C]">Designer</p>
                        <p class="font-medium text-[#18352A]">{{ $designer?->professionalProfile?->displayName() ?? $designer?->name ?? 'Not selected' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <img src="{{ $contractor?->professionalProfile?->listingImageUrl() ?: $contractor?->profile_photo_url }}" alt="" class="h-8 w-8 rounded-full object-cover">
                    <div>
                        <p class="text-[11px] text-[#66756C]">Contractor</p>
                        <p class="font-medium text-[#18352A]">{{ $contractor?->professionalProfile?->displayName() ?? $contractor?->name ?? 'Not selected' }}</p>
                    </div>
                </div>
            </div>
        @endif

        <a href="{{ route('homeowner.projects.show', $project) }}" class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-[#123D2B] px-4 py-2.5 text-sm font-medium text-white">View Project</a>
    </div>
</article>
