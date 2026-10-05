<x-contractor-layout :title="$quotation ? 'Edit quotation' : 'New quotation'">
    <h1 class="rh-serif text-4xl text-[#123D2B]">{{ $quotation ? 'Edit '.$quotation->number : 'New project quotation' }}</h1>
    <p class="mt-2 text-sm text-[#66756C]">Totals are calculated on the server from quantity and unit price.</p>
    <form method="POST" action="{{ $quotation ? route('contractor.quotations.update', $quotation) : route('contractor.quotations.store') }}" class="mt-6 space-y-4 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
        @csrf
        @if ($quotation) @method('PUT') @endif
        @unless ($quotation)
            <label class="block text-sm text-[#66756C]">Project
                <select name="project_id" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2 text-[#123D2B]" required>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </label>
        @endunless
        <label class="block text-sm text-[#66756C]">Description
            <input name="description" value="{{ old('description', $quotation->description ?? '') }}" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2 text-[#123D2B]" required>
        </label>
        <label class="block text-sm text-[#66756C]">Valid until
            <input type="date" name="valid_until" value="{{ old('valid_until', optional($quotation?->valid_until)->format('Y-m-d')) }}" class="mt-1 rounded-2xl border border-[#ddd6c8] px-3 py-2 text-[#123D2B]">
        </label>
        @php
            $rows = old('items', $quotation?->items?->map(fn ($item) => $item->only(['item', 'category', 'description', 'quantity', 'unit_cost']))->all() ?? []);
            $rows = array_pad($rows, 4, ['item' => '', 'category' => 'materials', 'description' => '', 'quantity' => 1, 'unit_cost' => '']);
        @endphp
        <div class="space-y-3">
            @foreach ($rows as $index => $row)
                <div class="grid gap-2 rounded-2xl bg-[#F7F4EE] p-3 md:grid-cols-5">
                    <input name="items[{{ $index }}][item]" value="{{ $row['item'] ?? '' }}" placeholder="Item" class="rounded-xl border border-[#ddd6c8] px-3 py-2 text-sm">
                    <select name="items[{{ $index }}][category]" class="rounded-xl border border-[#ddd6c8] px-3 py-2 text-sm">
                        @foreach (['materials' => 'Materials', 'labour' => 'Labour', 'equipment' => 'Equipment', 'other' => 'Other'] as $value => $label)
                            <option value="{{ $value }}" @selected(($row['category'] ?? 'materials') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <input name="items[{{ $index }}][description]" value="{{ $row['description'] ?? '' }}" placeholder="Description" class="rounded-xl border border-[#ddd6c8] px-3 py-2 text-sm">
                    <input name="items[{{ $index }}][quantity]" value="{{ $row['quantity'] ?? 1 }}" type="number" step="0.01" min="0" placeholder="Qty" class="rounded-xl border border-[#ddd6c8] px-3 py-2 text-sm">
                    <input name="items[{{ $index }}][unit_cost]" value="{{ $row['unit_cost'] ?? '' }}" type="number" step="0.01" min="0" placeholder="Unit price" class="rounded-xl border border-[#ddd6c8] px-3 py-2 text-sm">
                </div>
            @endforeach
        </div>
        @if ($errors->any())
            <ul class="text-sm text-red-700">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        @endif
        <div class="flex gap-2">
            <button class="rounded-full border border-[#c9c2b4] px-4 py-2 text-sm text-[#123D2B]">Save draft</button>
            <button name="submit" value="1" class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Submit to homeowner</button>
        </div>
    </form>
</x-contractor-layout>
