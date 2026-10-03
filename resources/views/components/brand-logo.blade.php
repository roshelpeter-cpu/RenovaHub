@props([
    'href' => '/',
    'tone' => 'dark',
])

@php
    $color = $tone === 'light' ? 'text-white' : 'text-forest';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center gap-2.5 {$color}"]) }}>
    <svg viewBox="0 0 32 32" class="h-7 w-7" fill="currentColor" aria-hidden="true">
        <path d="M25.8 5.2c-7.2.4-14.6 4.6-16.8 13.4 3.7-2.8 8-4.1 12.4-3.6-1.1 4.1-3.8 7.4-7.6 9.4 7.2.8 14.6-3.6 16.6-11.4.7-2.6.2-5.2-4.6-7.8Z"/>
        <path d="M14.6 17.4c.4 2.8-.2 5.5-1.6 7.8 2-1.2 3.6-2.8 4.7-4.8.7-1.3 1.1-2.6 1.1-3.9-1.5.2-2.9.4-4.2.9Z" opacity=".8"/>
    </svg>
    <span class="font-outfit text-[1.05rem] font-semibold tracking-[-0.02em]">RenovaHub</span>
</a>
