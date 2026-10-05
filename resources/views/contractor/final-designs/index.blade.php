<x-contractor-layout title="Final Designs">
    <div>
        <h1 class="rh-serif text-3xl text-[#123D2B]">Final Designs</h1>
        <p class="mt-2 text-sm text-[#66756C]">View approved final design packages from your projects.</p>
    </div>
    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($projects as $project)
            @php $approved = $project->designConcepts->first(fn ($concept) => $concept->status === \App\Models\DesignConcept::STATUS_APPROVED); @endphp
            <article class="overflow-hidden rounded-2xl border border-[#ece7dc] bg-white shadow-sm">
                <img src="{{ $project->coverUrl() ?: asset('images/renova/feature-plans.jpg') }}" alt="" class="h-44 w-full object-cover">
                <div class="p-5">
                    <div class="flex items-start justify-between gap-3">
                        <h2 class="rh-serif text-xl text-[#123D2B]">{{ $project->name }}</h2>
                        <span class="rounded-full bg-[#F6F1E7] px-2.5 py-1 text-xs text-[#123D2B]">{{ $approved ? 'Approved' : 'Waiting' }}</span>
                    </div>
                    <p class="mt-1 text-sm text-[#66756C]">{{ $project->cataloguePlaceLabel() }}</p>
                    <p class="mt-3 text-sm text-[#66756C]">{{ $approved?->approved_at?->format('j M Y') ?? 'Not approved for construction' }} · {{ $project->materialRequirements->count() }} materials</p>
                    @if ($approved)
                        <a href="{{ route('contractor.final-designs.show', $project) }}" class="mt-4 inline-flex rounded-xl bg-[#123D2B] px-4 py-2 text-sm text-white">View Design</a>
                    @endif
                </div>
            </article>
        @empty
            <p class="text-sm text-[#66756C]">Approved final designs appear here after the homeowner accepts a designer package.</p>
        @endforelse
    </div>
</x-contractor-layout>
