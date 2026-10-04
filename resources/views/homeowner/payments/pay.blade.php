<x-homeowner-layout title="Pay">
    <article class="mx-auto mt-8 max-w-lg rounded-3xl border border-[#ece7dc] bg-[#F6F1E7] p-8 text-center shadow-sm">
        <h1 class="font-serif text-3xl text-forest">Payment gateway integration will be connected here.</h1>
        <p class="mt-3 text-sm leading-relaxed text-mist">{{ $notice }}</p>
        <p class="mt-4 text-sm text-charcoal">{{ $payment->reference }} · {{ $payment->money() }}</p>
        <a href="{{ route('homeowner.payments.show', [$project, $payment]) }}" class="mt-6 inline-flex rounded-full border border-forest px-5 py-3 text-sm text-forest">Back to payment</a>
    </article>
</x-homeowner-layout>
