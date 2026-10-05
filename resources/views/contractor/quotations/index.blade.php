<x-contractor-layout title="Quotations">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="rh-serif text-4xl text-[#123D2B]">Project Quotations</h1>
            <p class="mt-2 max-w-2xl text-sm text-[#66756C]">Quotations you prepare for the homeowner. Supplier prices are separate and live under Suppliers.</p>
        </div>
        <a href="{{ route('contractor.quotations.create') }}" class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Create quotation</a>
    </div>
    <form method="GET" class="mt-4">
        <select name="project" class="rounded-2xl border border-[#ddd6c8] bg-white px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
            <option value="">All projects</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" @selected($projectId === $project->id)>{{ $project->name }}</option>
            @endforeach
        </select>
    </form>
    <div class="mt-6 overflow-x-auto rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#F6F1E7] text-xs uppercase tracking-wide text-[#66756C]">
                <tr>
                    <th class="px-4 py-3">Number</th>
                    <th class="px-4 py-3">Project</th>
                    <th class="px-4 py-3">Description</th>
                    <th class="px-4 py-3">Total</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($quotations as $quotation)
                    <tr class="border-t border-[#ece7dc]">
                        <td class="px-4 py-3 font-medium text-[#123D2B]">{{ $quotation->number }}</td>
                        <td class="px-4 py-3">{{ $quotation->project?->name }}</td>
                        <td class="px-4 py-3 text-[#66756C]">{{ \Illuminate\Support\Str::limit($quotation->description, 80) }}</td>
                        <td class="px-4 py-3">{{ $quotation->money($quotation->total) }}</td>
                        <td class="px-4 py-3">{{ $quotation->contractorStatusLabel() }}</td>
                        <td class="px-4 py-3"><a class="text-[#123D2B] underline" href="{{ route('contractor.quotations.show', $quotation) }}">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-[#66756C]">No quotations yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-contractor-layout>
