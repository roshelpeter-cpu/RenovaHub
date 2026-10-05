<x-homeowner-layout :title="$payment->reference">
    <article class="mt-4 max-w-xl rounded-3xl border border-[#ece7dc] bg-white p-6 shadow-sm">
        <p class="text-xs uppercase tracking-[0.14em] text-olive">{{ $payment->status === \App\Models\Payment::STATUS_PAID ? 'Paid' : $payment->statusLabel() }}</p>
        <h1 class="mt-2 font-serif text-3xl text-forest">{{ $payment->reference }}</h1>
        <dl class="mt-4 space-y-1 text-sm text-[#66756C]">
            <div class="flex justify-between"><dt>Project amount</dt><dd>{{ $payment->formatMoney($payment->renovationAmount()) }}</dd></div>
            <div class="flex justify-between"><dt>RenovaHub Service Charge</dt><dd>{{ $payment->formatMoney($payment->platformFee()) }}</dd></div>
            <div class="flex justify-between font-medium text-[#123D2B]"><dt>Total</dt><dd>{{ $payment->money() }}</dd></div>
        </dl>
        <p class="mt-2 text-sm text-mist">{{ $project->name }}</p>
        <div class="mt-5 flex flex-wrap gap-3">
            @if ($payment->status === \App\Models\Payment::STATUS_PAID)
                <button type="button" class="rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory" data-receipt-open="payment-{{ $payment->id }}">View Receipt</button>
            @endif
            @can('pay', $payment)
                <a href="{{ route('homeowner.payments.pay', [$project, $payment]) }}" class="rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory">Pay Now</a>
            @endcan
        </div>
    </article>
    <x-payment-receipt :receipts="$receipts" />
</x-homeowner-layout>
