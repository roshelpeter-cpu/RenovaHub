<x-designer-layout title="My Projects">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="font-serif text-4xl text-[#123D2B] sm:text-5xl">My Projects</h1>
            <p class="mt-2 text-sm text-[#66756C]">Design work you have accepted.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach (['all' => 'All', 'active' => 'Active', 'awaiting' => 'Awaiting Approval', 'completed' => 'Completed'] as $key => $label)
                <a href="{{ route('designer.projects.index', ['filter' => $key]) }}" class="rounded-full px-4 py-2 text-sm {{ $filter === $key ? 'bg-[#123D2B] text-white' : 'bg-white text-[#66756C]' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>
    <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($projects as $project)
            @include('designer.partials.project-card', ['project' => $project])
        @empty
            <p class="text-sm text-[#66756C]">No projects in this view. Accept an invitation to begin.</p>
        @endforelse
    </div>
</x-designer-layout>
