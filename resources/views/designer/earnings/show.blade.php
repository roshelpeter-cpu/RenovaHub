<x-designer-layout title="Earning">
    <p class="text-sm text-[#66756C]"><a href="{{ route('designer.earnings.index') }}" class="hover:text-[#123D2B]">Earnings</a> › {{ $earning->reference }}</p>
    <h1 class="mt-2 font-serif text-4xl text-[#123D2B]">{{ $earning->project->name }}</h1>
    <p class="mt-2 text-sm text-[#66756C]">{{ $earning->notes }}</p>
    <dl class="mt-6 grid gap-4 rounded-[1.4rem] border border-[#ece7dc] bg-white p-6 shadow-sm sm:grid-cols-2">
        <div><dt class="text-xs text-[#66756C]">Gross professional amount</dt><dd class="font-serif text-2xl text-[#123D2B]">{{ $earning->money((float) $earning->gross_amount) }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">RenovaHub professional service fee</dt><dd class="text-[#123D2B]">{{ rtrim(rtrim(number_format((float) $earning->fee_percent, 2), '0'), '.') }}%</dd></div>
        <div><dt class="text-xs text-[#66756C]">Fee</dt><dd class="text-[#123D2B]">{{ $earning->money((float) $earning->fee_amount) }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">Designer net earnings</dt><dd class="font-serif text-2xl text-[#123D2B]">{{ $earning->money((float) $earning->net_amount) }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">Payment status</dt><dd>{{ $earning->statusLabel() }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">Transaction reference</dt><dd>{{ $earning->reference }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">Date</dt><dd>{{ $earning->recorded_on?->format('j M Y') }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">Payment</dt><dd>{{ $earning->label }}</dd></div>
    </dl>
</x-designer-layout>
