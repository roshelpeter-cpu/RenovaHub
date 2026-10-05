<x-designer-layout title="Project Invitations">
    <h1 class="font-serif text-4xl text-[#123D2B] sm:text-5xl">Project Invitations</h1>
    <p class="mt-2 text-sm text-[#66756C]">Review and respond to renovation project offers from homeowners.</p>
    <div class="mt-5 flex gap-2">
        @foreach (['pending' => 'Pending', 'accepted' => 'Accepted', 'declined' => 'Declined'] as $key => $label)
            <a href="{{ route('designer.invitations.index', ['tab' => $key]) }}" class="rounded-full px-4 py-2 text-sm {{ $tab === $key ? 'bg-[#123D2B] text-white' : 'bg-white text-[#66756C]' }}">{{ $label }}</a>
        @endforeach
    </div>
    <div class="mt-6 space-y-4">
        @forelse ($invitations as $invitation)
            @php $project = $invitation->project; @endphp
            <article class="grid gap-4 rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm lg:grid-cols-[16rem_1fr_auto]">
                <img src="{{ $project->coverUrl() ?: asset('images/renova/about-exterior.jpg') }}" alt="" class="h-40 w-full rounded-2xl object-cover">
                <div>
                    <h2 class="font-serif text-2xl text-[#123D2B]">{{ $project->name }}</h2>
                    <p class="mt-1 text-sm text-[#66756C]">Offered by {{ $project->homeowner?->name }} · Homeowner</p>
                    <dl class="mt-3 grid gap-2 text-sm text-[#66756C] sm:grid-cols-2">
                        <div><dt class="text-xs">Location</dt><dd class="text-[#123D2B]">{{ $project->cataloguePlaceLabel() }}</dd></div>
                        <div><dt class="text-xs">Budget</dt><dd class="text-[#123D2B]">{{ $project->money($project->estimated_budget) }}</dd></div>
                        <div><dt class="text-xs">Expected start</dt><dd class="text-[#123D2B]">{{ $project->expected_start_date?->format('F Y') ?? '—' }}</dd></div>
                        <div><dt class="text-xs">Expected completion</dt><dd class="text-[#123D2B]">{{ $project->expected_completion_date?->format('F Y') ?? '—' }}</dd></div>
                    </dl>
                    <p class="mt-3 text-sm leading-relaxed text-[#66756C]">{{ \Illuminate\Support\Str::limit($project->description, 220) }}</p>
                </div>
                <div class="flex flex-row gap-2 lg:flex-col">
                    @if ($tab === 'accepted')
                        <a href="{{ route('designer.projects.show', $project) }}" class="rounded-full bg-[#123D2B] px-4 py-2 text-center text-sm text-white">View Project</a>
                    @elseif ($tab === 'pending')
                        <form method="POST" action="{{ route('designer.invitations.accept', $invitation) }}">@csrf<button class="w-full rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Accept Offer</button></form>
                        <form method="POST" action="{{ route('designer.invitations.reject', $invitation) }}">@csrf<button class="w-full rounded-full border border-[#c9c2b4] px-4 py-2 text-sm text-[#123D2B]">Reject</button></form>
                    @else
                        <p class="text-sm text-[#66756C]">This offer was declined. The private workspace stays closed.</p>
                    @endif
                </div>
            </article>
        @empty
            <p class="rounded-2xl border border-[#ece7dc] bg-white p-6 text-sm text-[#66756C]">No {{ $tab }} invitations.</p>
        @endforelse
    </div>
</x-designer-layout>
