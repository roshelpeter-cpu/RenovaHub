<a
    href="{{ route('google.redirect') }}"
    {{ $attributes->merge(['class' => 'flex w-full items-center justify-center gap-3 rounded-full border border-line bg-white px-4 py-3 text-sm font-medium text-charcoal transition hover:border-forest/40 hover:bg-cream']) }}
>
    <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
        <path fill="#4285F4" d="M21.35 12.23c0-.79-.07-1.55-.2-2.27H12v4.3h5.24a4.48 4.48 0 0 1-1.94 2.94v2.45h3.14c1.84-1.69 2.91-4.18 2.91-7.42z"/>
        <path fill="#34A853" d="M12 21.99c2.63 0 4.84-.87 6.45-2.34l-3.14-2.45c-.87.58-1.98.93-3.31.93-2.54 0-4.69-1.72-5.46-4.03H3.3v2.53A9.75 9.75 0 0 0 12 21.99z"/>
        <path fill="#FBBC05" d="M6.54 14.1a5.86 5.86 0 0 1 0-3.75V7.82H3.3a9.77 9.77 0 0 0 0 8.81l3.24-2.53z"/>
        <path fill="#EA4335" d="M12 6.32c1.43 0 2.71.49 3.72 1.45l2.79-2.79C16.84 3.42 14.63 2.55 12 2.55a9.75 9.75 0 0 0-8.7 5.27l3.24 2.53C7.31 8.04 9.46 6.32 12 6.32z"/>
    </svg>
    Continue with Google
</a>
