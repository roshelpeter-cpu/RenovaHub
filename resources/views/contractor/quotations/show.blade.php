<x-contractor-layout :title="$quotation->number">
    <p class="text-sm text-[#66756C]">{{ $quotation->project?->name }}</p>
    <h1 class="rh-serif mt-1 text-4xl text-[#123D2B]">{{ $quotation->number }}</h1>
    <p class="mt-2 text-sm text-[#66756C]">{{ $quotation->description }}</p>
    <p class="mt-3 inline-flex rounded-full bg-[#F6F1E7] px-3 py-1 text-sm text-[#123D2B]">{{ $quotation->contractorStatusLabel() }}</p>
    <div class="mt-6 overflow-x-auto rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#F6F1E7] text-xs uppercase tracking-wide text-[#66756C]"><tr><th class="px-4 py-3">Item</th><th class="px-4 py-3">Qty</th><th class="px-4 py-3">Unit</th><th class="px-4 py-3">Line total</th></tr></thead>
            <tbody>
                @foreach ($quotation->items as $item)
                    <tr class="border-t border-[#ece7dc]">
                        <td class="px-4 py-3 text-[#123D2B]">{{ $item->item }}<div class="text-xs text-[#66756C]">{{ $item->description }}</div></td>
                        <td class="px-4 py-3">{{ $item->quantity }}</td>
                        <td class="px-4 py-3">{{ $quotation->money($item->unit_cost) }}</td>
                        <td class="px-4 py-3">{{ $quotation->money($item->total) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p class="rh-serif mt-4 text-3xl text-[#123D2B]">{{ $quotation->money($quotation->total) }}</p>
    <div class="mt-4 flex gap-2">
        @can('prepare', $quotation)
            <a href="{{ route('contractor.quotations.edit', $quotation) }}" class="rounded-full border border-[#c9c2b4] px-4 py-2 text-sm text-[#123D2B]">Edit draft</a>
            <form method="POST" action="{{ route('contractor.quotations.submit', $quotation) }}">@csrf<button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Submit</button></form>
        @endcan
    </div>
</x-contractor-layout>
