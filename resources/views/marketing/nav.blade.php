<header data-marketing-nav class="fixed inset-x-0 top-0 z-40">
    <div class="mx-auto flex h-[4.25rem] max-w-[1200px] items-center justify-between gap-4 px-5 lg:px-6">
        <x-brand-logo />

        <nav class="hidden min-w-0 items-center gap-x-1 text-[13px] lg:flex xl:gap-x-2 xl:text-sm" aria-label="Primary">
            <a href="#top" data-nav="top" class="nav-link whitespace-nowrap rounded-full px-2.5 py-1.5 transition hover:bg-cream/80 is-active">Home</a>
            <a href="#about" data-nav="about" class="nav-link whitespace-nowrap rounded-full px-2.5 py-1.5 transition hover:bg-cream/80">About</a>
            <a href="#features" data-nav="features" class="nav-link whitespace-nowrap rounded-full px-2.5 py-1.5 transition hover:bg-cream/80">Features</a>
            <a href="#how-it-works" data-nav="how-it-works" class="nav-link whitespace-nowrap rounded-full px-2.5 py-1.5 transition hover:bg-cream/80">How It Works</a>
            <a href="#contact" data-nav="contact" class="nav-link whitespace-nowrap rounded-full px-2.5 py-1.5 transition hover:bg-cream/80">Contact</a>
        </nav>

        <div class="flex items-center gap-2 sm:gap-3">
            <button type="button" data-open-search class="inline-flex h-10 w-10 items-center justify-center rounded-full text-charcoal transition hover:bg-white/70" aria-label="Search RenovaHub">
                <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/></svg>
            </button>
            <a href="{{ route('login') }}" class="hidden rounded-full border border-[#d7d2c6] bg-white/80 px-4 py-2 text-[13px] font-medium text-charcoal shadow-sm transition hover:border-forest hover:text-forest sm:inline-flex">Log in</a>
            <a href="{{ route('register') }}" class="rh-btn hidden px-4 py-2.5 text-[13px] sm:inline-flex">
                Get Started
                <svg viewBox="0 0 16 16" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
            </a>
            <button type="button" data-menu-toggle class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-line bg-white/80 text-charcoal lg:hidden" aria-expanded="false" aria-controls="mobile-menu" aria-label="Open menu">
                <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>
    </div>

    <div id="mobile-menu" data-mobile-menu class="hidden border-t border-line bg-ivory lg:hidden">
        <nav class="mx-auto flex max-w-[1200px] flex-col px-5 py-4 text-[15px]" aria-label="Mobile">
            <a href="#top" class="border-b border-line/80 py-3 text-charcoal">Home</a>
            <a href="#about" class="border-b border-line/80 py-3 text-charcoal">About</a>
            <a href="#features" class="border-b border-line/80 py-3 text-charcoal">Features</a>
            <a href="#how-it-works" class="border-b border-line/80 py-3 text-charcoal">How It Works</a>
            <a href="#contact" class="py-3 text-charcoal">Contact</a>
            <div class="mt-3 flex gap-2">
                <a href="{{ route('login') }}" class="rh-btn-outline flex-1">Log in</a>
                <a href="{{ route('register') }}" class="rh-btn flex-1">Get Started</a>
            </div>
        </nav>
    </div>
</header>
