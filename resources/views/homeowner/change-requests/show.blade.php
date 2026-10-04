<x-homeowner-layout :title="$change->title" :flush="true" :canvas="true">
    <x-project-context :project="$project" section="change-requests">
    <article class="max-w-3xl rounded-3xl border border-[#ece7dc] bg-white p-6 shadow-sm">
        <p class="text-xs uppercase tracking-[0.14em] text-olive">{{ $change->statusLabel() }} · {{ ucfirst($change->priority) }} priority</p>
        <h1 class="mt-2 font-serif text-3xl text-forest">{{ $change->title }}</h1>
        <p class="mt-3 text-sm leading-relaxed text-charcoal">{{ $change->description }}</p>
        <dl class="mt-5 grid gap-3 text-sm sm:grid-cols-2">
            <div><dt class="text-mist">Requested by</dt><dd>{{ $change->requester?->name }}</dd></div>
            <div><dt class="text-mist">Date</dt><dd>{{ $change->created_at->format('j M Y') }}</dd></div>
            <div><dt class="text-mist">Project</dt><dd>{{ $project->name }}</dd></div>
            <div><dt class="text-mist">Category</dt><dd>{{ ucfirst($change->category) }}</dd></div>
            <div><dt class="text-mist">Cost impact</dt><dd>{{ $change->cost_impact !== null ? 'LKR '.number_format((float) $change->cost_impact, 2) : 'Not provided yet' }}</dd></div>
            <div><dt class="text-mist">Timeline impact</dt><dd>{{ $change->timeline_impact ?: 'Not provided yet' }}</dd></div>
        </dl>
        @if ($change->reason)<p class="mt-4 text-sm text-mist"><span class="font-medium text-charcoal">Reason. </span>{{ $change->reason }}</p>@endif
        <div class="mt-4 rounded-2xl bg-[#F6F1E7] p-4 text-sm">
            <p><span class="font-medium">Contractor. </span>{{ $change->contractor_response ?: 'No response yet.' }}</p>
            <p class="mt-2"><span class="font-medium">Designer. </span>{{ $change->designer_response ?: 'No response yet.' }}</p>
        </div>
    </article>
    </x-project-context>
</x-homeowner-layout>
