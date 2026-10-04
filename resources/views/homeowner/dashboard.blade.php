<x-homeowner-layout title="Dashboard">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-olive">Homeowner workspace</p>
            <h1 class="mt-2 font-serif text-3xl font-medium tracking-[-0.03em] text-forest sm:text-4xl">
                {{ now()->hour < 12 ? 'Good morning' : (now()->hour < 17 ? 'Good afternoon' : 'Good evening') }}, {{ auth()->user()->name }}
            </h1>
            <p class="mt-2 max-w-xl text-sm leading-relaxed text-mist">Here’s what’s happening with your renovation projects.</p>
        </div>
        <a href="{{ route('homeowner.projects.create') }}" class="inline-flex items-center justify-center rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory transition duration-300 hover:bg-leaf">Create Project</a>
    </div>

    <dl class="mt-8 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach ([
            ['Active Projects', $counts['active']],
            ['Pending Quotations', $counts['quotations']],
            ['Pending Payment', $counts['payments']],
            ['Open Change Request', $counts['changes']],
        ] as [$label, $count])
            <div class="rounded-2xl border border-[#ece7dc] bg-white px-4 py-4 shadow-sm">
                <dt class="text-xs uppercase tracking-[0.14em] text-mist">{{ $label }}</dt>
                <dd class="mt-2 font-serif text-3xl text-forest">{{ $count }}</dd>
            </div>
        @endforeach
    </dl>

    <section class="mt-8">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-serif text-2xl text-forest">My Projects</h2>
            <a href="{{ route('homeowner.projects.index') }}" class="text-sm font-medium text-forest transition hover:text-leaf">View all</a>
        </div>

        @if ($projects->isEmpty())
            <div class="mt-4 rounded-3xl border border-dashed border-olive/40 bg-white/70 px-6 py-10 text-center">
                <p class="font-medium text-charcoal">No projects yet</p>
                <p class="mt-1 text-sm text-mist">Create a project to save its details, location and team.</p>
            </div>
        @else
            <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($projects as $project)
                    <article class="overflow-hidden rounded-3xl border border-[#ece7dc] bg-white shadow-sm">
                        @if ($project->cover_image)
                            <img src="{{ asset($project->cover_image) }}" alt="" class="h-36 w-full object-cover">
                        @endif
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="font-serif text-xl text-forest">{{ $project->name }}</h3>
                                <span class="shrink-0 rounded-full bg-[#e7f0e4] px-3 py-1 text-xs font-medium text-forest">{{ $project->statusLabel() }}</span>
                            </div>
                            <p class="mt-2 text-sm text-mist">{{ $project->locationLabel() ?: 'Location not added yet' }}</p>
                            <a href="{{ route('homeowner.projects.show', $project) }}" class="mt-4 inline-flex text-sm font-medium text-forest underline-offset-2 hover:underline">View Details</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="mt-8 max-w-xl rounded-3xl border border-dashed border-olive/40 bg-white/70 px-5 py-5">
        <h2 class="font-serif text-xl text-forest">Recent Activity</h2>
        <p class="mt-2 text-sm text-mist">No activity has been recorded yet. Project updates will appear here when that history is added.</p>
    </section>
</x-homeowner-layout>
