<x-homeowner-layout :title="$payment->reference">
    <article class="mt-4 max-w-xl rounded-3xl border border-[#ece7dc] bg-white p-6 shadow-sm">
        <p class="text-xs uppercase tracking-[0.14em] text-olive">{{ $payment->statusLabel() }}</p>
        <h1 class="mt-2 font-serif text-3xl text-forest">{{ $payment->reference }}</h1>
        <dl class="mt-4 space-y-1 text-sm text-[#66756C]">
            <div class="flex justify-between"><dt>Renovation amount</dt><dd>{{ $payment->formatMoney($payment->renovationAmount()) }}</dd></div>
            <div class="flex justify-between"><dt>RenovaHub service fee</dt><dd>{{ $payment->formatMoney($payment->platformFee()) }}</dd></div>
            <div class="flex justify-between font-medium text-[#123D2B]"><dt>Homeowner total</dt><dd>{{ $payment->money() }}</dd></div>
        </dl>
        <p class="mt-2 text-sm text-mist">{{ $project->name }}</p>
        @if ($payment->notes)<p class="mt-3 text-sm text-charcoal">{{ $payment->notes }}</p>@endif
        @can('pay', $payment)
            <a href="{{ route('homeowner.payments.pay', [$project, $payment]) }}" class="mt-5 inline-flex rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory">Pay Now</a>
        @endcan
    </article>
</x-homeowner-layout>
