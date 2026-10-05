<x-homeowner-layout title="Edit Project">
    <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-olive">{{ $project->name }}</p>
    <h1 class="mt-2 font-serif text-3xl font-medium tracking-[-0.03em] text-forest">Edit Project</h1>

    <form method="POST" action="{{ route('homeowner.projects.update', $project) }}" class="mt-6 max-w-3xl rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm sm:p-6">
        @csrf
        @method('PUT')
        <x-validation-errors class="mb-5" />

        <div>
            <label for="name" class="mb-2 block text-sm font-medium">Project name</label>
            <input id="name" name="name" type="text" required maxlength="150" value="{{ old('name', $project->name) }}" class="block w-full rounded-2xl border border-line px-4 py-3 text-sm outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">
        </div>
        <div class="mt-4">
            <label for="description" class="mb-2 block text-sm font-medium">Description</label>
            <textarea id="description" name="description" rows="5" required maxlength="5000" class="block w-full rounded-2xl border border-line px-4 py-3 text-sm outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">{{ old('description', $project->description) }}</textarea>
        </div>

        <fieldset class="mt-5">
            <legend class="text-sm font-medium">Renovation type</legend>
            <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3">
                @foreach (\App\Models\Project::renovationTypes() as $value => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="renovation_type" value="{{ $value }}" class="peer sr-only" @checked(old('renovation_type', $project->renovation_type) === $value)>
                        <span class="block rounded-2xl border border-line bg-ivory px-3 py-3 text-center text-sm peer-checked:border-forest peer-checked:bg-[#e7f0e4]">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </fieldset>

        <fieldset class="mt-5">
            <legend class="text-sm font-medium">Property type</legend>
            <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3">
                @foreach (\App\Models\Project::propertyTypes() as $value => $label)
                    <label class="cursor-pointer">
                        <input type="radio" name="property_type" value="{{ $value }}" class="peer sr-only" @checked(old('property_type', $project->property_type) === $value)>
                        <span class="block rounded-2xl border border-line bg-ivory px-3 py-3 text-center text-sm peer-checked:border-forest peer-checked:bg-[#e7f0e4]">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </fieldset>

        <div class="mt-5">
            <label for="requirements" class="mb-2 block text-sm font-medium">Project requirements</label>
            <textarea id="requirements" name="requirements" rows="4" maxlength="5000" class="block w-full rounded-2xl border border-line px-4 py-3 text-sm outline-none focus:border-forest focus:ring-4 focus:ring-forest/10">{{ old('requirements', $project->requirements) }}</textarea>
        </div>
        <div class="mt-5">
            <label for="additional_instructions" class="mb-2 block text-sm font-medium">Project instructions</label>
            <textarea id="additional_instructions" name="additional_instructions" rows="3" maxlength="5000" class="block w-full rounded-2xl border border-line px-4 py-3 text-sm outline-none focus:border-forest focus:ring-4 focus:ring-forest/10">{{ old('additional_instructions', $project->additional_instructions) }}</textarea>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2">
            <div>
                <label for="address" class="mb-2 block text-sm font-medium">Address</label>
                <input id="address" name="address" type="text" value="{{ old('address', $project->address) }}" class="block w-full rounded-2xl border border-line px-4 py-3 text-sm outline-none focus:border-forest focus:ring-4 focus:ring-forest/10">
            </div>
            <div>
                <label for="city" class="mb-2 block text-sm font-medium">City</label>
                <input id="city" name="city" type="text" value="{{ old('city', $project->city) }}" class="block w-full rounded-2xl border border-line px-4 py-3 text-sm outline-none focus:border-forest focus:ring-4 focus:ring-forest/10">
            </div>
            <div>
                <label for="province" class="mb-2 block text-sm font-medium">Province</label>
                <input id="province" name="province" type="text" value="{{ old('province', $project->province) }}" class="block w-full rounded-2xl border border-line px-4 py-3 text-sm outline-none focus:border-forest focus:ring-4 focus:ring-forest/10">
            </div>
            <div>
                <label for="postal_code" class="mb-2 block text-sm font-medium">Postal code</label>
                <input id="postal_code" name="postal_code" type="text" value="{{ old('postal_code', $project->postal_code) }}" class="block w-full rounded-2xl border border-line px-4 py-3 text-sm outline-none focus:border-forest focus:ring-4 focus:ring-forest/10">
            </div>
        </div>
        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div>
                <label for="estimated_budget" class="mb-2 block text-sm font-medium">Estimated budget (LKR)</label>
                <input id="estimated_budget" name="estimated_budget" type="number" min="0" step="0.01" value="{{ old('estimated_budget', $project->estimated_budget) }}" class="block w-full rounded-2xl border border-line px-4 py-3 text-sm outline-none focus:border-forest focus:ring-4 focus:ring-forest/10">
            </div>
            <div>
                <label for="timeline_notes" class="mb-2 block text-sm font-medium">Timeline notes</label>
                <input id="timeline_notes" name="timeline_notes" type="text" value="{{ old('timeline_notes', $project->timeline_notes) }}" class="block w-full rounded-2xl border border-line px-4 py-3 text-sm outline-none focus:border-forest focus:ring-4 focus:ring-forest/10">
            </div>
            <div>
                <label for="expected_start_date" class="mb-2 block text-sm font-medium">Expected start date</label>
                <input id="expected_start_date" name="expected_start_date" type="date" value="{{ old('expected_start_date', $project->expected_start_date?->format('Y-m-d')) }}" class="block w-full rounded-2xl border border-line px-4 py-3 text-sm outline-none focus:border-forest focus:ring-4 focus:ring-forest/10">
            </div>
            <div>
                <label for="expected_completion_date" class="mb-2 block text-sm font-medium">Expected completion date</label>
                <input id="expected_completion_date" name="expected_completion_date" type="date" value="{{ old('expected_completion_date', $project->expected_completion_date?->format('Y-m-d')) }}" class="block w-full rounded-2xl border border-line px-4 py-3 text-sm outline-none focus:border-forest focus:ring-4 focus:ring-forest/10">
            </div>
        </div>
        @error('expected_completion_date') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror

        <div class="mt-8 flex flex-wrap justify-between gap-3">
            <a href="{{ route('homeowner.projects.show', $project) }}" class="rounded-full border border-[#ddd6c8] px-5 py-3 text-sm">Cancel</a>
            <button type="submit" class="rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory hover:bg-leaf">Save changes</button>
        </div>
    </form>
</x-homeowner-layout>
