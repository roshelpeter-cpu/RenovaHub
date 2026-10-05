<x-homeowner-layout title="Payments" :canvas="true">
    <p class="text-sm text-[#66756C]">
        <a href="{{ route('homeowner.home') }}" class="hover:text-[#123D2B]">Home</a>
        <span class="mx-1.5 text-[#c9c2b4]">›</span>
        Payments
    </p>
    <div class="mt-4 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="font-serif text-4xl text-[#123D2B] sm:text-5xl">Payments</h1>
            <p class="mt-2 max-w-2xl text-sm text-[#66756C]">Renovation payments across your projects. PayHere checkout will be connected after hosting.</p>
        </div>
        <form method="GET" action="{{ route('homeowner.payments.index') }}">
            <label class="block text-sm">
                <span class="mb-1 block text-xs text-[#66756C]">Project</span>
                <select name="project" class="min-w-56 rounded-2xl border border-[#ece7dc] bg-white px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                    <option value="">All Projects</option>
                    @foreach ($projects as $option)
                        <option value="{{ $option->id }}" @selected($selected === $option->id)>{{ $option->name }}</option>
                    @endforeach
                </select>
            </label>
        </form>
    </div>
    <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach ([['Renovation recorded', $summary['value']], ['Paid', $summary['paid']], ['Outstanding', $summary['outstanding']], ['RenovaHub service fee', $summary['fees']]] as [$label, $amount])
            <article class="rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm">
                <p class="text-xs text-[#66756C]">{{ $label }}</p>
                <p class="mt-1 font-serif text-2xl text-[#123D2B]">LKR {{ number_format($amount, 0) }}</p>
            </article>
        @endforeach
    </div>
    @if ($payments->isEmpty())
        <div class="mt-6">@include('homeowner.partials.empty', ['title' => 'No payments yet', 'body' => 'A payment record appears after a quotation is approved. Nothing has been sent to PayHere.'])</div>
    @else
        <div class="mt-6 overflow-x-auto rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
            <table class="min-w-[80rem] w-full text-left text-sm">
                <thead class="bg-[#F6F1E7] text-[11px] uppercase tracking-[0.12em] text-[#66756C]">
                    <tr>
                        <th class="px-4 py-3">Payment</th>
                        <th class="px-4 py-3">Project</th>
                        <th class="px-4 py-3">Quotation</th>
                        <th class="px-4 py-3">Renovation</th>
                        <th class="px-4 py-3">Service fee</th>
                        <th class="px-4 py-3">Homeowner total</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Method</th>
                        <th class="px-4 py-3">Reference</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr class="border-t border-[#ece7dc]">
                            <td class="px-4 py-3 font-medium text-[#123D2B]">{{ $payment->reference }}</td>
                            <td class="px-4 py-3">@include('homeowner.partials.project-chip', ['project' => $payment->project])</td>
                            <td class="px-4 py-3">{{ $payment->quotation?->number ?? ($payment->supplierOrder ? 'Supplier order' : '—') }}</td>
                            <td class="px-4 py-3">{{ $payment->formatMoney($payment->renovationAmount()) }}</td>
                            <td class="px-4 py-3">{{ $payment->formatMoney($payment->platformFee()) }}</td>
                            <td class="px-4 py-3">{{ $payment->money() }}</td>
                            <td class="px-4 py-3">{{ $payment->statusLabel() }}</td>
                            <td class="px-4 py-3">{{ ($payment->paid_at ?? $payment->created_at)->format('j M Y') }}</td>
                            <td class="px-4 py-3">{{ $payment->method ?: 'Not set' }}</td>
                            <td class="px-4 py-3">{{ $payment->provider_reference ?: '—' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('homeowner.payments.show', [$payment->project, $payment]) }}" class="font-medium text-[#123D2B] hover:underline">View</a>
                                @can('pay', $payment)
                                    <a href="{{ route('homeowner.payments.pay', [$payment->project, $payment]) }}" class="ml-3 font-medium text-[#123D2B] hover:underline">Pay</a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $payments->links() }}</div>
    @endif
</x-homeowner-layout>
