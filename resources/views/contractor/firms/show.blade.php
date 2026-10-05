<x-contractor-layout :title="$firm->name">
    <div class="overflow-hidden rounded-2xl border border-[#ece7dc] bg-white shadow-sm">
        <img src="{{ asset(($firm->portfolio[0] ?? 'images/renova/about-exterior.jpg')) }}" alt="" class="h-56 w-full object-cover">
        <div class="flex flex-wrap items-center justify-between gap-4 p-5">
            <div class="flex items-center gap-4">
                <span class="flex h-16 w-16 items-center justify-center rounded-full bg-[#123D2B] text-lg text-white">{{ $firm->initials() }}</span>
                <div>
                    <h1 class="rh-serif text-3xl text-[#123D2B]">{{ $firm->name }}</h1>
                    <p class="mt-1 text-sm text-[#66756C]">{{ $firm->specialisation }} · {{ $firm->rating }} · {{ $firm->review_count }} reviews · {{ $firm->years_experience }}+ years</p>
                    <p class="text-sm text-[#66756C]">{{ $firm->location }}</p>
                </div>
            </div>
            <a href="{{ route('contractor.firms.quote', $firm) }}" class="rounded-xl bg-[#123D2B] px-4 py-2 text-sm text-white">Request Project Quotation</a>
        </div>
    </div>
    <div class="mt-5 grid gap-4 lg:grid-cols-[1.2fr_0.8fr]">
        <article class="rounded-2xl border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="rh-serif text-2xl text-[#123D2B]">About</h2>
            <p class="mt-3 text-sm leading-relaxed text-[#66756C]">{{ $firm->about }}</p>
            <div class="mt-4 grid grid-cols-3 gap-2">
                @foreach ($firm->portfolio ?? [] as $image)
                    <img src="{{ asset($image) }}" alt="" class="h-24 w-full rounded-xl object-cover">
                @endforeach
            </div>
        </article>
        <article class="rounded-2xl border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="rh-serif text-2xl text-[#123D2B]">Why this firm</h2>
            <ul class="mt-3 space-y-2 text-sm text-[#66756C]">
                @foreach ($firm->highlights ?? [] as $line)
                    <li>{{ $line }}</li>
                @endforeach
            </ul>
            <p class="mt-4 text-sm text-[#123D2B]">Starting from {{ $firm->money() }}</p>
        </article>
    </div>
</x-contractor-layout>
