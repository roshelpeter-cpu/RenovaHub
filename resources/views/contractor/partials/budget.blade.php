@php $total = $project->budgetTotalAmount(); $spent = $project->amountSpent(); $remaining = $project->amountRemaining(); @endphp
<section class="mt-6 grid gap-4 lg:grid-cols-[16rem_1fr]">
    <div class="space-y-3">
        @foreach ([['Total Project Budget', $project->money($total)], ['Total Spent', $project->money($spent)], ['Remaining Budget', $project->money($remaining)], ['Spending', $project->spentPercent().'%']] as [$label, $value])
            <article class="rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm">
                <p class="text-xs text-[#66756C]">{{ $label }}</p>
                <p class="rh-serif mt-1 text-2xl text-[#123D2B]">{{ $value }}</p>
            </article>
        @endforeach
    </div>
    <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
        <h2 class="rh-serif text-2xl text-[#123D2B]">Budget breakdown</h2>
        <form method="POST" action="{{ route('contractor.budget.update', $project) }}" class="mt-4 space-y-4">
            @csrf
            @method('PUT')
            @forelse ($project->budgetItems as $item)
                <div>
                    <div class="flex items-center justify-between gap-3 text-sm">
                        <span class="text-[#123D2B]">{{ $item->category }}</span>
                        <span class="text-[#66756C]">{{ $project->money($item->amount) }} · spent {{ $item->spent_percent }}%</span>
                    </div>
                    <div class="mt-1 h-2 overflow-hidden rounded-full bg-[#F6F1E7]"><div class="h-full rounded-full bg-[#1F7A4D]" style="width: {{ (int) $item->spent_percent }}%"></div></div>
                    <label class="mt-2 block text-xs text-[#66756C]">Update spent percent
                        <input type="number" name="spent[{{ $item->id }}]" value="{{ (int) $item->spent_percent }}" min="0" max="100" class="mt-1 w-28 rounded-xl border border-[#ddd6c8] px-3 py-1.5 text-sm">
                    </label>
                </div>
            @empty
                <p class="text-sm text-[#66756C]">No budget lines yet.</p>
            @endforelse
            @if ($project->budgetItems->isNotEmpty() && ! $project->isClosedRecord())
                <button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Save budget</button>
            @endif
        </form>
    </article>
</section>
