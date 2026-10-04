<x-homeowner-layout :title="$project ? $project->name.' tasks' : 'My Tasks'" :canvas="true" :flush="(bool) $project">
    @if ($project)
        <x-project-context :project="$project" section="tasks">
            <h2 class="font-serif text-3xl text-[#123D2B]">Project Tasks</h2>
            <p class="mt-2 text-sm text-[#66756C]">Tasks for this project only. The contractor creates and updates them.</p>
            @include('homeowner.tasks.partials.list')
        </x-project-context>
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
        @include('homeowner.tasks.partials.list')
    @endif
</x-homeowner-layout>
