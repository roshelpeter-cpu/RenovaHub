<x-contractor-layout title="Budget">
    <h1 class="rh-serif text-4xl text-[#123D2B]">Project Budget</h1>
    <p class="mt-2 text-sm text-[#66756C]">Manage project budget and remaining spend.</p>
    @if ($projects->isEmpty())
        <p class="mt-6 text-sm text-[#66756C]">Accept a project before managing its budget.</p>
    @else
        <form method="GET" class="mt-4">
            <select name="project" class="rounded-2xl border border-[#ddd6c8] bg-white px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                @foreach ($projects as $option)
                    <option value="{{ $option->id }}" @selected($project && $project->id === $option->id)>{{ $option->name }}</option>
                @endforeach
            </select>
        </form>
        @if ($project)
            <h2 class="rh-serif mt-6 text-2xl text-[#123D2B]">{{ $project->name }}</h2>
            @include('contractor.partials.budget', ['project' => $project])
        @endif
    @endif
</x-contractor-layout>
