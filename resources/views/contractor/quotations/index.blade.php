<x-contractor-layout title="Quotations">
    @php $type = request('type') === 'firms' ? 'firms' : 'suppliers'; @endphp
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="rh-serif text-3xl text-[#123D2B]">Quotations</h1>
            <p class="mt-2 text-sm text-[#66756C]">Supplier prices and construction firm quotations. The homeowner approves the option you send.</p>
        </div>
    </div>
    <div class="mt-4 flex gap-4 border-b border-[#ece7dc]">
        <a href="{{ route('contractor.quotations.index', ['type' => 'suppliers']) }}" class="border-b-2 px-1 py-2 text-sm {{ $type === 'suppliers' ? 'border-[#123D2B] font-medium text-[#123D2B]' : 'border-transparent text-[#66756C]' }}">Supplier Quotations</a>
        <a href="{{ route('contractor.quotations.index', ['type' => 'firms']) }}" class="border-b-2 px-1 py-2 text-sm {{ $type === 'firms' ? 'border-[#123D2B] font-medium text-[#123D2B]' : 'border-transparent text-[#66756C]' }}">Construction Firm Quotations</a>
    </div>
    @php
        $rows = $type === 'firms' ? $firmQuotes : $prices;
        $awaiting = $rows->whereIn('status', ['proposed', 'offered', 'received'])->count();
        $approved = $rows->where('status', 'approved')->count() + $rows->where('status', 'accepted')->count();
        $rejected = $rows->where('status', 'rejected')->count();
    @endphp
    <div class="mt-4 grid gap-3 sm:grid-cols-4">
        <article class="rounded-2xl border border-[#ece7dc] bg-white p-4"><p class="text-xs text-[#66756C]">Total</p><p class="mt-1 text-xl font-semibold">{{ $rows->count() }}</p></article>
        <article class="rounded-2xl border border-[#ece7dc] bg-white p-4"><p class="text-xs text-[#66756C]">Awaiting approval</p><p class="mt-1 text-xl font-semibold">{{ $awaiting }}</p></article>
        <article class="rounded-2xl border border-[#ece7dc] bg-white p-4"><p class="text-xs text-[#66756C]">Approved</p><p class="mt-1 text-xl font-semibold">{{ $approved }}</p></article>
        <article class="rounded-2xl border border-[#ece7dc] bg-white p-4"><p class="text-xs text-[#66756C]">Rejected</p><p class="mt-1 text-xl font-semibold">{{ $rejected }}</p></article>
    </div>
    <div class="mt-4 overflow-x-auto rounded-2xl border border-[#ece7dc] bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#F6F1E7] text-xs uppercase tracking-wide text-[#66756C]">
                <tr><th class="px-4 py-3">Project</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Supplier / firm</th><th class="px-4 py-3">Amount</th><th class="px-4 py-3">Duration</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr>
            </thead>
            <tbody>
                @if ($type === 'suppliers')
                    @forelse ($prices as $price)
                        <tr class="border-t border-[#ece7dc]">
                            <td class="px-4 py-3">{{ $price->request->project->name }}</td>
                            <td class="px-4 py-3">Supplier</td>
                            <td class="px-4 py-3 text-[#123D2B]">{{ $price->supplier->name }}</td>
                            <td class="px-4 py-3">{{ $price->money() }}</td>
                            <td class="px-4 py-3">{{ $price->lead_time_days }} days</td>
                            <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $price->status)) }}</td>
                            <td class="px-4 py-3"><a class="underline" href="{{ route('contractor.procurement.suppliers', $price->request->project) }}">Compare</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-[#66756C]">No supplier quotations yet. <a class="underline" href="{{ route('contractor.suppliers.index') }}">Find Suppliers</a></td></tr>
                    @endforelse
                @else
                    @forelse ($firmQuotes as $quote)
                        <tr class="border-t border-[#ece7dc]">
                            <td class="px-4 py-3">{{ $quote->project->name }}</td>
                            <td class="px-4 py-3">Construction firm</td>
                            <td class="px-4 py-3 text-[#123D2B]">{{ $quote->firm->name }}</td>
                            <td class="px-4 py-3">{{ $quote->money() }}</td>
                            <td class="px-4 py-3">{{ $quote->duration_days }} days</td>
                            <td class="px-4 py-3">{{ $quote->statusLabel() }}</td>
                            <td class="px-4 py-3">
                                <a class="underline" href="{{ route('contractor.procurement.firms', $quote->project) }}">Compare</a>
                                @if ($quote->status === \App\Models\ConstructionFirmQuotation::STATUS_APPROVED)
                                    <form method="POST" action="{{ route('contractor.firms.assign', [$quote->project, $quote]) }}" class="inline">@csrf<button class="ml-3 underline">Assign</button></form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-8 text-[#66756C]">No construction firm quotations yet. <a class="underline" href="{{ route('contractor.firms.index') }}">Find firms</a></td></tr>
                    @endforelse
                @endif
            </tbody>
        </table>
    </div>
</x-contractor-layout>
