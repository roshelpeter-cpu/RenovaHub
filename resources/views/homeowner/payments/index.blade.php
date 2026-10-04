<x-homeowner-layout title="Payments">
    @if ($project) @include('homeowner.projects.partials.tabs', ['project' => $project]) @endif
    <h1 class="mt-4 font-serif text-3xl text-forest sm:text-4xl">Payments</h1>
    <p class="mt-2 text-sm text-mist">Payment history only. The payment provider is not connected yet.</p>
    <dl class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach (['Recorded value' => $summary['value'], 'Total paid' => $summary['paid'], 'Outstanding' => $summary['outstanding']] as $label => $amount)
            <div class="rounded-2xl bg-[#F6F1E7] p-4"><dt class="text-xs uppercase tracking-[0.12em] text-mist">{{ $label }}</dt><dd class="mt-1 font-serif text-2xl text-forest">LKR {{ number_format($amount, 2) }}</dd></div>
        @endforeach
        <div class="rounded-2xl bg-[#F6F1E7] p-4"><dt class="text-xs uppercase tracking-[0.12em] text-mist">Pending payments</dt><dd class="mt-1 font-serif text-2xl text-forest">{{ $summary['pending'] }}</dd></div>
    </dl>
    @if ($payments->isEmpty())
        <div class="mt-6">@include('homeowner.partials.empty', ['title' => 'No payments yet', 'body' => 'Payment records appear after a quotation is underway.'])</div>
    @else
        <div class="mt-6 overflow-x-auto rounded-3xl border border-[#ece7dc] bg-white shadow-sm">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-[#F6F1E7] text-xs uppercase tracking-[0.12em] text-mist"><tr><th class="px-4 py-3">Payment</th><th class="px-4 py-3">Project</th><th class="px-4 py-3">Amount</th><th class="px-4 py-3">Date</th><th class="px-4 py-3">Method</th><th class="px-4 py-3">Status</th></tr></thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr class="border-t border-[#ece7dc]">
                            <td class="px-4 py-3"><a class="text-forest hover:underline" href="{{ route('homeowner.payments.show', [$payment->project, $payment]) }}">{{ $payment->reference }}</a></td>
                            <td class="px-4 py-3">{{ $payment->project->name }}</td>
                            <td class="px-4 py-3">{{ $payment->money() }}</td>
                            <td class="px-4 py-3">{{ ($payment->paid_at ?? $payment->created_at)->format('j M Y') }}</td>
                            <td class="px-4 py-3">{{ $payment->method ?: 'Not set' }}</td>
                            <td class="px-4 py-3">{{ $payment->statusLabel() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $payments->links() }}</div>
    @endif
</x-homeowner-layout>
