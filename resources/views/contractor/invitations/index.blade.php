<x-contractor-layout title="Project Invitations">
    <h1 class="rh-serif text-4xl text-[#123D2B] sm:text-5xl">Project Invitations</h1>
    <p class="mt-2 text-sm text-[#66756C]">Review and respond to renovation project offers from homeowners.</p>
    <div class="mt-5 flex gap-2">
        @foreach (['pending' => 'Pending', 'accepted' => 'Accepted', 'declined' => 'Declined'] as $key => $label)
            <a href="{{ route('contractor.invitations.index', ['tab' => $key]) }}" class="rounded-full px-4 py-2 text-sm {{ $tab === $key ? 'bg-[#123D2B] text-white' : 'border border-[#ece7dc] bg-white text-[#66756C]' }}">{{ $label }}</a>
        @endforeach
    </div>
    <div class="mt-6 space-y-4">
        @forelse ($invitations as $invitation)
            @php $project = $invitation->project; @endphp
            <article class="grid gap-4 rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm lg:grid-cols-[18rem_1fr_12rem]">
                <img src="{{ $project->coverUrl() ?: asset('images/renova/about-exterior.jpg') }}" alt="" class="h-44 w-full rounded-2xl object-cover">
                <div>
                    <h2 class="rh-serif text-2xl text-[#123D2B]">{{ $project->name }}</h2>
                    <p class="mt-1 text-sm text-[#66756C]">{{ $project->cataloguePlaceLabel() }} · {{ $project->propertyTypeLabel() }} · {{ $project->displayTypeLabel() }}</p>
                    <p class="mt-3 text-sm leading-relaxed text-[#66756C]">{{ \Illuminate\Support\Str::limit($project->description, 280) }}</p>
                    <dl class="mt-4 grid gap-2 text-sm text-[#66756C] sm:grid-cols-2">
                        <div><dt class="text-xs">Estimated budget</dt><dd class="text-[#123D2B]">{{ $project->money($project->estimated_budget) }}</dd></div>
                        <div><dt class="text-xs">Expected start</dt><dd class="text-[#123D2B]">{{ $project->expected_start_date?->format('j M Y') ?? '—' }}</dd></div>
                        <div><dt class="text-xs">Expected completion</dt><dd class="text-[#123D2B]">{{ $project->expected_completion_date?->format('j M Y') ?? '—' }}</dd></div>
                        <div><dt class="text-xs">Homeowner</dt><dd class="text-[#123D2B]">{{ $project->homeowner?->name }}</dd></div>
                    </dl>
                </div>
                <div class="flex flex-col items-start gap-3">
                    <div class="flex items-center gap-2">
                        <img src="{{ $project->homeowner?->profile_photo_url }}" alt="" class="h-10 w-10 rounded-full object-cover">
                        <div>
                            <p class="text-sm font-medium text-[#123D2B]">{{ $project->homeowner?->name }}</p>
                            <p class="text-xs text-[#66756C]">Homeowner</p>
                        </div>
                    </div>
                    @if ($tab === 'pending')
                        <a href="{{ route('contractor.invitations.index', ['tab' => 'pending']) }}" class="text-sm text-[#123D2B] underline">View Details</a>
                        <form method="POST" action="{{ route('contractor.invitations.reject', $invitation) }}" class="w-full">@csrf<button class="w-full rounded-full border border-[#c9c2b4] px-4 py-2 text-sm text-[#123D2B]">Reject</button></form>
                        <form method="POST" action="{{ route('contractor.invitations.accept', $invitation) }}" class="w-full">@csrf<button class="w-full rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Accept Offer</button></form>
                    @elseif ($tab === 'accepted')
                        <a href="{{ route('contractor.projects.show', $project) }}" class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">View Project</a>
                    @else
                        <p class="text-sm text-[#66756C]">Declined. The homeowner can invite another contractor.</p>
                    @endif
                </div>
            </article>
        @empty
            <p class="rounded-2xl border border-[#ece7dc] bg-white p-6 text-sm text-[#66756C]">No {{ $tab }} invitations.</p>
        @endforelse
    </div>
</x-contractor-layout>
