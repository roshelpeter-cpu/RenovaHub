<x-contractor-layout :title="$project->name">
    @include('contractor.partials.project-header', ['project' => $project, 'section' => $section])

    @if ($section === 'overview')
        <div class="mt-6 grid gap-4 lg:grid-cols-3">
            <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm lg:col-span-2">
                <h2 class="rh-serif text-2xl text-[#123D2B]">About this project</h2>
                <p class="mt-3 text-sm leading-relaxed text-[#66756C]">{{ $project->description }}</p>
                <div class="mt-5 space-y-3">
                    @foreach ($project->workspaceStages() as $stage)
                        <div>
                            <div class="flex justify-between text-sm"><span class="text-[#123D2B]">{{ $stage->label }}</span><span class="text-[#66756C]">{{ $stage->status }}</span></div>
                            <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-[#F6F1E7]"><div class="h-full rounded-full bg-[#1F7A4D]" style="width: {{ $stage->percent }}%"></div></div>
                        </div>
                    @endforeach
                </div>
            </article>
            <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
                <h2 class="rh-serif text-2xl text-[#123D2B]">Approved design</h2>
                <p class="mt-3 text-sm text-[#66756C]">The designer prepares the design. You quote and build after the homeowner approves it.</p>
                @if ($project->moodBoard?->approved_at)
                    <p class="mt-3 text-sm text-[#123D2B]">Mood board approved {{ $project->moodBoard->approved_at->format('j M Y') }}.</p>
                @else
                    <p class="mt-3 text-sm text-[#66756C]">No approved mood board is recorded yet.</p>
                @endif
            </article>
        </div>
    @elseif ($section === 'quotations')
        <div class="mt-6 overflow-x-auto rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-[#F6F1E7] text-xs uppercase tracking-wide text-[#66756C]"><tr><th class="px-4 py-3">Quotation</th><th class="px-4 py-3">Total</th><th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr></thead>
                <tbody>
                    @forelse ($project->quotations as $quotation)
                        <tr class="border-t border-[#ece7dc]">
                            <td class="px-4 py-3 text-[#123D2B]">{{ $quotation->number }}</td>
                            <td class="px-4 py-3">{{ $quotation->money($quotation->total) }}</td>
                            <td class="px-4 py-3">{{ $quotation->contractorStatusLabel() }}</td>
                            <td class="px-4 py-3"><a href="{{ route('contractor.quotations.show', $quotation) }}" class="text-[#123D2B] underline">View</a></td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-6 text-[#66756C]" colspan="4">No quotations yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <a href="{{ route('contractor.quotations.create') }}" class="mt-4 inline-flex rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Create quotation</a>
    @elseif ($section === 'budget')
        @include('contractor.partials.budget', ['project' => $project])
    @elseif ($section === 'suppliers')
        <div class="mt-6 grid gap-4 lg:grid-cols-2">
            @forelse ($project->supplierOrders as $order)
                <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
                    <p class="text-xs text-[#66756C]">{{ $order->number }}</p>
                    <h2 class="rh-serif text-2xl text-[#123D2B]">{{ $order->item }}</h2>
                    <p class="mt-1 text-sm text-[#66756C]">{{ $order->supplier?->name }} · {{ $order->money() }}</p>
                    <p class="mt-2 text-sm text-[#123D2B]">{{ $order->statusLabel() }}</p>
                    <a href="{{ route('contractor.orders.show', $order) }}" class="mt-3 inline-flex text-sm text-[#123D2B] underline">View order</a>
                </article>
            @empty
                <p class="text-sm text-[#66756C]">No supplier orders for this project yet.</p>
            @endforelse
        </div>
        <a href="{{ route('contractor.suppliers.index') }}" class="mt-4 inline-flex rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Find suppliers</a>
    @elseif ($section === 'tasks')
        <div class="mt-6 overflow-x-auto rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-[#F6F1E7] text-xs uppercase tracking-wide text-[#66756C]"><tr><th class="px-4 py-3">Task</th><th class="px-4 py-3">Assigned</th><th class="px-4 py-3">Due</th><th class="px-4 py-3">Progress</th><th class="px-4 py-3">Status</th></tr></thead>
                <tbody>
                    @forelse ($project->tasks as $task)
                        <tr class="border-t border-[#ece7dc]">
                            <td class="px-4 py-3 text-[#123D2B]">{{ $task->name }}</td>
                            <td class="px-4 py-3">{{ $task->assigneeLabel() }}</td>
                            <td class="px-4 py-3">{{ $task->due_on?->format('j M Y') ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $task->progress }}%</td>
                            <td class="px-4 py-3">{{ $task->contractorStatusLabel() }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-6 text-[#66756C]" colspan="5">No tasks yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @elseif ($section === 'documents')
        <ul class="mt-6 space-y-3">
            @forelse ($project->documents as $document)
                <li class="flex items-center justify-between rounded-2xl border border-[#ece7dc] bg-white px-4 py-3 text-sm">
                    <span class="text-[#123D2B]">{{ $document->name }} <span class="text-[#66756C]">· {{ $document->categoryLabel() }}</span></span>
                    <a href="{{ route('contractor.documents.download', $document) }}" class="text-[#123D2B] underline">Download</a>
                </li>
            @empty
                <li class="text-sm text-[#66756C]">No documents yet.</li>
            @endforelse
        </ul>
    @elseif ($section === 'change-requests')
        <div class="mt-6 space-y-3">
            @forelse ($project->changeRequests as $change)
                <a href="{{ route('contractor.change-requests.show', $change) }}" class="block rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm">
                    <p class="font-medium text-[#123D2B]">{{ $change->title }}</p>
                    <p class="text-sm text-[#66756C]">{{ $change->statusLabel() }} · {{ $change->requester?->name }}</p>
                </a>
            @empty
                <p class="text-sm text-[#66756C]">No change requests.</p>
            @endforelse
        </div>
    @endif
</x-contractor-layout>
