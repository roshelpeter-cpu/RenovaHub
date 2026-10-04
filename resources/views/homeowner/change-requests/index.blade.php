<x-homeowner-layout title="Change Requests" :canvas="true" :flush="(bool) $project">
    @php
        $openProjects = $projects->filter(fn ($option) => ! $option->isClosedRecord());
        $canSubmit = $project ? ! $project->isClosedRecord() : $openProjects->isNotEmpty();
    @endphp
    @if ($project)
        <x-project-context :project="$project" section="change-requests">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="font-serif text-3xl text-[#123D2B]">Project Change Requests</h2>
                    <p class="mt-2 text-sm text-[#66756C]">Requests for this project only.</p>
                </div>
                @if ($canSubmit)
                    <a href="{{ route('homeowner.projects.change-requests.create', $project) }}" class="rounded-full bg-[#123D2B] px-4 py-2 text-sm font-medium text-white">+ Submit Change Request</a>
                @endif
            </div>
            @include('homeowner.change-requests.partials.groups')
        </x-project-context>
    @else
        <p class="text-sm text-[#66756C]">
            <a href="{{ route('homeowner.home') }}" class="hover:text-[#123D2B]">Home</a>
            <span class="mx-1.5 text-[#c9c2b4]">›</span>
            Change Requests
        </p>
        <div class="mt-4 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="font-serif text-4xl text-[#123D2B] sm:text-5xl">Change Requests</h1>
                <p class="mt-2 max-w-2xl text-sm text-[#66756C]">Track and manage all change requests across your renovation projects.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <form method="GET">
                    <label class="block text-sm">
                        <span class="mb-1 block text-xs text-[#66756C]">Project</span>
                        <select name="project" class="min-w-56 rounded-2xl border border-[#ece7dc] bg-white px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                            <option value="">All Projects</option>
                            @foreach ($projects as $option)
                                <option value="{{ $option->id }}" @selected((int) ($filters['project'] ?? 0) === $option->id)>{{ $option->name }}</option>
                            @endforeach
                        </select>
                    </label>
                </form>
                @if ($canSubmit)
                    <button type="button" class="rounded-full bg-[#123D2B] px-4 py-2 text-sm font-medium text-white" onclick="document.getElementById('new-change').classList.toggle('hidden')">+ Submit Change Request</button>
                @endif
            </div>
        </div>
        @if ($canSubmit)
            <form id="new-change" method="GET" action="" class="mt-4 hidden rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm">
                <p class="text-sm text-[#66756C]">Choose an open project. Completed projects cannot receive a new request.</p>
                <div class="mt-3 flex flex-col gap-3 sm:flex-row">
                    <select class="rounded-2xl border border-[#ece7dc] px-3 py-2 text-sm" onchange="if (this.value) window.location = this.value">
                        <option value="">Select a project</option>
                        @foreach ($openProjects as $option)
                            <option value="{{ route('homeowner.projects.change-requests.create', $option) }}">{{ $option->name }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        @endif
        <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach ([['Total Change Requests', $summary['total'], 'Across all projects'], ['Pending Review', $summary['pending'], 'Awaiting your approval'], ['Approved', $summary['approved'], 'Accepted changes'], ['Rejected', $summary['rejected'], 'Declined changes']] as [$label, $value, $hint])
                <article class="rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm">
                    <p class="text-sm text-[#123D2B]">{{ $label }}</p>
                    <p class="mt-1 font-serif text-3xl text-[#123D2B]">{{ $value }}</p>
                    <p class="text-xs text-[#66756C]">{{ $hint }}</p>
                </article>
            @endforeach
        </div>
        <form method="GET" action="{{ route('homeowner.change-requests.index') }}" class="mt-4 grid gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm md:grid-cols-2 xl:grid-cols-5">
            @if (! empty($filters['project']))<input type="hidden" name="project" value="{{ $filters['project'] }}">@endif
            <label class="block text-sm xl:col-span-2"><span class="mb-1 block text-xs text-[#66756C]">Search</span><input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search change requests..." class="w-full rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] px-3 py-2 text-sm"></label>
            <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">Status</span>
                <select name="status" class="w-full rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                    <option value="">All Status</option>
                    @foreach (['pending' => 'Pending', 'in_review' => 'In Review', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">Category</span>
                <select name="category" class="w-full rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                    <option value="">All Categories</option>
                    @foreach (\App\Models\ChangeRequest::categories() as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['category'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">From</span><input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] px-3 py-2 text-sm"></label>
            <div class="flex items-end gap-2">
                <button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm font-medium text-white">Search</button>
                <a href="{{ route('homeowner.change-requests.index', array_filter(['project' => $filters['project'] ?? null])) }}" class="rounded-full border border-[#ece7dc] px-4 py-2 text-sm">Clear Filters</a>
            </div>
        </form>
        @include('homeowner.change-requests.partials.groups')
    @endif
</x-homeowner-layout>
