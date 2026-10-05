<x-contractor-layout title="My Projects">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="rh-serif text-4xl text-[#123D2B] sm:text-5xl">My Projects</h1>
            <p class="mt-2 text-sm text-[#66756C]">Projects where you are the confirmed contractor.</p>
        </div>
    </div>
    <div class="mt-5 flex flex-wrap gap-2">
        @foreach (['all' => 'All', 'active' => 'Active', 'planning' => 'Planning', 'construction' => 'Construction', 'completed' => 'Completed'] as $key => $label)
            <a href="{{ route('contractor.projects.index', ['filter' => $key]) }}" class="rounded-full px-4 py-2 text-sm {{ $filter === $key ? 'bg-[#123D2B] text-white' : 'border border-[#ece7dc] bg-white text-[#66756C]' }}">{{ $label }}</a>
        @endforeach
    </div>
    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        @forelse ($projects as $project)
            @include('contractor.partials.project-card', ['project' => $project])
        @empty
            <p class="text-sm text-[#66756C]">No projects in this filter.</p>
        @endforelse
    </div>
</x-contractor-layout>
