<x-designer-layout title="Earning">
    <p class="text-sm text-[#66756C]"><a href="{{ route('designer.earnings.index') }}" class="hover:text-[#123D2B]">Earnings</a> › {{ $earning->reference }}</p>
    <h1 class="mt-2 font-serif text-4xl text-[#123D2B]">{{ $earning->project->name }}</h1>
    <dl class="mt-6 grid gap-4 rounded-[1.4rem] border border-[#ece7dc] bg-white p-6 shadow-sm sm:grid-cols-2">
        <div><dt class="text-xs text-[#66756C]">Gross Designer Fee</dt><dd class="font-serif text-2xl text-[#123D2B]">{{ $earning->money((float) $earning->gross_amount) }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">RenovaHub Service Charge</dt><dd class="text-[#123D2B]">{{ $earning->money((float) $earning->fee_amount) }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">Net Designer Earnings</dt><dd class="font-serif text-2xl text-[#123D2B]">{{ $earning->money((float) $earning->net_amount) }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">Payment Status</dt><dd>{{ $earning->statusLabel() }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">Transaction reference</dt><dd>{{ $earning->reference }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">Date</dt><dd>{{ $earning->recorded_on?->format('j M Y') }}</dd></div>
    </dl>
    @if ($earning->status === \App\Models\DesignerEarning::STATUS_RECORDED)
        <button type="button" class="mt-5 rounded-full bg-[#123D2B] px-5 py-3 text-sm font-medium text-white" data-receipt-open="earning-{{ $earning->id }}">View</button>
    @endif
    <x-payment-receipt :receipts="$receipts" />
</x-designer-layout>
