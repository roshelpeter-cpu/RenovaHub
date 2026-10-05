<x-contractor-layout title="Budget">
    <div class="flex flex-wrap items-end justify-between gap-3">
        <div>
            <h1 class="rh-serif text-3xl text-[#123D2B]">Project Budget</h1>
            <p class="mt-2 text-sm text-[#66756C]">One budget for the homeowner. It includes the designer, materials, construction, your fee and the RenovaHub platform service fee.</p>
        </div>
        @if ($projects->isNotEmpty())
            <form method="GET">
                <label class="text-sm text-[#66756C]">Project
                    <select name="project" class="ml-2 rounded-xl border border-[#ddd6c8] bg-white px-3 py-2 text-sm text-[#123D2B]" onchange="this.form.submit()">
                        @foreach ($projects as $option)
                            <option value="{{ $option->id }}" @selected($project?->id === $option->id)>{{ $option->name }}</option>
                        @endforeach
                    </select>
                </label>
            </form>
        @endif
    </div>

    @if (! $project)
        <p class="mt-6 text-sm text-[#66756C]">Accept a project invitation before preparing a budget.</p>
    @else
        @php
            $lines = $submission ? $submission->lines() : [
                ['label' => 'Designer Fee', 'amount' => $preview['designer_fee']],
                ['label' => 'Materials', 'amount' => $preview['materials']],
                ['label' => 'Construction & Labour', 'amount' => $preview['construction']],
                ['label' => 'Contractor Fee', 'amount' => $preview['contractor_fee']],
                ['label' => 'Additional Changes', 'amount' => $preview['changes']],
                ['label' => 'RenovaHub Platform Service Fee', 'amount' => $preview['platform_fee']],
            ];
            $total = $submission ? (float) $submission->total : $preview['total'];
            $spent = (float) $project->budgetItems->sum(fn ($item) => $item->spentAmount());
            $remaining = max(0, (float) $project->estimated_budget - $spent);
            $percent = (float) $project->estimated_budget > 0 ? min(100, round(($spent / (float) $project->estimated_budget) * 100)) : 0;
        @endphp
        <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-[#ece7dc] bg-white p-4"><p class="text-xs text-[#66756C]">Total project budget</p><p class="mt-2 text-xl font-semibold">{{ $project->money($project->estimated_budget) }}</p></article>
            <article class="rounded-2xl border border-[#ece7dc] bg-white p-4"><p class="text-xs text-[#66756C]">Amount spent</p><p class="mt-2 text-xl font-semibold">LKR {{ number_format($spent, 0) }}</p></article>
            <article class="rounded-2xl border border-[#ece7dc] bg-white p-4"><p class="text-xs text-[#66756C]">Remaining</p><p class="mt-2 text-xl font-semibold">LKR {{ number_format($remaining, 0) }}</p></article>
            <article class="rounded-2xl border border-[#ece7dc] bg-white p-4"><p class="text-xs text-[#66756C]">Spending</p><p class="mt-2 text-xl font-semibold">{{ $percent }}%</p></article>
        </section>
        <div class="mt-5 grid gap-4 lg:grid-cols-[1.2fr_0.8fr]">
            <section class="rounded-2xl border border-[#ece7dc] bg-white p-5 shadow-sm">
                <h2 class="rh-serif text-2xl text-[#123D2B]">Budget breakdown</h2>
                <ul class="mt-4 space-y-4">
                    @foreach ($lines as $line)
                        @php $share = $total > 0 ? (int) round(($line['amount'] / $total) * 100) : 0; @endphp
                        <li>
                            <div class="flex justify-between text-sm"><span class="text-[#123D2B]">{{ $line['label'] }}</span><span>LKR {{ number_format($line['amount'], 0) }} · {{ $share }}%</span></div>
                            <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-[#F6F1E7]"><div class="h-full rounded-full bg-[#1F7A4D]" style="width: {{ $share }}%"></div></div>
                        </li>
                    @endforeach
                </ul>
                <p class="mt-5 text-sm text-[#123D2B]">Final project total <span class="text-lg font-semibold">LKR {{ number_format($total, 0) }}</span></p>
                <p class="mt-1 text-xs text-[#66756C]">Platform service fee {{ $preview['fee_percent'] }}% is stored on the submission. It is not a subscription.</p>
                @if ($submission)
                    <p class="mt-3 text-sm text-[#123D2B]">Status: {{ $submission->statusLabel() }}</p>
                @endif
                @if (! $submission || $submission->status === \App\Models\BudgetSubmission::STATUS_REJECTED)
                    <button type="button" class="mt-4 rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white" onclick="document.getElementById('budget-receipt').showModal()">Make Final Budget</button>
                @else
                    <button type="button" class="mt-4 rounded-full border border-[#123D2B] px-4 py-2 text-sm text-[#123D2B]" onclick="document.getElementById('budget-receipt').showModal()">View budget</button>
                @endif
                <dialog id="budget-receipt" class="w-full max-w-lg rounded-3xl border border-[#ece7dc] p-0 backdrop:bg-[#123D2B]/40" @if(request()->boolean('receipt')) open @endif>
                    <div class="border-b border-[#ece7dc] px-6 py-5">
                        <p class="text-xs uppercase tracking-[0.16em] text-[#66756C]">RenovaHub</p>
                        <h3 class="mt-1 font-serif text-2xl text-[#123D2B]">Final Project Budget</h3>
                    </div>
                    <div class="px-6 py-5 text-sm">
                        <dl class="grid grid-cols-2 gap-2 text-[#66756C]">
                            <div><dt>Project</dt><dd class="text-[#123D2B]">{{ $project->name }}</dd></div>
                            <div><dt>Homeowner</dt><dd class="text-[#123D2B]">{{ $project->homeowner?->name }}</dd></div>
                            <div><dt>Contractor</dt><dd class="text-[#123D2B]">{{ auth()->user()->name }}</dd></div>
                            <div><dt>Designer</dt><dd class="text-[#123D2B]">{{ $project->designer?->name ?? '—' }}</dd></div>
                        </dl>
                        <ul class="mt-4 space-y-2 border-t border-[#ece7dc] pt-4">
                            @foreach ($lines as $line)
                                <li class="flex justify-between"><span>{{ $line['label'] }}</span><span>LKR {{ number_format($line['amount'], 0) }}</span></li>
                            @endforeach
                        </ul>
                        <p class="mt-4 flex justify-between border-t border-[#ece7dc] pt-3 font-medium text-[#123D2B]"><span>Final total</span><span>LKR {{ number_format($total, 0) }}</span></p>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-[#ece7dc] px-6 py-4">
                        <button type="button" class="rounded-full border border-[#c9c2b4] px-4 py-2 text-sm" onclick="document.getElementById('budget-receipt').close()">Close</button>
                        @if (! $submission || $submission->status === \App\Models\BudgetSubmission::STATUS_REJECTED)
                            <form method="POST" action="{{ route('contractor.budget.submit', $project) }}">
                                @csrf
                                <button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Send to Homeowner</button>
                            </form>
                        @endif
                    </div>
                </dialog>
            </section>
            <section class="rounded-2xl border border-[#ece7dc] bg-white p-5 shadow-sm">
                <h2 class="rh-serif text-2xl text-[#123D2B]">Recent changes</h2>
                <ul class="mt-4 space-y-3 text-sm">
                    @forelse ($project->changeRequests as $change)
                        <li class="border-b border-[#ece7dc] pb-3">
                            <p class="text-[#123D2B]">{{ $change->title }}</p>
                            <p class="text-[#66756C]">{{ $change->status }} · LKR {{ number_format((float) $change->cost_impact, 0) }}</p>
                        </li>
                    @empty
                        <li class="text-[#66756C]">Approved cost changes will appear here.</li>
                    @endforelse
                </ul>
            </section>
        </div>
    @endif
</x-contractor-layout>
