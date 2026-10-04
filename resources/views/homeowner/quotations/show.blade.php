<x-homeowner-layout :title="$quotation->number" :flush="true" :canvas="true">
    <x-project-context :project="$project" section="quotations">
    <div class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,1fr)_18rem]">
        <section class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm sm:p-6">
            <p class="text-xs uppercase tracking-[0.14em] text-olive">{{ $quotation->number }} · {{ $quotation->statusLabel() }}</p>
            <h1 class="mt-2 font-serif text-3xl text-forest">{{ $quotation->description }}</h1>
            <p class="mt-2 text-sm text-mist">{{ $quotation->contractor?->professionalProfile?->displayName() ?? $quotation->contractor?->name }} @if($quotation->valid_until) · Valid until {{ $quotation->valid_until->format('j M Y') }} @endif</p>
            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="text-xs uppercase tracking-[0.12em] text-mist"><tr><th class="py-2">Item</th><th>Description</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead>
                    <tbody>
                        @foreach ($quotation->items as $item)
                            <tr class="border-t border-[#ece7dc]"><td class="py-2 pr-3">{{ $item->item }}</td><td class="pr-3">{{ $item->description }}</td><td class="pr-3">{{ $item->quantity }}</td><td class="pr-3">{{ $quotation->money($item->unit_cost) }}</td><td>{{ $quotation->money($item->total) }}</td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <dl class="mt-5 space-y-1 text-sm">
                <div class="flex justify-between"><dt>Materials</dt><dd>{{ $quotation->money($quotation->materials) }}</dd></div>
                <div class="flex justify-between"><dt>Labour</dt><dd>{{ $quotation->money($quotation->labour) }}</dd></div>
                <div class="flex justify-between"><dt>Additional</dt><dd>{{ $quotation->money($quotation->additional_costs) }}</dd></div>
                <div class="flex justify-between"><dt>Discount</dt><dd>{{ $quotation->money($quotation->discount) }}</dd></div>
                <div class="flex justify-between font-medium text-forest"><dt>Total</dt><dd>{{ $quotation->money($quotation->total) }}</dd></div>
            </dl>
            @if ($quotation->notes)<p class="mt-4 text-sm text-mist">{{ $quotation->notes }}</p>@endif
        </section>
        @can('update', $quotation)
            <form method="POST" action="{{ route('homeowner.quotations.decide', [$project, $quotation]) }}" class="h-fit rounded-3xl border border-[#ece7dc] bg-[#F6F1E7] p-5">
                @csrf
                <h2 class="font-serif text-xl text-forest">Your decision</h2>
                <label for="notes" class="mt-3 block text-sm">Note</label>
                <textarea id="notes" name="notes" rows="4" class="mt-1 w-full rounded-2xl border border-line px-3 py-2 text-sm"></textarea>
                <div class="mt-3 flex flex-col gap-2">
                    <button name="decision" value="approved" class="rounded-full bg-forest px-4 py-2 text-sm text-ivory">Approve</button>
                    <button name="decision" value="clarification_required" class="rounded-full border border-forest px-4 py-2 text-sm text-forest">Request Clarification</button>
                    <button name="decision" value="rejected" class="rounded-full border border-red-200 px-4 py-2 text-sm text-red-700">Reject</button>
                </div>
            </form>
        @endcan
    </div>
    </x-project-context>
</x-homeowner-layout>
