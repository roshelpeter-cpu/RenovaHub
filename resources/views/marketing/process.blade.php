<section id="how-it-works" class="relative scroll-mt-24 overflow-hidden">
    <div class="pointer-events-none absolute right-[2%] top-4 hidden h-[22rem] w-[44%] lg:block" aria-hidden="true">
        <svg viewBox="0 0 560 280" class="h-full w-full text-[#b7b3a4]" fill="none" stroke="currentColor" stroke-width="1.15" style="mask-image: linear-gradient(to bottom, #000 62%, transparent 100%);">
            <path d="M36 214h500" />
            <path d="M78 214V96h250v118" />
            <path d="M108 96V52h190v44" />
            <path d="M96 52h214" />
            <path d="M124 78h42M182 78h42M240 78h42" />
            <path d="M124 78v18M166 78v18M182 78v18M224 78v18M240 78v18M282 78v18" />
            <path d="M110 132h70v40h-70zM196 132h70v40h-70zM282 132h28v82" />
            <path d="M118 148h18M146 148h18M204 148h18M232 148h18" />
            <path d="M328 214V138h168v76" />
            <path d="M348 158h36v24h-36zM398 158h36v24h-36zM448 158h28v56" />
            <path d="M78 214V168h36" />
            <path d="M470 214c8-36 28-58 18-96 24 10 46 40 42 78" />
            <path d="M488 118c10-18 6-34-4-46" />
            <path d="M40 188c16-22 8-44-6-58 16 4 34 22 36 46" />
        </svg>
    </div>
    <div class="pointer-events-none absolute -left-8 top-16 hidden h-32 w-32 text-[#8ea57a]/30 lg:block" aria-hidden="true">
        <x-leaf class="h-32 w-32" />
    </div>

    <div class="relative mx-auto max-w-[1200px] px-5 py-16 lg:px-6 lg:py-20">
        <p class="text-[11px] font-medium uppercase tracking-[0.22em] text-olive">How It Works</p>
        <h2 class="mt-3 max-w-xl font-serif text-[2.15rem] font-medium leading-[1.08] tracking-[-0.03em] text-forest sm:text-[2.7rem]">
            A Simple Process,<br>A Better Renovation
            <x-leaf class="ml-1 inline h-7 w-7 -translate-y-1 sm:h-8 sm:w-8" />
        </h2>
        <p class="mt-4 max-w-xl text-[15px] leading-relaxed text-mist">
            From idea to completion, RenovaHub keeps every conversation, decision and update connected.
        </p>

        @php
            $steps = [
                ['01', 'Create Your Project', 'Set your project goals, budget and timeline.', 'doc', true],
                ['02', 'Invite Your Team', 'Add homeowners, designers and contractors.', 'users', false],
                ['03', 'Plan & Assign Tasks', 'Break the project into tasks and assign responsibilities.', 'list', false],
                ['04', 'Manage Quotations', 'Request, compare and approve quotations.', 'file', false],
                ['05', 'Track Progress', 'Monitor project progress and updates.', 'chart', false],
                ['06', 'Complete Your Renovation', 'Finalize the project and enjoy your new space.', 'check', false],
            ];
        @endphp

        <ol class="relative mt-12 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-6 lg:gap-4">
            @foreach ($steps as [$index, $title, $copy, $icon, $active])
                <li class="relative text-center lg:px-1">
                    @if (! $loop->last)
                        <span class="pointer-events-none absolute left-[calc(50%+1.35rem)] top-4 hidden text-olive/70 lg:block" aria-hidden="true">
                            <svg viewBox="0 0 28 12" class="h-3 w-7" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M0 6h22M18 2l6 4-6 4"/></svg>
                        </span>
                    @endif
                    <span class="relative z-10 mx-auto flex h-10 w-10 items-center justify-center rounded-full font-serif text-[13px] {{ $active ? 'bg-forest text-ivory' : 'border border-forest/30 bg-white text-forest' }}">{{ $index }}</span>
                    <span class="mx-auto mt-4 flex h-8 w-8 items-center justify-center text-forest">
                        @switch($icon)
                            @case('doc')
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M7 3.5h7l4 4V20.5H7Z"/><path d="M14 3.5V8h4M9 13h6M9 17h4"/></svg>
                                @break
                            @case('users')
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M8 12a3 3 0 1 0-3-3 3 3 0 0 0 3 3Zm8 .5a2.5 2.5 0 1 0-2.5-2.5A2.5 2.5 0 0 0 16 12.5ZM3.5 19a4.5 4.5 0 0 1 9 0M14.2 19a3.4 3.4 0 0 1 6.3-1.7"/></svg>
                                @break
                            @case('list')
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M8 6h11M8 12h11M8 18h11"/><path d="M4 6h.01M4 12h.01M4 18h.01"/></svg>
                                @break
                            @case('file')
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M7 3.5h8l4 4V20.5H7Z"/><path d="M15 3.5V8h4"/></svg>
                                @break
                            @case('chart')
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 19h16M7 16v-4M12 16V8M17 16v-6"/></svg>
                                @break
                            @default
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="8"/><path d="m8.5 12.2 2.4 2.4 4.6-5"/></svg>
                        @endswitch
                    </span>
                    <h3 class="mt-3 text-[14px] font-semibold text-charcoal">{{ $title }}</h3>
                    <p class="mt-1.5 text-[12.5px] leading-relaxed text-mist">{{ $copy }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
