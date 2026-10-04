<x-homeowner-layout :title="$profile->displayName()">
    <a href="{{ url()->previous() }}" class="text-sm font-medium text-forest hover:underline">Back</a>
    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-olive">{{ $professional->isDesigner() ? 'Designer' : 'Contractor' }}</p>
            <h1 class="mt-2 font-serif text-3xl font-medium tracking-[-0.03em] text-forest sm:text-4xl">{{ $profile->displayName() }}</h1>
            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-mist">{{ $profile->bio }}</p>
        </div>
        <dl class="grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-2xl bg-white px-4 py-3 shadow-sm">
                <dt class="text-mist">Location</dt>
                <dd class="font-medium text-charcoal">{{ $profile->location ?: 'Not listed' }}</dd>
            </div>
            <div class="rounded-2xl bg-white px-4 py-3 shadow-sm">
                <dt class="text-mist">Experience</dt>
                <dd class="font-medium text-charcoal">{{ $profile->years_experience !== null ? $profile->years_experience.' years' : 'Not listed' }}</dd>
            </div>
        </dl>
    </div>

    @if ($profile->rating !== null)
        <p class="mt-4 text-sm text-charcoal">Rating {{ $profile->rating }}</p>
    @endif

    <h2 class="mt-8 font-serif text-2xl text-forest">Portfolio</h2>
    @if ($profile->portfolioItems->isEmpty())
        <p class="mt-3 text-sm text-mist">No completed work has been added yet.</p>
    @else
        <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($profile->portfolioItems as $item)
                <article class="overflow-hidden rounded-3xl border border-[#ece7dc] bg-white shadow-sm">
                    <img src="{{ asset($item->image) }}" alt="{{ $item->title }}" class="h-44 w-full object-cover">
                    <div class="p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-olive">{{ $item->category }}@if($item->completion_year) · {{ $item->completion_year }}@endif</p>
                        <h3 class="mt-1 font-serif text-xl text-forest">{{ $item->title }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-mist">{{ $item->description }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
</x-homeowner-layout>
