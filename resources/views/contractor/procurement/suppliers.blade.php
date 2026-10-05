<x-contractor-layout title="Compare suppliers">
    <h1 class="rh-serif text-3xl text-[#123D2B]">Supplier comparison</h1>
    <p class="mt-2 text-sm text-[#66756C]">{{ $project->name }}. Send the selected prices to the homeowner. You cannot approve a supplier yourself.</p>
    @if ($prices->count() < 2)
        <p class="mt-6 rounded-2xl border border-[#ece7dc] bg-white p-6 text-sm text-[#66756C]">No supplier quotations yet. Request prices from suppliers using your approved material requirements. <a href="{{ route('contractor.suppliers.index') }}" class="underline">Find Suppliers</a></p>
    @else
        <form method="POST" action="{{ route('contractor.procurement.suppliers.send', $project) }}" class="mt-6">
            @csrf
            <div class="grid gap-4 lg:grid-cols-3">
                @foreach ($prices as $price)
                    <label class="block rounded-2xl border border-[#ece7dc] bg-white p-5 shadow-sm">
                        <span class="flex items-center justify-between gap-3">
                            <span class="rh-serif text-xl text-[#123D2B]">{{ $price->supplier->name }}</span>
                            <input type="checkbox" name="prices[]" value="{{ $price->id }}" class="h-4 w-4" checked>
                        </span>
                        <span class="mt-3 block text-2xl font-semibold text-[#123D2B]">{{ $price->money() }}</span>
                        <dl class="mt-4 space-y-2 text-sm text-[#66756C]">
                            <div class="flex justify-between"><dt>Delivery</dt><dd>{{ $price->lead_time_days }} days · {{ $price->delivery }}</dd></div>
                            <div class="flex justify-between"><dt>Warranty</dt><dd>{{ $price->warranty ?: '—' }}</dd></div>
                            <div class="flex justify-between"><dt>Rating</dt><dd>{{ $price->supplier->rating }}</dd></div>
                            <div class="flex justify-between"><dt>Valid until</dt><dd>{{ $price->valid_until?->format('j M Y') ?? '—' }}</dd></div>
                        </dl>
                    </label>
                @endforeach
            </div>
            <button class="mt-5 rounded-xl bg-[#123D2B] px-5 py-2.5 text-sm text-white">Send Selected Option to Homeowner</button>
        </form>
    @endif
</x-contractor-layout>
