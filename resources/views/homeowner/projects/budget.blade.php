<x-homeowner-layout title="Budget & Timeline">
    <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-olive">{{ $project->name }}</p>
    <h1 class="mt-2 font-serif text-3xl font-medium tracking-[-0.03em] text-forest sm:text-4xl">Budget & Timeline</h1>
    <p class="mt-2 max-w-2xl text-sm text-mist">Set your initial budget and expected timeline.</p>

    <div class="mt-6">
        @include('homeowner.projects.partials.steps', ['current' => 3, 'project' => $project])
    </div>

    <form method="POST" action="{{ route('homeowner.projects.budget.update', $project) }}" class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_18rem]">
        @csrf
        @method('PUT')
        <div class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm sm:p-6">
            <x-validation-errors class="mb-5" />
            <div>
                <label for="estimated_budget" class="mb-2 block text-sm font-medium text-charcoal">Estimated budget (LKR)</label>
                <input id="estimated_budget" name="estimated_budget" type="number" min="0" step="0.01" value="{{ old('estimated_budget', $project->estimated_budget) }}" class="block w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">
                @error('estimated_budget') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="expected_start_date" class="mb-2 block text-sm font-medium text-charcoal">Expected start date</label>
                    <input id="expected_start_date" name="expected_start_date" type="date" value="{{ old('expected_start_date', $project->expected_start_date?->format('Y-m-d')) }}" class="block w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">
                    @error('expected_start_date') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="expected_completion_date" class="mb-2 block text-sm font-medium text-charcoal">Expected completion date</label>
                    <input id="expected_completion_date" name="expected_completion_date" type="date" value="{{ old('expected_completion_date', $project->expected_completion_date?->format('Y-m-d')) }}" class="block w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">
                    @error('expected_completion_date') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-4">
                <label for="timeline_notes" class="mb-2 block text-sm font-medium text-charcoal">Timeline notes</label>
                <textarea id="timeline_notes" name="timeline_notes" rows="4" maxlength="2000" class="block w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">{{ old('timeline_notes', $project->timeline_notes) }}</textarea>
            </div>
            @if ($project->expectedDurationLabel())
                <p class="mt-4 text-sm text-charcoal">Estimated duration: {{ $project->expectedDurationLabel() }}</p>
            @endif
            <div class="mt-8 flex flex-wrap items-center justify-between gap-3">
                <a href="{{ route('homeowner.projects.location', $project) }}" class="rounded-full border border-[#ddd6c8] px-5 py-3 text-sm font-medium text-charcoal transition hover:border-forest">Back</a>
                <button type="submit" class="rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory transition duration-300 hover:bg-leaf">Save and continue</button>
            </div>
        </div>
        <aside class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="font-serif text-xl text-forest">Budget Guidance</h2>
            <ul class="mt-3 space-y-2 text-sm leading-relaxed text-mist">
                <li>The budget you enter is your initial project estimate. Final construction costs will be provided through contractor quotations.</li>
            </ul>
        </aside>
    </form>
</x-homeowner-layout>
