<x-contractor-layout :title="$project->name">
    <p class="text-sm text-[#66756C]">
        <a href="{{ route('contractor.earnings.index') }}" class="hover:text-[#123D2B]">Earnings</a>
        <span class="mx-1.5 text-[#c9c2b4]">›</span>
        {{ $project->name }}
    </p>
    <h1 class="rh-serif mt-3 text-4xl text-[#123D2B]">Project Payment Breakdown</h1>
    <p class="mt-2 text-sm text-[#66756C]">{{ $project->name }} · {{ $project->homeowner?->name ?: 'Homeowner' }}</p>

    <div class="mt-6 grid gap-4">
        @foreach ($sections as $section)
            <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-xs font-semibold uppercase tracking-[0.16em] text-[#66756C]">{{ $section['heading'] }}</h2>
                <dl class="mt-4 grid gap-3 sm:grid-cols-2">
                    @foreach ($section['rows'] as $row)
                        <div>
                            <dt class="text-xs text-[#66756C]">{{ $row['label'] }}</dt>
                            <dd class="mt-1 text-sm font-medium text-[#123D2B]">{{ $row['value'] }}</dd>
                        </div>
                    @endforeach
                </dl>
                <div class="mt-5">
                    @if ($section['action'] === 'view')
                        <button type="button" class="rounded-full bg-[#123D2B] px-5 py-2.5 text-sm font-medium text-white" data-receipt-open="{{ $section['receipt_id'] }}">View</button>
                    @elseif ($section['action'] === 'pay')
                        <button type="button" class="rounded-full bg-[#123D2B] px-5 py-2.5 text-sm font-medium text-white" data-pay-open="pay-{{ $section['allocation']->id }}">Pay</button>
                    @endif
                </div>
            </article>

            @if ($section['action'] === 'pay')
                <div id="pay-{{ $section['allocation']->id }}" class="rh-pay-root" hidden>
                    <div class="rh-pay-backdrop" data-pay-close></div>
                    <section class="rh-pay-card" role="dialog" aria-modal="true" aria-labelledby="pay-title-{{ $section['allocation']->id }}">
                        <button type="button" class="rh-pay-close" data-pay-close aria-label="Close">&times;</button>
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#66756C]">RenovaHub</p>
                        <h3 id="pay-title-{{ $section['allocation']->id }}" class="rh-serif mt-2 text-3xl text-[#123D2B]">Confirm Payment</h3>
                        <dl class="mt-5 space-y-3 text-sm">
                            <div class="flex justify-between gap-4"><dt class="text-[#66756C]">{{ $section['confirm']['party_label'] }}</dt><dd class="text-right font-medium text-[#123D2B]">{{ $section['confirm']['party'] }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-[#66756C]">Project</dt><dd class="text-right font-medium text-[#123D2B]">{{ $section['confirm']['project'] }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-[#66756C]">{{ $section['confirm']['detail_label'] }}</dt><dd class="text-right font-medium text-[#123D2B]">{{ $section['confirm']['detail'] }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-[#66756C]">Amount</dt><dd class="text-right font-medium text-[#123D2B]">{{ $section['confirm']['amount'] }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-[#66756C]">Payment Reference</dt><dd class="text-right font-medium text-[#123D2B]">{{ $section['confirm']['reference'] }}</dd></div>
                        </dl>
                        <form method="POST" action="{{ route('contractor.earnings.allocations.pay', [$project, $section['allocation']]) }}" class="mt-6">
                            @csrf
                            <button type="submit" class="w-full rounded-full bg-[#123D2B] px-5 py-3 text-sm font-semibold tracking-wide text-white">Confirm Payment</button>
                        </form>
                    </section>
                </div>
            @endif
        @endforeach
    </div>

    <x-payment-receipt :receipts="$receipts" />

    <style>
        .rh-pay-root[hidden] { display: none !important; }
        .rh-pay-root { position: fixed; inset: 0; z-index: 70; display: flex; align-items: center; justify-content: center; padding: 24px 16px; }
        .rh-pay-backdrop { position: absolute; inset: 0; background: rgba(18, 24, 28, 0.48); }
        .rh-pay-card { position: relative; width: min(440px, 100%); border-radius: 18px; background: #fff; padding: 28px 26px 24px; box-shadow: 0 28px 70px rgba(16, 24, 40, 0.22); }
        .rh-pay-close { position: absolute; top: 12px; right: 14px; border: 0; background: transparent; color: #6b7280; font-size: 26px; line-height: 1; cursor: pointer; }
    </style>
    <script>
        document.addEventListener('click', function (event) {
            const open = event.target.closest('[data-pay-open]');
            if (open) {
                const modal = document.getElementById(open.getAttribute('data-pay-open'));
                if (modal) {
                    modal.hidden = false;
                    document.body.style.overflow = 'hidden';
                }
            }
            if (event.target.closest('[data-pay-close]')) {
                const modal = event.target.closest('.rh-pay-root');
                if (modal) {
                    modal.hidden = true;
                    document.body.style.overflow = '';
                }
            }
        });
        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }
            document.querySelectorAll('.rh-pay-root').forEach(function (modal) {
                modal.hidden = true;
            });
        });
    </script>
</x-contractor-layout>
