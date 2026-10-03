<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="RenovaHub helps homeowners, designers and contractors plan, collaborate and manage renovation projects in one connected workspace.">
        <title>RenovaHub — Renovate Smarter, Live Greener</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:400,500,600|outfit:300,400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body data-page="marketing" class="bg-cream font-outfit text-charcoal antialiased">
        @include('marketing.nav')

        <main>
            @include('marketing.hero')
            @include('marketing.about')
            @include('marketing.features')
            @include('marketing.process')
            @include('marketing.contact')
        </main>

        <div data-search-modal class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="search-title">
            <button type="button" class="absolute inset-0 bg-charcoal/50 backdrop-blur-sm" data-close-search aria-label="Close search"></button>
            <div class="relative mx-auto mt-[12vh] w-[min(640px,92vw)] overflow-hidden rounded-[28px] border border-line bg-ivory shadow-2xl">
                <div class="flex items-center gap-3 border-b border-line px-5 py-4">
                    <svg viewBox="0 0 24 24" class="h-5 w-5 text-olive" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="11" cy="11" r="6.5"/><path d="m16 16 4 4"/></svg>
                    <label id="search-title" class="sr-only" for="site-search">Search RenovaHub</label>
                    <input id="site-search" type="search" placeholder="Search pages and features" class="w-full border-0 bg-transparent text-base text-charcoal outline-none ring-0 placeholder:text-mist focus:outline-none focus:ring-0">
                    <button type="button" data-close-search class="text-xs uppercase tracking-[0.14em] text-mist">Close</button>
                </div>
                <ul class="max-h-[50vh] overflow-auto p-3">
                    @foreach ([
                        ['Home', '#top', 'Landing page'],
                        ['About', '#about', 'Homeowners, designers and contractors'],
                        ['Features', '#features', 'Project management, tasks, quotations, documents'],
                        ['How It Works', '#how-it-works', 'Create, invite, plan, quote, track, complete'],
                        ['Contact', '#contact', 'Email, phone and Colombo studio'],
                        ['Project Management', '#features', 'Timelines and project organisation'],
                        ['Team Collaboration', '#features', 'One workspace for the whole team'],
                        ['Quotations & Approvals', '#features', 'Request, compare and approve'],
                        ['Sustainable Choices', '#features', 'Materials with a lighter footprint'],
                        ['Log in', route('login'), 'Return to your renovation journey'],
                        ['Get Started', route('register'), 'Create a RenovaHub account'],
                    ] as [$label, $href, $hint])
                        <li data-search-item="{{ $label }} {{ $hint }}">
                            <a href="{{ $href }}" data-close-search class="flex items-center justify-between rounded-2xl px-3 py-3 transition hover:bg-cream">
                                <span>
                                    <span class="block text-sm font-medium text-charcoal">{{ $label }}</span>
                                    <span class="block text-xs text-mist">{{ $hint }}</span>
                                </span>
                                <span class="text-forest" aria-hidden="true">→</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div data-video-modal class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="video-title">
            <button type="button" class="absolute inset-0 bg-charcoal/70 backdrop-blur-sm" data-close-video aria-label="Close video"></button>
            <div class="relative mx-auto mt-[8vh] w-[min(960px,94vw)] overflow-hidden rounded-[28px] bg-forest text-ivory shadow-2xl">
                <button type="button" data-close-video class="absolute right-4 top-4 z-10 rounded-full bg-white/10 px-3 py-1.5 text-xs uppercase tracking-[0.14em] text-white transition hover:bg-white/20">Close</button>
                <div class="relative flex min-h-[320px] flex-col items-center justify-center px-6 py-16 text-center sm:min-h-[460px]" style="background-image: linear-gradient(to top, rgba(23,63,42,0.82), rgba(23,63,42,0.35)), url('{{ asset('images/renova/hero.jpg') }}'); background-size: cover; background-position: center;">
                    <span class="inline-flex h-20 w-20 items-center justify-center rounded-full border border-white/30 bg-white/10 backdrop-blur">
                        <svg viewBox="0 0 24 24" class="ml-1 h-8 w-8" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13l11-6.5-11-6.5Z"/></svg>
                    </span>
                    <p class="mt-6 text-[11px] uppercase tracking-[0.22em] text-white/70">RenovaHub</p>
                    <h2 id="video-title" class="mt-3 font-serif text-4xl font-medium tracking-[-0.03em] sm:text-5xl">How RenovaHub Works</h2>
                    <p class="mt-4 max-w-md text-sm leading-relaxed text-white/80">
                        A short introduction to planning a renovation, inviting your team, comparing quotations and following progress in one calm workspace. The full film will play here when it is ready.
                    </p>
                </div>
            </div>
        </div>
    </body>
</html>
