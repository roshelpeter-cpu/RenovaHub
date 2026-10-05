<x-homeowner-layout title="Payments" :canvas="true">
    <p class="text-sm text-[#66756C]">
        <a href="{{ route('homeowner.home') }}" class="hover:text-[#123D2B]">Home</a>
        <span class="mx-1.5 text-[#c9c2b4]">›</span>
        Payments
    </p>
    <h1 class="mt-4 font-serif text-4xl text-[#123D2B] sm:text-5xl">Payments</h1>
    <p class="mt-2 max-w-2xl text-sm text-[#66756C]">Project payments for your renovations. A payment is marked paid only after checkout is confirmed.</p>

    <section class="mt-6">
        <h2 class="text-xs font-semibold uppercase tracking-[0.16em] text-[#66756C]">Payment Summary</h2>
        <div class="mt-3 grid gap-3 sm:grid-cols-3">
            @foreach ([['Total Paid', $summary['paid']], ['Pending', $summary['pending']]] as [$label, $amount])
                <article class="rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm">
                    <p class="text-xs text-[#66756C]">{{ $label }}</p>
                    <p class="mt-1 font-serif text-2xl text-[#123D2B]">LKR {{ number_format($amount, 2) }}</p>
                </article>
            @endforeach
            <article class="rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm">
                <p class="text-xs text-[#66756C]">Successful Transactions</p>
                <p class="mt-1 font-serif text-2xl text-[#123D2B]">{{ $summary['successful'] }}</p>
            </article>
        </div>
    </section>

    <section class="mt-8">
        <h2 class="text-xs font-semibold uppercase tracking-[0.16em] text-[#66756C]">Project Payments</h2>
        @if ($featured->isEmpty())
            <p class="mt-3 text-sm text-[#66756C]">A final project payment appears here after the budget is approved.</p>
        @else
            <div class="mt-4 grid gap-4">
                @foreach ($featured as $payment)
                    <article class="flex flex-col gap-4 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="font-serif text-2xl text-[#123D2B]">{{ $payment->project->name }}</h3>
                            <p class="mt-1 text-sm text-[#66756C]">Final Project Budget</p>
                            <p class="mt-1 text-lg font-medium text-[#123D2B]">{{ $payment->formatMoney($payment->renovationAmount()) }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium {{ $payment->status === \App\Models\Payment::STATUS_PAID ? 'text-[#0E8A4C]' : 'text-[#9A6B12]' }}">
                                {{ $payment->status === \App\Models\Payment::STATUS_PAID ? 'Paid' : 'Payment Pending' }}
                            </span>
                            @if ($payment->status === \App\Models\Payment::STATUS_PAID)
                                <button type="button" class="rounded-full bg-[#123D2B] px-4 py-2.5 text-sm font-medium text-white" data-receipt-open="payment-{{ $payment->id }}">View Receipt</button>
                            @elseif ($payment->status === \App\Models\Payment::STATUS_PENDING)
                                <a href="{{ route('homeowner.payments.pay', [$payment->project, $payment]) }}" class="rounded-full bg-[#123D2B] px-4 py-2.5 text-sm font-medium text-white">Pay Now</a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="mt-8">
        <h2 class="text-xs font-semibold uppercase tracking-[0.16em] text-[#66756C]">Payment History</h2>
        <div class="mt-4 overflow-x-auto rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-[#F6F1E7] text-[11px] uppercase tracking-[0.12em] text-[#66756C]">
                    <tr>
                        <th class="px-4 py-3">Project</th>
                        <th class="px-4 py-3">Transaction Number</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($history as $payment)
                        <tr class="border-t border-[#ece7dc]">
                            <td class="px-4 py-3 text-[#123D2B]">{{ $payment->project->name }}</td>
                            <td class="px-4 py-3">{{ $payment->reference }}</td>
                            <td class="px-4 py-3">{{ $payment->money() }}</td>
                            <td class="px-4 py-3">{{ ($payment->paid_at ?? $payment->created_at)?->format('j M Y') }}</td>
                            <td class="px-4 py-3">{{ $payment->status === \App\Models\Payment::STATUS_PAID ? 'Paid' : $payment->statusLabel() }}</td>
                            <td class="px-4 py-3">
                                @if ($payment->status === \App\Models\Payment::STATUS_PAID)
                                    <button type="button" class="font-medium text-[#123D2B] underline" data-receipt-open="payment-{{ $payment->id }}">View</button>
                                @elseif ($payment->status === \App\Models\Payment::STATUS_PENDING)
                                    <a href="{{ route('homeowner.payments.pay', [$payment->project, $payment]) }}" class="font-medium text-[#123D2B] underline">Pay Now</a>
                                @else
                                    <span class="text-[#66756C]">{{ $payment->statusLabel() }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-[#66756C]">No payments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $history->links() }}</div>
    </section>

    <x-payment-receipt :receipts="$receipts" />
</x-homeowner-layout>
