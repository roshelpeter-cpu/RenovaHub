<x-homeowner-layout title="Mood Board">
    @include('homeowner.projects.partials.tabs', ['project' => $project])
    @php $board = $project->moodBoard; @endphp
    <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="font-serif text-3xl text-forest">Mood Board</h1>
            <p class="text-sm text-mist">{{ $project->name }}</p>
        </div>
        @if ($board && ! $board->approved_at)
            <form method="POST" action="{{ route('homeowner.projects.mood-board.approve', $project) }}">
                @csrf
                <button class="rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory">Approve Design</button>
            </form>
        @endif
    </div>
    @if (! $board)
        @if ($project->status === \App\Models\Project::STATUS_IN_PROGRESS)
            <div class="mt-8 max-w-xl rounded-[1.4rem] border border-[#ece7dc] bg-white p-8">
                <h2 class="font-serif text-2xl text-[#123D2B]">Not yet decided</h2>
                <p class="mt-2 text-sm leading-relaxed text-[#66756C]">Mood board not yet finalized. The design direction and material selections are still being finalized.</p>
            </div>
        @else
            <div class="mt-6">@include('homeowner.partials.empty', ['title' => 'No mood board yet', 'body' => 'The designer has not published a mood board for this project.'])</div>
        @endif
    @else
        <p class="mt-4 text-sm text-charcoal">Version {{ $board->version }} · Updated {{ $board->updated_at->format('j M Y') }} · {{ $board->author?->name ?? 'Designer' }} @if($board->approved_at) · Approved {{ $board->approved_at->format('j M Y') }} @endif</p>
        <p class="mt-2 max-w-3xl text-sm leading-relaxed text-mist">{{ $board->summary }}</p>
        <div class="mt-6 columns-1 gap-4 sm:columns-2 lg:columns-3">
            @foreach ($board->items as $item)
                <article class="mb-4 break-inside-avoid overflow-hidden rounded-3xl border border-[#ece7dc] bg-white shadow-sm">
                    @if ($item->imageUrl())
                        <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" class="h-48 w-full object-cover">
                    @elseif ($item->colour)
                        <div class="h-24" style="background: {{ $item->colour }}"></div>
                    @endif
                    <div class="p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-olive">{{ $item->kind }}</p>
                        <h2 class="font-serif text-xl text-forest">{{ $item->title }}</h2>
                        @if ($item->body)<p class="mt-1 text-sm text-mist">{{ $item->body }}</p>@endif
                    </div>
                </article>
            @endforeach
        </div>
        <section class="mt-8 max-w-3xl rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm">
            <h2 class="font-serif text-2xl text-forest">Design feedback</h2>
            <p class="mt-1 text-sm text-mist">Comments about finishes and furniture. Use a change request if the work may affect cost or time.</p>
            <form method="POST" action="{{ route('homeowner.projects.mood-board.feedback', $project) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                @csrf
                <div><label for="feedback-title" class="mb-1 block text-sm">Feedback title</label><input id="feedback-title" name="title" required maxlength="150" class="w-full rounded-2xl border border-line px-3 py-2 text-sm"></div>
                <div><label for="feedback-comment" class="mb-1 block text-sm">Comment</label><textarea id="feedback-comment" name="comment" required rows="4" class="w-full rounded-2xl border border-line px-3 py-2 text-sm"></textarea></div>
                <div><label for="feedback-file" class="mb-1 block text-sm">Optional attachment</label><input id="feedback-file" name="attachment" type="file" accept=".jpg,.jpeg,.png,.webp,.pdf" class="text-sm"></div>
                <button class="rounded-full bg-forest px-5 py-3 text-sm font-medium text-ivory">Request Changes</button>
            </form>
            <ul class="mt-6 space-y-3">
                @foreach ($board->feedback as $note)
                    <li class="rounded-2xl bg-[#F6F1E7] p-4">
                        <p class="font-medium text-charcoal">{{ $note->title }}</p>
                        <p class="mt-1 text-sm text-charcoal">{{ $note->comment }}</p>
                        <p class="mt-1 text-xs text-mist">{{ $note->author?->name }} · {{ $note->created_at->format('j M Y') }}</p>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</x-homeowner-layout>
