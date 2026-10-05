<x-designer-layout title="Earnings">
    <h1 class="font-serif text-4xl text-[#123D2B] sm:text-5xl">Earnings</h1>
    <p class="mt-2 max-w-2xl text-sm text-[#66756C]">Design fees for your projects. Each paid record shows the gross designer fee, the RenovaHub service charge, and the net amount.</p>
    <section class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['Gross Designer Fees', $totals['gross']],
            ['RenovaHub Service Charge', $totals['fees']],
            ['Net Designer Earnings', $totals['net']],
            ['Pending', $totals['pending']],
        ] as [$label, $amount])
            <article class="rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm">
                <p class="text-xs text-[#66756C]">{{ $label }}</p>
                <p class="mt-2 font-serif text-2xl text-[#123D2B]">LKR {{ number_format($amount, 0) }}</p>
            </article>
        @endforeach
    </section>
    <div class="mt-6 overflow-x-auto rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
            <thead class="text-xs uppercase tracking-wide text-[#66756C]">
                <tr>
                    <th class="px-4 py-3">Project</th>
                    <th class="px-4 py-3">Gross Designer Fee</th>
                    <th class="px-4 py-3">RenovaHub Service Charge</th>
                    <th class="px-4 py-3">Net Designer Earnings</th>
                    <th class="px-4 py-3">Payment Status</th>
                    <th class="px-4 py-3">Date</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($earnings as $earning)
                    <tr class="border-t border-[#ece7dc]">
                        <td class="px-4 py-3 text-[#123D2B]">{{ $earning->project->name }}</td>
                        <td class="px-4 py-3">{{ $earning->money((float) $earning->gross_amount) }}</td>
                        <td class="px-4 py-3">{{ $earning->money((float) $earning->fee_amount) }}</td>
                        <td class="px-4 py-3">{{ $earning->money((float) $earning->net_amount) }}</td>
                        <td class="px-4 py-3">{{ $earning->statusLabel() }}</td>
                        <td class="px-4 py-3">{{ $earning->recorded_on?->format('j M Y') ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if ($earning->status === \App\Models\DesignerEarning::STATUS_RECORDED)
                                <button type="button" class="font-medium text-[#123D2B] underline" data-receipt-open="earning-{{ $earning->id }}">View</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-[#66756C]">No earnings recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <x-payment-receipt :receipts="$receipts" />
</x-designer-layout>
