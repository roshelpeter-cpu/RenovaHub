@props(['title' => 'Dashboard', 'flush' => false])

@php
    $navCounts = $navCounts ?? ['notifications' => 0];
    $user = auth()->user();
    $onProject = request()->routeIs('designer.projects.*', 'designer.concepts.show', 'designer.concepts.store', 'designer.concepts.update', 'designer.concepts.submit');
    $links = [
        ['label' => 'Dashboard', 'href' => route('designer.dashboard'), 'active' => request()->routeIs('designer.dashboard')],
        ['label' => 'My Projects', 'href' => route('designer.projects.index'), 'active' => $onProject],
        ['label' => 'Invitations', 'href' => route('designer.invitations.index'), 'active' => request()->routeIs('designer.invitations.*')],
        ['label' => 'Design Work', 'href' => route('designer.design-work'), 'active' => request()->routeIs('designer.design-work')],
        ['label' => 'Mood Boards', 'href' => route('designer.mood-boards.index'), 'active' => request()->routeIs('designer.mood-boards.*')],
        ['label' => 'Documents', 'href' => route('designer.documents.index'), 'active' => request()->routeIs('designer.documents.*')],
        ['label' => 'Tasks', 'href' => route('designer.tasks.index'), 'active' => request()->routeIs('designer.tasks.*')],
        ['label' => 'Messages', 'href' => route('designer.messages.index'), 'active' => request()->routeIs('designer.messages.*')],
        ['label' => 'Earnings', 'href' => route('designer.earnings.index'), 'active' => request()->routeIs('designer.earnings.*')],
    ];
    $profile = $user->professionalProfile;
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title }} — RenovaHub</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=fraunces:500,600|outfit:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-[#F7F4EE] font-outfit text-[#18352A] antialiased">
        <input id="designer-menu" type="checkbox" class="peer/menu sr-only">
        <header class="sticky top-0 z-40 border-b border-[#ece7dc] bg-white">
            <div class="mx-auto flex max-w-[90rem] items-center gap-3 px-4 py-3.5 sm:px-6 lg:px-8">
                <a href="{{ route('designer.dashboard') }}" class="flex shrink-0 items-center gap-2 text-[#123D2B]">
                    <svg viewBox="0 0 32 32" class="h-8 w-8" fill="currentColor" aria-hidden="true"><path d="M16 3.2 3.4 14.1a1.2 1.2 0 0 0-.4.9V27.2A2.3 2.3 0 0 0 5.3 29.5h6.2v-8.1c0-.7.6-1.3 1.3-1.3h6.4c.7 0 1.3.6 1.3 1.3v8.1h6.2a2.3 2.3 0 0 0 2.3-2.3V15a1.2 1.2 0 0 0-.4-.9L16 3.2Z"/></svg>
                    <span class="text-[1.15rem] font-semibold leading-none tracking-[-0.02em]">RenovaHub</span>
                </a>
                <nav class="hidden min-w-0 flex-1 items-center justify-center gap-0.5 overflow-x-auto xl:flex" aria-label="Designer">
                    @foreach ($links as $item)
                        <a href="{{ $item['href'] }}" class="relative shrink-0 whitespace-nowrap px-2 py-2 text-[13px] {{ $item['active'] ? 'font-medium text-[#123D2B]' : 'text-[#66756C] hover:text-[#123D2B]' }}" @if($item['active']) aria-current="page" @endif>
                            {{ $item['label'] }}
                            @if ($item['active'])
                                <span class="absolute inset-x-2 -bottom-3.5 h-0.5 rounded-full bg-[#123D2B]"></span>
                            @endif
                        </a>
                    @endforeach
                </nav>
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('designer.notifications.index') }}" class="relative flex h-10 w-10 items-center justify-center rounded-full text-[#123D2B] hover:bg-[#F6F1E7]" aria-label="Notifications">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 16.2V10a6 6 0 1 1 12 0v6.2l1.4 2.2H4.6L6 16.2Z"/></svg>
                        @if (($navCounts['notifications'] ?? 0) > 0)
                            <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-[#123D2B]"></span>
                        @endif
                    </a>
                    <details class="relative">
                        <summary class="flex cursor-pointer list-none items-center gap-2 rounded-full py-1 pl-1 pr-2 hover:bg-[#F6F1E7] [&::-webkit-details-marker]:hidden">
                            <img src="{{ $profile?->avatarUrl() ?: $user->profile_photo_url }}" alt="" class="h-10 w-10 rounded-full object-cover">
                            <span class="hidden text-left sm:block">
                                <span class="block text-sm font-medium leading-tight text-[#123D2B]">{{ $profile?->displayName() ?? $user->name }}</span>
                                <span class="block text-[11px] text-[#66756C]">Designer</span>
                            </span>
                        </summary>
                        <div class="absolute right-0 z-20 mt-2 w-48 overflow-hidden rounded-2xl border border-[#ece7dc] bg-white py-1 text-sm shadow-lg">
                            <a href="{{ route('designer.profile.edit') }}" class="block px-4 py-2 hover:bg-[#F6F1E7]">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">@csrf<button class="block w-full px-4 py-2 text-left hover:bg-[#F6F1E7]">Log out</button></form>
                        </div>
                    </details>
                    <label for="designer-menu" class="inline-flex cursor-pointer items-center rounded-full border border-[#ddd6c8] px-3 py-1.5 text-sm text-[#123D2B] xl:hidden">Menu</label>
                </div>
            </div>
            <nav class="hidden border-t border-[#ece7dc] px-4 py-3 peer-checked/menu:block xl:hidden">
                @foreach ($links as $item)
                    <a href="{{ $item['href'] }}" class="block rounded-xl px-3 py-2 text-sm {{ $item['active'] ? 'bg-[#F6F1E7] font-medium text-[#123D2B]' : 'text-[#66756C]' }}">{{ $item['label'] }}</a>
                @endforeach
            </nav>
        </header>
        <main class="{{ $flush ? '' : 'mx-auto max-w-[90rem] px-4 py-6 sm:px-6 lg:px-8 lg:py-8' }}">
            @session('status')
                <div class="mb-5 rounded-2xl border border-[#ece7dc] bg-white px-4 py-3 text-sm text-[#123D2B]" role="status">{{ $value }}</div>
            @endsession
            {{ $slot }}
        </main>
        @livewireScripts
    </body>
</html>
