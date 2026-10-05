<x-contractor-layout title="Dashboard">
    @php
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
        $first = strtok(auth()->user()->name, ' ');
    @endphp
    <div class="grid items-center gap-6 overflow-hidden rounded-[1.6rem] border border-[#ece7dc] bg-white shadow-sm lg:grid-cols-[1.15fr_0.85fr]">
        <div class="p-6 sm:p-8">
            <p class="text-sm text-[#66756C]">Dashboard</p>
            <h1 class="rh-serif mt-2 text-4xl text-[#123D2B] sm:text-5xl">{{ $greeting }}, {{ $first }}!</h1>
            <p class="mt-3 max-w-xl text-sm leading-relaxed text-[#66756C]">Here's an overview of your construction projects, quotations, supplier orders and earnings.</p>
        </div>
        <img src="{{ asset('images/renova/hero.jpg') }}" alt="" class="h-56 w-full object-cover lg:h-full">
    </div>

    <section class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ([
            ['Active Projects', $summary['active']],
            ['Pending Invitations', $summary['invitations']],
            ['Quotations Awaiting Approval', $summary['quotations']],
            ['Active Change Requests', $summary['changes']],
            ['This Month\'s Earnings', 'LKR '.number_format($summary['month_earnings'], 0)],
        ] as [$label, $value])
            <article class="rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm">
                <p class="text-xs text-[#66756C]">{{ $label }}</p>
                <p class="rh-serif mt-2 text-2xl text-[#123D2B]">{{ $value }}</p>
            </article>
        @endforeach
    </section>

    <section class="mt-8">
        <div class="flex items-end justify-between">
            <h2 class="rh-serif text-2xl text-[#123D2B]">My Active Projects</h2>
            <a href="{{ route('contractor.projects.index') }}" class="text-sm text-[#123D2B] hover:underline">View all</a>
        </div>
        <div class="mt-4 grid gap-4 lg:grid-cols-3">
            @forelse ($projects as $project)
                @include('contractor.partials.project-card', ['project' => $project])
            @empty
                <p class="text-sm text-[#66756C]">Accepted projects will appear here.</p>
            @endforelse
        </div>
    </section>

    <section class="mt-8 grid gap-4 lg:grid-cols-3">
        <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="rh-serif text-xl text-[#123D2B]">Recent Activity</h2>
            <ul class="mt-4 space-y-3">
                @forelse ($activity as $item)
                    <li>
                        <p class="text-sm text-[#123D2B]">{{ $item->description }}</p>
                        <p class="text-xs text-[#66756C]">{{ $item->created_at->diffForHumans() }}</p>
                    </li>
                @empty
                    <li class="text-sm text-[#66756C]">No construction activity yet.</li>
                @endforelse
            </ul>
        </article>
        <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="rh-serif text-xl text-[#123D2B]">Upcoming Tasks</h2>
            <ul class="mt-4 space-y-3">
                @forelse ($tasks as $task)
                    <li>
                        <p class="text-sm font-medium text-[#123D2B]">{{ $task->name }}</p>
                        <p class="text-xs text-[#66756C]">{{ $task->project?->name }} · {{ $task->due_on?->format('j M Y') ?? 'No date' }} · {{ $task->contractorStatusLabel() }} · {{ $task->progress }}%</p>
                    </li>
                @empty
                    <li class="text-sm text-[#66756C]">No open construction tasks.</li>
                @endforelse
            </ul>
        </article>
        <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="rh-serif text-xl text-[#123D2B]">Latest Messages</h2>
            <ul class="mt-4 space-y-3">
                @forelse ($messages as $conversation)
                    @php $other = $conversation->counterpart(auth()->user()); @endphp
                    <li>
                        <a href="{{ route('contractor.messages.show', $conversation) }}" class="block hover:underline">
                            <p class="text-sm font-medium text-[#123D2B]">{{ $other?->name ?? 'Conversation' }}</p>
                            <p class="text-xs text-[#66756C]">{{ $other?->role ? ucfirst($other->role) : 'Support' }} · {{ $conversation->last_preview ?: 'Open conversation' }}</p>
                        </a>
                    </li>
                @empty
                    <li class="text-sm text-[#66756C]">No messages yet.</li>
                @endforelse
            </ul>
        </article>
    </section>
</x-contractor-layout>
