<article class="rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm">
    <div class="flex items-start justify-between gap-3">
        <a href="{{ route('homeowner.projects.tasks.show', [$task->project, $task]) }}" class="font-medium text-[#123D2B]">{{ $task->name }}</a>
        @include('homeowner.tasks.partials.status', ['task' => $task])
    </div>
    @if ($showProject)
        <div class="mt-3">@include('homeowner.partials.project-chip', ['project' => $task->project])</div>
    @endif
    <p class="mt-2 text-sm text-[#66756C]">{{ \Illuminate\Support\Str::limit($task->description, 120) }}</p>
    <dl class="mt-3 grid grid-cols-2 gap-2 text-xs text-[#66756C]">
        <div><dt>Category</dt><dd class="font-medium text-[#123D2B]">{{ $task->categoryLabel() }}</dd></div>
        <div><dt>Assigned</dt><dd class="font-medium text-[#123D2B]">{{ $task->assigneeLabel() }}</dd></div>
        <div><dt>Due</dt><dd class="font-medium text-[#123D2B]">{{ $task->due_on?->format('j M Y') ?? '—' }}</dd></div>
        <div><dt>Progress</dt><dd class="font-medium text-[#123D2B]">{{ (int) $task->progress }}%</dd></div>
    </dl>
</article>
