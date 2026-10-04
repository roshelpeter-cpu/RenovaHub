<section class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
    @if ($project->isCompleted())
        <h2 class="font-serif text-xl text-[#123D2B]">Project Timeline</h2>
        <ol class="mt-5 space-y-0">
            @forelse ($project->milestones as $milestone)
                <li class="relative pb-5 pl-8 last:pb-0">
                    @if (! $loop->last)
                        <span class="absolute bottom-0 left-[9px] top-5 w-px bg-[#D5E4D6]"></span>
                    @endif
                    <span class="absolute left-0 top-0 flex h-5 w-5 items-center justify-center rounded-full {{ $milestone->completed_at ? 'bg-[#2F6B49] text-white' : 'border border-[#c9c2b4] bg-white text-[#66756C]' }}">
                        @if ($milestone->completed_at)
                            <svg viewBox="0 0 16 16" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2"><path d="m3.5 8.2 3 3 6-6.4"/></svg>
                        @endif
                    </span>
                    <p class="text-sm font-medium text-[#123D2B]">{{ $milestone->title }}</p>
                    <p class="text-xs text-[#66756C]">{{ $milestone->completed_at?->format('j M Y') ?? $milestone->due_on?->format('j M Y') ?? 'Date not set' }}</p>
                    @if ($milestone->description)
                        <p class="mt-0.5 text-xs leading-relaxed text-[#66756C]">{{ $milestone->description }}</p>
                    @endif
                </li>
            @empty
                <li class="text-sm text-[#66756C]">Timeline milestones have not been recorded yet.</li>
            @endforelse
        </ol>
    @else
        <h2 class="font-serif text-xl text-[#123D2B]">Project Stage Timeline</h2>
        <ol class="mt-5 space-y-0">
            @foreach ($project->workspaceStages() as $stage)
                <li class="relative pb-5 pl-8 last:pb-0">
                    @if (! $loop->last)
                        <span class="absolute bottom-0 left-[9px] top-5 w-px {{ $stage->state === 'completed' ? 'bg-[#D5E4D6]' : 'bg-[#ece7dc]' }}"></span>
                    @endif
                    <span class="absolute left-0 top-0 flex h-5 w-5 items-center justify-center rounded-full {{ $stage->state === 'completed' ? 'bg-[#2F6B49] text-white' : ($stage->state === 'current' ? 'bg-[#2F6B49] text-white' : 'border border-[#c9c2b4] bg-white') }}">
                        @if ($stage->state === 'completed')
                            <svg viewBox="0 0 16 16" class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2"><path d="m3.5 8.2 3 3 6-6.4"/></svg>
                        @elseif ($stage->state === 'current')
                            <span class="h-2 w-2 rounded-full bg-white"></span>
                        @endif
                    </span>
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-sm font-medium text-[#123D2B]">{{ $stage->label }}</p>
                        @if ($stage->state === 'current')
                            <span class="rounded-full bg-[#E7F0E4] px-2 py-0.5 text-[11px] font-medium text-[#123D2B]">In Progress</span>
                        @elseif ($stage->state === 'upcoming')
                            <span class="text-[11px] text-[#66756C]">Not Started</span>
                        @else
                            <span class="text-[11px] text-[#2F6B49]">Completed</span>
                        @endif
                    </div>
                    @if ($stage->dates)
                        <p class="text-xs text-[#66756C]">{{ $stage->dates }}</p>
                    @endif
                    @if ($stage->notes)
                        <p class="mt-0.5 text-xs leading-relaxed text-[#66756C]">{{ $stage->notes }}</p>
                    @endif
                </li>
            @endforeach
        </ol>
    @endif
</section>
