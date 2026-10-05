<x-designer-layout title="Design Work">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="font-serif text-4xl text-[#123D2B] sm:text-5xl">Design Work</h1>
            <p class="mt-2 text-sm text-[#66756C]">Manage the design workflow across your active projects.</p>
        </div>
        <form method="GET">
            <label class="text-xs text-[#66756C]">Project
                <select name="project" class="mt-1 block rounded-2xl border border-[#ece7dc] bg-white px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                    <option value="" @selected(! $focus)>All Projects</option>
                    @foreach ($projects as $project)
                        <option value="{{ $project->id }}" @selected($focus && $focus->id === $project->id)>{{ $project->name }}</option>
                    @endforeach
                </select>
            </label>
        </form>
    </div>
    @forelse ($boards as $board)
        <article class="mt-6 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="font-serif text-2xl text-[#123D2B]">{{ $board['project']->name }}</h2>
            <ol class="mt-5 grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                @foreach ($board['stages'] as $stage)
                    <li class="rounded-2xl bg-[#F7F4EE] p-4">
                        <p class="font-medium text-[#123D2B]">{{ $stage['label'] }}</p>
                        <p class="mt-1 text-xs text-[#66756C]">{{ $stage['status'] }}</p>
                    </li>
                @endforeach
            </ol>
            <div class="mt-5 flex flex-wrap gap-3 text-sm">
                <a class="text-[#123D2B] hover:underline" href="{{ route('designer.projects.brief', $board['project']) }}">Design brief</a>
                <a class="text-[#123D2B] hover:underline" href="{{ route('designer.projects.mood-board', $board['project']) }}">Mood board</a>
                <a class="text-[#123D2B] hover:underline" href="{{ route('designer.concepts.index', ['project' => $board['project']->id]) }}">Design concepts</a>
                <a class="text-[#123D2B] hover:underline" href="{{ route('designer.projects.revisions', $board['project']) }}">Revisions</a>
                <a class="text-[#123D2B] hover:underline" href="{{ route('designer.projects.final-design', $board['project']) }}">Final design</a>
            </div>
        </article>
    @empty
        <p class="mt-6 text-sm text-[#66756C]">Accept a project offer to start the design workflow.</p>
    @endforelse
</x-designer-layout>
