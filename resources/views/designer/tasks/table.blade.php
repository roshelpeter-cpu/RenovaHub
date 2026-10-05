<div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
    <div>
        <h1 class="font-serif text-4xl text-[#123D2B]">Tasks</h1>
        <p class="mt-2 text-sm text-[#66756C]">Design tasks only. Construction tasks stay with the contractor.</p>
    </div>
    @if (! $project && $projects->isNotEmpty())
        <form method="GET">
            <select name="project" class="rounded-2xl border border-[#ece7dc] bg-white px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                <option value="">All Projects</option>
                @foreach ($projects as $option)
                    <option value="{{ $option->id }}" @selected(request('project') == $option->id)>{{ $option->name }}</option>
                @endforeach
            </select>
        </form>
    @endif
</div>
<div class="mt-4 space-y-3">
    @forelse ($tasks as $task)
        <article class="rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h2 class="font-medium text-[#123D2B]">{{ $task->name }}</h2>
                    <p class="text-xs text-[#66756C]">{{ $task->project->name }} · {{ ucfirst($task->category) }} · Due {{ $task->due_on?->format('j M Y') ?? '—' }} · {{ $task->statusLabel() }} · {{ $task->progress }}%</p>
                    @if ($task->notes)<p class="mt-2 text-sm text-[#66756C]">{{ $task->notes }}</p>@endif
                </div>
            </div>
            <form method="POST" action="{{ route('designer.tasks.update', $task) }}" enctype="multipart/form-data" class="mt-3 grid gap-2 md:grid-cols-4">
                @csrf
                @method('PUT')
                <select name="status" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                    @foreach (['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $value => $label)
                        <option value="{{ $value }}" @selected($task->status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <input type="number" name="progress" min="0" max="100" value="{{ $task->progress }}" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                <input name="notes" value="{{ $task->notes }}" placeholder="Notes" class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm">
                <div class="flex gap-2">
                    <input type="file" name="attachment" class="text-xs">
                    <button class="rounded-full bg-[#123D2B] px-3 py-2 text-sm text-white">Save</button>
                </div>
            </form>
        </article>
    @empty
        <p class="text-sm text-[#66756C]">No design tasks.</p>
    @endforelse
</div>
@if (method_exists($tasks, 'links'))
    <div class="mt-4">{{ $tasks->links() }}</div>
@endif
