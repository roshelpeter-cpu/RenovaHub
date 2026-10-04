<x-homeowner-layout title="Change Requests">
    @if ($project) @include('homeowner.projects.partials.tabs', ['project' => $project]) @endif
    <div class="mt-4 flex items-end justify-between gap-3">
        <div>
            <h1 class="font-serif text-3xl text-forest sm:text-4xl">Change Requests</h1>
            <p class="mt-2 max-w-xl text-sm text-mist">A change request can affect cost, time or construction. Design comments belong on the mood board.</p>
        </div>
        @if ($project)
            <a href="{{ route('homeowner.projects.change-requests.create', $project) }}" class="rounded-full bg-forest px-4 py-2 text-sm font-medium text-ivory">+ New Change Request</a>
        @endif
    </div>
    @if ($changes->isEmpty())
        <div class="mt-6">@include('homeowner.partials.empty', ['title' => 'No change requests', 'body' => 'Open a project and submit a change when the scope, material or timeline needs to move.'])</div>
    @else
        <div class="mt-6 space-y-3">
            @foreach ($changes as $change)
                <a href="{{ route('homeowner.change-requests.show', [$change->project, $change]) }}" class="block rounded-3xl border border-[#ece7dc] bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-lg">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="font-serif text-xl text-forest">{{ $change->title }}</h2>
                        <span class="rounded-full bg-[#e7f0e4] px-3 py-1 text-xs text-forest">{{ $change->statusLabel() }}</span>
                    </div>
                    <p class="mt-1 text-sm text-mist">{{ $change->project->name }} · {{ ucfirst($change->category) }} · {{ ucfirst($change->priority) }}</p>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $changes->links() }}</div>
    @endif
</x-homeowner-layout>
