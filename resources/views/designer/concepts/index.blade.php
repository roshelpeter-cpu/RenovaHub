<x-designer-layout title="Design Concepts">
    <h1 class="font-serif text-4xl text-[#123D2B] sm:text-5xl">Design Concepts</h1>
    <p class="mt-2 text-sm text-[#66756C]">Draft renders and floor plans, then submit them for homeowner approval.</p>
    @if ($projects->isNotEmpty())
        @php($target = $projects->firstWhere('id', (int) request('project')) ?? $projects->first())
        <form method="POST" action="{{ route('designer.concepts.store', $target) }}" enctype="multipart/form-data" class="mt-6 grid gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm md:grid-cols-2">
            @csrf
            <label class="text-sm md:col-span-2">Project
                <select id="concept-project" class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2" onchange="this.form.action = this.selectedOptions[0].dataset.action">
                    @foreach ($projects as $project)
                        <option data-action="{{ route('designer.concepts.store', $project) }}" @selected(request('project') == $project->id)>{{ $project->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="text-sm">Title<input name="title" required class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2"></label>
            <label class="text-sm">Images<input name="images[]" type="file" accept="image/*" multiple class="mt-1 w-full text-sm"></label>
            <label class="text-sm md:col-span-2">Description<textarea name="description" class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2"></textarea></label>
            <button class="w-fit rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Save draft</button>
        </form>
    @endif
    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        @forelse ($concepts as $concept)
            <article class="overflow-hidden rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
                @if ($concept->files->first())
                    <img src="{{ $concept->files->first()->url() }}" alt="" class="h-40 w-full object-cover">
                @endif
                <div class="p-4">
                    <p class="text-xs text-[#66756C]">{{ $concept->project->name }}</p>
                    <h2 class="mt-1 font-serif text-xl text-[#123D2B]">{{ $concept->title }}</h2>
                    <p class="mt-1 text-sm text-[#66756C]">{{ $concept->statusLabel() }} · {{ $concept->submitted_at?->format('j M Y') ?? 'Not submitted' }}</p>
                    <a href="{{ route('designer.concepts.show', [$concept->project, $concept]) }}" class="mt-3 inline-flex text-sm text-[#123D2B] hover:underline">View Details</a>
                </div>
            </article>
        @empty
            <p class="text-sm text-[#66756C]">No concepts yet.</p>
        @endforelse
    </div>
</x-designer-layout>
