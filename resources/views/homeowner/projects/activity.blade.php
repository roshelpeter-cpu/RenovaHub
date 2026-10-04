<x-homeowner-layout :title="$project->name">
    @include('homeowner.projects.partials.tabs', ['project' => $project])

    <h1 class="mt-6 font-serif text-3xl text-forest">Activity</h1>
    <p class="mt-2 text-sm text-mist">{{ $project->name }}</p>

    @if ($entries->isEmpty())
        <div class="mt-6">@include('homeowner.partials.empty', ['title' => 'No activity yet', 'body' => 'Invitations, quotations, documents and messages will appear here as the project moves.'])</div>
    @else
        <ol class="mt-6 space-y-3">
            @foreach ($entries as $entry)
                <li class="rounded-2xl border border-[#ece7dc] bg-white px-5 py-4 shadow-sm">
                    <p class="text-sm text-charcoal">{{ $entry->description }}</p>
                    <p class="mt-1 text-xs uppercase tracking-[0.12em] text-mist">{{ $entry->created_at->format('j M Y, H:i') }}@if($entry->user) · {{ $entry->user->name }}@endif</p>
                </li>
            @endforeach
        </ol>
        <div class="mt-6">{{ $entries->links() }}</div>
    @endif
</x-homeowner-layout>
