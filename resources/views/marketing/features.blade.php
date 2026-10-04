<section id="features" class="relative scroll-mt-24 overflow-hidden">
    <div class="pointer-events-none absolute -right-6 top-24 hidden h-40 w-40 text-[#8ea57a]/25 lg:block" aria-hidden="true">
        <x-leaf class="h-40 w-40 rotate-12" />
    </div>

    <div class="mx-auto max-w-[1200px] px-5 py-14 lg:px-6 lg:py-16">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-[11px] font-medium uppercase tracking-[0.22em] text-olive">Features</p>
                <h2 class="mt-3 font-serif text-[2.15rem] font-medium leading-[1.08] tracking-[-0.03em] text-forest sm:text-[2.7rem]">
                    Powerful Tools for Every Step
                    <x-leaf class="ml-1 inline h-7 w-7 -translate-y-1 sm:h-8 sm:w-8" />
                </h2>
            </div>
            <a href="#all-features" class="shrink-0 text-sm font-medium text-forest transition hover:text-leaf">Explore All Features →</a>
        </div>
        <p class="mt-4 max-w-2xl text-[15px] leading-relaxed text-mist">
            RenovaHub connects the important parts of a renovation workflow, from the first brief to the finished space.
        </p>

        @php
            $features = [
                ['Project Management', 'Create and organise projects with clear timelines.', 'feature-plans.jpg', 'Architectural drawings on a planning table', 'layers'],
                ['Team Collaboration', 'Work with homeowners, designers and contractors in one place.', 'feature-collab.jpg', 'People reviewing plans together', 'users'],
                ['Task Management', 'Assign, track and complete tasks with ease.', 'feature-tasks.jpg', 'Tablet and materials on a work table', 'check'],
                ['Quotations & Approvals', 'Request, compare and approve quotations.', 'feature-quotes.jpg', 'Quotation documents arranged on a desk', 'doc'],
                ['Document Management', 'Keep project documents organised and accessible.', 'feature-docs.jpg', 'Laptop and project documents on a desk', 'folder'],
                ['Progress Tracking', 'Monitor real-time progress with updates and reports.', 'feature-progress.jpg', 'Modern home exterior during a renovation', 'chart'],
                ['Change Requests', 'Capture change requests and manage approval workflows.', 'feature-changes.jpg', 'Material samples and a notebook', 'edit'],
                ['Notifications', 'Stay informed with real-time project updates.', 'feature-notes.jpg', 'A phone showing project notifications', 'bell'],
            ];
        @endphp

        <div id="all-features" class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($features as [$title, $copy, $image, $alt, $icon])
                <article class="group flex h-full flex-col overflow-hidden rounded-[22px] border border-[#ece7dc] bg-white shadow-[0_16px_36px_-28px_rgba(30,36,32,0.45)] transition duration-500 hover:-translate-y-1 hover:shadow-xl">
                    <img src="{{ asset('images/renova/'.$image) }}" alt="{{ $alt }}" class="h-[148px] w-full object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="relative flex flex-1 flex-col px-4 pb-4 pt-0">
                        <span class="-mt-5 inline-flex h-10 w-10 items-center justify-center rounded-full border-4 border-white bg-[#e7f0e4] text-forest">
                            @switch($icon)
                                @case('layers')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="m12 4 8 4-8 4-8-4 8-4Z"/><path d="m4 12 8 4 8-4"/><path d="m4 16 8 4 8-4"/></svg>
                                    @break
                                @case('users')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M8 12a3 3 0 1 0-3-3 3 3 0 0 0 3 3Zm8 .5a2.5 2.5 0 1 0-2.5-2.5A2.5 2.5 0 0 0 16 12.5ZM3.5 19a4.5 4.5 0 0 1 9 0M14.2 19a3.4 3.4 0 0 1 6.3-1.7"/></svg>
                                    @break
                                @case('check')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M8 6h11M8 12h11M8 18h11"/><path d="m4 6 1 1 2-2M4 12l1 1 2-2M4 18l1 1 2-2"/></svg>
                                    @break
                                @case('doc')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M7 3.5h7l4 4V20.5H7Z"/><path d="M14 3.5V8h4M9 13h6M9 17h4"/></svg>
                                    @break
                                @case('folder')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3.5 7.5h6l2 2h9v9.5h-17Z"/></svg>
                                    @break
                                @case('chart')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 19h16M7 16v-4M12 16V8M17 16v-6"/></svg>
                                    @break
                                @case('edit')
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 20h4l11-11-4-4L4 16v4Z"/><path d="m13 7 4 4"/></svg>
                                    @break
                                @default
                                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 16.2V10a6 6 0 1 1 12 0v6.2l1.4 2.2H4.6L6 16.2Z"/><path d="M10 19.2a2 2 0 0 0 4 0"/></svg>
                            @endswitch
                        </span>
                        <h3 class="mt-2 text-[15px] font-semibold text-charcoal">{{ $title }}</h3>
                        <p class="mt-1 text-[13px] leading-relaxed text-mist">{{ $copy }}</p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
