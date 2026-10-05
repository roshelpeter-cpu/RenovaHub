<x-contractor-layout title="Request quotation">
    <h1 class="rh-serif text-3xl text-[#123D2B]">Request project quotation</h1>
    <p class="mt-2 text-sm text-[#66756C]">{{ $firm->name }}. Record the price, duration and scope the firm provided. The homeowner still has to approve it.</p>
    <form method="POST" action="{{ route('contractor.firms.quote.store', $firm) }}" class="mt-6 max-w-2xl space-y-4 rounded-2xl border border-[#ece7dc] bg-white p-6 shadow-sm">
        @csrf
        <label class="block text-sm">Project
            <select name="project_id" class="mt-1 w-full rounded-xl border border-[#ddd6c8] px-3 py-2" required>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
        </label>
        <div class="grid gap-4 sm:grid-cols-2">
            <label class="block text-sm">Price (LKR)
                <input name="price" type="number" min="1" step="0.01" required class="mt-1 w-full rounded-xl border border-[#ddd6c8] px-3 py-2">
            </label>
            <label class="block text-sm">Duration (days)
                <input name="duration_days" type="number" min="1" required class="mt-1 w-full rounded-xl border border-[#ddd6c8] px-3 py-2">
            </label>
            <label class="block text-sm">Start date
                <input name="start_date" type="date" required class="mt-1 w-full rounded-xl border border-[#ddd6c8] px-3 py-2">
            </label>
            <label class="block text-sm">Completion date
                <input name="completion_date" type="date" required class="mt-1 w-full rounded-xl border border-[#ddd6c8] px-3 py-2">
            </label>
        </div>
        <label class="block text-sm">Scope
            <textarea name="scope" required rows="3" class="mt-1 w-full rounded-xl border border-[#ddd6c8] px-3 py-2"></textarea>
        </label>
        <label class="block text-sm">Terms
            <textarea name="terms" rows="2" class="mt-1 w-full rounded-xl border border-[#ddd6c8] px-3 py-2"></textarea>
        </label>
        <label class="block text-sm">Warranty
            <input name="warranty" class="mt-1 w-full rounded-xl border border-[#ddd6c8] px-3 py-2">
        </label>
        <button class="rounded-xl bg-[#123D2B] px-5 py-2.5 text-sm text-white">Save quotation</button>
    </form>
</x-contractor-layout>
