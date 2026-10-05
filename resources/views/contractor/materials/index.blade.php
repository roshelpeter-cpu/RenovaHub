<x-contractor-layout title="Materials">
    <div>
        <h1 class="rh-serif text-3xl text-[#123D2B]">Material Requirements</h1>
        <p class="mt-2 text-sm text-[#66756C]">Taken from the approved final design. Request prices from suppliers instead of rebuilding this list.</p>
    </div>
    <form method="GET" class="mt-5 grid gap-3 rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm md:grid-cols-4">
        <select name="project" class="rounded-xl border border-[#ddd6c8] px-3 py-2 text-sm" aria-label="Project">
            <option value="">All projects</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" @selected(request('project') == $project->id)>{{ $project->name }}</option>
            @endforeach
        </select>
        <select name="room" class="rounded-xl border border-[#ddd6c8] px-3 py-2 text-sm" aria-label="Room">
            <option value="">All rooms</option>
            @foreach ($rooms as $room)
                <option value="{{ $room }}" @selected(request('room') === $room)>{{ $room }}</option>
            @endforeach
        </select>
        <select name="category" class="rounded-xl border border-[#ddd6c8] px-3 py-2 text-sm" aria-label="Category">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category }}" @selected(request('category') === $category)>{{ $category }}</option>
            @endforeach
        </select>
        <button class="rounded-xl bg-[#123D2B] px-4 py-2 text-sm text-white">Filter</button>
    </form>
    @php
        $total = $materials->count();
        $value = $materials->sum('estimated_value');
    @endphp
    <div class="mt-4 grid gap-3 sm:grid-cols-3">
        <article class="rounded-2xl border border-[#ece7dc] bg-white p-4"><p class="text-xs text-[#66756C]">Total items</p><p class="mt-1 text-xl font-semibold">{{ $total }}</p></article>
        <article class="rounded-2xl border border-[#ece7dc] bg-white p-4"><p class="text-xs text-[#66756C]">Categories</p><p class="mt-1 text-xl font-semibold">{{ $materials->pluck('category')->unique()->count() }}</p></article>
        <article class="rounded-2xl border border-[#ece7dc] bg-white p-4"><p class="text-xs text-[#66756C]">Estimated material value</p><p class="mt-1 text-xl font-semibold">LKR {{ number_format($value, 0) }}</p></article>
    </div>
    <div class="mt-4 overflow-x-auto rounded-2xl border border-[#ece7dc] bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#F6F1E7] text-xs uppercase tracking-wide text-[#66756C]"><tr><th class="px-4 py-3">Material</th><th class="px-4 py-3">Room</th><th class="px-4 py-3">Quantity</th><th class="px-4 py-3">Unit</th><th class="px-4 py-3">Specification</th><th class="px-4 py-3">Status</th></tr></thead>
            <tbody>
                @forelse ($materials as $item)
                    <tr class="border-t border-[#ece7dc]">
                        <td class="px-4 py-3 text-[#123D2B]">{{ $item->name }}<span class="block text-xs text-[#66756C]">{{ $item->project->name }}</span></td>
                        <td class="px-4 py-3">{{ $item->room }}</td>
                        <td class="px-4 py-3">{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                        <td class="px-4 py-3">{{ $item->unit }}</td>
                        <td class="px-4 py-3 text-[#66756C]">{{ $item->specification }}</td>
                        <td class="px-4 py-3">{{ ucfirst($item->status) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-sm text-[#66756C]">No supplier quotations yet. Request prices from suppliers using your approved material requirements. <a href="{{ route('contractor.suppliers.index') }}" class="underline">Find Suppliers</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-contractor-layout>
