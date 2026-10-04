<x-homeowner-layout title="My Projects" :flush="true">
    @php
        $query = fn (?array $extra = []) => array_filter(array_merge([
            'status' => ($status ?? 'all') === 'all' ? null : $status,
            'search' => $search ?: null,
            'type' => $type ?: null,
            'sort' => ($sort ?? 'newest') === 'newest' ? null : $sort,
        ], $extra ?? []), fn ($value) => $value !== null && $value !== '');
        $completed = $projects->getCollection()->where('status', \App\Models\Project::STATUS_COMPLETED);
        $ongoing = $projects->getCollection()->where('status', \App\Models\Project::STATUS_IN_PROGRESS);
        $other = $projects->getCollection()->whereNotIn('status', [\App\Models\Project::STATUS_COMPLETED, \App\Models\Project::STATUS_IN_PROGRESS]);
        $grouped = ($status ?? 'all') === 'all' && ! $search && ! $type;
    @endphp

    <x-slot:hero>
        <section class="relative overflow-hidden">
            <div class="pointer-events-none absolute inset-y-0 right-0 hidden w-[56%] md:block" aria-hidden="true">
                <img src="{{ asset('images/renova/about-interior.jpg') }}" alt="" class="h-full w-full object-cover object-center">
                <div class="absolute inset-0" style="background: linear-gradient(90deg, #F6F1E7 0%, rgba(246,241,231,0.94) 14%, rgba(246,241,231,0.62) 38%, rgba(246,241,231,0.18) 68%, rgba(246,241,231,0.02) 100%);"></div>
            </div>
            <div class="relative mx-auto max-w-[90rem] px-4 pb-8 pt-7 sm:px-6 lg:px-8 lg:pb-10 lg:pt-9">
                <p class="text-sm text-[#66756C]">Home <span class="mx-1 text-[#c9c2b4]">›</span> My Projects</p>
                <h1 class="mt-3 font-serif text-4xl font-medium tracking-[-0.03em] text-[#123D2B] sm:text-5xl">My Projects</h1>
                <p class="mt-2 max-w-xl text-sm leading-relaxed text-[#66756C] sm:text-base">Manage all your renovation projects in one place.</p>

                <dl class="mt-8 grid max-w-3xl grid-cols-2 gap-3 lg:grid-cols-4">
                    @foreach ([
                        ['Total Projects', $summary['total'], 'M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z'],
                        ['In Progress', $summary['in_progress'], 'M12 7v5l3 2M12 21a9 9 0 1 1 0-18 9 9 0 0 1 0 18Z'],
                        ['Completed', $summary['completed'], 'M5 13l4 4L19 7'],
                        ['Total Project Value', \App\Models\Project::compactMoney($summary['value']), 'M4 7h16v12H4Z M8 7V4h8v3'],
                    ] as [$label, $value, $icon])
                        <div class="flex items-center gap-3 rounded-2xl border border-[#ece7dc] bg-white/90 px-4 py-3.5 shadow-[0_10px_28px_-20px_rgba(18,61,43,0.55)] backdrop-blur-sm">
                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#F6F1E7] text-[#123D2B]">
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="{{ $icon }}"/></svg>
                            </span>
                            <div>
                                <dd class="font-serif text-2xl leading-none text-[#123D2B] sm:text-[1.7rem]">{{ $value }}</dd>
                                <dt class="mt-1 text-xs text-[#66756C]">{{ $label }}</dt>
                            </div>
                        </div>
                    @endforeach
                </dl>
            </div>
        </section>
    </x-slot>

    <div class="mx-auto max-w-[90rem] px-4 pb-10 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-3 rounded-[1.5rem] border border-[#ece7dc] bg-white/85 p-2.5 shadow-sm xl:flex-row xl:items-center xl:justify-between">
            <div class="flex flex-wrap gap-1.5" role="navigation" aria-label="Filter projects">
                @foreach ([
                    'all' => 'All ('.$summary['total'].')',
                    'in_progress' => 'In Progress ('.$summary['in_progress'].')',
                    'planning' => 'Planning ('.$summary['planning'].')',
                    'completed' => 'Completed ('.$summary['completed'].')',
                ] as $value => $label)
                    <a href="{{ route('homeowner.projects.index', $query(['status' => $value === 'all' ? null : $value])) }}" class="rounded-full px-4 py-2 text-sm transition duration-300 {{ ($status ?? 'all') === $value ? 'bg-[#123D2B] text-[#F6F1E7]' : 'text-[#18352A] hover:bg-[#F6F1E7]' }}" @if(($status ?? 'all') === $value) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </div>

            <div class="flex w-full flex-col gap-2 sm:flex-row xl:w-auto">
                <form method="GET" action="{{ route('homeowner.projects.index') }}" class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row xl:flex-none">
                    @if (($status ?? 'all') !== 'all')
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    <label class="relative min-w-0 flex-1 xl:w-64">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#66756C]">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="6.5"/><path d="m20 20-3.2-3.2"/></svg>
                        </span>
                        <span class="sr-only">Search projects</span>
                        <input id="project-search" name="search" type="search" value="{{ $search }}" placeholder="Search projects by name, location or type..." class="w-full rounded-full border border-line bg-[#F6F1E7] py-2.5 pl-9 pr-4 text-sm outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">
                    </label>
                    <label class="sr-only" for="project-type">Renovation type</label>
                    <select id="project-type" name="type" onchange="this.form.submit()" class="rounded-full border border-line bg-[#F6F1E7] px-4 py-2.5 text-sm">
                        <option value="">All Types</option>
                        @foreach (\App\Models\Project::renovationTypes() as $value => $label)
                            <option value="{{ $value }}" @selected(($type ?? null) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <label class="sr-only" for="project-sort">Sort</label>
                    <select id="project-sort" name="sort" onchange="this.form.submit()" class="rounded-full border border-line bg-[#F6F1E7] px-4 py-2.5 text-sm">
                        @foreach (['newest' => 'Newest', 'oldest' => 'Oldest', 'budget_high' => 'Highest Budget', 'budget_low' => 'Lowest Budget', 'progress' => 'Progress'] as $value => $label)
                            <option value="{{ $value }}" @selected(($sort ?? 'newest') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('homeowner.projects.create') }}" class="inline-flex items-center justify-center rounded-full bg-[#123D2B] px-5 py-2.5 text-sm font-medium text-[#F6F1E7] transition hover:bg-leaf">+ Create Project</a>
            </div>
        </div>

        @if ($projects->isEmpty())
            <div class="mt-8">
                @include('homeowner.partials.empty', [
                    'title' => $search || ($status ?? 'all') !== 'all' ? 'No projects match this view.' : 'You have not created a project yet.',
                    'body' => 'Create a project to start planning the renovation with a designer and contractor.',
                    'action' => ['url' => route('homeowner.projects.create'), 'label' => 'Create Project'],
                ])
            </div>
        @elseif ($grouped)
            @if ($completed->isNotEmpty())
                <div class="mt-7 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($completed as $project)
                        @include('homeowner.projects.partials.card', ['project' => $project])
                    @endforeach
                </div>
            @endif
            @if ($ongoing->isNotEmpty())
                <div class="mt-5 grid gap-5 lg:grid-cols-2">
                    @foreach ($ongoing as $project)
                        @include('homeowner.projects.partials.card', ['project' => $project, 'wide' => true])
                    @endforeach
                </div>
            @endif
            @if ($other->isNotEmpty())
                <div class="mt-5 grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($other as $project)
                        @include('homeowner.projects.partials.card', ['project' => $project])
                    @endforeach
                </div>
            @endif
        @else
            <div class="mt-7 grid gap-5 {{ ($status ?? '') === 'in_progress' ? 'lg:grid-cols-2' : 'md:grid-cols-2 xl:grid-cols-3' }}">
                @foreach ($projects as $project)
                    @include('homeowner.projects.partials.card', ['project' => $project, 'wide' => ($status ?? '') === 'in_progress'])
                @endforeach
            </div>
        @endif

        <div class="mt-8">{{ $projects->links() }}</div>
    </div>
</x-homeowner-layout>
