<x-contractor-layout :title="$change->reference()">
    <p class="text-sm text-[#66756C]">{{ $change->project?->name }} · {{ $change->reference() }}</p>
    <h1 class="rh-serif mt-1 text-4xl text-[#123D2B]">{{ $change->title }}</h1>
    <p class="mt-3 max-w-3xl text-sm leading-relaxed text-[#66756C]">{{ $change->description }}</p>
    <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-3">
        <div><dt class="text-xs text-[#66756C]">Status</dt><dd>{{ $change->statusLabel() }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">Cost impact</dt><dd>{{ $change->cost_impact !== null ? 'LKR '.number_format((float) $change->cost_impact, 0) : 'Not set' }}</dd></div>
        <div><dt class="text-xs text-[#66756C]">Time impact</dt><dd>{{ $change->timeline_impact ?: 'Not set' }}</dd></div>
    </dl>
    @can('respond', $change)
        <form method="POST" action="{{ route('contractor.change-requests.respond', $change) }}" class="mt-6 max-w-2xl space-y-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
            @csrf
            <h2 class="rh-serif text-2xl text-[#123D2B]">Your review</h2>
            <label class="block text-sm text-[#66756C]">Cost impact (LKR)
                <input type="number" step="0.01" name="cost_impact" value="{{ old('cost_impact', $change->cost_impact) }}" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2" required>
            </label>
            <label class="block text-sm text-[#66756C]">Time impact
                <input name="timeline_impact" value="{{ old('timeline_impact', $change->timeline_impact) }}" placeholder="+ 7 days" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2" required>
            </label>
            <label class="block text-sm text-[#66756C]">Feasibility
                <select name="feasibility" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2">
                    <option value="possible">Possible</option>
                    <option value="needs_design">Needs design</option>
                    <option value="not_possible">Not possible</option>
                </select>
            </label>
            <label class="flex items-center gap-2 text-sm text-[#123D2B]"><input type="checkbox" name="design_affected" value="1"> Design is affected — notify the designer</label>
            <label class="block text-sm text-[#66756C]">Response
                <textarea name="contractor_response" rows="4" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2" required>{{ old('contractor_response', $change->contractor_response) }}</textarea>
            </label>
            <button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Send impact to homeowner</button>
        </form>
    @else
        <p class="mt-4 text-sm text-[#66756C]">{{ $change->contractor_response ?: 'Waiting for the next decision.' }}</p>
    @endif
</x-contractor-layout>
