@forelse ($groups as $group)
    <article class="mt-5 overflow-hidden rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
        <header class="flex flex-col gap-3 border-b border-[#ece7dc] px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                @if ($group->coverUrl())
                    <img src="{{ $group->coverUrl() }}" alt="" class="h-14 w-20 rounded-xl object-cover">
                @endif
                <div>
                    <a href="{{ route('homeowner.projects.show', $group) }}" class="font-serif text-xl text-[#123D2B] hover:underline">{{ $group->name }}</a>
                    <span class="ml-2 rounded-full px-2 py-0.5 text-[11px] {{ $group->isCompleted() ? 'bg-[#E7F0E4] text-[#2F6B49]' : 'bg-[#F3EFE4] text-[#123D2B]' }}">{{ $group->isCompleted() ? 'Completed' : 'In Progress' }}</span>
                    <p class="mt-1 text-xs text-[#66756C]">{{ $group->cataloguePlaceLabel() }} · {{ $group->displayTypeLabel() }}</p>
                </div>
            </div>
            <p class="text-sm text-[#66756C]">{{ $group->isCompleted() ? 'Final Project Cost' : 'Project Budget' }}<br><span class="font-medium text-[#123D2B]">LKR {{ number_format($group->isCompleted() ? $group->finalCostAmount() : $group->budgetTotalAmount(), 0) }}</span></p>
        </header>
        <div class="overflow-x-auto">
            <table class="min-w-[72rem] w-full text-left text-sm">
                <thead class="text-[11px] uppercase tracking-[0.12em] text-[#66756C]">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Quotation No.</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3">Submitted By</th>
                        <th class="px-4 py-3">Submitted Date</th>
                        <th class="px-4 py-3">Amount</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Approval Date</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($group->quotations as $quotation)
                        @php
                            $person = $quotation->contractor?->professionalProfile?->displayName() ?? $quotation->contractor?->name ?? 'Contractor';
                            $role = $quotation->contractor?->professionalProfile?->title ?? 'Contractor';
                            $badge = match ($quotation->status) {
                                'approved' => 'bg-[#E7F0E4] text-[#2F6B49]',
                                'rejected' => 'bg-red-50 text-red-700',
                                'clarification_required' => 'bg-sky-50 text-sky-800',
                                default => 'bg-amber-50 text-amber-800',
                            };
                        @endphp
                        <tr class="border-t border-[#ece7dc]">
                            <td class="px-4 py-3 text-[#66756C]">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium text-[#123D2B]">{{ $quotation->number }}</td>
                            <td class="px-4 py-3">{{ $quotation->description }}</td>
                            <td class="px-4 py-3">
                                <p>{{ $person }}</p>
                                <p class="text-xs text-[#66756C]">{{ $role }}</p>
                            </td>
                            <td class="px-4 py-3">{{ $quotation->created_at->format('j M Y') }}</td>
                            <td class="px-4 py-3">LKR {{ number_format((float) $quotation->total, 0) }}</td>
                            <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs {{ $badge }}">{{ $quotation->reviewLabel() }}</span></td>
                            <td class="px-4 py-3">{{ $quotation->approved_at?->format('j M Y') ?? '—' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('homeowner.quotations.show', [$group, $quotation]) }}" class="font-medium text-[#123D2B] hover:underline">View</a>
                                @can('update', $quotation)
                                    <form method="POST" action="{{ route('homeowner.quotations.decide', [$group, $quotation]) }}" class="inline">
                                        @csrf
                                        <button name="decision" value="approved" class="ml-3 font-medium text-[#2F6B49] hover:underline">Approve</button>
                                        <button name="decision" value="rejected" class="ml-3 font-medium text-red-700 hover:underline">Reject</button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </article>
@empty
    <div class="mt-6">@include('homeowner.partials.empty', ['title' => 'No quotations yet', 'body' => 'Contractor quotations will appear here when they are submitted.'])</div>
@endforelse
@if ($groups->hasPages())
    <div class="mt-6">{{ $groups->links() }}</div>
@endif
