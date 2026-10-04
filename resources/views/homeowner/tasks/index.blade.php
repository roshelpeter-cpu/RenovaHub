<x-homeowner-layout :title="$project ? $project->name.' tasks' : 'My Tasks'" :canvas="true">
    @if ($project)
        <p class="text-sm text-[#66756C]">
            <a href="{{ route('homeowner.home') }}" class="hover:text-[#123D2B]">Home</a>
            <span class="mx-1.5 text-[#c9c2b4]">›</span>
            <a href="{{ route('homeowner.projects.show', $project) }}" class="hover:text-[#123D2B]">{{ $project->name }}</a>
            <span class="mx-1.5 text-[#c9c2b4]">›</span>
            Tasks
        </p>
        @include('homeowner.projects.partials.tabs', ['project' => $project])
        <h1 class="mt-6 font-serif text-4xl text-[#123D2B]">Tasks</h1>
        <p class="mt-2 text-sm text-[#66756C]">Tasks for this project only.</p>
    @else
        <p class="text-sm text-[#66756C]">
            <a href="{{ route('homeowner.home') }}" class="hover:text-[#123D2B]">Home</a>
            <span class="mx-1.5 text-[#c9c2b4]">›</span>
            Tasks
        </p>
        <h1 class="mt-4 font-serif text-4xl text-[#123D2B] sm:text-5xl">My Tasks</h1>
        <p class="mt-2 max-w-2xl text-sm text-[#66756C]">Track and manage tasks across all your renovation projects.</p>

        @if ($summary)
            <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
                @foreach ([['All tasks', $summary->total], ['In progress', $summary->in_progress], ['Completed', $summary->completed], ['Overdue', $summary->overdue]] as [$label, $value])
                    <article class="rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm">
                        <p class="text-xs text-[#66756C]">{{ $label }}</p>
                        <p class="mt-1 font-serif text-2xl text-[#123D2B]">{{ $value }}</p>
                    </article>
                @endforeach
            </div>
        @endif

        <form method="GET" action="{{ route('homeowner.tasks.index') }}" class="mt-6 grid gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm md:grid-cols-2 xl:grid-cols-5">
            <label class="block text-sm">
                <span class="mb-1 block text-xs text-[#66756C]">Project</span>
                <select name="project" class="w-full rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                    <option value="">All Projects</option>
                    @foreach ($projects as $option)
                        <option value="{{ $option->id }}" @selected((int) ($filters['project'] ?? 0) === $option->id)>{{ $option->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm">
                <span class="mb-1 block text-xs text-[#66756C]">Status</span>
                <select name="status" class="w-full rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                    <option value="">All Status</option>
                    @foreach (\App\Models\ProjectTask::filterStatuses() as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm">
                <span class="mb-1 block text-xs text-[#66756C]">Category</span>
                <select name="category" class="w-full rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                    <option value="">All Categories</option>
                    @foreach (\App\Models\ProjectTask::categories() as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['category'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm md:col-span-2 xl:col-span-2">
                <span class="mb-1 block text-xs text-[#66756C]">Search</span>
                <span class="flex gap-2">
                    <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search tasks..." class="w-full rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] px-3 py-2 text-sm">
                    <button class="shrink-0 rounded-full bg-[#123D2B] px-4 py-2 text-sm font-medium text-white">Search</button>
                </span>
            </label>
        </form>
    @endif

    @php
        $filteredEmpty = $tasks->isEmpty();
        $noTasksAtAll = ! $project && $summary && (int) $summary->total === 0;
    @endphp

    @if ($filteredEmpty)
        <div class="mt-6">
            @if ($project)
                @include('homeowner.partials.empty', ['title' => 'No tasks found for this project.', 'body' => 'Tasks for this project will appear here when the team adds them.'])
            @elseif ($noTasksAtAll)
                @include('homeowner.partials.empty', ['title' => 'No tasks yet', 'body' => "You don't have any tasks assigned across your projects."])
            @elseif (! empty($filters['project']))
                @include('homeowner.partials.empty', ['title' => 'No tasks found for this project.', 'body' => 'Try another project, or clear the status and category filters.'])
            @else
                @include('homeowner.partials.empty', ['title' => 'No tasks match these filters.', 'body' => 'Adjust the project, status, category or search and try again.'])
            @endif
        </div>
    @else
        <div class="mt-6 space-y-3 md:hidden">
            @foreach ($tasks as $task)
                @include('homeowner.tasks.partials.card', ['task' => $task, 'showProject' => $project === null])
            @endforeach
        </div>
        <div class="mt-6 hidden overflow-x-auto rounded-[1.4rem] border border-[#ece7dc] bg-white shadow-sm md:block">
            <table class="min-w-[68rem] w-full text-left text-sm">
                <thead class="bg-[#F6F1E7] text-[11px] uppercase tracking-[0.12em] text-[#66756C]">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Task</th>
                        @unless ($project)<th class="px-4 py-3">Project</th>@endunless
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="px-4 py-3">Assigned To</th>
                        <th class="px-4 py-3">Start Date</th>
                        <th class="px-4 py-3">Due Date</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Progress</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        <tr class="border-t border-[#ece7dc] align-top">
                            <td class="px-4 py-3 text-[#66756C]">{{ $tasks->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3 font-medium text-[#123D2B]">
                                <a href="{{ route('homeowner.projects.tasks.show', [$task->project, $task]) }}" class="hover:underline">{{ $task->name }}</a>
                            </td>
                            @unless ($project)
                                <td class="px-4 py-3">@include('homeowner.partials.project-chip', ['project' => $task->project])</td>
                            @endunless
                            <td class="max-w-xs px-4 py-3 text-[#66756C]">{{ \Illuminate\Support\Str::limit($task->description, 90) }}</td>
                            <td class="px-4 py-3">{{ $task->categoryLabel() }}</td>
                            <td class="px-4 py-3">{{ $task->assigneeLabel() }}</td>
                            <td class="px-4 py-3">{{ $task->started_on?->format('j M Y') ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $task->due_on?->format('j M Y') ?? '—' }}</td>
                            <td class="px-4 py-3">@include('homeowner.tasks.partials.status', ['task' => $task])</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 w-16 overflow-hidden rounded-full bg-[#EFE8DA]">
                                        <div class="h-full rounded-full bg-[#123D2B]" style="width: {{ (int) $task->progress }}%"></div>
                                    </div>
                                    <span class="text-xs text-[#66756C]">{{ (int) $task->progress }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('homeowner.projects.tasks.show', [$task->project, $task]) }}" class="font-medium text-[#123D2B] hover:underline">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $tasks->links() }}</div>
    @endif
</x-homeowner-layout>
