<div>
    <div class="grid gap-3 lg:grid-cols-[1.4fr_12rem_12rem_12rem]">
        <label class="relative block">
            <span class="sr-only">Search suppliers</span>
            <input wire:model.live.debounce.300ms="search" type="search" placeholder="Search suppliers, products or categories" class="w-full rounded-full border border-[#ddd6c8] bg-white py-3 pl-4 pr-4 text-sm outline-none focus:border-[#123D2B]">
        </label>
        <select wire:model.live="category" class="rounded-full border border-[#ddd6c8] bg-white px-4 py-3 text-sm">
            <option value="">All Categories</option>
            @foreach ($categories as $item)
                <option value="{{ $item->slug }}">{{ $item->name }}</option>
            @endforeach
        </select>
        <select wire:model.live="location" class="rounded-full border border-[#ddd6c8] bg-white px-4 py-3 text-sm">
            <option value="">Location</option>
            @foreach ($cities as $city)
                <option value="{{ $city }}">{{ $city }}</option>
            @endforeach
        </select>
        <select wire:model.live="sort" class="rounded-full border border-[#ddd6c8] bg-white px-4 py-3 text-sm">
            <option value="relevant">Most Relevant</option>
            <option value="rating">Highest rated</option>
            <option value="orders">Most orders</option>
        </select>
    </div>
    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($suppliers as $supplier)
            <article class="overflow-hidden rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
                <div class="grid grid-cols-3 gap-1 p-3">
                    @foreach (array_slice($supplier->portfolio_images ?? [], 0, 3) as $image)
                        <img src="{{ asset($image) }}" alt="" class="h-20 w-full rounded-xl object-cover">
                    @endforeach
                </div>
                <div class="px-4 pb-4">
                    <div class="flex items-center gap-3">
                        @if ($supplier->logoUrl())
                            <img src="{{ $supplier->logoUrl() }}" alt="" class="h-12 w-12 rounded-full object-cover">
                        @else
                            <span class="flex h-12 w-12 items-center justify-center rounded-full bg-[#123D2B] text-sm font-semibold text-white">{{ $supplier->initials() }}</span>
                        @endif
                        <div>
                            <h2 class="text-lg font-medium text-[#123D2B]">{{ $supplier->name }}</h2>
                            <p class="text-xs text-[#66756C]">{{ number_format((float) $supplier->rating, 1) }} · {{ $supplier->review_count }} reviews</p>
                            <p class="text-xs text-[#66756C]">{{ $supplier->location }}</p>
                        </div>
                    </div>
                    <p class="mt-3 text-xs text-[#66756C]">{{ $supplier->categories->pluck('name')->join(' · ') }}</p>
                    <p class="mt-3 text-xs font-medium uppercase tracking-wide text-[#66756C]">Average starting prices</p>
                    <ul class="mt-1 space-y-1 text-sm text-[#123D2B]">
                        @foreach ($supplier->startingPrices->take(3) as $price)
                            <li class="flex justify-between gap-2"><span class="text-[#66756C]">{{ $price->label }}</span><span>{{ $price->label() }}</span></li>
                        @endforeach
                    </ul>
                    <p class="mt-3 text-xs text-[#66756C]">{{ $supplier->experienceLabel() }} · {{ $supplier->ordersLabel() }}</p>
                    <a href="{{ route('contractor.suppliers.show', $supplier) }}" class="mt-4 inline-flex rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">View Supplier</a>
                </div>
            </article>
        @empty
            <p class="text-sm text-[#66756C]">No suppliers match those filters.</p>
        @endforelse
    </div>
</div>
