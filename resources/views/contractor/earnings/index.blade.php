<x-contractor-layout title="Earnings">
    <h1 class="rh-serif text-4xl text-[#123D2B]">Your Earnings</h1>
    <p class="mt-2 max-w-2xl text-sm text-[#66756C]">RenovaHub charges a platform service fee on successful transactions. This is not a salary deduction, and accounts stay free.</p>
    <section class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ([
            ['Total earnings', $summary['net']],
            ['This month', $summary['month']],
            ['Pending', $summary['pending']],
            ['Paid', $summary['paid']],
            ['RenovaHub fees', $summary['fees']],
            ['Net earnings', $summary['net']],
        ] as [$label, $amount])
            <article class="rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm">
                <p class="text-xs text-[#66756C]">{{ $label }}</p>
                <p class="rh-serif mt-2 text-2xl text-[#123D2B]">LKR {{ number_format($amount, 0) }}</p>
            </article>
        @endforeach
    </section>
    <div class="mt-6 overflow-x-auto rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#F6F1E7] text-xs uppercase tracking-wide text-[#66756C]">
                <tr><th class="px-4 py-3">Project</th><th class="px-4 py-3">Gross</th><th class="px-4 py-3">Fee</th><th class="px-4 py-3">Net</th><th class="px-4 py-3">Status</th></tr>
            </thead>
            <tbody>
                @forelse ($earnings as $earning)
                    <tr class="border-t border-[#ece7dc]">
                        <td class="px-4 py-3"><a class="text-[#123D2B] underline" href="{{ route('contractor.earnings.show', $earning) }}">{{ $earning->label }}</a><div class="text-xs text-[#66756C]">{{ $earning->project?->name }}</div></td>
                        <td class="px-4 py-3">{{ $earning->money((float) $earning->gross_amount) }}</td>
                        <td class="px-4 py-3">{{ rtrim(rtrim(number_format((float) $earning->fee_percent, 2), '0'), '.') }}% · {{ $earning->money((float) $earning->fee_amount) }}</td>
                        <td class="px-4 py-3">{{ $earning->money((float) $earning->net_amount) }}</td>
                        <td class="px-4 py-3">{{ $earning->statusLabel() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-[#66756C]">Earnings appear when a supplier order creates a payment request.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-contractor-layout>
