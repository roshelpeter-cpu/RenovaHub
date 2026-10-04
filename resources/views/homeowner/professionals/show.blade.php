<x-homeowner-layout :title="$profile->displayName()">
    <a href="{{ url()->previous() }}" class="text-sm font-medium text-forest hover:underline">Back</a>
    <div class="mt-4 flex flex-col gap-5 rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm sm:p-6 lg:flex-row lg:items-start lg:justify-between">
        <div class="flex gap-4">
            @if ($profile->avatarUrl())
                <img src="{{ $profile->avatarUrl() }}" alt="" class="h-20 w-20 rounded-full object-cover">
            @else
                <span class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-[#e7f0e4] font-serif text-3xl text-forest">{{ strtoupper(substr($professional->name, 0, 1)) }}</span>
            @endif
            <div>
                <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-olive">{{ $professional->isDesigner() ? 'Designer' : 'Contractor' }}</p>
                <h1 class="mt-1 font-serif text-3xl text-forest sm:text-4xl">{{ $profile->displayName() }}</h1>
                <p class="text-sm text-mist">{{ $profile->title }}@if($profile->location) · {{ $profile->location }}@endif</p>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-charcoal">{{ $profile->bio }}</p>
            </div>
        </div>
        <dl class="grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-2xl bg-[#F6F1E7] px-4 py-3"><dt class="text-mist">Experience</dt><dd class="font-medium">{{ $profile->years_experience }} years</dd></div>
            <div class="rounded-2xl bg-[#F6F1E7] px-4 py-3"><dt class="text-mist">Rating</dt><dd class="font-medium">{{ $profile->rating ?? 'Not rated' }}</dd></div>
            <div class="rounded-2xl bg-[#F6F1E7] px-4 py-3"><dt class="text-mist">Completed</dt><dd class="font-medium">{{ $profile->completed_projects_count }}</dd></div>
            <div class="rounded-2xl bg-[#F6F1E7] px-4 py-3"><dt class="text-mist">From</dt><dd class="font-medium">{{ $profile->starting_price ? 'LKR '.number_format((float) $profile->starting_price, 0) : 'On request' }}</dd></div>
        </dl>
    </div>

    @if ($project)
        <form method="POST" action="{{ route('homeowner.projects.team.update', $project) }}" class="mt-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="designer_id" value="{{ $professional->isDesigner() ? $professional->id : $project->designer_id }}">
            <input type="hidden" name="contractor_id" value="{{ $professional->isContractor() ? $professional->id : $project->contractor_id }}">
            <button type="submit" class="rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory transition hover:bg-leaf">Select {{ $professional->isDesigner() ? 'Designer' : 'Contractor' }}</button>
        </form>
    @endif

    <div class="mt-8 flex flex-wrap gap-2">
        <a href="{{ route('homeowner.professionals.show', array_filter(['professional' => $professional, 'project' => $project?->id])) }}" class="rounded-full px-4 py-2 text-sm {{ $category === '' ? 'bg-forest text-ivory' : 'bg-white text-charcoal' }}">All</a>
        @foreach (['Living Room', 'Kitchen', 'Bedroom', 'Bathroom', 'Outdoor', 'Full House', 'Villa', 'Extension'] as $group)
            <a href="{{ route('homeowner.professionals.show', array_filter(['professional' => $professional, 'project' => $project?->id, 'category' => $group])) }}" class="rounded-full px-4 py-2 text-sm {{ $category === $group ? 'bg-forest text-ivory' : 'bg-white text-charcoal' }}">{{ $group }}</a>
        @endforeach
    </div>

    <h2 class="mt-8 font-serif text-2xl text-forest">Portfolio</h2>
    <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($profile->portfolioItems->when($category !== '', fn ($items) => $items->where('category', $category)) as $item)
            <article class="overflow-hidden rounded-3xl border border-[#ece7dc] bg-white shadow-sm">
                <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" class="h-48 w-full object-cover">
                <div class="p-4">
                    <p class="text-xs uppercase tracking-[0.14em] text-olive">{{ $item->category }}@if($item->completion_year) · {{ $item->completion_year }}@endif @if($item->location) · {{ $item->location }}@endif</p>
                    <h3 class="mt-1 font-serif text-xl text-forest">{{ $item->title }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-mist">{{ $item->description }}</p>
                    @if ($item->budget_min)
                        <p class="mt-2 text-sm text-charcoal">LKR {{ number_format((float) $item->budget_min, 0) }}@if($item->budget_max) – {{ number_format((float) $item->budget_max, 0) }}@endif</p>
                    @endif
                </div>
            </article>
        @endforeach
    </div>

    <h2 class="mt-10 font-serif text-2xl text-forest">Reviews</h2>
    @forelse ($profile->reviews as $review)
        <article class="mt-3 rounded-2xl border border-[#ece7dc] bg-white p-4">
            <p class="text-sm font-medium text-charcoal">{{ $review->author_name }} · {{ $review->rating }}</p>
            <p class="text-xs text-mist">{{ $review->project_title }}</p>
            <p class="mt-2 text-sm leading-relaxed text-charcoal">{{ $review->body }}</p>
        </article>
    @empty
        <p class="mt-3 text-sm text-mist">No reviews have been recorded yet.</p>
    @endforelse
</x-homeowner-layout>
