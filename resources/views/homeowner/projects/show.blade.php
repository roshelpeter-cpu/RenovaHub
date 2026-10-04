<x-homeowner-layout :title="$project->name">
    <div class="overflow-hidden rounded-3xl border border-[#ece7dc] bg-white shadow-sm">
        @if ($project->coverUrl())
            <img src="{{ $project->coverUrl() }}" alt="{{ $project->name }}" class="h-56 w-full object-cover sm:h-72">
        @endif
        <div class="flex flex-col gap-4 p-5 sm:p-6 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-olive">{{ $project->locationLabel() ?: 'Location not added' }}</p>
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <h1 class="font-serif text-3xl font-medium tracking-[-0.03em] text-forest sm:text-4xl">{{ $project->name }}</h1>
                    <span class="rounded-full bg-[#e7f0e4] px-3 py-1 text-xs font-medium text-forest">{{ $project->statusLabel() }}</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('homeowner.projects.edit', $project) }}" class="rounded-full border border-forest px-4 py-2 text-sm font-medium text-forest transition hover:bg-forest hover:text-ivory">Edit Project</a>
                @include('homeowner.projects.partials.delete-form', ['project' => $project, 'buttonLabel' => 'Delete Project'])
            </div>
        </div>
    </div>

    @include('homeowner.projects.partials.tabs', ['project' => $project])

    <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1.3fr)_minmax(16rem,0.7fr)]">
        <section class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm sm:p-6">
            <h2 class="font-serif text-2xl text-forest">Project details</h2>
            <p class="mt-3 text-sm leading-relaxed text-charcoal">{{ $project->description }}</p>
            @if ($project->requirements)
                <h3 class="mt-5 text-sm font-medium text-forest">Requirements</h3>
                <p class="mt-1 text-sm leading-relaxed text-charcoal">{{ $project->requirements }}</p>
            @endif
            <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                <div><dt class="text-mist">Location</dt><dd class="font-medium">{{ $project->locationLabel() ?: 'Not added' }}</dd></div>
                <div><dt class="text-mist">Renovation</dt><dd class="font-medium">{{ $project->renovationTypeLabel() }} · {{ $project->propertyTypeLabel() }}</dd></div>
                <div><dt class="text-mist">Dates</dt><dd class="font-medium">@if($project->expected_start_date){{ $project->expected_start_date->format('j M Y') }} – {{ $project->actual_completion_date?->format('j M Y') ?? $project->expected_completion_date?->format('j M Y') ?? 'Open' }}@else Not set @endif</dd></div>
                <div><dt class="text-mist">Initial budget</dt><dd class="font-medium">{{ $project->money($project->estimated_budget) }}</dd></div>
                <div><dt class="text-mist">Current budget</dt><dd class="font-medium">{{ $project->money($project->current_budget) }}</dd></div>
                <div><dt class="text-mist">Paid / outstanding</dt><dd class="font-medium">LKR {{ number_format($project->paidAmount(), 2) }} / LKR {{ number_format($project->outstandingAmount(), 2) }}</dd></div>
            </dl>
        </section>

        <div class="space-y-4">
            <section class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm">
                <h2 class="font-serif text-xl text-forest">Progress</h2>
                <p class="mt-2 font-serif text-4xl text-forest">{{ $project->progress ?? 0 }}%</p>
                <ul class="mt-4 space-y-3">
                    @forelse ($project->orderedProgress() as $stage)
                        <li>
                            <div class="flex justify-between text-xs uppercase tracking-[0.12em] text-mist"><span>{{ $stage->label() }}</span><span>{{ $stage->percent }}%</span></div>
                            <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-sand" role="progressbar" aria-valuenow="{{ $stage->percent }}" aria-valuemin="0" aria-valuemax="100"><div class="h-full bg-forest" style="width: {{ $stage->percent }}%"></div></div>
                        </li>
                    @empty
                        <li class="text-sm text-mist">Stage progress has not been recorded yet.</li>
                    @endforelse
                </ul>
            </section>

            <section class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm">
                <h2 class="font-serif text-xl text-forest">Project team</h2>
                @foreach (['designer' => 'Designer', 'contractor' => 'Contractor'] as $role => $roleLabel)
                    @php $member = $project->{$role}; $invite = $project->latestInvitation($role); @endphp
                    <div class="mt-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-mist">{{ $roleLabel }}</p>
                        @if ($member)
                            <p class="font-medium text-charcoal">{{ $member->professionalProfile?->displayName() ?? $member->name }}</p>
                            <p class="text-sm text-mist">{{ $invite?->status ? ucfirst($invite->status) : 'Selected' }}</p>
                            <a href="{{ route('homeowner.professionals.show', ['professional' => $member, 'project' => $project->id]) }}" class="text-sm text-forest hover:underline">View Profile</a>
                        @else
                            <p class="text-sm text-charcoal">Not selected</p>
                        @endif
                    </div>
                @endforeach
                <a href="{{ route('homeowner.projects.team', $project) }}" class="mt-4 inline-flex text-sm font-medium text-forest hover:underline">Change team</a>
            </section>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <section class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="font-serif text-xl text-forest">Upcoming milestones</h2>
            @php $openMilestones = $project->milestones->whereNull('completed_at'); @endphp
            @forelse ($openMilestones as $milestone)
                <p class="mt-3 text-sm text-charcoal">{{ $milestone->title }} <span class="text-mist">· {{ $milestone->due_on?->format('j M Y') }}</span></p>
            @empty
                <p class="mt-3 text-sm text-mist">No open milestones.</p>
            @endforelse
        </section>
        <section class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="font-serif text-xl text-forest">Recent activity</h2>
            @forelse ($project->activity->sortByDesc('created_at')->take(6) as $entry)
                <p class="mt-3 text-sm text-charcoal">{{ $entry->description }} <span class="text-mist">· {{ $entry->created_at->format('j M') }}</span></p>
            @empty
                <p class="mt-3 text-sm text-mist">No activity yet.</p>
            @endforelse
        </section>
    </div>
</x-homeowner-layout>
