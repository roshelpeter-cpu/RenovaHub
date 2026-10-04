<x-homeowner-layout :title="$profile->displayName()" :flush="true">
    <div class="mx-auto max-w-[90rem] px-4 py-6 sm:px-6 lg:px-8">
        <p class="text-sm text-[#66756C]">
            <a href="{{ route('homeowner.home') }}" class="hover:text-[#123D2B]">Home</a>
            <span class="mx-1 text-[#c9c2b4]">›</span>
            <a href="{{ route('homeowner.explore') }}" class="hover:text-[#123D2B]">Professionals</a>
            <span class="mx-1 text-[#c9c2b4]">›</span>
            {{ $profile->displayName() }}
        </p>

        <div class="mt-6 grid gap-8 lg:grid-cols-[13.5rem_minmax(0,1fr)_18rem]">
            <img src="{{ $profile->avatarUrl() }}" alt="" class="mx-auto h-52 w-52 rounded-[1.4rem] object-cover lg:mx-0">

            <div>
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h1 class="font-serif text-4xl text-[#123D2B]">{{ $profile->displayName() }}</h1>
                        <p class="mt-1 text-sm text-[#66756C]">{{ $profile->title }}</p>
                        <p class="mt-2 text-sm text-[#123D2B]">★ {{ number_format((float) $profile->rating, 1) }} <span class="text-[#66756C]">({{ $profile->review_count }} reviews)</span>
                            @if ($profile->verified)
                                <span class="ml-2 rounded-full bg-[#E7F0E4] px-2 py-0.5 text-[11px] font-medium">Verified</span>
                            @endif
                        </p>
                        <p class="mt-2 text-sm text-[#66756C]">{{ $profile->location }}{{ str_contains($profile->location ?? '', 'Sri Lanka') ? '' : ', Sri Lanka' }}</p>
                    </div>
                </div>
                <p class="mt-4 max-w-2xl text-sm leading-relaxed text-[#66756C]">{{ $profile->bio }}</p>
            </div>

            <div class="space-y-3">
                <form method="POST" action="{{ route('homeowner.professionals.favourite', $professional) }}" class="flex justify-end">
                    @csrf
                    <button type="submit" class="flex h-10 w-10 items-center justify-center rounded-full border border-[#ece7dc] text-[#123D2B]" aria-label="Favourite">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="{{ $favourite ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.7"><path d="M12 20s-7-4.4-9.2-8.2C1 8.8 2.8 5 6.5 5 8.6 5 10 6.2 12 8.2 14 6.2 15.4 5 17.5 5 21.2 5 23 8.8 21.2 11.8 19 15.6 12 20 12 20Z"/></svg>
                    </button>
                </form>
                <form method="POST" action="{{ route('homeowner.professionals.contact', $professional) }}">
                    @csrf
                    <button type="submit" class="w-full rounded-full bg-[#123D2B] px-4 py-2.5 text-sm font-medium text-white">Contact {{ $professional->isDesigner() ? 'Designer' : 'Contractor' }}</button>
                </form>
                <dl class="grid grid-cols-3 gap-2 pt-2 text-center text-sm">
                    <div><dt class="font-serif text-lg text-[#123D2B]">{{ $profile->years_experience }}+</dt><dd class="text-[11px] text-[#66756C]">Years of Experience</dd></div>
                    <div><dt class="font-serif text-lg text-[#123D2B]">{{ $profile->completed_projects_count }}+</dt><dd class="text-[11px] text-[#66756C]">Projects Completed</dd></div>
                    <div><dt class="font-serif text-lg text-[#123D2B]">{{ $profile->client_satisfaction ?? 95 }}%</dt><dd class="text-[11px] text-[#66756C]">Client Satisfaction</dd></div>
                </dl>
                <p class="text-center text-sm font-medium text-[#123D2B]">LKR {{ number_format((float) ($profile->starting_price ?? 0), 0) }}+ <span class="block text-xs font-normal text-[#66756C]">Starting Project Budget</span></p>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap gap-6 border-b border-[#ece7dc] text-sm">
            @foreach (['overview' => 'Overview', 'portfolio' => 'Portfolio', 'reviews' => 'Reviews', 'services' => 'Services', 'availability' => 'Availability', 'faqs' => 'FAQs'] as $key => $label)
                <a href="{{ route('homeowner.professionals.show', [$professional, 'tab' => $key]) }}" class="pb-3 {{ $tab === $key ? 'border-b-2 border-[#123D2B] font-medium text-[#123D2B]' : 'text-[#66756C]' }}">{{ $label }}</a>
            @endforeach
        </div>

        @if ($tab === 'overview' || $tab === 'services')
            <div class="mt-8 grid gap-8 lg:grid-cols-2">
                <section>
                    <h2 class="font-serif text-2xl text-[#123D2B]">About Me</h2>
                    <p class="mt-3 text-sm leading-relaxed text-[#66756C]">{{ $profile->bio }} I believe that every space has a story, and my goal is to bring that story to life through thoughtful design, practical solutions and attention to detail.</p>
                </section>
                <section>
                    <h2 class="font-serif text-2xl text-[#123D2B]">Areas of Expertise</h2>
                    <ul class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($profile->services as $service)
                            <li class="flex items-center gap-3 rounded-2xl border border-[#ece7dc] px-4 py-3 text-sm text-[#123D2B]">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#E7F0E4]">+</span>
                                {{ $service->name }}
                            </li>
                        @endforeach
                    </ul>
                </section>
            </div>
        @endif

        <section class="mt-10">
            <div class="flex items-end justify-between">
                <h2 class="font-serif text-2xl text-[#123D2B]">Project Portfolio</h2>
                <a href="{{ route('homeowner.professionals.show', [$professional, 'tab' => 'portfolio']) }}" class="text-sm font-medium text-[#123D2B]">View All Projects →</a>
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach (['All', 'Residential', 'Commercial', 'Modern', 'Minimal', 'Scandinavian', 'Luxury'] as $filter)
                    <span class="rounded-full {{ $filter === 'All' ? 'bg-[#123D2B] text-white' : 'bg-[#F6F1E7] text-[#123D2B]' }} px-3 py-1 text-xs">{{ $filter }}</span>
                @endforeach
            </div>
            <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($caseStudies as $item)
                    <a href="{{ route('homeowner.professionals.project', [$professional, $item->slug]) }}" class="overflow-hidden rounded-[1.3rem] border border-[#ece7dc] bg-white">
                        <img src="{{ $item->heroUrl() }}" alt="" class="h-44 w-full object-cover">
                        <div class="p-4">
                            <h3 class="font-medium text-[#123D2B]">{{ $item->title }}</h3>
                            <p class="mt-1 text-xs text-[#66756C]">{{ $item->category }} · {{ $item->location }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>

        @if ($tab === 'reviews')
            <section class="mt-10">
                <h2 class="font-serif text-2xl text-[#123D2B]">Reviews</h2>
                @forelse ($profile->reviews as $review)
                    <article class="mt-3 rounded-2xl border border-[#ece7dc] p-4">
                        <p class="text-sm font-medium text-[#123D2B]">{{ $review->author_name }} · {{ $review->rating }}</p>
                        <p class="text-xs text-[#66756C]">{{ $review->project_title }}</p>
                        <p class="mt-2 text-sm text-[#66756C]">{{ $review->body }}</p>
                    </article>
                @empty
                    <p class="mt-3 text-sm text-[#66756C]">No reviews yet.</p>
                @endforelse
            </section>
        @endif
    </div>
</x-homeowner-layout>
