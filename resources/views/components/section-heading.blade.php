@props(['eyebrow' => null])

<div {{ $attributes }}>
    @if ($eyebrow)
        <p class="text-[11px] font-medium uppercase tracking-[0.22em] text-olive">{{ $eyebrow }}</p>
    @endif
    <h2 class="font-serif text-[2.15rem] font-medium leading-[1.12] tracking-[-0.02em] text-forest sm:text-5xl {{ $eyebrow ? 'mt-3' : '' }}">
        {{ $slot }}
    </h2>
</div>
