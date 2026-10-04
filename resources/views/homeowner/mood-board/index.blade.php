<x-homeowner-layout title="Mood Board" :canvas="true">
    @php
        $openProjects = $projects->filter(fn ($option) => ! $option->isClosedRecord());
        $showAdd = $selected > 0
            ? $openProjects->contains('id', $selected)
            : $openProjects->isNotEmpty();
    @endphp
    <p class="text-sm text-[#66756C]">
        <a href="{{ route('homeowner.home') }}" class="hover:text-[#123D2B]">Home</a>
        <span class="mx-1.5 text-[#c9c2b4]">›</span>
        Mood Board
    </p>
    <div class="mt-4 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h1 class="font-serif text-4xl text-[#123D2B] sm:text-5xl">Mood Board</h1>
            <p class="mt-2 text-sm text-[#66756C]">Your renovation inspiration, materials and design direction across all your projects.</p>
        </div>
        <form method="GET" action="{{ route('homeowner.mood-board.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <label class="block text-sm">
                <span class="mb-1 block text-xs text-[#66756C]">Project</span>
                <select name="project" class="w-full min-w-56 rounded-2xl border border-[#ece7dc] bg-white px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                    <option value="">All Projects</option>
                    @foreach ($projects as $option)
                        <option value="{{ $option->id }}" @selected($selected === $option->id)>{{ $option->name }}</option>
                    @endforeach
                </select>
            </label>
            @if ($showAdd)
                <button type="button" class="rounded-full bg-[#123D2B] px-4 py-2 text-sm font-medium text-white" onclick="document.getElementById('add-inspiration').classList.toggle('hidden')">+ Add Inspiration</button>
            @endif
        </form>
    </div>
    @if ($showAdd)
        <div id="add-inspiration" class="{{ $errors->any() ? '' : 'hidden' }}">
            @include('homeowner.mood-board.partials.inspiration-form', ['projects' => $projects, 'project' => null, 'selected' => $selected])
        </div>
    @endif

    <div class="mt-6 space-y-4">
        @forelse ($boards as $project)
            <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm sm:p-5">
                @include('homeowner.mood-board.partials.board', ['project' => $project])
            </article>
        @empty
            @include('homeowner.partials.empty', ['title' => 'No mood board yet', 'body' => 'A mood board appears after a project is created.'])
        @endforelse
    </div>
</x-homeowner-layout>
