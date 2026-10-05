<x-contractor-layout :title="$supplier->name">
    <section class="overflow-hidden rounded-[1.6rem] border border-[#ece7dc] bg-white shadow-sm">
        <div class="relative h-64">
            <img src="{{ $supplier->heroUrl() }}" alt="" class="h-full w-full object-cover">
        </div>
        <div class="flex flex-col gap-4 p-6 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex items-center gap-4">
                <span class="flex h-20 w-20 items-center justify-center rounded-full bg-[#123D2B] text-xl font-semibold text-white">{{ $supplier->initials() }}</span>
                <div>
                    <h1 class="rh-serif text-4xl text-[#123D2B]">{{ $supplier->name }}</h1>
                    <p class="text-sm text-[#66756C]">{{ $supplier->categories->pluck('name')->join(' · ') }}</p>
                    <p class="mt-1 text-sm text-[#66756C]">{{ number_format((float) $supplier->rating, 1) }} · {{ $supplier->review_count }} reviews · {{ $supplier->ordersLabel() }} · {{ $supplier->experienceLabel() }}</p>
                    <p class="text-sm text-[#66756C]">{{ $supplier->location }} · {{ $supplier->service_area }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('contractor.suppliers.request-price', $supplier) }}" class="rounded-full border border-[#c9c2b4] px-4 py-2 text-sm text-[#123D2B]">Request Price</a>
                <a href="{{ route('contractor.orders.index') }}" class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Place Order</a>
            </div>
        </div>
    </section>
    <div class="mt-6 grid gap-4 lg:grid-cols-[1.2fr_0.8fr]">
        <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="rh-serif text-2xl text-[#123D2B]">About Us</h2>
            <p class="mt-3 text-sm leading-relaxed text-[#66756C]">{{ $supplier->about }}</p>
            <h2 class="rh-serif mt-6 text-2xl text-[#123D2B]">Categories</h2>
            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($supplier->categories as $category)
                    <span class="rounded-full bg-[#F6F1E7] px-3 py-1 text-sm text-[#123D2B]">{{ $category->name }}</span>
                @endforeach
            </div>
            <h2 class="rh-serif mt-6 text-2xl text-[#123D2B]">Our Work</h2>
            <div class="mt-3 grid grid-cols-3 gap-2">
                @foreach (array_slice($supplier->portfolio_images ?? [], 0, 6) as $image)
                    <img src="{{ asset($image) }}" alt="" class="h-28 w-full rounded-xl object-cover">
                @endforeach
            </div>
        </article>
        <div class="space-y-4">
            <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
                <h2 class="rh-serif text-2xl text-[#123D2B]">Average Starting Prices</h2>
                <ul class="mt-3 space-y-2 text-sm">
                    @foreach ($supplier->startingPrices as $price)
                        <li class="flex justify-between gap-3"><span class="text-[#66756C]">{{ $price->label }}</span><span class="text-[#123D2B]">{{ $price->label() }}</span></li>
                    @endforeach
                </ul>
            </article>
            <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
                <h2 class="rh-serif text-2xl text-[#123D2B]">Why Choose Us</h2>
                <ul class="mt-3 list-disc space-y-1 pl-5 text-sm text-[#66756C]">
                    @foreach ($supplier->why_choose ?? [] as $reason)
                        <li>{{ $reason }}</li>
                    @endforeach
                </ul>
            </article>
            <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
                <h2 class="rh-serif text-2xl text-[#123D2B]">Reviews</h2>
                @foreach ($supplier->reviews as $review)
                    <p class="mt-3 text-sm text-[#123D2B]">{{ $review->author_name }} · {{ $review->rating }}</p>
                    <p class="text-sm text-[#66756C]">{{ $review->body }}</p>
                @endforeach
            </article>
        </div>
    </div>
</x-contractor-layout>
