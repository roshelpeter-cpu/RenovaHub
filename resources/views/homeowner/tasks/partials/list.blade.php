@php
    $filteredEmpty = $tasks->isEmpty();
    $noTasksAtAll = ! $project && $summary && (int) $summary->total === 0;
@endphp

@if ($filteredEmpty)
    <div class="mt-6">
        @if ($project)
            @include('homeowner.partials.empty', ['title' => 'No tasks found for this project.', 'body' => 'Tasks for this project will appear here when the contractor adds them.'])
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
