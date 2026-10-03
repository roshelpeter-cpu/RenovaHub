<section id="about" class="scroll-mt-24">
    <div class="mx-auto grid max-w-[1240px] items-center gap-10 px-5 py-16 lg:grid-cols-[1.05fr_1fr_0.95fr] lg:gap-12 lg:px-8 lg:py-24">
        <div class="grid grid-cols-2 gap-3">
            <img src="{{ asset('images/renova/about-interior.jpg') }}" alt="Warm living room with timber, plaster and natural light" class="h-56 w-full rounded-[22px] object-cover sm:h-72 lg:h-[340px]">
            <img src="{{ asset('images/renova/about-exterior.jpg') }}" alt="Modern residential facade with large windows and greenery" class="mt-8 h-56 w-full rounded-[22px] object-cover sm:h-72 lg:h-[340px]">
        </div>

        <div>
            <x-section-heading eyebrow="About RenovaHub">
                Everything Your Renovation Needs, In One Place
            </x-section-heading>
            <p class="mt-5 text-[15px] leading-relaxed text-mist">
                RenovaHub brings homeowners, designers and contractors together in one collaborative workspace. Plan the project, assign tasks, compare quotations, keep documents close, and follow progress without losing the thread.
            </p>
            <button type="button" data-story-toggle class="rh-btn mt-7">
                Our Story
                <svg viewBox="0 0 16 16" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
            </button>
            <div data-story class="mt-6 hidden rounded-3xl border border-line bg-ivory p-5 text-sm leading-relaxed text-mist">
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
                <article class="flex items-start gap-4 rounded-3xl border border-line bg-ivory px-4 py-4 transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_16px_30px_-24px_rgba(30,36,32,0.6)]">
                    <span class="mt-0.5 inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-line bg-cream text-forest">
                        @if ($icon === 'home')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z"/></svg>
                        @elseif ($icon === 'pen')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 20h4l11-11-4-4L4 16v4Z"/><path d="m13 7 4 4"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 20h16M6 20V9l6-4 6 4v11"/><path d="M10 20v-5h4v5"/></svg>
                        @endif
                    </span>
                    <span>
                        <span class="block text-[11px] font-medium uppercase tracking-[0.16em] text-olive">{{ $title }}</span>
                        <span class="mt-1 block text-sm leading-relaxed text-mist">{{ $copy }}</span>
                    </span>
                </article>
            @endforeach
        </div>
    </div>
</section>
