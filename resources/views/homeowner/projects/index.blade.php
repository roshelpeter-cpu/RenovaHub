<x-homeowner-layout title="My Projects">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-olive">Projects</p>
            <h1 class="mt-2 font-serif text-3xl font-medium tracking-[-0.03em] text-forest sm:text-4xl">My Projects</h1>
            <p class="mt-2 text-sm text-mist">Manage all your renovation projects in one place.</p>
        </div>
        <a href="{{ route('homeowner.projects.create') }}" class="inline-flex items-center justify-center rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory transition duration-300 hover:bg-leaf">+ Create Project</a>
    </div>

    <form method="GET" action="{{ route('homeowner.projects.index') }}" class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        @if ($status)
            <input type="hidden" name="status" value="{{ $status }}">
        @endif
        <div class="flex flex-wrap gap-2" role="navigation" aria-label="Filter projects">
            @foreach ([
                'all' => 'All',
                'in_progress' => 'In Progress',
                'planning' => 'Planning',
                'completed' => 'Completed',
            ] as $value => $label)
                <a href="{{ route('homeowner.projects.index', array_filter(['status' => $value === 'all' ? null : $value, 'search' => $search ?: null])) }}" class="rounded-full border px-4 py-2 text-sm transition duration-300 {{ ($status ?? 'all') === $value ? 'border-forest bg-forest text-ivory' : 'border-[#ddd6c8] bg-white text-charcoal hover:border-forest' }}" @if(($status ?? 'all') === $value) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </div>
        <label class="sr-only" for="project-search">Search projects</label>
        <input id="project-search" name="search" type="search" value="{{ $search }}" placeholder="Search projects..." class="w-full rounded-full border border-line bg-white px-4 py-2.5 text-sm outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10 sm:max-w-xs">
    </form>

    @if ($projects->isEmpty())
        <div class="mt-8 rounded-3xl border border-dashed border-olive/40 bg-white/70 px-6 py-12 text-center">
            <p class="font-medium text-charcoal">{{ $search || $status ? 'No projects match this view.' : 'You have not created a project yet.' }}</p>
        </div>
    @else
        <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($projects as $project)
                <article class="flex h-full flex-col overflow-hidden rounded-3xl border border-[#ece7dc] bg-white shadow-sm">
                    @if ($project->cover_image)
                        <img src="{{ asset($project->cover_image) }}" alt="" class="h-40 w-full object-cover">
                    @endif
                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="font-serif text-2xl leading-tight text-forest">{{ $project->name }}</h2>
                            <span class="shrink-0 rounded-full bg-[#e7f0e4] px-3 py-1 text-xs font-medium text-forest">{{ $project->statusLabel() }}</span>
                        </div>
                        <p class="mt-3 text-sm text-charcoal">{{ $project->locationLabel() ?: 'Location not added yet' }}</p>
                        <p class="mt-1 text-sm text-mist">
                            @if ($project->expected_start_date && $project->expected_completion_date)
                                {{ $project->expected_start_date->format('M Y') }} – {{ $project->expected_completion_date->format('M Y') }}
                            @else
                                Dates not set
                            @endif
                        </p>
                        <p class="mt-1 text-sm text-charcoal">
                            {{ $project->estimated_budget !== null ? 'LKR '.number_format((float) $project->estimated_budget, 0) : 'Budget not set' }}
                        </p>
                        @if ($project->progress !== null)
                            <p class="mt-3 text-xs uppercase tracking-[0.14em] text-mist">Progress {{ $project->progress }}%</p>
                            <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-sand" role="progressbar" aria-valuenow="{{ $project->progress }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="h-full rounded-full bg-forest" style="width: {{ $project->progress }}%"></div>
                            </div>
                        @endif
                        <div class="mt-auto flex flex-wrap items-center gap-2 pt-5">
                            <a href="{{ route('homeowner.projects.show', $project) }}" class="inline-flex rounded-full border border-forest px-4 py-2 text-sm font-medium text-forest transition duration-300 hover:bg-forest hover:text-ivory">View Details</a>
                            @include('homeowner.projects.partials.delete-form', ['project' => $project])
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="mt-8">{{ $projects->links() }}</div>
    @endif
</x-homeowner-layout>
