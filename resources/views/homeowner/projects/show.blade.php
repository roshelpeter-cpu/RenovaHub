<x-homeowner-layout :title="$project->name">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-olive">Project</p>
            <div class="mt-2 flex flex-wrap items-center gap-3">
                <h1 class="font-serif text-3xl font-medium tracking-[-0.03em] text-forest sm:text-4xl">{{ $project->name }}</h1>
                <span class="rounded-full bg-[#e7f0e4] px-3 py-1 text-xs font-medium text-forest">{{ $project->statusLabel() }}</span>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('homeowner.projects.edit', $project) }}" class="rounded-full border border-forest px-4 py-2 text-sm font-medium text-forest transition hover:bg-forest hover:text-ivory">Edit Project</a>
            @include('homeowner.projects.partials.delete-form', ['project' => $project, 'buttonLabel' => 'Delete Project', 'class' => 'rounded-full border border-red-200 px-4 py-2 text-sm font-medium text-red-700 transition hover:bg-red-50'])
        </div>
    </div>

    <nav class="mt-6 flex gap-2 overflow-x-auto pb-1" aria-label="Project sections">
        @foreach ([
            'overview' => 'Overview',
            'tasks' => 'Tasks',
            'documents' => 'Documents',
            'mood-board' => 'Mood Board',
            'quotations' => 'Quotations',
            'change-requests' => 'Change Requests',
            'payments' => 'Payments',
        ] as $key => $label)
            <a href="{{ route('homeowner.projects.show', ['project' => $project, 'tab' => $key]) }}" class="shrink-0 rounded-full px-4 py-2 text-sm transition {{ $tab === $key ? 'bg-forest text-ivory' : 'bg-white text-charcoal hover:text-forest' }}" @if($tab === $key) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
    </nav>

    @if ($tab !== 'overview')
        <div class="mt-6 rounded-3xl border border-dashed border-olive/40 bg-white px-6 py-10">
            <h2 class="font-serif text-2xl text-forest">{{ ucfirst(str_replace('-', ' ', $tab)) }}</h2>
            <p class="mt-2 max-w-xl text-sm text-mist">This section is not available yet. It will be added in a later stage, so there is nothing to show here.</p>
        </div>
    @else
        <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(16rem,0.8fr)]">
            <section class="overflow-hidden rounded-3xl border border-[#ece7dc] bg-white shadow-sm">
                @if ($project->cover_image)
                    <img src="{{ asset($project->cover_image) }}" alt="{{ $project->name }}" class="h-56 w-full object-cover sm:h-72">
                @endif
                <div class="p-5 sm:p-6">
                    <h2 class="font-serif text-2xl text-forest">Project details</h2>
                    <p class="mt-3 text-sm leading-relaxed text-charcoal">{{ $project->description }}</p>
                    <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                        <div>
                            <dt class="text-mist">Location</dt>
                            <dd class="font-medium">{{ $project->locationLabel() ?: 'Not added' }}</dd>
                        </div>
                        <div>
                            <dt class="text-mist">Renovation</dt>
                            <dd class="font-medium">{{ $project->renovationTypeLabel() }} · {{ $project->propertyTypeLabel() }}</dd>
                        </div>
                        <div>
                            <dt class="text-mist">Dates</dt>
                            <dd class="font-medium">
                                @if ($project->expected_start_date && $project->expected_completion_date)
                                    {{ $project->expected_start_date->format('j M Y') }} – {{ $project->expected_completion_date->format('j M Y') }}
                                @else
                                    Not set
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-mist">Budget</dt>
                            <dd class="font-medium">{{ $project->estimated_budget !== null ? 'LKR '.number_format((float) $project->estimated_budget, 0) : 'Not set' }}</dd>
                        </div>
                    </dl>
                </div>
            </section>

            <div class="space-y-4">
                <section class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm">
                    <h2 class="font-serif text-xl text-forest">Project progress</h2>
                    @if ($project->progress !== null)
                        <p class="mt-3 font-serif text-4xl text-forest">{{ $project->progress }}%</p>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-sand" role="progressbar" aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100" aria-label="Project progress">
                            <div class="h-full rounded-full bg-forest" style="width: {{ $project->progress }}%"></div>
                        </div>
                    @else
                        <p class="mt-3 text-sm text-mist">Progress has not been set.</p>
                    @endif
                </section>

                <section class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm">
                    <h2 class="font-serif text-xl text-forest">Project team</h2>
                    <div class="mt-4 space-y-4 text-sm">
                        <div>
                            <p class="text-mist">Designer</p>
                            @if ($project->designer)
                                <p class="font-medium text-charcoal">{{ $project->designer->professionalProfile?->displayName() ?? $project->designer->name }}</p>
                                <a href="{{ route('homeowner.professionals.show', $project->designer) }}" class="text-forest hover:underline">View Portfolio</a>
                            @else
                                <p class="text-charcoal">Not selected</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-mist">Contractor</p>
                            @if ($project->contractor)
                                <p class="font-medium text-charcoal">{{ $project->contractor->professionalProfile?->displayName() ?? $project->contractor->name }}</p>
                                <a href="{{ route('homeowner.professionals.show', $project->contractor) }}" class="text-forest hover:underline">View Portfolio</a>
                            @else
                                <p class="text-charcoal">Not selected</p>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('homeowner.projects.team', $project) }}" class="mt-4 inline-flex text-sm font-medium text-forest hover:underline">Change team</a>
                </section>
            </div>
        </div>
    @endif
</x-homeowner-layout>
