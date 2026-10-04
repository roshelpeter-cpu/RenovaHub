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
            <table class="min-w-[76rem] w-full text-left text-sm">
                <thead class="text-[11px] uppercase tracking-[0.12em] text-[#66756C]">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Request No.</th>
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Requested By</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Cost Impact</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Approval Date</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($group->changeRequests as $change)
                        @php
                            $badge = match ($change->reviewLabel()) {
                                'Approved' => 'bg-[#E7F0E4] text-[#2F6B49]',
                                'Rejected' => 'bg-red-50 text-red-700',
                                'Pending' => 'bg-amber-50 text-amber-800',
                                default => 'bg-sky-50 text-sky-800',
                            };
                        @endphp
                        <tr class="border-t border-[#ece7dc]">
                            <td class="px-4 py-3 text-[#66756C]">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 font-medium">{{ $change->reference() }}</td>
                            <td class="px-4 py-3">{{ $change->title }}</td>
                            <td class="max-w-xs px-4 py-3 text-[#66756C]">{{ \Illuminate\Support\Str::limit($change->description, 80) }}</td>
                            <td class="px-4 py-3">{{ $change->categoryLabel() }}</td>
                            <td class="px-4 py-3">{{ $change->requester?->name ?? 'Homeowner' }}<br><span class="text-xs text-[#66756C]">{{ ucfirst($change->requester?->role ?? 'homeowner') }}</span></td>
                            <td class="px-4 py-3">{{ $change->created_at->format('j M Y') }}</td>
                            <td class="px-4 py-3">{{ $change->cost_impact === null ? '—' : 'LKR '.number_format((float) $change->cost_impact, 0) }}</td>
                            <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs {{ $badge }}">{{ $change->reviewLabel() }}</span></td>
                            <td class="px-4 py-3">{{ $change->approved_at?->format('j M Y') ?? '—' }}</td>
                            <td class="px-4 py-3"><a href="{{ route('homeowner.change-requests.show', [$group, $change]) }}" class="font-medium text-[#123D2B] hover:underline">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </article>
@empty
    <div class="mt-6">@include('homeowner.partials.empty', ['title' => 'No change requests', 'body' => 'Change requests for your projects will appear here.'])</div>
@endforelse
@if ($groups->hasPages())
    <div class="mt-6">{{ $groups->links() }}</div>
@endif
