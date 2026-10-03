<footer class="bg-[#0a1f16] text-ivory">
    <div class="mx-auto max-w-[1200px] px-5 py-8 lg:px-6">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-[0.9fr_1fr_1fr_1fr] lg:gap-0">
            <div class="lg:pr-6">
                <x-brand-logo tone="light" href="{{ url('/') }}" />
            </div>
            <div class="lg:border-l lg:border-white/15 lg:px-6">
                <h3 class="text-sm font-semibold">Sustainable Spaces</h3>
                <p class="mt-1 text-sm text-white/60">Eco-friendly materials and practices.</p>
            </div>
            <div class="lg:border-l lg:border-white/15 lg:px-6">
                <h3 class="text-sm font-semibold">Trusted Professionals</h3>
                <p class="mt-1 text-sm text-white/60">Verified designers and contractors.</p>
            </div>
            <div class="lg:border-l lg:border-white/15 lg:px-6">
                <h3 class="text-sm font-semibold">Beautiful Results</h3>
                <p class="mt-1 text-sm text-white/60">Homes that feel calm, stylish and lasting.</p>
            </div>
        </div>

        <div class="mt-7 flex flex-col gap-4 border-t border-white/10 pt-5 text-xs text-white/55 sm:flex-row sm:items-center sm:justify-between">
            <p>© 2026 RenovaHub. Colombo, Sri Lanka.</p>
            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('privacy') }}" class="transition hover:text-white">Privacy Policy</a>
                <a href="{{ route('terms.public') }}" class="transition hover:text-white">Terms of Service</a>
                <div class="flex items-center gap-2 text-white/80">
                    <a href="https://www.instagram.com" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/20 transition hover:border-white/50" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="4" y="4" width="16" height="16" rx="4"/><circle cx="12" cy="12" r="3.5"/><circle cx="17.5" cy="6.5" r="0.8" fill="currentColor" stroke="none"/></svg>
                    </a>
                    <a href="https://www.linkedin.com" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/20 transition hover:border-white/50" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="currentColor"><path d="M6.5 9H4V20h2.5V9ZM5.2 4A1.5 1.5 0 1 0 5.2 7a1.5 1.5 0 0 0 0-3ZM20 20h-2.5v-5.6c0-1.6-.6-2.6-2-2.6s-1.7.9-2 1.8c-.1.2-.1.6-.1.9V20H11V9h2.4v1.5c.4-.7 1.5-1.8 3.4-1.8 2.4 0 4.2 1.6 4.2 5V20Z"/></svg>
                    </a>
                    <a href="https://x.com" class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/20 transition hover:border-white/50" aria-label="X" target="_blank" rel="noopener noreferrer">
                        <svg viewBox="0 0 24 24" class="h-3.5 w-3.5" fill="currentColor"><path d="M14.7 10.3 21.2 3h-1.6l-5.6 6.4L9.4 3H3.6l6.8 9.8L3.4 21h1.6l6-6.8L14.8 21h5.8l-7-10.7Zm-2.1 2.4-.7-1L6.2 4.2h2.4l4.5 6.4.7 1 5.8 8.2h-2.4l-4.6-6.1Z"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
