<x-designer-layout title="Design Brief">
    @include('designer.partials.project-header')
    @php $brief = ($project->workspace_meta ?? [])['brief'] ?? []; @endphp
    <div class="mt-6 grid gap-4 lg:grid-cols-2">
        <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="font-serif text-2xl text-[#123D2B]">Client requirements</h2>
            <p class="mt-3 text-sm leading-relaxed text-[#66756C]">{{ $project->requirements ?: $project->description }}</p>
            <dl class="mt-4 space-y-2 text-sm">
                <div><dt class="text-xs text-[#66756C]">Property</dt><dd>{{ $project->displayTypeLabel() }} · {{ $project->size_sq_ft ? number_format($project->size_sq_ft).' sq ft' : 'Size to confirm' }}</dd></div>
                <div><dt class="text-xs text-[#66756C]">Rooms</dt><dd>{{ $brief['rooms'] ?? 'See the homeowner notes.' }}</dd></div>
                <div><dt class="text-xs text-[#66756C]">Style</dt><dd>{{ $brief['style'] ?? 'To be confirmed with the homeowner.' }}</dd></div>
                <div><dt class="text-xs text-[#66756C]">Colour preferences</dt><dd>{{ $brief['colours'] ?? 'Warm neutrals unless the brief says otherwise.' }}</dd></div>
                <div><dt class="text-xs text-[#66756C]">Functional requirements</dt><dd>{{ $brief['functional'] ?? $project->additional_instructions }}</dd></div>
            </dl>
        </article>
        <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="font-serif text-2xl text-[#123D2B]">Homeowner inspiration</h2>
            <p class="mt-3 text-sm text-[#66756C]">{{ $brief['notes'] ?? 'Reference images collected for this project.' }}</p>
            <div class="mt-4 grid grid-cols-2 gap-2">
                @foreach ($project->referenceImages->take(6) as $image)
                    <img src="{{ $image->url() }}" alt="" class="h-28 w-full rounded-xl object-cover">
                @endforeach
            </div>
        </article>
    </div>
</x-designer-layout>
