<x-contractor-layout :title="$earning->label">
    <h1 class="rh-serif text-4xl text-[#123D2B]">{{ $earning->label }}</h1>
    <p class="mt-2 text-sm text-[#66756C]">{{ $earning->project?->name }} · {{ $earning->statusLabel() }}</p>
    <dl class="mt-6 max-w-xl space-y-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 text-sm shadow-sm">
        <div class="flex justify-between"><dt>Gross project payment</dt><dd>{{ $earning->money((float) $earning->gross_amount) }}</dd></div>
        <div class="flex justify-between"><dt>RenovaHub service fee</dt><dd>{{ rtrim(rtrim(number_format((float) $earning->fee_percent, 2), '0'), '.') }}% = {{ $earning->money((float) $earning->fee_amount) }}</dd></div>
        <div class="flex justify-between font-medium text-[#123D2B]"><dt>Net contractor amount</dt><dd>{{ $earning->money((float) $earning->net_amount) }}</dd></div>
    </dl>
    <p class="mt-4 max-w-xl text-sm text-[#66756C]">{{ $earning->notes }}</p>
</x-contractor-layout>
