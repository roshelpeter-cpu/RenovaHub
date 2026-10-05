<x-dynamic-component :component="$layout" :title="$project->name">
    <a href="{{ route($invitation->role === 'designer' ? 'designer.invitations.index' : 'contractor.invitations.index') }}" class="text-sm text-[#66756C] hover:text-[#123D2B]">← Invitations</a>
    <article class="mt-4 overflow-hidden rounded-[1.5rem] border border-[#ece7dc] bg-white shadow-sm">
        <div class="grid lg:grid-cols-[1.2fr_0.8fr]">
            <img src="{{ $project->coverUrl() ?: asset('images/renova/about-exterior.jpg') }}" alt="" class="h-72 w-full object-cover lg:h-full">
            <div class="p-6">
                <p class="text-xs uppercase tracking-[0.14em] text-[#66756C]">{{ $roleLabel }} invitation</p>
                <h1 class="mt-2 font-serif text-4xl text-[#123D2B]">{{ $project->name }}</h1>
                <p class="mt-2 text-sm text-[#66756C]">{{ $project->homeowner?->name }} · Homeowner</p>
                <p class="mt-4 text-sm leading-relaxed text-[#66756C]">{{ $project->description }}</p>
            </div>
        </div>
        <dl class="grid gap-4 border-t border-[#ece7dc] p-6 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([
                'Location' => $project->cataloguePlaceLabel(),
                'Renovation type' => $project->displayTypeLabel(),
                'Property type' => $project->propertyTypeLabel(),
                'Estimated budget' => $project->money($project->estimated_budget),
                'Desired start' => $project->expected_start_date?->format('j M Y') ?? 'Not set',
                'Desired completion' => $project->expected_completion_date?->format('j M Y') ?? 'Not set',
                'Invitation date' => $invitation->created_at?->format('j M Y') ?? '—',
                'Your role' => $roleLabel,
            ] as $label => $value)
                <div>
                    <dt class="text-xs text-[#66756C]">{{ $label }}</dt>
                    <dd class="mt-1 text-sm text-[#123D2B]">{{ $value }}</dd>
                </div>
            @endforeach
        </dl>
        @if ($project->requirements)
            <div class="border-t border-[#ece7dc] px-6 py-5">
                <h2 class="font-serif text-2xl text-[#123D2B]">Project requirements</h2>
                <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-[#66756C]">{{ $project->requirements }}</p>
            </div>
        @endif
        @if ($invitation->isPending())
            <div class="flex flex-col gap-3 border-t border-[#ece7dc] px-6 py-5 sm:flex-row sm:justify-end">
                <form method="POST" action="{{ route($invitation->role === 'designer' ? 'designer.invitations.reject' : 'contractor.invitations.reject', $invitation) }}">
                    @csrf
                    <button class="w-full rounded-full border border-[#c9c2b4] px-5 py-2.5 text-sm text-[#123D2B]">Reject Invitation</button>
                </form>
                <form method="POST" action="{{ route($invitation->role === 'designer' ? 'designer.invitations.accept' : 'contractor.invitations.accept', $invitation) }}">
                    @csrf
                    <button class="w-full rounded-full bg-[#123D2B] px-5 py-2.5 text-sm text-white">Accept Invitation</button>
                </form>
            </div>
        @else
            <p class="border-t border-[#ece7dc] px-6 py-5 text-sm text-[#66756C]">This invitation is {{ $invitation->status }}.</p>
        @endif
    </article>
    @if ($project->referenceImages->count() > 1)
        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-5">
            @foreach ($project->referenceImages->take(5) as $image)
                <img src="{{ str_starts_with($image->path, 'images/') ? asset($image->path) : \Illuminate\Support\Facades\Storage::disk('public')->url($image->path) }}" alt="" class="h-24 w-full rounded-2xl object-cover">
            @endforeach
        </div>
    @endif
</x-dynamic-component>
