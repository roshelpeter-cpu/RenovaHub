<x-homeowner-layout title="Dashboard">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-olive">{{ now()->format('l, j F Y') }}</p>
            <h1 class="mt-2 font-serif text-3xl font-medium tracking-[-0.03em] text-forest sm:text-4xl">
                {{ now()->hour < 12 ? 'Good morning' : (now()->hour < 17 ? 'Good afternoon' : 'Good evening') }}, {{ str($userName = auth()->user()->name)->before(' ') }}
            </h1>
            <p class="mt-2 max-w-xl text-sm leading-relaxed text-mist">Here's what needs your attention across your renovation projects.</p>
        </div>
        <a href="{{ route('homeowner.projects.index') }}" class="inline-flex items-center justify-center rounded-full border border-forest px-5 py-3 text-sm font-medium text-forest transition duration-300 hover:bg-forest hover:text-ivory">My Projects</a>
    </div>

    <dl class="mt-8 grid grid-cols-2 gap-3 xl:grid-cols-5">
        @foreach ([
            ['Active Projects', $counts['active'], 'Projects currently in progress'],
            ['Pending Quotations', $counts['quotations'], 'Waiting for your decision'],
            ['Pending Payments', $counts['payments'], 'Amounts still outstanding'],
            ['Open Change Requests', $counts['changes'], 'Changes not yet closed'],
            ['Unread Messages', $counts['messages'], 'Replies from your team'],
        ] as [$label, $count, $hint])
            <div class="rounded-2xl border border-[#ece7dc] bg-[#F6F1E7] px-4 py-4 shadow-sm">
                <dt class="text-xs uppercase tracking-[0.14em] text-mist">{{ $label }}</dt>
                <dd class="mt-2 font-serif text-3xl text-forest">{{ $count }}</dd>
                <p class="mt-1 text-xs text-mist">{{ $hint }}</p>
            </div>
        @endforeach
    </dl>

    <div class="mt-8 grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(18rem,0.8fr)]">
        <section class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm sm:p-6">
            <h2 class="font-serif text-2xl text-forest">Needs your attention</h2>
            @if ($attention->isEmpty())
                <p class="mt-4 text-sm text-mist">Nothing is waiting on you right now.</p>
            @else
                <ul class="mt-4 divide-y divide-[#ece7dc]">
                    @foreach ($attention as $item)
                        <li class="flex flex-col gap-3 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm font-medium text-charcoal">{{ $item['title'] }}</p>
                                <p class="text-sm text-forest">{{ $item['project'] }}</p>
                                <p class="mt-1 text-sm text-mist">{{ $item['body'] }}</p>
                                <p class="mt-1 text-xs uppercase tracking-[0.12em] text-olive">{{ $item['time'] }}</p>
                            </div>
                            <a href="{{ $item['url'] }}" class="inline-flex shrink-0 rounded-full bg-forest px-4 py-2 text-sm font-medium text-ivory transition hover:bg-leaf">{{ $item['action'] }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>

        <section class="rounded-3xl border border-[#ece7dc] bg-[#F6F1E7] p-5 shadow-sm">
            <h2 class="font-serif text-2xl text-forest">Financial snapshot</h2>
            <dl class="mt-4 space-y-3 text-sm">
                @foreach ([
                    'Total project budget' => $finance['budget'],
                    'Approved quotation value' => $finance['approved'],
                    'Paid amount' => $finance['paid'],
                    'Outstanding amount' => $finance['outstanding'],
                ] as $label => $amount)
                    <div class="flex items-center justify-between gap-3 border-b border-[#e5dccb] pb-3">
                        <dt class="text-mist">{{ $label }}</dt>
                        <dd class="font-medium text-charcoal">LKR {{ number_format($amount, 2) }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>
    </div>

    <section class="mt-8">
        <h2 class="font-serif text-2xl text-forest">Project health</h2>
        @if ($projects->isEmpty())
            <div class="mt-4">@include('homeowner.partials.empty', ['title' => 'No projects yet', 'body' => 'Create a project to see design, planning and construction progress here.', 'action' => ['url' => route('homeowner.projects.create'), 'label' => 'Create Project']])</div>
        @else
            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                @foreach ($projects as $project)
                    <article class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-serif text-xl text-forest">{{ $project->name }}</h3>
                                <p class="text-sm text-mist">{{ $project->locationLabel() }} · {{ $project->statusLabel() }}</p>
                            </div>
                            <a href="{{ route('homeowner.projects.show', $project) }}" class="text-sm font-medium text-forest hover:underline">Open</a>
                        </div>
                        <ul class="mt-4 space-y-2">
                            @forelse ($project->orderedProgress() as $stage)
                                <li>
                                    <div class="flex justify-between text-xs uppercase tracking-[0.12em] text-mist">
                                        <span>{{ $stage->label() }}</span><span>{{ $stage->percent }}%</span>
                                    </div>
                                    <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-sand">
                                        <div class="h-full rounded-full bg-forest" style="width: {{ $stage->percent }}%"></div>
                                    </div>
                                </li>
                            @empty
                                <li class="text-sm text-mist">Overall progress {{ $project->progress ?? 0 }}%</li>
                            @endforelse
                        </ul>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <section class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="font-serif text-2xl text-forest">Recent activity</h2>
            @if ($activity->isEmpty())
                <p class="mt-3 text-sm text-mist">No activity has been recorded yet.</p>
            @else
                <ol class="mt-4 space-y-3">
                    @foreach ($activity as $entry)
                        <li class="border-l-2 border-[#DCE7D8] pl-3">
                            <p class="text-sm text-charcoal">{{ $entry->description }}</p>
                            <p class="text-xs text-mist">{{ $entry->created_at->format('j M Y, H:i') }}</p>
                        </li>
                    @endforeach
                </ol>
            @endif
        </section>
        <section class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="font-serif text-2xl text-forest">Upcoming</h2>
            <h3 class="mt-4 text-sm font-medium uppercase tracking-[0.14em] text-olive">Milestones</h3>
            @forelse ($milestones as $milestone)
                <p class="mt-2 text-sm text-charcoal">{{ $milestone->title }} <span class="text-mist">· {{ $milestone->due_on?->format('j M Y') ?? 'Date not set' }}</span></p>
            @empty
                <p class="mt-2 text-sm text-mist">No upcoming milestones.</p>
            @endforelse
            <h3 class="mt-5 text-sm font-medium uppercase tracking-[0.14em] text-olive">Payments</h3>
            @forelse ($upcomingPayments as $payment)
                <p class="mt-2 text-sm text-charcoal">{{ $payment->reference }} · {{ $payment->money() }}</p>
            @empty
                <p class="mt-2 text-sm text-mist">No payments are due.</p>
            @endforelse
        </section>
    </div>
</x-homeowner-layout>
