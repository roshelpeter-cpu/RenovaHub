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
        @if ($project->moodBoard?->status === 'awaiting_approval')
            <div class="mt-4 rounded-2xl border border-[#ece7dc] bg-white p-4">
                <p class="text-sm text-[#123D2B]">The designer submitted this mood board for your decision.</p>
                <div class="mt-3 flex flex-col gap-3 lg:flex-row lg:items-end">
                    <form method="POST" action="{{ route('homeowner.projects.mood-board.approve', $project) }}">@csrf<button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Approve mood board</button></form>
                    <form method="POST" action="{{ route('homeowner.projects.mood-board.request-changes', $project) }}" class="flex flex-1 flex-col gap-2 sm:flex-row">
                        @csrf
                        <input name="revision_note" required placeholder="What should change?" class="w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                        <button class="rounded-full border border-[#123D2B] px-4 py-2 text-sm text-[#123D2B]">Request changes</button>
                    </form>
                </div>
            </div>
        @endif
        @if ($project->moodBoard?->isFinal() && ! $project->isClosedRecord())
            <details class="mt-4 rounded-2xl border border-[#ece7dc] bg-white p-4">
                <summary class="cursor-pointer text-sm font-medium text-[#123D2B]">Request a design change</summary>
                <form method="POST" action="{{ route('homeowner.projects.design-changes.store', $project) }}" class="mt-3 grid gap-2">
                    @csrf
                    <input name="title" required placeholder="Title" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                    <textarea name="description" required placeholder="Describe the change" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm"></textarea>
                    <button class="w-fit rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Send to designer</button>
                </form>
            </details>
            @foreach ($project->designConcepts->where('status', 'awaiting_approval') as $concept)
                <div class="mt-3 rounded-2xl border border-[#ece7dc] bg-white p-4">
                    <p class="font-medium text-[#123D2B]">{{ $concept->title }} is awaiting your decision.</p>
                    <form method="POST" action="{{ route('homeowner.concepts.decide', [$project, $concept]) }}" class="mt-2 flex flex-wrap gap-2">
                        @csrf
                        <button name="decision" value="approve" class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Approve concept</button>
                        <input name="note" placeholder="Changes, if you are not approving" class="min-w-48 flex-1 rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                        <button name="decision" value="changes" class="rounded-full border border-[#123D2B] px-4 py-2 text-sm text-[#123D2B]">Request changes</button>
                    </form>
                </div>
            @endforeach
        @endif
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
