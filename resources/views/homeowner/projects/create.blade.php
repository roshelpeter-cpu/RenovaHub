<x-homeowner-layout title="Create New Project">
    <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-olive">New project</p>
    <h1 class="mt-2 font-serif text-3xl font-medium tracking-[-0.03em] text-forest sm:text-4xl">Create New Project</h1>
    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-mist">Start with the project details. Location, budget and team selection follow.</p>

    <div class="mt-6">
        @include('homeowner.projects.partials.steps', ['current' => 1])
    </div>

    <div class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_18rem]">
        <form method="POST" action="{{ route('homeowner.projects.store') }}" enctype="multipart/form-data" class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm sm:p-6">
            @csrf
            <x-validation-errors class="mb-5" />

            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-charcoal">Project name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="150" class="block w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm text-charcoal outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">
                @error('name')
                    <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-5">
                <label for="description" class="mb-2 block text-sm font-medium text-charcoal">Description</label>
                <textarea id="description" name="description" rows="5" required maxlength="5000" class="block w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm text-charcoal outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-5">
                <label for="requirements" class="mb-2 block text-sm font-medium text-charcoal">Project requirements</label>
                <textarea id="requirements" name="requirements" rows="4" maxlength="5000" class="block w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">{{ old('requirements') }}</textarea>
            </div>
            <div class="mt-5">
                <label for="additional_instructions" class="mb-2 block text-sm font-medium text-charcoal">Additional instructions</label>
                <textarea id="additional_instructions" name="additional_instructions" rows="3" maxlength="5000" class="block w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">{{ old('additional_instructions') }}</textarea>
            </div>
            <div class="mt-5">
                <label for="reference_images" class="mb-2 block text-sm font-medium text-charcoal">Reference images</label>
                <input id="reference_images" name="reference_images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple class="block w-full text-sm text-charcoal">
                <p class="mt-1 text-xs text-mist">JPG, PNG or WebP. Up to six images.</p>
            </div>

            <fieldset class="mt-6">
                <legend class="text-sm font-medium text-charcoal">Renovation type</legend>
                <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3">
                    @foreach (\App\Models\Project::renovationTypes() as $value => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="renovation_type" value="{{ $value }}" class="peer sr-only" @checked(old('renovation_type') === $value) @required($loop->first)>
                            <span class="block rounded-2xl border border-line bg-ivory px-3 py-3 text-center text-sm transition duration-300 peer-checked:border-forest peer-checked:bg-[#e7f0e4] peer-focus-visible:ring-2 peer-focus-visible:ring-forest">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('renovation_type')
                    <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </fieldset>

            <fieldset class="mt-6">
                <legend class="text-sm font-medium text-charcoal">Property type</legend>
                <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-3">
                    @foreach (\App\Models\Project::propertyTypes() as $value => $label)
                        <label class="cursor-pointer">
                            <input type="radio" name="property_type" value="{{ $value }}" class="peer sr-only" @checked(old('property_type') === $value) @required($loop->first)>
                            <span class="block rounded-2xl border border-line bg-ivory px-3 py-3 text-center text-sm transition duration-300 peer-checked:border-forest peer-checked:bg-[#e7f0e4] peer-focus-visible:ring-2 peer-focus-visible:ring-forest">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('property_type')
                    <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </fieldset>

            <div class="mt-8 flex flex-wrap items-center justify-between gap-3">
                <a href="{{ route('homeowner.projects.index') }}" class="rounded-full border border-[#ddd6c8] px-5 py-3 text-sm font-medium text-charcoal transition hover:border-forest hover:text-forest">Cancel</a>
                <button type="submit" class="rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory transition duration-300 hover:bg-leaf">Save and continue</button>
            </div>
        </form>

        <aside class="overflow-hidden rounded-3xl border border-[#ece7dc] bg-forest text-ivory shadow-sm">
            <img src="{{ asset('images/renovahub-hero.jpg') }}" alt="Timber and glass house with a pool and landscaping" class="h-44 w-full object-cover">
            <div class="p-5">
                <h2 class="font-serif text-2xl">Project details first</h2>
                <p class="mt-2 text-sm leading-relaxed text-white/75">After this step you will enter the property address, then the budget, timeline and team.</p>
            </div>
        </aside>
    </div>
</x-homeowner-layout>
