<x-contractor-layout title="Tasks">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="rh-serif text-4xl text-[#123D2B]">Construction Tasks</h1>
            <p class="mt-2 text-sm text-[#66756C]">Tasks you manage on accepted projects.</p>
        </div>
    </div>
    <form method="GET" class="mt-4 flex flex-wrap gap-2">
        <select name="project" class="rounded-full border border-[#ddd6c8] bg-white px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
            <option value="">All projects</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}" @selected($projectId === $project->id)>{{ $project->name }}</option>
            @endforeach
        </select>
        <select name="status" class="rounded-full border border-[#ddd6c8] bg-white px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
            <option value="">All statuses</option>
            @foreach (['not_started' => 'Not Started', 'pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $value => $label)
                <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </form>
    <div class="mt-6 overflow-x-auto rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-[#F6F1E7] text-xs uppercase tracking-wide text-[#66756C]"><tr><th class="px-4 py-3">Task</th><th class="px-4 py-3">Project</th><th class="px-4 py-3">Assigned</th><th class="px-4 py-3">Due</th><th class="px-4 py-3">Progress</th><th class="px-4 py-3">Status</th></tr></thead>
            <tbody>
                @forelse ($tasks as $task)
                    <tr class="border-t border-[#ece7dc]">
                        <td class="px-4 py-3 text-[#123D2B]">{{ $task->name }}</td>
                        <td class="px-4 py-3">{{ $task->project?->name }}</td>
                        <td class="px-4 py-3">{{ $task->assigneeLabel() }}</td>
                        <td class="px-4 py-3">{{ $task->due_on?->format('j M Y') ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $task->progress }}%</td>
                        <td class="px-4 py-3">{{ $task->contractorStatusLabel() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-[#66756C]">No tasks for this filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <form method="POST" action="{{ route('contractor.tasks.store') }}" class="mt-6 grid gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-5 shadow-sm md:grid-cols-2">
        @csrf
        <h2 class="rh-serif text-2xl text-[#123D2B] md:col-span-2">Add task</h2>
        <select name="project_id" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm" required>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
        </select>
        <input name="name" placeholder="Task name" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm" required>
        <input name="assignee_label" value="Construction Team" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm">
        <select name="category" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm">
            @foreach (\App\Models\ProjectTask::categories() as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
        <select name="priority" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm">
            <option value="low">Low</option>
            <option value="normal" selected>Normal</option>
            <option value="high">High</option>
        </select>
        <select name="status" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm">
            <option value="not_started">Not Started</option>
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
        </select>
        <input type="date" name="started_on" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm">
        <input type="date" name="due_on" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm">
        <input type="number" name="progress" value="0" min="0" max="100" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm">
        <textarea name="notes" rows="2" placeholder="Notes" class="rounded-2xl border border-[#ddd6c8] px-3 py-2 text-sm md:col-span-2"></textarea>
        <button class="w-fit rounded-full bg-[#123D2B] px-4 py-2 text-sm text-white">Create task</button>
    </form>
</x-contractor-layout>
