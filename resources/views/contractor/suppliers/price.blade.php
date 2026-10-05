<x-contractor-layout title="Supplier Price">
    @php $requestModel = $price->request; @endphp
    <h1 class="rh-serif text-4xl text-[#123D2B]">Supplier Price Received</h1>
    <p class="mt-2 text-sm text-[#66756C]">This is the supplier's price to you. It is not your project quotation for the homeowner.</p>
    <article class="mt-6 max-w-3xl rounded-[1.4rem] border border-[#ece7dc] bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-xl text-[#123D2B]">{{ $price->supplier?->name }}</h2>
                <p class="text-sm text-[#66756C]">{{ number_format((float) $price->supplier?->rating, 1) }} · {{ $price->supplier?->review_count }} reviews</p>
            </div>
            <a href="{{ route('contractor.suppliers.show', $price->supplier) }}" class="text-sm text-[#123D2B] underline">Contact supplier</a>
        </div>
        <dl class="mt-4 grid gap-2 text-sm sm:grid-cols-2">
            <div><dt class="text-xs text-[#66756C]">Requested item</dt><dd class="text-[#123D2B]">{{ $requestModel->product }}</dd></div>
            <div><dt class="text-xs text-[#66756C]">Quantity</dt><dd class="text-[#123D2B]">{{ $requestModel->quantityLabel() }}</dd></div>
            <div><dt class="text-xs text-[#66756C]">Project</dt><dd class="text-[#123D2B]">{{ $requestModel->project?->name }}</dd></div>
            <div><dt class="text-xs text-[#66756C]">Material</dt><dd class="text-[#123D2B]">{{ $requestModel->material }}</dd></div>
        </dl>
        <div class="mt-5 rounded-2xl bg-[#F7F4EE] p-4">
            <p class="text-xs uppercase tracking-wide text-[#66756C]">Supplier quotation</p>
            <p class="rh-serif mt-1 text-3xl text-[#123D2B]">{{ $price->money() }}</p>
            <dl class="mt-3 grid gap-2 text-sm sm:grid-cols-2">
                <div><dt class="text-[#66756C]">Lead time</dt><dd>{{ $price->lead_time_days }} days</dd></div>
                <div><dt class="text-[#66756C]">Delivery</dt><dd>{{ $price->delivery ?: '—' }}</dd></div>
                <div><dt class="text-[#66756C]">Warranty</dt><dd>{{ $price->warranty ?: '—' }}</dd></div>
                <div><dt class="text-[#66756C]">Valid until</dt><dd>{{ $price->valid_until?->format('j M Y') ?? '—' }}</dd></div>
            </dl>
        </div>
        @if ($price->message)
            <p class="mt-4 text-sm text-[#66756C]">{{ $price->message }}</p>
        @endif
        @if ($price->canOrder())
            <div class="mt-5 flex flex-wrap gap-2">
                <form method="POST" action="{{ route('contractor.prices.revision', $price) }}" class="flex flex-1 gap-2">
                    @csrf
                    <input name="note" placeholder="What should change?" class="min-w-0 flex-1 rounded-full border border-[#ddd6c8] px-4 py-2 text-sm">
                    <button class="rounded-full border border-[#c9c2b4] px-4 py-2 text-sm text-[#123D2B]">Request Revision</button>
                </form>
                <form method="POST" action="{{ route('contractor.orders.store', $price) }}">
                    @csrf
                    <input type="hidden" name="delivery_address" value="{{ $requestModel->project?->address }}">
                    <button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Place Order</button>
                </form>
            </div>
        @else
            <p class="mt-4 text-sm text-[#66756C]">This price is {{ str_replace('_', ' ', $price->status) }}.</p>
        @endif
    </article>
</x-contractor-layout>
