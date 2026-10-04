<section id="top" class="relative scroll-mt-24">
    {{-- One fixed architectural photograph. There is no carousel and no slide controls. --}}
    <div class="relative min-h-[640px] overflow-hidden sm:min-h-[700px] lg:min-h-[760px]">
        <img
            src="{{ asset('images/renovahub-hero.jpg') }}"
            alt="Modern timber and glass house with a pool, trees and landscaping"
            class="absolute inset-0 h-full w-full object-cover object-[72%_center]"
        >

        <div class="hero-wash pointer-events-none absolute inset-0"></div>
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-[#F6F3EC] to-transparent"></div>

        <div class="pointer-events-none absolute -left-16 top-24 hidden h-56 w-56 text-[#8ea57a]/35 lg:block" aria-hidden="true">
            <x-leaf class="h-56 w-56" />
        </div>
        <div class="pointer-events-none absolute -right-8 bottom-28 hidden h-40 w-40 rotate-12 text-white/25 lg:block" aria-hidden="true">
            <x-leaf class="h-40 w-40" />
        </div>

        <div class="relative z-10 mx-auto grid max-w-[1200px] items-start gap-8 px-5 pb-28 pt-28 sm:pt-32 lg:grid-cols-12 lg:px-6 lg:pb-36 lg:pt-36">
            <div class="reveal lg:col-span-6">
                <p class="text-[11px] font-medium uppercase tracking-[0.22em] text-olive">Spaces today. A brighter tomorrow.</p>
                <h1 class="mt-4 max-w-[12ch] font-serif text-[3.15rem] font-medium leading-[0.94] tracking-[-0.035em] text-forest sm:text-6xl lg:text-[4.35rem]">
                    Renovate<br>
                    Smarter,<br>
                    Live Greener
                    <x-leaf class="ml-1 inline h-8 w-8 -translate-y-1 sm:h-10 sm:w-10" />
                </h1>
                <p class="mt-5 max-w-[34rem] text-[15px] leading-relaxed text-mist sm:text-base">
                    Plan, collaborate and manage your renovation projects in one connected workspace. Homeowners, designers and contractors share sustainable choices, trusted professionals and a clearer way to build better spaces.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="{{ route('register') }}" class="rh-btn px-5 py-3.5">
                        Get Started
                        <svg viewBox="0 0 16 16" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                    </a>
                    <a href="#about" class="inline-flex items-center gap-2 rounded-full border border-[#ddd6c8] bg-white/85 px-5 py-3.5 text-[0.9rem] font-medium text-charcoal shadow-sm transition hover:border-forest hover:text-forest">
                        Learn More
                        <svg viewBox="0 0 16 16" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 6.5 8 10.5 12 6.5"/></svg>
                    </a>
                </div>
            </div>

            <div class="pointer-events-none hidden justify-end pt-8 lg:col-span-6 lg:flex">
                <div class="max-w-[14rem] rounded-2xl bg-[#123d2b]/80 p-6 text-right text-[#f5f1e8] shadow-[0_18px_40px_-24px_rgba(0,0,0,0.65)] backdrop-blur-sm">
                    <p class="font-serif text-[1.9rem] font-medium leading-[1.05] tracking-[-0.03em]">
                        Good Design<br>Builds<br>Better<br>Lives
                    </p>
                    <span class="ml-auto mt-4 block h-px w-10 bg-[#f5f1e8]/70"></span>
                    <p class="mt-3 text-[13px] leading-relaxed text-[#f5f1e8]/90">
                        Design<br>Plan<br>Collaborate<br>Build together
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="relative z-20 mx-auto -mt-14 max-w-[1040px] px-5 lg:-mt-16">
        <dl class="grid grid-cols-2 gap-y-5 rounded-[26px] border border-white/80 bg-white px-4 py-5 shadow-[0_22px_50px_-28px_rgba(22,36,28,0.45)] sm:px-6 lg:grid-cols-4 lg:divide-x lg:divide-[#ece7dc] lg:py-6">
            @foreach ([
                ['500+', 'Projects Completed', 'home'],
                ['1,200+', 'Happy Users', 'users'],
                ['150+', 'Partner Professionals', 'badge'],
                ['95%', 'Satisfaction Rate', 'star'],
            ] as [$value, $label, $icon])
                <div class="flex items-center gap-3 px-2 lg:px-5">
                    <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#e7f0e4] text-forest">
                        @if ($icon === 'home')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z"/></svg>
                        @elseif ($icon === 'users')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M8 13a3 3 0 1 0-3-3 3 3 0 0 0 3 3Zm8 0a2.5 2.5 0 1 0-2.5-2.5A2.5 2.5 0 0 0 16 13ZM3.5 19.5a4.5 4.5 0 0 1 9 0M14 19.5a3.5 3.5 0 0 1 6.5-1.8"/></svg>
                        @elseif ($icon === 'badge')
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="9" r="4"/><path d="m8.5 13.5-1.5 7 5-2.5 5 2.5-1.5-7"/></svg>
                        @else
                            <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="m12 3 2.2 4.6L19 8.2l-3.5 3.4.8 4.9L12 14.8 7.7 16.5l.8-4.9L5 8.2l4.8-.6L12 3Z"/></svg>
                        @endif
                    </span>
                    <span>
                        <dt class="font-serif text-[1.65rem] font-medium leading-none tracking-[-0.03em] text-forest">{{ $value }}</dt>
                        <dd class="mt-1 text-[12px] leading-snug text-mist">{{ $label }}</dd>
                    </span>
                </div>
            @endforeach
        </dl>
    </div>
</section>
