<section id="how-it-works" class="relative scroll-mt-24 overflow-hidden">
    {{-- Supplied architectural drawing. Transparent areas keep the section background visible, so the sketch is not framed as a photograph. --}}
    <div class="group absolute right-[2%] top-4 hidden h-[22rem] w-[44%] lg:block" aria-hidden="true">
        <img
            src="{{ asset('images/renovahub-how-it-works.png') }}"
            alt=""
            class="h-full w-full object-contain object-right-top transition-transform duration-700 group-hover:scale-105"
        >
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
                ['01', 'Create Your Project', 'Add the project details and property location.', 'doc', true],
                ['02', 'Build Your Design', 'Shape the design direction for the renovation.', 'pen', false],
                ['03', 'Coordinate Your Team', 'Keep the homeowner, designer and contractor in one workspace.', 'users', false],
                ['04', 'Manage the Renovation', 'Follow the work from planning through completion.', 'check', false],
            ];
        @endphp

        <ol class="relative mt-12 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($steps as [$index, $title, $copy, $icon, $active])
                <li class="group relative rounded-2xl bg-white/75 p-4 text-center shadow-sm transition duration-500 hover:-translate-y-1 hover:shadow-xl">
                    @if (! $loop->last)
                        <span class="pointer-events-none absolute -right-3 top-7 z-10 hidden text-olive/70 lg:block" aria-hidden="true">
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
                            @case('pen')
                                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 20h4l11-11-4-4L4 16v4Z"/><path d="m13 7 4 4"/></svg>
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
