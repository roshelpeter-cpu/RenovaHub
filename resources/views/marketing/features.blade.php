<section id="features" class="scroll-mt-24">
    <div class="mx-auto max-w-[1240px] px-5 py-8 lg:px-8 lg:py-12">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <x-section-heading eyebrow="Features">
                Powerful Tools for Every Step
            </x-section-heading>
            <a href="#all-features" class="shrink-0 text-sm font-medium text-forest transition hover:text-leaf">Explore All Features →</a>
        </div>
        <p class="mt-4 max-w-xl text-[15px] leading-relaxed text-mist">
            RenovaHub connects the important parts of a renovation workflow, from the first brief to the finished space.
        </p>

        @php
            $features = [
                ['Project Management', 'Create and organise projects with clear timelines.', 'feature-plans.jpg', 'Architectural drawings spread across a planning table'],
                ['Team Collaboration', 'Work with homeowners, designers and contractors in one place.', 'feature-collab.jpg', 'Contemporary interior prepared for a design review'],
                ['Task Management', 'Break work into complete tasks with ease.', 'feature-tasks.jpg', 'Refined living space with natural materials'],
                ['Quotations & Approvals', 'Request, compare and approve quotations.', 'feature-quotes.jpg', 'Interior materials arranged for a design decision'],
                ['Document Management', 'Keep plans and documents organised and accessible.', 'feature-docs.jpg', 'Bright architectural interior with layered finishes'],
                ['Progress Tracking', 'Monitor real-time progress with updates and reports.', 'feature-progress.jpg', 'Modern home exterior during a considered renovation'],
                ['Change Requests', 'Capture scope changes and keep every decision visible.', 'feature-changes.jpg', 'Detail of a finished interior with warm materials'],
                ['Notifications', 'Stay informed with calm, timely project updates.', 'feature-notes.jpg', 'Quiet bedroom interior in warm neutral tones'],
                ['Sustainable Choices', 'Choose materials and methods with a lighter footprint.', 'feature-green.jpg', 'Lush planting beside a renovated garden edge'],
            ];
        @endphp

        <div id="all-features" class="mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($features as [$title, $copy, $image, $alt])
                <article class="group flex h-full flex-col rounded-[26px] border border-line bg-ivory p-3 shadow-[0_16px_40px_-32px_rgba(30,36,32,0.55)] transition duration-300 hover:-translate-y-1 hover:shadow-[0_22px_44px_-28px_rgba(23,63,42,0.45)]">
                    <div class="overflow-hidden rounded-[18px]">
                        <img src="{{ asset('images/renova/'.$image) }}" alt="{{ $alt }}" class="h-40 w-full object-cover transition duration-700 group-hover:scale-[1.04]">
                    </div>
                    <div class="flex flex-1 flex-col px-2 pb-2 pt-4">
                        <h3 class="text-[15px] font-medium text-charcoal">{{ $title }}</h3>
                        <p class="mt-1.5 flex-1 text-[13px] leading-relaxed text-mist">{{ $copy }}</p>
                        <span class="mt-4 inline-flex h-8 w-8 items-center justify-center rounded-full border border-line text-forest transition group-hover:border-forest group-hover:bg-forest group-hover:text-ivory" aria-hidden="true">
                            <svg viewBox="0 0 16 16" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                        </span>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
