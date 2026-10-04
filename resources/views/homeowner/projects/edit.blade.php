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

        <div class="mt-5 grid gap-4 sm:grid-cols-2">
            <div>
                <label for="status" class="mb-2 block text-sm font-medium">Status</label>
                <select id="status" name="status" class="block w-full rounded-2xl border border-line px-4 py-3 text-sm outline-none focus:border-forest focus:ring-4 focus:ring-forest/10">
                    @foreach (\App\Models\Project::statuses() as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $project->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="progress" class="mb-2 block text-sm font-medium">Progress (%)</label>
                <input id="progress" name="progress" type="number" min="0" max="100" value="{{ old('progress', $project->progress) }}" class="block w-full rounded-2xl border border-line px-4 py-3 text-sm outline-none focus:border-forest focus:ring-4 focus:ring-forest/10">
                @error('progress') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="mt-8 flex flex-wrap justify-between gap-3">
            <a href="{{ route('homeowner.projects.show', $project) }}" class="rounded-full border border-[#ddd6c8] px-5 py-3 text-sm">Cancel</a>
            <button type="submit" class="rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory hover:bg-leaf">Save changes</button>
        </div>
    </form>
</x-homeowner-layout>
