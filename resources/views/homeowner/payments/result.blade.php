<x-homeowner-layout title="Payment">
    <article class="mx-auto mt-8 max-w-lg rounded-[1.4rem] border border-[#ece7dc] bg-white p-8 text-center shadow-sm">
        @if ($payment->status === \App\Models\Payment::STATUS_PAID)
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#00A86B]">RenovaHub</p>
            <h1 class="mt-3 font-serif text-4xl text-[#123D2B]">Payment Successful</h1>
            <p class="mt-3 text-sm text-[#66756C]">{{ $project->name }} · {{ $payment->reference }}</p>
            <p class="mt-2 text-2xl font-semibold text-[#00A86B]">{{ $payment->currency ?: 'LKR' }} {{ number_format($payment->homeownerTotal(), 2) }}</p>
            <button type="button" class="mt-6 rounded-full bg-[#123D2B] px-5 py-3 text-sm font-medium text-white" data-receipt-open="payment-{{ $payment->id }}">View Receipt</button>
        @else
            <h1 class="font-serif text-3xl text-[#123D2B]">Payment pending</h1>
            <p class="mt-3 text-sm text-[#66756C]">This payment is not complete yet. Status changes when the payment result is confirmed.</p>
            <a href="{{ route('homeowner.payments.pay', [$project, $payment]) }}" class="mt-6 inline-flex rounded-full bg-[#123D2B] px-5 py-3 text-sm font-medium text-white">Return to checkout</a>
        @endif
        <div class="mt-4">
            <a href="{{ route('homeowner.payments.index') }}" class="text-sm text-[#66756C] hover:text-[#123D2B]">Back to Payments</a>
        </div>
    </article>
    <x-payment-receipt :receipts="$receipts" />
</x-homeowner-layout>
