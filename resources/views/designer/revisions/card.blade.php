<article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="font-serif text-2xl text-[#123D2B]">{{ $revision->title }}</h2>
            <p class="mt-1 text-sm text-[#66756C]">{{ $revision->project->name }} · {{ $revision->requester?->name }} · {{ $revision->created_at->format('j M Y') }}</p>
        </div>
        <span class="rounded-full bg-[#F3EFE4] px-3 py-1 text-xs text-[#123D2B]">{{ $revision->statusLabel() }}</span>
    </div>
    <p class="mt-3 text-sm text-[#66756C]">{{ $revision->description }}</p>
    @if ($revision->design_impact)
        <p class="mt-2 text-xs text-[#66756C]">Design impact: {{ $revision->design_impact }}</p>
    @endif
    @if ($revision->rejection_reason)
        <p class="mt-2 text-sm text-[#123D2B]">Reason: {{ $revision->rejection_reason }}</p>
    @endif
    @can('decide', $revision)
        <div class="mt-4 flex flex-col gap-3 lg:flex-row lg:items-end">
            <form method="POST" action="{{ route('designer.revisions.accept', $revision) }}">@csrf<button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Accept</button></form>
            <form method="POST" action="{{ route('designer.revisions.reject', $revision) }}" class="flex flex-1 flex-col gap-2 sm:flex-row">
                @csrf
                <input name="rejection_reason" required minlength="10" placeholder="Why this change cannot be taken" class="w-full rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                <button class="rounded-full border border-[#123D2B] px-4 py-2 text-sm text-[#123D2B]">Reject</button>
            </form>
        </div>
    @endcan
    @if ($revision->status === 'accepted')
        <form method="POST" action="{{ route('designer.revisions.resubmit', $revision) }}" enctype="multipart/form-data" class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-end">
            @csrf
            <label class="text-sm">Revised file<input type="file" name="file" class="mt-1 block text-sm"></label>
            <input name="note" placeholder="What changed" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
            <button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Submit revised design</button>
        </form>
    @endif
</article>
