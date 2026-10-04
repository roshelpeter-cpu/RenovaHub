<x-homeowner-layout title="Quotations" :canvas="true" :flush="(bool) $project">
    @if ($project)
        <x-project-context :project="$project" section="quotations">
            <h2 class="font-serif text-3xl text-[#123D2B]">Project Quotations</h2>
            <p class="mt-2 text-sm text-[#66756C]">Review contractor quotations for this project. You can approve or reject them.</p>
            @include('homeowner.quotations.partials.groups')
        </x-project-context>
    @else
        <p class="text-sm text-[#66756C]">
            <a href="{{ route('homeowner.home') }}" class="hover:text-[#123D2B]">Home</a>
            <span class="mx-1.5 text-[#c9c2b4]">›</span>
            Quotations
        </p>
        <div class="mt-4 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h1 class="font-serif text-4xl text-[#123D2B] sm:text-5xl">Quotations</h1>
                <p class="mt-2 max-w-2xl text-sm text-[#66756C]">View and manage all quotations from designers and contractors across your projects.</p>
            </div>
            <form method="GET" class="block text-sm">
                <span class="mb-1 block text-xs text-[#66756C]">Project</span>
                <select name="project" class="min-w-56 rounded-2xl border border-[#ece7dc] bg-white px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                    <option value="">All Projects</option>
                    @foreach ($projects as $option)
                        <option value="{{ $option->id }}" @selected((int) ($filters['project'] ?? 0) === $option->id)>{{ $option->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach ([['Total Quotations', $summary['total'], 'Across all projects'], ['Pending Review', $summary['pending'], 'Awaiting your action'], ['Approved', $summary['approved'], 'Accepted quotations'], ['Rejected', $summary['rejected'], 'Declined quotations']] as [$label, $value, $hint])
                <article class="rounded-2xl border border-[#ece7dc] bg-white p-4 shadow-sm">
                    <p class="text-sm text-[#123D2B]">{{ $label }}</p>
                    <p class="mt-1 font-serif text-3xl text-[#123D2B]">{{ $value }}</p>
                    <p class="text-xs text-[#66756C]">{{ $hint }}</p>
                </article>
            @endforeach
        </div>
        <form method="GET" action="{{ route('homeowner.quotations.index') }}" class="mt-4 grid gap-3 rounded-[1.4rem] border border-[#ece7dc] bg-white p-4 shadow-sm md:grid-cols-2 xl:grid-cols-5">
            @if (! empty($filters['project']))<input type="hidden" name="project" value="{{ $filters['project'] }}">@endif
            <label class="block text-sm xl:col-span-2"><span class="mb-1 block text-xs text-[#66756C]">Search</span><input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search quotations..." class="w-full rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] px-3 py-2 text-sm"></label>
            <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">Professional</span>
                <select name="professional" class="w-full rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                    <option value="">All Professionals</option>
                    @foreach ($professionals as $person)
                        <option value="{{ $person->id }}" @selected((int) ($filters['professional'] ?? 0) === $person->id)>{{ $person->name }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">Status</span>
                <select name="status" class="w-full rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] px-3 py-2 text-sm" onchange="this.form.requestSubmit()">
                    <option value="">All Status</option>
                    @foreach (['pending' => 'Pending', 'clarification_required' => 'In Review', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">From</span><input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] px-3 py-2 text-sm"></label>
            <label class="block text-sm"><span class="mb-1 block text-xs text-[#66756C]">To</span><input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-2xl border border-[#ece7dc] bg-[#F7F4EE] px-3 py-2 text-sm"></label>
            <div class="flex items-end gap-2 md:col-span-2 xl:col-span-5">
                <button class="rounded-full bg-[#123D2B] px-4 py-2 text-sm font-medium text-white">Search</button>
                <a href="{{ route('homeowner.quotations.index', array_filter(['project' => $filters['project'] ?? null])) }}" class="rounded-full border border-[#ece7dc] px-4 py-2 text-sm text-[#123D2B]">Clear Filters</a>
            </div>
        </form>
        @include('homeowner.quotations.partials.groups')
    @endif
</x-homeowner-layout>
