@props(['title' => 'Home', 'flush' => false, 'canvas' => false])

@php
    $navCounts = $navCounts ?? ['messages' => 0, 'notifications' => 0];
    $user = auth()->user();
    // Project URLs keep My Projects active. Global Tasks and Documents are separate pages.
    $onProject = request()->is('homeowner/projects', 'homeowner/projects/*');
    $links = [
        ['label' => 'Home', 'href' => route('homeowner.home'), 'active' => ! $onProject && request()->routeIs('homeowner.home', 'dashboard', 'homeowner.explore', 'homeowner.professionals.*')],
        ['label' => 'My Projects', 'href' => route('homeowner.projects.index'), 'active' => $onProject],
        ['label' => 'Tasks', 'href' => route('homeowner.tasks.index'), 'active' => request()->routeIs('homeowner.tasks.*')],
        ['label' => 'Documents', 'href' => route('homeowner.documents.index'), 'active' => request()->routeIs('homeowner.documents.*')],
        ['label' => 'Mood Board', 'href' => route('homeowner.mood-board.index'), 'active' => request()->routeIs('homeowner.mood-board.index')],
        ['label' => 'Quotations', 'href' => route('homeowner.quotations.index'), 'active' => request()->routeIs('homeowner.quotations.index')],
        ['label' => 'Change Requests', 'href' => route('homeowner.change-requests.index'), 'active' => request()->routeIs('homeowner.change-requests.index')],
        ['label' => 'Messages', 'href' => route('homeowner.messages.index'), 'active' => request()->routeIs('homeowner.messages.*') && ! $onProject],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title }} — RenovaHub</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=caveat:500,600|fraunces:500,600|outfit:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen {{ $canvas ? 'bg-[#F7F4EE]' : 'bg-white' }} font-outfit text-[#18352A] antialiased">
        <input id="homeowner-menu" type="checkbox" class="peer/menu sr-only">

        <header class="sticky top-0 z-40 border-b border-[#ece7dc] bg-white">
            <div class="mx-auto flex max-w-[90rem] items-center gap-3 px-4 py-2.5 sm:px-6 lg:px-8">
                <a href="{{ route('homeowner.home') }}" class="flex shrink-0 items-center gap-2 text-[#123D2B]">
                    <svg viewBox="0 0 32 32" class="h-7 w-7" fill="currentColor" aria-hidden="true">
                        <path d="M16 3.2 3.4 14.1a1.2 1.2 0 0 0-.4.9V27.2A2.3 2.3 0 0 0 5.3 29.5h6.2v-8.1c0-.7.6-1.3 1.3-1.3h6.4c.7 0 1.3.6 1.3 1.3v8.1h6.2a2.3 2.3 0 0 0 2.3-2.3V15a1.2 1.2 0 0 0-.4-.9L16 3.2Z"/>
                    </svg>
                    <span>
                        <span class="block text-[1.05rem] font-semibold leading-none tracking-[-0.02em]">RenovaHub</span>
                        <span class="mt-1 block text-[9px] tracking-[0.04em] text-[#66756C]">Plan · Design · Build · Together</span>
                    </span>
                </a>

                <nav class="hidden min-w-0 flex-1 items-center justify-center lg:flex" aria-label="Homeowner">
                    @foreach ($links as $item)
                        <a href="{{ $item['href'] }}" class="relative whitespace-nowrap px-2.5 py-2 text-[13px] transition {{ $item['active'] ? 'font-medium text-[#123D2B]' : 'text-[#66756C] hover:text-[#123D2B]' }}" @if($item['active']) aria-current="page" @endif>
                            {{ $item['label'] }}
                            @if ($item['active'])
                                <span class="absolute inset-x-2.5 -bottom-[0.65rem] h-0.5 rounded-full bg-[#123D2B]"></span>
                            @endif
                        </a>
                    @endforeach
                </nav>

                <div class="ml-auto flex items-center gap-2">
                    <form method="GET" action="{{ route('homeowner.explore') }}" class="relative hidden lg:block">
                        <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-[#66756C]">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="11" cy="11" r="6.5"/><path d="m20 20-3.2-3.2"/></svg>
                        </span>
                        <label class="sr-only" for="nav-search">Search</label>
                        <input id="nav-search" name="search" type="search" placeholder="Search..." class="w-44 rounded-full border border-[#ece7dc] bg-[#F7F4EE] py-1.5 pl-9 pr-3 text-sm outline-none focus:border-[#123D2B]">
                    </form>

                    <a href="{{ route('homeowner.notifications.index') }}" class="relative flex h-9 w-9 items-center justify-center rounded-full text-[#123D2B] hover:bg-[#F6F1E7]" aria-label="Notifications">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 16.2V10a6 6 0 1 1 12 0v6.2l1.4 2.2H4.6L6 16.2Z"/></svg>
                        @if (($navCounts['notifications'] ?? 0) > 0)
                            <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-full bg-[#123D2B]"></span>
                        @endif
                    </a>

                    <details class="relative">
                        <summary class="flex cursor-pointer list-none items-center gap-2 rounded-full py-0.5 pl-0.5 pr-2 marker:content-none hover:bg-[#F6F1E7] [&::-webkit-details-marker]:hidden">
                            <img src="{{ $user->profile_photo_url }}" alt="" class="h-9 w-9 rounded-full object-cover">
                            <span class="hidden text-left sm:block">
                                <span class="block text-sm font-medium leading-tight text-[#123D2B]">{{ $user->name }}</span>
                                <span class="block text-[11px] text-[#66756C]">Homeowner</span>
                            </span>
                            <svg viewBox="0 0 20 20" class="hidden h-4 w-4 text-[#66756C] sm:block" fill="currentColor" aria-hidden="true"><path d="M5.5 7.5 10 12l4.5-4.5"/></svg>
                        </summary>
                        <div class="absolute right-0 z-20 mt-2 w-48 overflow-hidden rounded-2xl border border-[#ece7dc] bg-white py-1 text-sm shadow-lg">
                            <a href="{{ route('homeowner.profile.edit') }}" class="block px-4 py-2 hover:bg-[#F6F1E7]">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left hover:bg-[#F6F1E7]">Log out</button>
                            </form>
                        </div>
                    </details>

                    <label for="homeowner-menu" class="inline-flex cursor-pointer items-center rounded-full border border-[#ddd6c8] px-3 py-1.5 text-sm text-[#123D2B] lg:hidden">Menu</label>
                </div>
            </div>

            <nav class="hidden border-t border-[#ece7dc] px-4 py-3 peer-checked/menu:block lg:hidden" aria-label="Homeowner mobile">
                @foreach ($links as $item)
                    <a href="{{ $item['href'] }}" class="block rounded-xl px-3 py-2 text-sm {{ $item['active'] ? 'bg-[#F6F1E7] font-medium text-[#123D2B]' : 'text-[#66756C]' }}">{{ $item['label'] }}</a>
                @endforeach
            </nav>
        </header>

        {{ $hero ?? '' }}

        <main class="{{ $flush ? '' : 'mx-auto max-w-[90rem] px-4 py-6 sm:px-6 lg:px-8 lg:py-8' }}">
            @session('status')
                <div class="{{ $flush ? 'mx-auto max-w-[90rem] px-4 sm:px-6 lg:px-8' : '' }} mb-5 rounded-2xl border border-[#ece7dc] bg-[#F6F1E7] px-4 py-3 text-sm text-[#123D2B]" role="status">{{ $value }}</div>
            @endsession
            {{ $slot }}
        </main>
        @livewireScripts
    </body>
</html>
