<x-designer-layout title="Earnings">
    <h1 class="font-serif text-4xl text-[#123D2B] sm:text-5xl">Earnings</h1>
    <p class="mt-2 max-w-2xl text-sm text-[#66756C]">Professional earnings from design work. RenovaHub keeps a 2% professional service fee. These records are not PayHere transactions.</p>
    <section class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['Total Earnings', $totals['gross']],
            ['This Month', $totals['month']],
            ['Pending Earnings', $totals['pending']],
            ['RenovaHub Fees', $totals['fees']],
        ] as [$label, $amount])
            <article class="rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm">
                <p class="text-xs text-[#66756C]">{{ $label }}</p>
                <p class="mt-2 font-serif text-2xl text-[#123D2B]">LKR {{ number_format($amount, 0) }}</p>
            </article>
        @endforeach
    </section>
    <div class="mt-6 overflow-x-auto rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
            <thead class="text-xs text-[#66756C]"><tr><th class="px-4 py-3">Project</th><th class="px-4 py-3">Payment</th><th class="px-4 py-3">Gross Amount</th><th class="px-4 py-3">RenovaHub Fee</th><th class="px-4 py-3">Net Earnings</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Date</th><th class="px-4 py-3"></th></tr></thead>
            <tbody>
                @forelse ($earnings as $earning)
                    <tr class="border-t border-[#ece7dc]">
                        <td class="px-4 py-3">{{ $earning->project->name }}</td>
                        <td class="px-4 py-3">{{ $earning->label }}</td>
                        <td class="px-4 py-3">{{ $earning->money((float) $earning->gross_amount) }}</td>
                        <td class="px-4 py-3">{{ rtrim(rtrim(number_format((float) $earning->fee_percent, 2), '0'), '.') }}% = {{ $earning->money((float) $earning->fee_amount) }}</td>
                        <td class="px-4 py-3">{{ $earning->money((float) $earning->net_amount) }}</td>
                        <td class="px-4 py-3">{{ $earning->statusLabel() }}</td>
                        <td class="px-4 py-3">{{ $earning->recorded_on?->format('j M Y') ?? '—' }}</td>
                        <td class="px-4 py-3"><a href="{{ route('designer.earnings.show', $earning) }}" class="text-[#123D2B]">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-6 text-[#66756C]">No earnings recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-designer-layout>
