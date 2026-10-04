<x-homeowner-layout :title="$payment->reference">
    <article class="mt-4 max-w-xl rounded-3xl border border-[#ece7dc] bg-white p-6 shadow-sm">
        <p class="text-xs uppercase tracking-[0.14em] text-olive">{{ $payment->statusLabel() }}</p>
        <h1 class="mt-2 font-serif text-3xl text-forest">{{ $payment->reference }}</h1>
        <p class="mt-2 font-serif text-2xl text-charcoal">{{ $payment->money() }}</p>
        <p class="mt-2 text-sm text-mist">{{ $project->name }}</p>
        @if ($payment->notes)<p class="mt-3 text-sm text-charcoal">{{ $payment->notes }}</p>@endif
        @if ($payment->status === 'pending')
            <a href="{{ route('homeowner.payments.pay', [$project, $payment]) }}" class="mt-5 inline-flex rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory">Pay Now</a>
        @endif
    </article>
</x-homeowner-layout>
