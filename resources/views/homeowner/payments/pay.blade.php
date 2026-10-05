<x-homeowner-layout title="Project Payment">
    <article class="mx-auto mt-6 max-w-lg rounded-[1.4rem] border border-[#ece7dc] bg-white p-6 shadow-sm sm:p-8">
        <div class="flex items-center gap-2 text-[#123D2B]">
            <svg viewBox="0 0 32 32" class="h-7 w-7" fill="currentColor" aria-hidden="true"><path d="M16 3.2 3.4 14.1a1.2 1.2 0 0 0-.4.9V27.2A2.3 2.3 0 0 0 5.3 29.5h6.2v-8.1c0-.7.6-1.3 1.3-1.3h6.4c.7 0 1.3.6 1.3 1.3v8.1h6.2a2.3 2.3 0 0 0 2.3-2.3V15a1.2 1.2 0 0 0-.4-.9L16 3.2Z"/></svg>
            <span class="text-lg font-semibold">RenovaHub</span>
        </div>
        <h1 class="mt-4 font-serif text-3xl text-[#123D2B]">Project Payment</h1>
        @if (session('cancelled'))
            <p class="mt-3 rounded-2xl bg-[#F6F1E7] px-4 py-3 text-sm text-[#123D2B]">Checkout was cancelled. The payment is still pending.</p>
        @endif
        <dl class="mt-5 space-y-3 text-sm">
            <div class="flex justify-between gap-4"><dt class="text-[#66756C]">Project</dt><dd class="text-right font-medium text-[#123D2B]">{{ $project->name }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-[#66756C]">Amount</dt><dd class="text-right text-lg font-semibold text-[#00A86B]">{{ $payment->currency ?: 'LKR' }} {{ number_format($payment->renovationAmount(), 2) }}</dd></div>
        </dl>
        <h2 class="mt-6 text-xs font-semibold uppercase tracking-[0.14em] text-[#66756C]">Payment Summary</h2>
        <dl class="mt-3 space-y-2 border-t border-[#ece7dc] pt-3 text-sm">
            <div class="flex justify-between gap-4"><dt class="text-[#66756C]">Project Amount</dt><dd>{{ $payment->currency ?: 'LKR' }} {{ number_format($payment->renovationAmount(), 2) }}</dd></div>
            <div class="flex justify-between gap-4"><dt class="text-[#66756C]">RenovaHub Service Charge</dt><dd>{{ $payment->currency ?: 'LKR' }} {{ number_format($payment->platformFee(), 2) }}</dd></div>
            <div class="flex justify-between gap-4 border-t border-[#ece7dc] pt-2 font-semibold text-[#123D2B]"><dt>Total</dt><dd>{{ $payment->currency ?: 'LKR' }} {{ number_format($payment->homeownerTotal(), 2) }}</dd></div>
        </dl>
        <p class="mt-4 text-xs text-[#66756C]">Reference {{ $payment->reference }}</p>

        @if ($checkout['mode'] === 'payhere')
            <form method="POST" action="{{ $checkout['action'] }}" class="mt-6">
                @foreach ($checkout['fields'] as $name => $value)
                    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="w-full rounded-full bg-[#123D2B] px-5 py-3 text-sm font-semibold text-white">Pay with PayHere</button>
            </form>
            <p class="mt-3 text-center text-xs text-[#66756C]">You will complete this payment on the PayHere sandbox. RenovaHub marks it paid only after PayHere confirms the result.</p>
        @else
            <form method="POST" action="{{ route('homeowner.payments.confirm', [$project, $payment]) }}" class="mt-6 rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] p-4">
                @csrf
                <input type="hidden" name="token" value="{{ $checkout['token'] }}">
                <p class="text-sm font-medium text-[#123D2B]">PayHere Sandbox</p>
                <p class="mt-1 text-sm text-[#66756C]">Confirm this demonstration payment. Opening this page does not mark it as paid.</p>
                <button type="submit" class="mt-4 w-full rounded-full bg-[#123D2B] px-5 py-3 text-sm font-semibold text-white">Confirm Payment</button>
            </form>
        @endif
        <a href="{{ route('homeowner.payments.index') }}" class="mt-4 inline-flex text-sm text-[#66756C] hover:text-[#123D2B]">Back to Payments</a>
    </article>
</x-homeowner-layout>
