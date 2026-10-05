<x-designer-layout title="Dashboard">
    @php
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
        $first = strtok(auth()->user()->name, ' ');
    @endphp
    <div class="grid items-center gap-6 overflow-hidden rounded-[1.6rem] border border-[#ece7dc] bg-white shadow-sm lg:grid-cols-[1.2fr_0.8fr]">
        <div class="p-6 sm:p-8">
            <p class="text-sm text-[#66756C]">Dashboard</p>
            <h1 class="mt-2 font-serif text-4xl text-[#123D2B] sm:text-5xl">{{ $greeting }}, {{ $first }}!</h1>
            <p class="mt-3 max-w-xl text-sm leading-relaxed text-[#66756C]">Here's an overview of your design projects, invitations, approvals and earnings.</p>
        </div>
        <img src="{{ asset('images/renova/about-interior.jpg') }}" alt="" class="h-52 w-full object-cover lg:h-full">
    </div>

    <section class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ([
            ['Active Projects', $summary['active']],
            ['Pending Invitations', $summary['invitations']],
            ['Awaiting Approval', $summary['awaiting']],
            ['Revision Requests', $summary['revisions']],
            ['This Month\'s Earnings', 'LKR '.number_format($summary['month_earnings'], 0)],
        ] as [$label, $value])
            <article class="rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm">
                <p class="text-xs text-[#66756C]">{{ $label }}</p>
                <p class="mt-2 font-serif text-2xl text-[#123D2B]">{{ $value }}</p>
            </article>
        @endforeach
    </section>

    <section class="mt-8">
        <div class="flex items-end justify-between">
            <h2 class="font-serif text-2xl text-[#123D2B]">My Active Projects</h2>
            <a href="{{ route('designer.projects.index') }}" class="text-sm text-[#123D2B] hover:underline">View all</a>
        </div>
        <div class="mt-4 grid gap-4 lg:grid-cols-3">
            @forelse ($projects as $project)
                @include('designer.partials.project-card', ['project' => $project])
            @empty
                <p class="text-sm text-[#66756C]">Accepted projects will appear here.</p>
            @endforelse
        </div>
    </section>

    <section class="mt-8 grid gap-4 lg:grid-cols-3">
        <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="font-serif text-xl text-[#123D2B]">Recent Activity</h2>
            <ul class="mt-4 space-y-3">
                @forelse ($activity as $item)
                    <li>
                        <p class="text-sm text-[#123D2B]">{{ $item->description }}</p>
                        <p class="text-xs text-[#66756C]">{{ $item->created_at->diffForHumans() }}</p>
                    </li>
                @empty
                    <li class="text-sm text-[#66756C]">No design activity yet.</li>
                @endforelse
            </ul>
        </article>
        <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="font-serif text-xl text-[#123D2B]">Upcoming Design Tasks</h2>
            <ul class="mt-4 space-y-3">
                @forelse ($tasks as $task)
                    <li>
                        <p class="text-sm font-medium text-[#123D2B]">{{ $task->name }}</p>
                        <p class="text-xs text-[#66756C]">{{ $task->project?->name }} · {{ $task->due_on?->format('j M') ?? 'No date' }} · {{ $task->statusLabel() }} · {{ $task->progress }}%</p>
                    </li>
                @empty
                    <li class="text-sm text-[#66756C]">No open design tasks.</li>
                @endforelse
            </ul>
        </article>
        <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="font-serif text-xl text-[#123D2B]">Latest Messages</h2>
            <ul class="mt-4 space-y-3">
                @forelse ($messages as $conversation)
                    @php $other = $conversation->counterpart(auth()->user()); @endphp
                    <li>
                        <a href="{{ route('designer.messages.show', $conversation) }}" class="block hover:underline">
                            <p class="text-sm font-medium text-[#123D2B]">{{ $other?->name ?? 'Conversation' }}</p>
                            <p class="text-xs text-[#66756C]">{{ $conversation->last_preview ?: 'Open conversation' }}</p>
                        </a>
                    </li>
                @empty
                    <li class="text-sm text-[#66756C]">No messages yet.</li>
                @endforelse
            </ul>
        </article>
    </section>
</x-designer-layout>
