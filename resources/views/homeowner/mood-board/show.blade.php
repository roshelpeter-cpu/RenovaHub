<x-homeowner-layout :title="$project->name.' mood board'" :flush="true" :canvas="true">
    <x-project-context :project="$project" section="mood-board">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="font-serif text-3xl text-[#123D2B]">{{ $project->isCompleted() ? 'Final Design Mood Board' : 'Design Mood Board' }}</h2>
                <p class="mt-1 text-sm text-[#66756C]">{{ $project->moodBoard?->summary ?: 'Design direction for this project.' }}</p>
            </div>
            @if (! $project->isClosedRecord())
                <button type="button" class="rounded-full bg-[#123D2B] px-4 py-2 text-sm font-medium text-white" onclick="document.getElementById('add-inspiration').classList.toggle('hidden')">+ Add Inspiration</button>
            @endif
        </div>
        @if (! $project->isClosedRecord())
            <div id="add-inspiration" class="{{ $errors->any() ? '' : 'hidden' }}">
                @include('homeowner.mood-board.partials.inspiration-form', ['projects' => collect([$project]), 'project' => $project])
            </div>
        @endif
        @if (! $project->moodBoard)
            <div class="mt-6">@include('homeowner.partials.empty', ['title' => 'No mood board yet', 'body' => 'Inspiration and material selections will appear here.'])</div>
        @else
            <div class="mt-6 rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm sm:p-5">
                @include('homeowner.mood-board.partials.board', ['project' => $project, 'embedded' => true])
            </div>
        @endif
    </x-project-context>
</x-homeowner-layout>
