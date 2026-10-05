<x-contractor-layout title="Dashboard">
    @php
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
        $first = strtok(auth()->user()->name, ' ');
        $cards = [
            ['Active Projects', $summary['active'], 'Assigned to you'],
            ['Pending Invitations', $summary['invitations'], 'Waiting for your reply'],
            ['Pending Quotations', $summary['quotations'], 'Prices still to compare'],
            ['Awaiting Homeowner Approval', $summary['awaiting'], 'Options or budget sent'],
            ['Total Project Value', 'LKR '.number_format($summary['value'], 0), 'Accepted projects'],
            ['Net Earnings', 'LKR '.number_format($summary['month_earnings'], 0), 'After the platform fee'],
        ];
    @endphp

    <div class="grid items-center gap-4 overflow-hidden rounded-2xl border border-[#ece7dc] bg-white shadow-sm lg:grid-cols-[1.4fr_0.8fr]">
        <div class="p-6">
            <h1 class="rh-serif text-3xl text-[#123D2B]">{{ $greeting }}, {{ $first }}!</h1>
            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-[#66756C]">Manage your renovation projects, suppliers, quotations and construction work from one workspace.</p>
        </div>
        <img src="{{ asset('images/renova/hero.jpg') }}" alt="" class="h-36 w-full object-cover lg:h-full">
    </div>

    <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-6">
        @foreach ($cards as [$label, $value, $hint])
            <article class="rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm">
                <p class="text-xs text-[#66756C]">{{ $label }}</p>
                <p class="mt-2 text-xl font-semibold text-[#123D2B]">{{ $value }}</p>
                <p class="mt-1 text-xs text-[#66756C]">{{ $hint }}</p>
            </article>
        @endforeach
    </section>

    <div class="mt-6 grid gap-4 xl:grid-cols-[1.5fr_0.8fr]">
        <section>
            <div class="mb-3 flex items-center justify-between">
                <h2 class="rh-serif text-2xl text-[#123D2B]">Current Projects</h2>
                <a href="{{ route('contractor.projects.index') }}" class="text-sm text-[#123D2B] underline">View all</a>
            </div>
            <div class="grid gap-4 lg:grid-cols-3">
                @forelse ($projects as $project)
                    @include('contractor.partials.project-card', ['project' => $project])
                @empty
                    <p class="text-sm text-[#66756C]">No accepted projects yet. Review your invitations to get started.</p>
                @endforelse
            </div>
        </section>
        <div class="space-y-4">
            <section class="rounded-2xl border border-[#ece7dc] bg-white p-5 shadow-sm">
                <h2 class="rh-serif text-2xl text-[#123D2B]">Action Required</h2>
                <ul class="mt-4 space-y-3">
                    @forelse ($actions as $action)
                        <li>
                            <a href="{{ $action['href'] }}" class="block rounded-xl bg-[#F7F4EE] px-3 py-3 hover:bg-[#F6F1E7]">
                                <span class="block text-sm font-medium text-[#123D2B]">{{ $action['title'] }}</span>
                                <span class="mt-1 block text-xs text-[#66756C]">{{ $action['project'] }} · {{ $action['time'] }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="text-sm text-[#66756C]">Nothing is waiting on you right now.</li>
                    @endforelse
                </ul>
            </section>
            <section class="rounded-2xl border border-[#ece7dc] bg-white p-5 shadow-sm">
                <h2 class="rh-serif text-2xl text-[#123D2B]">Recent Activity</h2>
                <ul class="mt-4 space-y-3">
                    @forelse ($activity as $entry)
                        <li class="border-b border-[#ece7dc] pb-3 last:border-0">
                            <p class="text-sm text-[#123D2B]">{{ $entry->description }}</p>
                            <p class="mt-1 text-xs text-[#66756C]">{{ $entry->created_at?->diffForHumans() }}</p>
                        </li>
                    @empty
                        <li class="text-sm text-[#66756C]">Activity appears as invitations, approvals and payments move.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    </div>
</x-contractor-layout>
