@php
    $completed = $project->isCompleted();
    $items = $project->budgetItems;
    $final = $project->finalCostAmount();
    $shareBase = $completed ? $final : $project->budgetTotalAmount();
@endphp

<section class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
    @if ($completed)
        <h2 class="font-serif text-xl text-[#123D2B]">Final Project Budget</h2>
        <dl class="mt-4 space-y-2 text-sm">
            <div class="flex items-center justify-between gap-3">
                <dt class="text-[#66756C]">Original Estimated Budget</dt>
                <dd class="font-medium text-[#123D2B]">LKR {{ number_format((float) $project->estimated_budget, 0) }}</dd>
            </div>
            <div class="flex items-center justify-between gap-3">
                <dt class="text-[#66756C]">Approved Budget</dt>
                <dd class="font-medium text-[#123D2B]">LKR {{ number_format($project->approvedBudgetAmount(), 0) }}</dd>
            </div>
            <div class="flex items-center justify-between gap-3 rounded-xl bg-[#F3F7F1] px-3 py-2">
                <dt class="font-medium text-[#123D2B]">Final Project Cost</dt>
                <dd class="font-semibold text-[#123D2B]">LKR {{ number_format($final, 0) }}</dd>
            </div>
        </dl>
    @else
        <div class="flex items-start justify-between gap-3">
            <h2 class="font-serif text-xl text-[#123D2B]">Project Budget</h2>
        </div>
        <div class="mt-3 flex items-end justify-between gap-3">
            <p class="text-sm text-[#66756C]">Total Project Budget</p>
            <p class="font-serif text-2xl text-[#123D2B]">LKR {{ number_format($project->budgetTotalAmount(), 0) }}</p>
        </div>
        <div class="mt-3">
            <div class="mb-1 flex items-center justify-between text-xs text-[#66756C]">
                <span>{{ $project->spentPercent() }}% spent</span>
                <span>LKR {{ number_format($project->amountSpent(), 0) }} spent</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-[#EFE8DA]">
                <div class="h-full rounded-full bg-[#2F6B49]" style="width: {{ $project->spentPercent() }}%"></div>
            </div>
            <p class="mt-1 text-right text-xs text-[#66756C]">LKR {{ number_format($project->amountRemaining(), 0) }} remaining</p>
        </div>
    @endif

    @if ($items->isNotEmpty())
        <ul class="mt-5 space-y-3">
            @foreach ($items as $item)
                @php
                    $percent = $completed ? $item->shareOf($shareBase) : min(100, (int) $item->spent_percent);
                @endphp
                <li>
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span class="text-[#123D2B]">{{ $item->category }}</span>
                        <span class="shrink-0 text-[#123D2B]">
                            LKR {{ number_format((float) $item->amount, 0) }}
                            <span class="ml-2 text-xs text-[#66756C]">{{ $percent }}%</span>
                        </span>
                    </div>
                    <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-[#EFE8DA]">
                        <div class="h-full rounded-full bg-[#2F6B49]" style="width: {{ $percent }}%"></div>
                    </div>
                </li>
            @endforeach
        </ul>
    @endif
</section>
