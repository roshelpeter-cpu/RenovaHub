<div class="rounded-3xl border border-dashed border-olive/40 bg-white/70 px-6 py-12 text-center">
    <p class="font-serif text-2xl text-forest">{{ $title }}</p>
    <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-mist">{{ $body }}</p>
    @if (! empty($action))
        <a href="{{ $action['url'] }}" class="mt-5 inline-flex rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory transition duration-300 hover:bg-leaf">{{ $action['label'] }}</a>
    @endif
</div>
