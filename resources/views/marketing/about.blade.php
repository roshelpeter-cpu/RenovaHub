<section id="about" class="relative scroll-mt-24 overflow-hidden">
    <div class="pointer-events-none absolute -left-10 bottom-8 hidden h-36 w-36 text-[#8ea57a]/30 lg:block" aria-hidden="true">
        <x-leaf class="h-36 w-36 -rotate-12" />
    </div>

    <div class="mx-auto grid max-w-[1200px] items-center gap-10 px-5 pb-8 pt-16 lg:grid-cols-[0.95fr_1.05fr_0.9fr] lg:gap-12 lg:px-6 lg:pb-10 lg:pt-20">
        <div class="relative mx-auto h-[340px] w-full max-w-[420px] sm:h-[390px] lg:mx-0 lg:h-[420px]">
            <img src="{{ asset('images/renova/about-interior.jpg') }}" alt="Sunlit living room with timber, plants and a leather sofa" class="absolute left-0 top-0 h-[78%] w-[78%] rounded-[22px] object-cover shadow-[0_18px_40px_-28px_rgba(30,36,32,0.55)]">
            <img src="{{ asset('images/renova/about-exterior.jpg') }}" alt="Modern house with warm timber cladding and a garden" class="absolute bottom-0 right-0 h-[46%] w-[58%] rounded-[18px] border-4 border-[#F6F3EC] object-cover shadow-[0_16px_36px_-24px_rgba(30,36,32,0.55)]">
        </div>

        <div>
            <p class="text-[11px] font-medium uppercase tracking-[0.22em] text-olive">— About RenovaHub</p>
            <h2 class="mt-3 font-serif text-[2.15rem] font-medium leading-[1.08] tracking-[-0.03em] text-forest sm:text-[2.7rem]">
                Everything Your Renovation Needs, In One Place
                <x-leaf class="ml-1 inline h-7 w-7 -translate-y-1 sm:h-8 sm:w-8" />
            </h2>
            <p class="mt-5 max-w-md text-[15px] leading-relaxed text-mist">
                RenovaHub brings homeowners, designers and contractors together in one collaborative workspace. From planning and quotations to documents, tasks and progress tracking, we help you create beautiful, functional and sustainable spaces.
            </p>
            <button type="button" data-story-toggle class="rh-btn mt-7">
                Our Story
                <svg viewBox="0 0 16 16" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
            </button>
            <div data-story class="mt-6 hidden rounded-3xl border border-line bg-white p-5 text-sm leading-relaxed text-mist">
                <p>
                    RenovaHub started from a familiar problem: beautiful renovations were being managed across scattered chats, spreadsheets and site visits. Decisions got lost, quotations lived in inboxes, and nobody shared the same picture of progress.
                </p>
                <p class="mt-3">
                    The studio was built to feel closer to an architectural practice than a generic project tool — calm, precise, and shared by the people actually shaping the space.
                </p>
            </div>
        </div>

        <div class="space-y-3">
            @foreach ([
                ['For Homeowners', 'Plan and manage renovation projects with trusted professionals.', 'home'],
                ['For Designers', 'Collaborate with clients and bring design ideas to life.', 'pen'],
                ['For Contractors', 'Manage projects, tasks and client communication efficiently.', 'build'],
            ] as [$title, $copy, $icon])
                <article class="flex items-start gap-4 rounded-[22px] border border-[#ece7dc] bg-white px-4 py-4 shadow-[0_12px_30px_-24px_rgba(30,36,32,0.45)]">
                    <span class="mt-0.5 inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#e7f0e4] text-forest">
                        @if ($icon === 'home')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z"/></svg>
                        @elseif ($icon === 'pen')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 20h4l11-11-4-4L4 16v4Z"/><path d="m13 7 4 4"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 20h16M6 20V9l6-4 6 4v11"/><path d="M10 20v-5h4v5"/></svg>
                        @endif
                    </span>
                    <span>
                        <span class="block text-[15px] font-semibold text-charcoal">{{ $title }}</span>
                        <span class="mt-1 block text-[13px] leading-relaxed text-mist">{{ $copy }}</span>
                    </span>
                </article>
            @endforeach
        </div>
    </div>
</section>
