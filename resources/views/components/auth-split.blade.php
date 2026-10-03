@props([
    'image',
    'imageCaption',
    'imageAlt' => 'Modern architectural home',
])

<div class="min-h-screen bg-cream font-outfit text-charcoal lg:grid lg:grid-cols-2">
    <aside class="relative h-[38vh] min-h-[240px] lg:sticky lg:top-0 lg:h-screen lg:min-h-screen">
        <img src="{{ $image }}" alt="{{ $imageAlt }}" class="absolute inset-0 h-full w-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-charcoal/75 via-charcoal/20 to-charcoal/25"></div>
        <div class="relative flex h-full flex-col justify-end p-7 sm:p-10 lg:p-14">
            <p class="max-w-md font-serif text-[2.35rem] font-medium leading-[1.05] tracking-[-0.03em] text-white sm:text-5xl lg:text-[3.35rem]">
                {{ $imageTitle }}
            </p>
            <p class="mt-4 max-w-xs text-sm leading-relaxed text-white/80">{{ $imageCaption }}</p>
        </div>
    </aside>

    <section class="flex flex-col px-5 py-6 sm:px-10 lg:px-14 lg:py-8">
        <div class="flex items-center justify-between gap-4">
            <x-brand-logo />
            <div class="text-right text-[13px] text-mist">
                {{ $header ?? '' }}
            </div>
        </div>

        <div class="mx-auto flex w-full max-w-[440px] flex-1 flex-col justify-center py-8 sm:py-12">
            {{ $slot }}
        </div>
    </section>
</div>
