@props(['title' => 'Dashboard'])

@php
    $navigation = [
        ['label' => 'Dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard'), 'icon' => 'home'],
        ['label' => 'My Projects', 'href' => route('homeowner.projects.index'), 'active' => request()->routeIs('homeowner.projects.*'), 'icon' => 'folder'],
        ['label' => 'Tasks', 'href' => route('homeowner.sections.show', 'tasks'), 'active' => request()->route('section') === 'tasks', 'icon' => 'check'],
        ['label' => 'Documents', 'href' => route('homeowner.sections.show', 'documents'), 'active' => request()->route('section') === 'documents', 'icon' => 'doc'],
        ['label' => 'Mood Board', 'href' => route('homeowner.sections.show', 'mood-board'), 'active' => request()->route('section') === 'mood-board', 'icon' => 'image'],
        ['label' => 'Quotations', 'href' => route('homeowner.sections.show', 'quotations'), 'active' => request()->route('section') === 'quotations', 'icon' => 'file'],
        ['label' => 'Change Requests', 'href' => route('homeowner.sections.show', 'change-requests'), 'active' => request()->route('section') === 'change-requests', 'icon' => 'edit'],
        ['label' => 'Payments', 'href' => route('homeowner.sections.show', 'payments'), 'active' => request()->route('section') === 'payments', 'icon' => 'card'],
        ['label' => 'Notifications', 'href' => route('homeowner.sections.show', 'notifications'), 'active' => request()->route('section') === 'notifications', 'icon' => 'bell'],
    ];

    $account = [
        ['label' => 'Profile', 'href' => route('profile.show'), 'active' => request()->routeIs('profile.show'), 'icon' => 'user'],
        ['label' => 'Settings', 'href' => route('profile.show'), 'active' => false, 'icon' => 'cog'],
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
        <link href="https://fonts.bunny.net/css?family=fraunces:500,600|outfit:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-cream font-outfit text-charcoal antialiased">
        <div class="min-h-screen lg:flex">
            <input id="homeowner-menu" type="checkbox" class="peer sr-only">

            <div class="flex items-center justify-between bg-forest px-4 py-4 text-ivory lg:hidden">
                <x-brand-logo href="{{ route('dashboard') }}" tone="light" />
                <label for="homeowner-menu" class="inline-flex cursor-pointer items-center rounded-full border border-white/25 px-3 py-2 text-sm">Menu</label>
            </div>

            <aside class="hidden w-full bg-forest text-ivory peer-checked:flex peer-checked:max-h-[calc(100vh-4.5rem)] peer-checked:flex-col peer-checked:overflow-y-auto lg:flex lg:min-h-screen lg:w-[17rem] lg:shrink-0 lg:flex-col">
                <div class="hidden px-5 py-6 lg:block">
                    <x-brand-logo href="{{ route('dashboard') }}" tone="light" />
                </div>

                <nav class="space-y-1 px-3 pb-4" aria-label="Homeowner">
                    @foreach ($navigation as $item)
                        <a href="{{ $item['href'] }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition duration-300 {{ $item['active'] ? 'bg-white/15 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}" @if($item['active']) aria-current="page" @endif>
                            <span class="inline-flex h-5 w-5 shrink-0" aria-hidden="true">
                                @switch($item['icon'])
                                    @case('home')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z"/></svg>
                                        @break
                                    @case('folder')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3.5 7.5h6l2 2h9v9.5h-17Z"/></svg>
                                        @break
                                    @case('check')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M8 6h11M8 12h11M8 18h11"/><path d="m4 6 1 1 2-2M4 12l1 1 2-2M4 18l1 1 2-2"/></svg>
                                        @break
                                    @case('doc')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M7 3.5h7l4 4V20.5H7Z"/><path d="M14 3.5V8h4"/></svg>
                                        @break
                                    @case('image')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="5" width="16" height="14" rx="2"/><path d="m8 15 2.5-2.5L14 16l2-2 2 2"/></svg>
                                        @break
                                    @case('file')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M7 3.5h8l4 4V20.5H7Z"/><path d="M9 13h6M9 17h4"/></svg>
                                        @break
                                    @case('edit')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 20h4l11-11-4-4L4 16v4Z"/></svg>
                                        @break
                                    @case('card')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M3 10h18"/></svg>
                                        @break
                                    @case('bell')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 16.2V10a6 6 0 1 1 12 0v6.2l1.4 2.2H4.6L6 16.2Z"/></svg>
                                        @break
                                    @case('user')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="8" r="3"/><path d="M5 19a7 7 0 0 1 14 0"/></svg>
                                        @break
                                    @default
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="3"/><path d="M12 3v2M12 19v2M3 12h2M19 12h2M5.6 5.6l1.4 1.4M17 17l1.4 1.4M18.4 5.6 17 7M7 17l-1.4 1.4"/></svg>
                                @endswitch
                            </span>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="mt-auto border-t border-white/10 px-3 py-4">
                    <nav class="space-y-1" aria-label="Account">
                        @foreach ($account as $item)
                            <a href="{{ $item['href'] }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm transition duration-300 {{ $item['active'] ? 'bg-white/15 text-white' : 'text-white/75 hover:bg-white/10 hover:text-white' }}" @if($item['active']) aria-current="page" @endif>
                                <span class="inline-flex h-5 w-5 shrink-0" aria-hidden="true">
                                    @if ($item['icon'] === 'user')
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="8" r="3"/><path d="M5 19a7 7 0 0 1 14 0"/></svg>
                                    @else
                                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="3"/><path d="M12 3v2M12 19v2M3 12h2M19 12h2M5.6 5.6l1.4 1.4M17 17l1.4 1.4M18.4 5.6 17 7M7 17l-1.4 1.4"/></svg>
                                    @endif
                                </span>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>

                <div class="border-t border-white/10 px-4 py-4">
                    <p class="truncate text-sm font-medium">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-white/60">Homeowner</p>
                    <form method="POST" action="{{ route('logout') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="text-sm text-white/80 underline-offset-2 transition hover:text-white hover:underline">Log out</button>
                    </form>
                </div>
            </aside>

            <div class="min-w-0 flex-1">
                <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                    @session('status')
                        <div class="mb-5 rounded-2xl border border-line bg-sand/70 px-4 py-3 text-sm text-forest" role="status">{{ $value }}</div>
                    @endsession
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
