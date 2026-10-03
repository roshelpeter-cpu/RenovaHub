<section id="top" class="scroll-mt-24">
    <div class="mx-auto grid max-w-[1240px] items-center gap-8 px-5 pb-8 pt-6 sm:pt-8 lg:grid-cols-12 lg:gap-10 lg:px-8 lg:pb-16 lg:pt-10">
        <div class="reveal lg:col-span-5">
            <p class="text-[11px] font-medium uppercase tracking-[0.22em] text-olive">Spaces today. A brighter tomorrow.</p>
            <h1 class="mt-4 font-serif text-[3.15rem] font-medium leading-[0.96] tracking-[-0.035em] text-forest sm:text-6xl lg:text-[4.35rem]">
                Renovate<br>
                Smarter,<br>
                Live Greener
                <svg viewBox="0 0 32 32" class="ml-1 inline h-8 w-8 -translate-y-1 text-leaf sm:h-10 sm:w-10" fill="currentColor" aria-hidden="true"><path d="M25.8 5.2c-7.2.4-14.6 4.6-16.8 13.4 3.7-2.8 8-4.1 12.4-3.6-1.1 4.1-3.8 7.4-7.6 9.4 7.2.8 14.6-3.6 16.6-11.4.7-2.6.2-5.2-4.6-7.8Z"/></svg>
            </h1>
            <p class="mt-5 max-w-md text-[15px] leading-relaxed text-mist sm:text-base">
                Plan, collaborate and manage your renovation projects in one connected workspace. Homeowners, designers and contractors share sustainable choices, trusted professionals and a calmer way to build.
            </p>

            <div class="mt-8 flex flex-wrap items-center gap-3">
                <a href="{{ route('register') }}" class="rh-btn px-5 py-3.5">
                    Get Started
                    <svg viewBox="0 0 16 16" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                </a>
                <a href="#about" class="rh-btn-outline px-5 py-3.5">
                    Learn More
                    <svg viewBox="0 0 16 16" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M8 3v10M4 9l4 4 4-4"/></svg>
                </a>
                <button type="button" data-open-video class="group ml-1 inline-flex items-center gap-3 text-left">
                    <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-forest text-ivory transition group-hover:bg-leaf">
                        <svg viewBox="0 0 24 24" class="ml-0.5 h-4 w-4" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13l11-6.5-11-6.5Z"/></svg>
                    </span>
                    <span>
                        <span class="block text-sm font-medium text-charcoal">Play Video</span>
                        <span class="block text-xs text-mist">See how it works</span>
                    </span>
                </button>
            </div>

            <dl class="mt-10 grid grid-cols-2 gap-x-6 gap-y-5 border-t border-line pt-6 sm:grid-cols-4">
                @foreach ([
                    ['500+', 'Projects Completed'],
                    ['1,200+', 'Happy Users'],
                    ['150+', 'Partner Professionals'],
                    ['95%', 'Satisfaction Rate'],
                ] as [$value, $label])
                    <div>
                        <dt class="font-serif text-[1.65rem] leading-none text-forest">{{ $value }}</dt>
                        <dd class="mt-2 text-[12px] leading-snug text-mist">{{ $label }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        <div class="reveal reveal-delay-2 lg:col-span-7">
            <div class="relative overflow-hidden rounded-[28px] shadow-[0_30px_70px_-36px_rgba(30,36,32,0.55)]">
                <img
                    src="{{ asset('images/renova/hero.jpg') }}"
                    alt="Contemporary house with a reflecting pool, glass, timber and lush planting"
                    class="h-[420px] w-full object-cover sm:h-[520px] lg:h-[680px]"
                >
                <div class="absolute inset-0 bg-gradient-to-l from-charcoal/55 via-charcoal/10 to-transparent"></div>
                <div class="absolute bottom-7 right-6 max-w-[13.5rem] text-right text-white sm:bottom-auto sm:right-8 sm:top-1/2 sm:max-w-[15rem] sm:-translate-y-1/2">
                    <p class="font-serif text-[1.85rem] font-medium leading-[1.08] tracking-[-0.03em] sm:text-4xl">Good Design Builds Better Lives</p>
                    <span class="ml-auto mt-4 block h-px w-10 bg-white/75"></span>
                    <p class="mt-4 text-[13px] leading-relaxed text-white/85">
                        Design<br>Plan<br>Collaborate<br>Build together
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
