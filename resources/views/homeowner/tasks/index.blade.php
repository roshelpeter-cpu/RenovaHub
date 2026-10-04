<x-homeowner-layout title="Tasks">
    @if ($project)
        <p class="text-[11px] font-medium uppercase tracking-[0.18em] text-olive">{{ $project->name }}</p>
        @include('homeowner.projects.partials.tabs', ['project' => $project])
    @endif
    <h1 class="mt-4 font-serif text-3xl text-forest sm:text-4xl">Tasks</h1>
    <p class="mt-2 text-sm text-mist">Work the team is tracking. Construction progress is updated by the project record, not edited here.</p>
    @unless ($project)
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach (['' => 'All', 'pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'blocked' => 'Blocked'] as $value => $label)
                <a href="{{ route('homeowner.tasks.index', ['status' => $value ?: null]) }}" class="rounded-full px-4 py-2 text-sm {{ $status === $value ? 'bg-forest text-ivory' : 'bg-white text-charcoal' }}">{{ $label }}</a>
            @endforeach
        </div>
    @endunless
    @if ($tasks->isEmpty())
        <div class="mt-6">@include('homeowner.partials.empty', ['title' => 'No tasks yet', 'body' => 'Tasks appear here once a project team starts the work.'])</div>
    @else
        <div class="mt-6 overflow-x-auto rounded-3xl border border-[#ece7dc] bg-white shadow-sm">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-[#F6F1E7] text-xs uppercase tracking-[0.12em] text-mist">
                    <tr><th class="px-4 py-3">Task</th><th class="px-4 py-3">Project</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Assigned</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Due</th></tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        <tr class="border-t border-[#ece7dc]">
                            <td class="px-4 py-3"><a href="{{ route('homeowner.projects.tasks.show', [$task->project, $task]) }}" class="font-medium text-forest hover:underline">{{ $task->name }}</a></td>
                            <td class="px-4 py-3">{{ $task->project->name }}</td>
                            <td class="px-4 py-3">{{ ucfirst($task->category) }}</td>
                            <td class="px-4 py-3">{{ $task->assignee?->name ?? 'Unassigned' }}</td>
                            <td class="px-4 py-3">{{ $task->statusLabel() }}</td>
                            <td class="px-4 py-3">{{ $task->due_on?->format('j M Y') ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $tasks->links() }}</div>
    @endif
</x-homeowner-layout>
