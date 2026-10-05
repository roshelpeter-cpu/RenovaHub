@php
    $wide = $wide ?? false;
    $completed = $project->status === \App\Models\Project::STATUS_COMPLETED;
    $upcoming = $project->isUpcoming();
    $declinedRole = $project->declinedRoleLabel();
    $designerSlot = $project->teamPresentation('designer');
    $contractorSlot = $project->teamPresentation('contractor');
    $dateLabel = $completed
        ? ($project->actual_completion_date?->format('M Y') ?? $project->expected_completion_date?->format('M Y'))
        : ($upcoming ? $project->created_at?->format('j M Y') : $project->expected_completion_date?->format('M Y'));
    $badge = $completed ? 'Completed' : ($upcoming ? 'Upcoming' : 'In Progress');
@endphp

<article class="overflow-hidden rounded-[1.5rem] border bg-white shadow-sm {{ $declinedRole ? 'border-[#C4503A]' : 'border-[#ece7dc]' }} {{ $wide ? '' : 'flex h-full flex-col' }}">
    @if ($declinedRole)
        <div class="flex items-start gap-3 bg-[#F8E8E4] px-5 py-3 text-sm text-[#8A3B2A]">
            <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#C4503A] text-sm font-semibold text-white" aria-hidden="true">!</span>
            <span>
                <span class="block font-medium">Action Required</span>
                <span class="block">{{ $declinedRole }} declined this project invitation. Select another {{ strtolower($declinedRole) }}.</span>
            </span>
        </div>
    @endif
    <div class="relative {{ $wide ? 'h-52' : 'h-44' }}">
        @if ($project->coverUrl())
            <img src="{{ $project->coverUrl() }}" alt="" class="h-full w-full object-cover">
        @else
            <div class="h-full w-full bg-[#EFE8DA]"></div>
        @endif
        <span class="absolute left-4 top-4 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium {{ $declinedRole ? 'bg-[#F8E8E4] text-[#8A3B2A]' : 'bg-[#E7F0E4] text-[#123D2B]' }}">
            {{ $badge }}
        </span>
    </div>

    <div class="flex flex-1 flex-col p-5">
        <h2 class="font-serif text-[1.45rem] leading-tight text-[#123D2B]">{{ $project->name }}</h2>
        <p class="mt-1.5 text-sm text-[#66756C]">{{ $project->cataloguePlaceLabel() }}</p>
        @if ($upcoming)
            <p class="text-xs text-[#66756C]">Created {{ $project->created_at?->format('j M Y') }} · {{ $project->propertyTypeLabel() }} · {{ $project->displayTypeLabel() }}</p>
        @elseif (! $completed)
            <p class="text-xs text-[#66756C]">{{ $project->propertyTypeLabel() }} · {{ $project->displayTypeLabel() }}</p>
        @endif
        <p class="mt-3 line-clamp-2 text-sm leading-relaxed text-[#66756C]">{{ $project->description }}</p>

        @if (! $completed && ! $upcoming)
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
                <p class="text-xs text-[#66756C]">{{ $completed ? 'Completed' : ($upcoming ? 'Created' : 'Expected Completion') }}</p>
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
                @foreach (['Designer' => $designerSlot, 'Contractor' => $contractorSlot] as $roleLabel => $slot)
                    @php $person = $slot['user']; @endphp
                    <div class="flex items-center gap-2">
                        @if ($person)
                            <img src="{{ $person->professionalProfile?->avatarUrl() ?: $person->profile_photo_url }}" alt="" class="h-8 w-8 rounded-full object-cover">
                        @endif
                        <div>
                            <p class="text-[11px] text-[#66756C]">{{ $roleLabel }}</p>
                            <p class="font-medium text-[#18352A]">{{ $person?->professionalProfile?->displayName() ?? $person?->name ?? 'Not selected' }}</p>
                            <p class="text-[11px] {{ $slot['state'] === 'declined' ? 'font-medium text-[#8A3B2A]' : ($slot['state'] === 'pending' ? 'text-[#8A6A2F]' : 'text-[#2F6B49]') }}">{{ $slot['label'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <a href="{{ route('homeowner.projects.show', $project) }}" class="mt-5 inline-flex w-full items-center justify-center rounded-full bg-[#123D2B] px-4 py-2.5 text-sm font-medium text-white">View Project</a>
    </div>
</article>
