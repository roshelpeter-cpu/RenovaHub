@props([
    'href' => '/',
    'tone' => 'dark',
])

@php
    $color = $tone === 'light' ? 'text-white' : 'text-forest';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => "inline-flex items-center gap-2.5 {$color}"]) }}>
    <svg viewBox="0 0 32 32" class="h-7 w-7" fill="currentColor" aria-hidden="true">
        <path d="M16 3.2 3.4 14.1a1.2 1.2 0 0 0-.4.9V27.2A2.3 2.3 0 0 0 5.3 29.5h6.2v-8.1c0-.7.6-1.3 1.3-1.3h6.4c.7 0 1.3.6 1.3 1.3v8.1h6.2a2.3 2.3 0 0 0 2.3-2.3V15a1.2 1.2 0 0 0-.4-.9L16 3.2Z"/>
    </svg>
    <span class="font-outfit text-[1.05rem] font-semibold tracking-[-0.02em]">RenovaHub</span>
</a>
