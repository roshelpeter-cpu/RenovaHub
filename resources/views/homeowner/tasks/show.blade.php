<x-homeowner-layout :title="$task->name" :flush="true" :canvas="true">
    <x-project-context :project="$project" section="tasks">
    <article class="mt-6 max-w-3xl rounded-3xl border border-[#ece7dc] bg-white p-6 shadow-sm">
        <p class="text-[11px] uppercase tracking-[0.16em] text-olive">{{ $project->name }}</p>
        <h1 class="mt-2 font-serif text-3xl text-forest">{{ $task->name }}</h1>
        <p class="mt-3 text-sm leading-relaxed text-charcoal">{{ $task->description ?: 'No further detail has been recorded.' }}</p>
        <dl class="mt-5 grid gap-3 text-sm sm:grid-cols-2">
            <div><dt class="text-mist">Status</dt><dd class="font-medium">{{ $task->statusLabel() }}</dd></div>
            <div><dt class="text-mist">Priority</dt><dd class="font-medium">{{ ucfirst($task->priority) }}</dd></div>
            <div><dt class="text-mist">Category</dt><dd class="font-medium">{{ ucfirst($task->category) }}</dd></div>
            <div><dt class="text-mist">Assigned</dt><dd class="font-medium">{{ $task->assignee?->name ?? 'Unassigned' }}</dd></div>
            <div><dt class="text-mist">Due</dt><dd class="font-medium">{{ $task->due_on?->format('j M Y') ?? 'Not set' }}</dd></div>
            <div><dt class="text-mist">Progress</dt><dd class="font-medium">{{ $task->progress }}%</dd></div>
        </dl>
        <p class="mt-5 text-sm text-[#66756C]">The contractor manages this task. You can review the status and progress.</p>
    </article>
    </x-project-context>
</x-homeowner-layout>
