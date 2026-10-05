<x-contractor-layout title="Request Price">
    <h1 class="rh-serif text-4xl text-[#123D2B]">Request Price from Supplier</h1>
    <p class="mt-2 text-sm text-[#66756C]">{{ $supplier->name }} · {{ number_format((float) $supplier->rating, 1) }} · {{ $supplier->review_count }} reviews</p>
    <form method="POST" action="{{ route('contractor.suppliers.request-price.store', $supplier) }}" class="mt-6 max-w-2xl space-y-4 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
        @csrf
        <label class="block text-sm text-[#66756C]">Project
            <select name="project_id" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2" required>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="block text-sm text-[#66756C]">Material / item
            <input name="material" value="{{ old('material', 'Kitchen Cabinets') }}" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2" required>
        </label>
        <label class="block text-sm text-[#66756C]">Product
            <input name="product" value="{{ old('product') }}" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2" required>
        </label>
        <div class="grid gap-3 sm:grid-cols-2">
            <label class="block text-sm text-[#66756C]">Quantity
                <input type="number" step="0.01" min="0.01" name="quantity" value="{{ old('quantity', 1) }}" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2" required>
            </label>
            <label class="block text-sm text-[#66756C]">Unit
                <input name="unit" value="{{ old('unit', 'Set') }}" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2" required>
            </label>
        </div>
        <label class="block text-sm text-[#66756C]">Required by
            <input type="date" name="required_by" value="{{ old('required_by') }}" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2" required>
        </label>
        <label class="block text-sm text-[#66756C]">Additional notes
            <textarea name="notes" rows="4" class="mt-1 w-full rounded-2xl border border-[#ddd6c8] px-3 py-2">{{ old('notes') }}</textarea>
        </label>
        @if ($errors->any())
            <ul class="text-sm text-red-700">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        @endif
        <div class="flex gap-2">
            <a href="{{ route('contractor.suppliers.show', $supplier) }}" class="rounded-full border border-[#c9c2b4] px-4 py-2 text-sm text-[#123D2B]">Cancel</a>
            <button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Send Price Request</button>
        </div>
    </form>
</x-contractor-layout>
