<div>
    <section class="relative overflow-hidden border-b border-[#ece7dc]">
        <div class="pointer-events-none absolute inset-y-0 right-0 hidden w-[48%] md:block" aria-hidden="true">
            <img src="{{ asset('images/renova/feature-collab.jpg') }}" alt="" class="h-full w-full object-cover">
            <div class="absolute inset-0" style="background: linear-gradient(90deg, #ffffff 0%, rgba(255,255,255,0.92) 18%, rgba(255,255,255,0.4) 55%, rgba(255,255,255,0.05) 100%);"></div>
        </div>
        <div class="relative mx-auto max-w-[88rem] px-4 pb-8 pt-8 sm:px-6 lg:px-8">
            <p class="text-sm text-[#66756C]">Home <span class="mx-1 text-[#c9c2b4]">›</span> Professionals</p>
            <h1 class="mt-3 font-serif text-4xl font-medium tracking-[-0.03em] text-[#123D2B] sm:text-5xl">Find Trusted Professionals</h1>
            <p class="mt-3 max-w-xl text-sm leading-relaxed text-[#66756C] sm:text-base">Work with experienced designers and contractors to bring your renovation vision to life.</p>
        </div>
    </section>

    <div class="mx-auto max-w-[88rem] px-4 py-6 sm:px-6 lg:px-8">
        <div class="inline-flex rounded-full bg-[#F6F1E7] p-1">
            <button type="button" wire:click="$set('type', 'designer')" class="rounded-full px-5 py-2 text-sm {{ $type === 'designer' ? 'bg-[#123D2B] text-white' : 'text-[#18352A]' }}">Designers</button>
            <button type="button" wire:click="$set('type', 'contractor')" class="rounded-full px-5 py-2 text-sm {{ $type === 'contractor' ? 'bg-[#123D2B] text-white' : 'text-[#18352A]' }}">Contractors</button>
        </div>

        <form wire:submit="applyFilters" class="mt-5 grid gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-3 lg:grid-cols-[minmax(0,1.4fr)_repeat(3,minmax(8rem,0.8fr))_auto] lg:items-end">
            <label class="relative min-w-0">
                <span class="mb-1 block text-xs font-medium text-[#66756C]">Search</span>
                <span class="pointer-events-none absolute bottom-2.5 left-3 text-[#66756C]">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="6.5"/><path d="m20 20-3.2-3.2"/></svg>
                </span>
                <input wire:model="search" type="search" placeholder="{{ $type === 'contractor' ? 'Search contractors...' : 'Search designers...' }}" class="w-full rounded-full border border-[#ece7dc] bg-[#F7F4EE] py-2.5 pl-9 pr-4 text-sm outline-none focus:border-forest">
            </label>
            <label>
                <span class="mb-1 block text-xs font-medium text-[#66756C]">Location</span>
                <select wire:model="location" class="w-full rounded-full border border-[#ece7dc] bg-[#F7F4EE] px-4 py-2.5 text-sm">
                    <option value="">All Locations</option>
                    @foreach ($locations as $place)
                        <option value="{{ $place }}">{{ $place }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="mb-1 block text-xs font-medium text-[#66756C]">Project Type</span>
                <select wire:model="projectType" class="w-full rounded-full border border-[#ece7dc] bg-[#F7F4EE] px-4 py-2.5 text-sm">
                    <option value="">All Types</option>
                    @foreach (['Residential', 'Commercial', 'Modern', 'Luxury', 'Kitchen', 'Minimal'] as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span class="mb-1 block text-xs font-medium text-[#66756C]">Price Range</span>
                <select wire:model="priceRange" class="w-full rounded-full border border-[#ece7dc] bg-[#F7F4EE] px-4 py-2.5 text-sm">
                    <option value="">Any Range</option>
                    <option value="under_200k">Under LKR 200,000</option>
                    <option value="200_500k">LKR 200,000 – 500,000</option>
                    <option value="over_500k">Over LKR 500,000</option>
                </select>
            </label>
            <button type="submit" class="inline-flex items-center justify-center rounded-full bg-[#123D2B] px-5 py-2.5 text-sm font-medium text-white">Filter</button>
        </form>

        <div class="mt-8 flex items-end justify-between">
            <h2 class="font-serif text-2xl text-[#123D2B]">Featured {{ $type === 'contractor' ? 'Contractors' : 'Designers' }}</h2>
            <a href="#all-professionals" class="text-sm font-medium text-[#123D2B] hover:underline">View All →</a>
        </div>

        <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @forelse ($featured as $profile)
                <article class="overflow-hidden rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
                    <div class="relative h-40">
                        <img src="{{ $profile->coverUrl() }}" alt="" class="h-full w-full object-cover">
                        <button type="button" wire:click="toggleFavourite({{ $profile->id }})" class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 text-[#123D2B]" aria-label="Favourite">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="{{ $favouriteIds->contains($profile->id) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.7"><path d="M12 20s-7-4.4-9.2-8.2C1 8.8 2.8 5 6.5 5 8.6 5 10 6.2 12 8.2 14 6.2 15.4 5 17.5 5 21.2 5 23 8.8 21.2 11.8 19 15.6 12 20 12 20Z"/></svg>
                        </button>
                    </div>
                    <div class="p-4">
                        <h3 class="font-medium text-[#123D2B]">{{ $profile->displayName() }}</h3>
                        <p class="mt-1 text-sm text-[#66756C]">★ {{ number_format((float) $profile->rating, 1) }} ({{ $profile->review_count }} reviews)</p>
                        <p class="text-sm text-[#66756C]">{{ $profile->location }}</p>
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach ($profile->tagList() as $tag)
                                <span class="rounded-full bg-[#E7F0E4] px-2.5 py-1 text-[11px] text-[#123D2B]">{{ $tag }}</span>
                            @endforeach
                        </div>
                        <a href="{{ route('homeowner.professionals.show', $profile->user) }}" class="mt-4 inline-flex rounded-full border border-[#123D2B] px-4 py-2 text-sm font-medium text-[#123D2B]">View Portfolio →</a>
                    </div>
                </article>
            @empty
                <p class="col-span-4 text-sm text-[#66756C]">No featured professionals match these filters.</p>
            @endforelse
        </div>

        <div id="all-professionals" class="mt-10 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <h2 class="font-serif text-2xl text-[#123D2B]">All {{ $type === 'contractor' ? 'Contractors' : 'Designers' }}</h2>
            <label class="text-sm text-[#66756C]">Sort by
                <select wire:model.live="sort" class="ml-2 rounded-full border border-[#ece7dc] bg-[#F7F4EE] px-3 py-1.5 text-sm text-[#123D2B]">
                    <option value="relevant">Most Relevant</option>
                    <option value="rating">Highest Rated</option>
                    <option value="reviews">Most Reviews</option>
                    <option value="name">Name</option>
                </select>
            </label>
        </div>

        <div class="mt-5 space-y-4">
            @forelse ($all as $profile)
                <article class="flex flex-col gap-4 rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 sm:flex-row sm:items-center">
                    <img src="{{ $profile->avatarUrl() }}" alt="" class="h-16 w-16 rounded-full object-cover">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-medium text-[#123D2B]">{{ $profile->displayName() }}</h3>
                            <span class="text-sm text-[#66756C]">★ {{ number_format((float) $profile->rating, 1) }} ({{ $profile->review_count }} reviews)</span>
                            @if ($profile->verified)
                                <span class="rounded-full bg-[#E7F0E4] px-2 py-0.5 text-[11px] font-medium text-[#123D2B]">Verified</span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-[#66756C]">{{ $profile->location }}</p>
                        <p class="mt-1 text-sm text-[#66756C]">{{ $profile->bio }}</p>
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @foreach ($profile->tagList() as $tag)
                                <span class="rounded-full bg-[#E7F0E4] px-2.5 py-1 text-[11px] text-[#123D2B]">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex shrink-0 gap-2">
                        @foreach ($profile->portfolioItems->take(3) as $item)
                            <img src="{{ $item->imageUrl() }}" alt="" class="h-16 w-20 rounded-xl object-cover">
                        @endforeach
                    </div>
                    <div class="flex shrink-0 flex-col gap-2">
                        <a href="{{ route('homeowner.professionals.show', $profile->user) }}" class="inline-flex items-center justify-center rounded-full bg-[#123D2B] px-4 py-2 text-sm font-medium text-white">View Portfolio →</a>
                        <a href="{{ route('homeowner.professionals.show', $profile->user) }}" class="inline-flex items-center justify-center rounded-full border border-[#123D2B] px-4 py-2 text-sm font-medium text-[#123D2B]">Contact</a>
                    </div>
                </article>
            @empty
                <p class="text-sm text-[#66756C]">No professionals match these filters.</p>
            @endforelse
        </div>
    </div>
</div>
