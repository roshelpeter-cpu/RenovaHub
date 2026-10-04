<x-homeowner-layout title="Property Location">
    <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-olive">{{ $project->name }}</p>
    <h1 class="mt-2 font-serif text-3xl font-medium tracking-[-0.03em] text-forest sm:text-4xl">Property Location</h1>
    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-mist">Search and specify your property location.</p>

    <div class="mt-6">
        @include('homeowner.projects.partials.steps', ['current' => 2, 'project' => $project])
    </div>

    <form method="POST" action="{{ route('homeowner.projects.location.update', $project) }}" class="mt-6 grid items-start gap-6 lg:grid-cols-2">
        @csrf
        @method('PUT')
        <x-validation-errors class="lg:col-span-2" />

        <div class="rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm sm:p-6">
            <div>
                <label for="address" class="mb-2 block text-sm font-medium text-charcoal">Address</label>
                <input id="address" name="address" type="text" value="{{ old('address', $project->address) }}" maxlength="255" autocomplete="street-address" class="block w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">
                @error('address') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="city" class="mb-2 block text-sm font-medium text-charcoal">City</label>
                    <input id="city" name="city" type="text" value="{{ old('city', $project->city) }}" maxlength="100" autocomplete="address-level2" class="block w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">
                    @error('city') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="province" class="mb-2 block text-sm font-medium text-charcoal">Province</label>
                    <input id="province" name="province" type="text" value="{{ old('province', $project->province) }}" maxlength="100" autocomplete="address-level1" class="block w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">
                    @error('province') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-4">
                <label for="postal_code" class="mb-2 block text-sm font-medium text-charcoal">Postal code</label>
                <input id="postal_code" name="postal_code" type="text" value="{{ old('postal_code', $project->postal_code) }}" maxlength="20" autocomplete="postal-code" class="block w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">
                @error('postal_code') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="latitude" class="mb-2 block text-sm font-medium text-charcoal">Latitude <span class="font-normal text-mist">(optional)</span></label>
                    <input id="latitude" name="latitude" type="text" inputmode="decimal" value="{{ old('latitude', $project->latitude) }}" class="block w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">
                    @error('latitude') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="longitude" class="mb-2 block text-sm font-medium text-charcoal">Longitude <span class="font-normal text-mist">(optional)</span></label>
                    <input id="longitude" name="longitude" type="text" inputmode="decimal" value="{{ old('longitude', $project->longitude) }}" class="block w-full rounded-2xl border border-line bg-white px-4 py-3 text-sm outline-none transition focus:border-forest focus:ring-4 focus:ring-forest/10">
                    @error('longitude') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-8 flex flex-wrap items-center justify-between gap-3">
                <a href="{{ route('homeowner.projects.edit', $project) }}" class="rounded-full border border-[#ddd6c8] px-5 py-3 text-sm font-medium text-charcoal transition hover:border-forest">Back</a>
                <button type="submit" class="rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory transition duration-300 hover:bg-leaf">Confirm Location</button>
            </div>
        </div>

        <aside class="rounded-3xl border border-dashed border-olive/50 bg-sand/50 p-5 sm:p-6" aria-label="Location preview">
            <p class="text-[11px] font-medium uppercase tracking-[0.16em] text-olive">Location preview</p>
            <h2 class="mt-2 font-serif text-2xl text-forest">Map area</h2>
            <p class="mt-2 text-sm leading-relaxed text-mist">This space is reserved for a map. Address suggestions and coordinates will come from a location provider later. Nothing here is a live map.</p>
            <dl class="mt-5 space-y-3 text-sm">
                <div>
                    <dt class="text-mist">Address</dt>
                    <dd class="font-medium text-charcoal">{{ $project->address ?: 'Not entered' }}</dd>
                </div>
                <div>
                    <dt class="text-mist">City and province</dt>
                    <dd class="font-medium text-charcoal">{{ $project->locationLabel() ?: 'Not entered' }}</dd>
                </div>
                <div>
                    <dt class="text-mist">Coordinates</dt>
                    <dd class="font-medium text-charcoal">
                        @if ($project->latitude !== null && $project->longitude !== null)
                            {{ $project->latitude }}, {{ $project->longitude }}
                        @else
                            Not entered
                        @endif
                    </dd>
                </div>
            </dl>
            <a href="{{ route('homeowner.projects.budget', $project) }}" class="mt-6 inline-flex rounded-full border border-forest px-4 py-2 text-sm font-medium text-forest transition hover:bg-forest hover:text-ivory">Next</a>
        </aside>
    </form>
</x-homeowner-layout>
