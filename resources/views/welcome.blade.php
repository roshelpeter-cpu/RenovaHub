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

        @include('marketing.footer')

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
                        ['How It Works', '#how-it-works', 'Create a project, build the design, coordinate the team'],
                        ['Contact', '#contact', 'Email, phone and Colombo studio'],
                        ['Project Management', '#features', 'Timelines and project organisation'],
                        ['Team Collaboration', '#features', 'One workspace for the whole team'],
                        ['Quotations & Approvals', '#features', 'Request, compare and approve'],
                        ['Notifications', '#features', 'Calm, timely project updates'],
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

        @php
            $introVideoPath = public_path('videos/renovahub-intro.mp4');
            $hasIntroVideo = is_file($introVideoPath) && filesize($introVideoPath) > 0;
        @endphp
        <div data-video-modal class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="video-title" @if($hasIntroVideo) data-video-available @endif>
            <button type="button" class="absolute inset-0 bg-black/75 backdrop-blur-sm" data-close-video aria-label="Close video"></button>
            <div class="video-dialog relative mx-auto mt-[7vh] w-[min(980px,94vw)]">
                <button type="button" data-close-video class="absolute -top-3 right-0 z-10 inline-flex h-10 w-10 items-center justify-center rounded-full bg-white text-charcoal shadow-lg transition hover:bg-sand sm:-right-3" aria-label="Close">
                    <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6l12 12M18 6 6 18"/></svg>
                </button>
                <div data-video-frame class="overflow-hidden rounded-[24px] bg-black text-ivory shadow-2xl">
                    <h2 id="video-title" class="sr-only">RenovaHub Introduction</h2>
                    @if ($hasIntroVideo)
                        <video
                            data-video-player
                            class="aspect-video w-full bg-black"
                            playsinline
                            preload="metadata"
                            poster="{{ asset('images/renovahub-hero.jpg') }}"
                        >
                            <source src="{{ asset('videos/renovahub-intro.mp4') }}" type="video/mp4">
                        </video>
                    @endif
                    <div data-video-fallback class="{{ $hasIntroVideo ? 'hidden' : 'flex' }} aspect-video flex-col items-center justify-center bg-cover px-6 text-center" style="background-image: linear-gradient(to top, rgba(10,31,22,0.88), rgba(10,31,22,0.42)), url('{{ asset('images/renovahub-hero.jpg') }}');">
                        <span class="inline-flex h-16 w-16 items-center justify-center rounded-full border border-white/30 bg-white/10">
                            <svg viewBox="0 0 24 24" class="ml-1 h-7 w-7" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13l11-6.5-11-6.5Z"/></svg>
                        </span>
                        <p class="mt-4 font-serif text-3xl">RenovaHub Introduction</p>
                        <p class="mt-2 max-w-md text-sm text-white/75">The introduction video will play here once it is added.</p>
                    </div>
                    <div data-video-controls class="flex items-center gap-3 bg-[#0a1f16] px-4 py-3">
                        <button type="button" data-video-play class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-white text-forest disabled:opacity-40" aria-label="Play" @disabled(! $hasIntroVideo)>
                            <svg data-icon-play viewBox="0 0 24 24" class="ml-0.5 h-4 w-4" fill="currentColor"><path d="M8 5.5v13l11-6.5-11-6.5Z"/></svg>
                            <svg data-icon-pause viewBox="0 0 24 24" class="hidden h-4 w-4" fill="currentColor"><path d="M7 5h3.5v14H7zM13.5 5H17v14h-3.5z"/></svg>
                        </button>
                        <input data-video-seek type="range" min="0" max="100" value="0" step="0.1" class="video-range min-w-0 flex-1" aria-label="Seek" @disabled(! $hasIntroVideo)>
                        <button type="button" data-video-mute class="inline-flex h-9 w-9 items-center justify-center rounded-full text-white disabled:opacity-40" aria-label="Mute" @disabled(! $hasIntroVideo)>
                            <svg data-icon-volume viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 10h3l4-3v10l-4-3H4Z"/><path d="M16 9.5a3.5 3.5 0 0 1 0 5M18.2 7.2a6.5 6.5 0 0 1 0 9.6"/></svg>
                            <svg data-icon-muted viewBox="0 0 24 24" class="hidden h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 10h3l4-3v10l-4-3H4Z"/><path d="m16 10 4 4M20 10l-4 4"/></svg>
                        </button>
                        <input data-video-volume type="range" min="0" max="1" value="1" step="0.05" class="video-range w-20" aria-label="Volume" @disabled(! $hasIntroVideo)>
                        <button type="button" data-video-full class="inline-flex h-9 w-9 items-center justify-center rounded-full text-white disabled:opacity-40" aria-label="Fullscreen" @disabled(! $hasIntroVideo)>
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M8 4H4v4M16 4h4v4M8 20H4v-4M16 20h4v-4"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
