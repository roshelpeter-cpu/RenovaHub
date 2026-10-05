<x-contractor-layout title="My Projects">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="rh-serif text-3xl text-[#123D2B]">My Projects</h1>
            <p class="mt-2 text-sm text-[#66756C]">Manage and track all renovation projects assigned to you.</p>
        </div>
    </div>
    <div class="mt-5 flex flex-wrap gap-2">
        @foreach (['all' => 'All', 'active' => 'Active', 'planning' => 'Planning', 'procurement' => 'Procurement', 'construction' => 'Construction', 'completed' => 'Completed'] as $key => $label)
            <a href="{{ route('contractor.projects.index', ['filter' => $key]) }}" class="border-b-2 px-3 py-2 text-sm {{ $filter === $key ? 'border-[#123D2B] font-medium text-[#123D2B]' : 'border-transparent text-[#66756C]' }}">{{ $label }}</a>
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
