<x-homeowner-layout :title="$caseStudy->title" :flush="true">
    <div class="mx-auto max-w-[90rem] px-4 py-6 sm:px-6 lg:px-8">
        <p class="text-sm text-[#66756C]">
            <a href="{{ route('homeowner.home') }}" class="hover:text-[#123D2B]">Home</a>
            <span class="mx-1 text-[#c9c2b4]">›</span>
            <a href="{{ route('homeowner.explore') }}" class="hover:text-[#123D2B]">Professionals</a>
            <span class="mx-1 text-[#c9c2b4]">›</span>
            <a href="{{ route('homeowner.professionals.show', $professional) }}" class="hover:text-[#123D2B]">{{ $profile->displayName() }}</a>
            <span class="mx-1 text-[#c9c2b4]">›</span>
            {{ $caseStudy->title }}
        </p>

        <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.55fr)_19rem]">
            <div>
                <div class="grid gap-3 lg:grid-cols-[minmax(0,1.7fr)_11rem]">
                    <div class="relative overflow-hidden rounded-[1.4rem]">
                        <img src="{{ $caseStudy->heroUrl() }}" alt="" class="h-[22rem] w-full object-cover">
                        <span class="absolute bottom-3 left-3 rounded-full bg-black/45 px-2.5 py-1 text-xs text-white">1 / {{ max(1, $gallery->count()) }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 lg:grid-cols-1">
                        @foreach ($gallery->skip(1)->take(3) as $path)
                            <img src="{{ asset($path) }}" alt="" class="h-[6.9rem] w-full rounded-2xl object-cover">
                        @endforeach
                        @if ($gallery->count() > 4)
                            <div class="relative">
                                <img src="{{ asset($gallery[4]) }}" alt="" class="h-[6.9rem] w-full rounded-2xl object-cover">
                                <span class="absolute inset-0 flex items-center justify-center rounded-2xl bg-black/45 text-sm font-medium text-white">+{{ $gallery->count() - 4 }} More Photos</span>
                            </div>
                        @endif
                    </div>
                </div>

                <h1 class="mt-6 font-serif text-4xl text-[#123D2B]">{{ $caseStudy->title }}</h1>
                <p class="mt-2 text-sm text-[#66756C]">{{ $caseStudy->location }} · {{ $caseStudy->property_type }} · Completed — {{ $caseStudy->completed_on?->format('M Y') }}</p>
                <p class="mt-4 max-w-4xl text-sm leading-relaxed text-[#66756C]">{{ $caseStudy->summary }}</p>

                <dl class="mt-6 grid gap-4 sm:grid-cols-3">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#E7F0E4] text-[#123D2B]">⌂</span>
                        <div><dt class="text-xs text-[#66756C]">Project Type</dt><dd class="text-sm font-medium text-[#123D2B]">{{ $caseStudy->project_type }}</dd></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#E7F0E4] text-[#123D2B]">▣</span>
                        <div><dt class="text-xs text-[#66756C]">Project Size</dt><dd class="text-sm font-medium text-[#123D2B]">{{ number_format((int) $caseStudy->size_sq_ft) }} sq. ft</dd></div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#E7F0E4] text-[#123D2B]">$</span>
                        <div><dt class="text-xs text-[#66756C]">Project Budget</dt><dd class="text-sm font-medium text-[#123D2B]">LKR {{ number_format((float) $caseStudy->budget, 0) }}</dd></div>
                    </div>
                </dl>

                <div class="mt-8 flex flex-wrap gap-5 border-b border-[#ece7dc] text-sm">
                    @foreach (['Overview', 'Design Process', 'Before & After', 'Client Feedback', 'Materials Used'] as $label)
                        <span class="pb-3 {{ $label === 'Overview' ? 'border-b-2 border-[#123D2B] font-medium text-[#123D2B]' : 'text-[#66756C]' }}">{{ $label }}</span>
                    @endforeach
                </div>

                <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_16rem]">
                    <section>
                        <h2 class="font-serif text-2xl text-[#123D2B]">Project Overview</h2>
                        <p class="mt-3 text-sm leading-relaxed text-[#66756C]">{{ $caseStudy->overview }}</p>
                    </section>
                    <aside class="rounded-[1.3rem] bg-[#F3F7F1] p-5">
                        <h3 class="font-medium text-[#123D2B]">Key Highlights</h3>
                        <ul class="mt-3 space-y-2 text-sm text-[#18352A]">
                            @foreach ($caseStudy->highlights ?? [] as $highlight)
                                <li class="flex gap-2"><span class="mt-0.5 text-[#2F6B49]">●</span>{{ $highlight }}</li>
                            @endforeach
                        </ul>
                    </aside>
                </div>

                <section class="mt-8">
                    <h2 class="font-serif text-2xl text-[#123D2B]">Design Process</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        @foreach ($caseStudy->process ?? [] as $index => $step)
                            <article>
                                <img src="{{ asset($step['image'] ?? $caseStudy->hero_image) }}" alt="" class="h-32 w-full rounded-2xl object-cover">
                                <h3 class="mt-3 text-sm font-medium text-[#123D2B]">{{ $index + 1 }}. {{ $step['title'] ?? 'Stage' }}</h3>
                                <p class="mt-1 text-xs leading-relaxed text-[#66756C]">{{ $step['body'] ?? '' }}</p>
                            </article>
                        @endforeach
                    </div>
                </section>
            </div>

            <aside class="space-y-5">
                <section class="rounded-[1.4rem] border border-[#ece7dc] p-5">
                    <div class="flex gap-3">
                        <img src="{{ $profile->avatarUrl() }}" alt="" class="h-16 w-16 rounded-full object-cover">
                        <div>
                            <h2 class="font-serif text-2xl leading-tight text-[#123D2B]">{{ $profile->displayName() }}</h2>
                            <p class="text-sm text-[#66756C]">{{ $profile->title }}</p>
                            <p class="mt-1 text-sm">★ {{ number_format((float) $profile->rating, 1) }} <span class="text-[#66756C]">({{ $profile->review_count }} reviews)</span>
                                @if ($profile->verified)<span class="ml-1 rounded-full bg-[#E7F0E4] px-2 py-0.5 text-[11px] font-medium text-[#123D2B]">Verified</span>@endif
                            </p>
                            <p class="mt-1 text-sm text-[#66756C]">{{ $profile->location }}, Sri Lanka</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm leading-relaxed text-[#66756C]">{{ $profile->bio }}</p>
                    <form method="POST" action="{{ route('homeowner.professionals.contact', $professional) }}" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full rounded-full bg-[#123D2B] px-4 py-2.5 text-sm font-medium text-white">Contact {{ $professional->isDesigner() ? 'Designer' : 'Contractor' }}</button>
                    </form>
                </section>

                <section class="rounded-[1.4rem] border border-[#ece7dc] p-5">
                    <div class="flex items-center justify-between">
                        <h2 class="font-serif text-xl text-[#123D2B]">More Projects by {{ str($profile->displayName())->before(' ') }}</h2>
                        <a href="{{ route('homeowner.professionals.show', $professional) }}" class="text-sm font-medium text-[#123D2B]">View All →</a>
                    </div>
                    <ul class="mt-4 space-y-3">
                        @foreach ($more as $item)
                            <li>
                                <a href="{{ route('homeowner.professionals.project', [$professional, $item->slug]) }}" class="flex items-center gap-3">
                                    <img src="{{ $item->heroUrl() }}" alt="" class="h-14 w-16 rounded-xl object-cover">
                                    <span>
                                        <span class="block text-sm font-medium text-[#123D2B]">{{ $item->title }}</span>
                                        <span class="block text-xs text-[#66756C]">{{ $item->category }} · {{ $item->location }}</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>

                @if ($testimonial)
                    <section class="rounded-[1.4rem] border border-[#ece7dc] p-5">
                        <h2 class="font-serif text-xl text-[#123D2B]">Client Testimonial</h2>
                        <p class="mt-3 text-sm leading-relaxed text-[#66756C]">“{{ $testimonial->body }}”</p>
                        <div class="mt-4 flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-[#E7F0E4] text-sm font-medium text-[#123D2B]">{{ strtoupper(substr($testimonial->author_name, 0, 1)) }}</span>
                            <div>
                                <p class="text-sm font-medium text-[#123D2B]">{{ $testimonial->author_name }}</p>
                                <p class="text-xs text-[#66756C]">{{ $testimonial->project_title }}</p>
                                <p class="text-xs text-[#123D2B]">★ {{ number_format((float) $testimonial->rating, 1) }}</p>
                            </div>
                        </div>
                    </section>
                @endif
            </aside>
        </div>
    </div>
</x-homeowner-layout>
