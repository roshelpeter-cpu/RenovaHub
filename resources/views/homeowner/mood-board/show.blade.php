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
            <div class="mt-4 flex flex-wrap items-center gap-3 rounded-2xl border border-[#ece7dc] bg-white p-4">
                <p class="text-sm text-[#123D2B]">The designer submitted this mood board for your decision.</p>
                <form method="POST" action="{{ route('homeowner.projects.mood-board.approve', $project) }}">@csrf<button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Approve Mood Board</button></form>
                <button type="button" class="rounded-full border border-[#123D2B] px-4 py-2 text-sm text-[#123D2B]" onclick="document.getElementById('mood-change').showModal()">Request Changes</button>
            </div>
            <dialog id="mood-change" class="w-full max-w-lg rounded-3xl border border-[#ece7dc] p-6 backdrop:bg-[#123D2B]/40">
                <form method="POST" action="{{ route('homeowner.projects.mood-board.request-changes', $project) }}" class="grid gap-3">
                    @csrf
                    <h3 class="font-serif text-2xl text-[#123D2B]">Request Changes</h3>
                    <label class="text-sm text-[#66756C]">Change request<input name="title" required class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-[#123D2B]" placeholder="Please use warmer wood tones."></label>
                    <label class="text-sm text-[#66756C]">Comment<textarea name="revision_note" required minlength="8" class="mt-1 w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-[#123D2B]" placeholder="Tell the designer what should change."></textarea></label>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="rounded-full border border-[#c9c2b4] px-4 py-2 text-sm" onclick="document.getElementById('mood-change').close()">Cancel</button>
                        <button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Submit</button>
                    </div>
                </form>
            </dialog>
        @elseif ($project->moodBoard?->status === 'revision_requested')
            <p class="mt-4 rounded-2xl bg-[#F6F1E7] px-4 py-3 text-sm text-[#123D2B]">Changes Requested</p>
        @elseif ($project->moodBoard?->isFinal())
            <p class="mt-4 rounded-2xl bg-[#E7F0E4] px-4 py-3 text-sm text-[#123D2B]">Approved</p>
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
            <section class="mt-6 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
                <h3 class="font-serif text-2xl text-[#123D2B]">Homeowner Change Requests</h3>
                <ul class="mt-3 divide-y divide-[#ece7dc] text-sm">
                    @forelse ($project->designChangeRequests->where('design_impact', 'mood_board') as $change)
                        <li class="py-3">
                            <p class="font-medium text-[#123D2B]">{{ $change->title }}</p>
                            <p class="text-[#66756C]">{{ $change->created_at?->format('j M Y') }} · {{ $change->statusLabel() }}</p>
                            <p class="mt-1 text-[#66756C]">{{ $change->description }}</p>
                        </li>
                    @empty
                        <li class="py-3 text-[#66756C]">No change requests yet.</li>
                    @endforelse
                </ul>
            </section>
        @endif
    </x-project-context>
</x-homeowner-layout>
