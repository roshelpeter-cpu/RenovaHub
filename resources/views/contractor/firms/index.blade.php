<x-contractor-layout title="Construction Firms">
    <div>
        <h1 class="rh-serif text-3xl text-[#123D2B]">Construction Firms</h1>
        <p class="mt-2 text-sm text-[#66756C]">Request project quotations, compare duration and price, then send the options to the homeowner.</p>
    </div>
    <form method="GET" class="mt-5 grid gap-3 lg:grid-cols-[1.4fr_12rem_12rem]">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Search firms, specialisations or cities" aria-label="Search construction firms" class="rounded-2xl border border-[#ddd6c8] bg-white px-4 py-3 text-sm">
        <select name="city" aria-label="Location" class="rounded-2xl border border-[#ddd6c8] bg-white px-3 py-3 text-sm">
            <option value="">Location</option>
            @foreach ($cities as $city)
                <option value="{{ $city }}" @selected(request('city') === $city)>{{ $city }}</option>
            @endforeach
        </select>
        <select name="sort" aria-label="Sort" class="rounded-2xl border border-[#ddd6c8] bg-white px-3 py-3 text-sm">
            <option value="orders">Most projects</option>
            <option value="rating" @selected(request('sort') === 'rating')>Highest rated</option>
        </select>
    </form>
    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @forelse ($firms as $firm)
            <article class="overflow-hidden rounded-2xl border border-[#ece7dc] bg-white shadow-sm">
                <div class="grid grid-cols-3 gap-1 p-3">
                    @foreach (array_slice($firm->portfolio ?? [], 0, 3) as $image)
                        <img src="{{ asset($image) }}" alt="" class="h-16 w-full rounded-lg object-cover">
                    @endforeach
                </div>
                <div class="px-4 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-full bg-[#123D2B] text-sm text-white">{{ $firm->initials() }}</span>
                        <div>
                            <h2 class="text-base font-semibold text-[#123D2B]">{{ $firm->name }}</h2>
                            <p class="text-xs text-[#66756C]">{{ $firm->rating }} · {{ $firm->review_count }} reviews</p>
                        </div>
                    </div>
                    <p class="mt-3 text-sm text-[#66756C]">{{ $firm->location }}</p>
                    <p class="mt-1 text-sm text-[#123D2B]">{{ $firm->specialisation }}</p>
                    <p class="mt-2 text-sm">From {{ $firm->money() }}</p>
                    <p class="mt-1 text-xs text-[#66756C]">{{ $firm->years_experience }}+ years · {{ $firm->completed_projects }}+ projects</p>
                    <a href="{{ route('contractor.firms.show', $firm) }}" class="mt-4 inline-flex rounded-xl bg-[#123D2B] px-4 py-2 text-sm text-white">View Firm</a>
                </div>
            </article>
        @empty
            <p class="text-sm text-[#66756C]">No construction firms match that search.</p>
        @endforelse
    </div>
</x-contractor-layout>
