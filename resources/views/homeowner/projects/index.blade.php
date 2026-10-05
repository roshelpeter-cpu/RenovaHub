<x-homeowner-layout title="My Projects" :flush="true">
    <div class="relative overflow-hidden">
        <div class="pointer-events-none absolute -left-10 top-8 hidden h-40 w-40 opacity-[0.12] lg:block" aria-hidden="true">
            <svg viewBox="0 0 120 120" class="h-full w-full text-[#123D2B]"><path fill="currentColor" d="M20 90c20-40 40-70 70-80 8 22-6 40-18 52-16 16-40 22-52 28Z"/></svg>
        </div>
        <div class="mx-auto max-w-[90rem] px-4 pb-2 pt-8 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h1 class="font-serif text-4xl font-medium tracking-[-0.03em] text-[#123D2B] sm:text-5xl">My Projects</h1>
                    <p class="mt-2 max-w-xl text-sm leading-relaxed text-[#66756C] sm:text-base">Track and manage all your renovation projects in one place.</p>
                </div>
                <a href="{{ route('homeowner.projects.create') }}" class="inline-flex shrink-0 items-center justify-center rounded-full bg-[#123D2B] px-5 py-2.5 text-sm font-medium text-white">+ Create New Project</a>
            </div>

            <dl class="mt-8 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                @foreach ([
                    ['Started Projects', $summary['total'], 'M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z'],
                    ['Ongoing', $summary['ongoing'], 'M12 7v5l3 2M12 21a9 9 0 1 1 0-18 9 9 0 0 1 0 18Z'],
                    ['Upcoming', $summary['upcoming'], 'M8 7V4h8v3M6 7h12v13H6Z'],
                    ['Completed', $summary['completed'], 'M5 13l4 4L19 7'],
                    ['Total Project Value', \App\Models\Project::compactMoney($summary['value']), 'M4 7h16v12H4Z M8 7V4h8v3'],
                ] as [$label, $value, $icon])
                    <div class="flex items-center gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white px-5 py-4 shadow-sm">
                        <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#E7F0E4] text-[#123D2B]">
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="{{ $icon }}"/></svg>
                        </span>
                        <div>
                            <dd class="font-serif text-3xl leading-none text-[#123D2B]">{{ $value }}</dd>
                            <dt class="mt-1 text-xs text-[#66756C]">{{ $label }}</dt>
                        </div>
                    </div>
                @endforeach
            </dl>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex flex-wrap gap-2" role="navigation" aria-label="Filter projects">
                    @foreach ([
                        'all' => 'All ('.$summary['total'].')',
                        'ongoing' => 'Ongoing ('.$summary['ongoing'].')',
                        'completed' => 'Completed ('.$summary['completed'].')',
                        'upcoming' => 'Upcoming ('.$summary['upcoming'].')',
                    ] as $value => $label)
                        <a href="{{ route('homeowner.projects.index', array_filter(['status' => $value === 'all' ? null : $value, 'sort' => $sort === 'newest' ? null : $sort])) }}" class="rounded-full px-4 py-2 text-sm {{ $status === $value ? 'bg-[#123D2B] text-white' : 'bg-[#F6F1E7] text-[#18352A]' }}" @if($status === $value) aria-current="page" @endif>{{ $label }}</a>
                    @endforeach
                </div>
                <form method="GET" action="{{ route('homeowner.projects.index') }}" class="text-sm text-[#66756C]">
                    @if ($status !== 'all')
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    <label>Sort by
                        <select name="sort" onchange="this.form.submit()" class="ml-2 rounded-full border border-[#ece7dc] bg-white px-3 py-1.5 text-sm text-[#123D2B]">
                            <option value="newest" @selected($sort === 'newest')>Most Recent</option>
                            <option value="oldest" @selected($sort === 'oldest')>Oldest</option>
                            <option value="budget_high" @selected($sort === 'budget_high')>Highest Budget</option>
                        </select>
                    </label>
                </form>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-[90rem] px-4 pb-12 sm:px-6 lg:px-8">
        @if ($projects->isEmpty())
            <div class="mt-8">
                @include('homeowner.partials.empty', [
                    'title' => 'No projects match this view.',
                    'body' => 'Create a project to start planning the renovation with a designer and contractor.',
                    'action' => ['url' => route('homeowner.projects.create'), 'label' => 'Create New Project'],
                ])
            </div>
        @else
            @if ($status === 'upcoming' && $upcoming->isNotEmpty())
                <section class="mt-8">
                    <h2 class="font-serif text-2xl text-[#123D2B]">Upcoming Projects ({{ $upcoming->count() }})</h2>
                    <p class="mt-1 text-sm text-[#66756C]">These projects have been created and are waiting to start. They stay here until work begins.</p>
                    <div class="mt-4 grid gap-5 lg:grid-cols-2">
                        @foreach ($upcoming as $project)
                            @include('homeowner.projects.partials.card', ['project' => $project, 'wide' => true])
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($status !== 'completed' && $status !== 'upcoming' && $ongoing->isNotEmpty())
                <section class="mt-8">
                    <h2 class="font-serif text-2xl text-[#123D2B]">Ongoing Projects ({{ $ongoing->count() }})</h2>
                    <div class="mt-4 grid gap-5 lg:grid-cols-2">
                        @foreach ($ongoing as $project)
                            @include('homeowner.projects.partials.card', ['project' => $project, 'wide' => true])
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($status !== 'ongoing' && $status !== 'upcoming' && $completed->isNotEmpty())
                <section class="mt-10">
                    <h2 class="font-serif text-2xl text-[#123D2B]">Completed Projects ({{ $completed->count() }})</h2>
                    <div class="mt-4 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($completed as $project)
                            @include('homeowner.projects.partials.card', ['project' => $project, 'wide' => false])
                        @endforeach
                    </div>
                </section>
            @endif
        @endif
    </div>
</x-homeowner-layout>
